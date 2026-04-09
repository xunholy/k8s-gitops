package main

import (
	"bytes"
	"context"
	"crypto/tls"
	"crypto/x509"
	"encoding/base64"
	"fmt"
	"log"
	"net/http"
	"net/http/httputil"
	"net/url"
	"os"
	"path"
	"path/filepath"
	"reflect"
	"regexp"
	"strings"

	oidc "github.com/coreos/go-oidc"
	"github.com/spf13/cast"
	"github.com/spf13/cobra"
	"github.com/spf13/viper"
)

var (
	config_file string
	debug       bool
)

type debugTransport struct {
	t http.RoundTripper
}

func (d debugTransport) RoundTrip(req *http.Request) (*http.Response, error) {
	reqDump, err := httputil.DumpRequest(req, true)
	if err != nil {
		return nil, err
	}
	log.Printf("%s", reqDump)

	resp, err := d.t.RoundTrip(req)
	if err != nil {
		return nil, err
	}

	respDump, err := httputil.DumpResponse(resp, true)
	if err != nil {
		resp.Body.Close()
		return nil, err
	}
	log.Printf("%s", respDump)
	return resp, nil
}

// Define each cluster
type Cluster struct {
	Name                      string
	Namespace                 string
	Short_Description         string
	Description               string
	Issuer                    string
	Client_Secret             string
	Client_ID                 string
	Connector_ID              string
	K8s_Master_URI            string
	K8s_Ca_URI                string
	K8s_Ca_Pem                string
	K8s_Ca_Pem_File           string
	K8s_Ca_Pem_Base64_Encoded string
	Static_Context_Name       bool
	Scopes                    []string

	Verifier       *oidc.IDTokenVerifier
	Provider       *oidc.Provider
	OfflineAsScope bool
	Client         *http.Client
	Redirect_URI   string
	Config         Config
}

// Define our configuration
type Config struct {
	Clusters             []Cluster
	Listen               string
	Web_Path_Prefix      string
	TLS_Cert             string
	TLS_Key              string
	IDP_Ca_URI           string
	IDP_Ca_Pem           string
	IDP_Ca_Pem_File      string
	Logo_Uri             string
	Static_Context_Name  bool
	Trusted_Root_Ca      []string
	Trusted_Root_Ca_File string
	Kubectl_Version      string
}

func substituteEnvVars(text string) string {
	re := regexp.MustCompile(`\${([a-zA-Z0-9\-_]+)}`)
	matches := re.FindAllStringSubmatch(text, -1)
	for _, val := range matches {
		envVar := os.Getenv(val[1])
		text = strings.Replace(text, val[0], envVar, -1)
	}
	return text
}

// Start the app
// Do some config parsing
// Setup http-clients and oidc providers
// Define per-cluster handlers
func start_app(config Config) {

	// Config validation
	listenURL, err := url.Parse(config.Listen)
	if err != nil {
		log.Fatalf("parse listen address: %v", err)
	}

	var s struct {
		ScopesSupported []string `json:"scopes_supported"`
	}

	certp, err := x509.SystemCertPool()
	if err != nil {
		log.Fatalf("error reading the system cert pool: %v", err)
	}
	// Load Inline CA certs
	if len(config.Trusted_Root_Ca) > 0 {
		for _, cert := range config.Trusted_Root_Ca {
			ok := certp.AppendCertsFromPEM([]byte(cert))
			if !ok {
				log.Fatalf("Failed to parse a trusted cert, pem format expected")
			}
		}
	}
	// Load CA certs from file
	if config.Trusted_Root_Ca_File != "" {
		content, err := os.ReadFile(config.Trusted_Root_Ca_File)
		if err != nil {
			log.Fatalf("Failed to read file Trusted Root CA %s, %v", config.Trusted_Root_Ca_File, err)
		}
		ok := certp.AppendCertsFromPEM([]byte(content))
		if !ok {
			log.Fatalf("Failed to parse a trusted cert from file %s, pem format expected", config.Trusted_Root_Ca_File)
		}
	}

	mTlsConfig := &tls.Config{
		MinVersion: tls.VersionTLS12, // minimum TLS 1.2
		// P curve order does not matter, as breaking one means all others can be brute-forced as well:
		// Golang developers prefer:
		CurvePreferences:         []tls.CurveID{tls.X25519, tls.CurveP256, tls.CurveP384, tls.CurveP521},
		PreferServerCipherSuites: true, // Server chooses ciphersuite, order matters below:
		CipherSuites: []uint16{
			tls.TLS_ECDHE_ECDSA_WITH_CHACHA20_POLY1305_SHA256,
			tls.TLS_ECDHE_RSA_WITH_CHACHA20_POLY1305_SHA256,
			tls.TLS_ECDHE_ECDSA_WITH_CHACHA20_POLY1305,
			tls.TLS_ECDHE_ECDSA_WITH_AES_256_GCM_SHA384,
			tls.TLS_ECDHE_ECDSA_WITH_AES_128_GCM_SHA256,
			tls.TLS_ECDHE_RSA_WITH_CHACHA20_POLY1305,
			tls.TLS_CHACHA20_POLY1305_SHA256, // TLS 1.3
			tls.TLS_AES_256_GCM_SHA384,       // TLS 1.3
			tls.TLS_ECDHE_RSA_WITH_AES_128_GCM_SHA256,
			tls.TLS_AES_128_GCM_SHA256, // TLS 1.3
		},
		RootCAs: certp,
	}

	tr := &http.Transport{
		TLSClientConfig: mTlsConfig,

		// Set proxy callback for proxy support in this transport
		Proxy: http.ProxyFromEnvironment,
	}

	// Ensure trailing slash on web-path-prefix
	web_path_prefix := config.Web_Path_Prefix
	if web_path_prefix != "/" {
		web_path_prefix = fmt.Sprintf("%s/", path.Clean(web_path_prefix))
		config.Web_Path_Prefix = web_path_prefix
	}

	// Generate handlers for each cluster
	for i := range config.Clusters {
		cluster := config.Clusters[i]
		if debug {
			if cluster.Client == nil {
				cluster.Client = &http.Client{
					Transport: debugTransport{tr},
				}
			} else {
				cluster.Client.Transport = debugTransport{tr}
			}
		} else {
			cluster.Client = &http.Client{Transport: tr}
		}

		ctx := oidc.ClientContext(context.Background(), cluster.Client)
		log.Printf("Creating new provider %s", cluster.Issuer)
		provider, err := oidc.NewProvider(ctx, cluster.Issuer)

		if err != nil {
			log.Fatalf("Failed to query provider %q: %v\n", cluster.Issuer, err)
		}

		cluster.Provider = provider

		log.Printf("Verifying client %s", cluster.Client_ID)

		verifier := provider.Verifier(&oidc.Config{ClientID: cluster.Client_ID})

		cluster.Verifier = verifier

		if err := provider.Claims(&s); err != nil {
			log.Fatalf("Failed to parse provider scopes_supported: %v", err)
		}

		if len(s.ScopesSupported) == 0 {
			// scopes_supported is a "RECOMMENDED" discovery claim, not a required
			// one. If missing, assume that the provider follows the spec and has
			// an "offline_access" scope.
			cluster.OfflineAsScope = true
		} else {
			// See if scopes_supported has the "offline_access" scope.
			cluster.OfflineAsScope = func() bool {
				for _, scope := range s.ScopesSupported {
					if scope == oidc.ScopeOfflineAccess {
						return true
					}
				}
				return false
			}()
		}

		if len(cluster.Scopes) == 0 {
			cluster.Scopes = []string{"openid", "profile", "email", "offline_access", "groups"}
		}

		if cluster.K8s_Ca_Pem_File != "" {
			content, err := os.ReadFile(cluster.K8s_Ca_Pem_File)
			if err != nil {
				log.Fatalf("Failed to load CA from file %s, %s", cluster.K8s_Ca_Pem_File, err)
			}
			cluster.K8s_Ca_Pem = cast.ToString(content)
		}

		if cluster.K8s_Ca_Pem == "" && cluster.K8s_Ca_Pem_Base64_Encoded != "" {
			p, err := base64.StdEncoding.DecodeString(cluster.K8s_Ca_Pem_Base64_Encoded)
			if err != nil {
				log.Fatalf("Failed to base64 decode ca pem: %s", err.Error())
			}

			cluster.K8s_Ca_Pem = string(p)
		}

		cluster.Config = config

		base_redirect_uri, err := url.Parse(cluster.Redirect_URI)

		if err != nil {
			log.Fatalf("Parsing redirect_uri address: %v", err)
		}

		// Each cluster gets a different login and callback URL
		http.HandleFunc(base_redirect_uri.Path, cluster.handleCallback)
		log.Printf("Registered callback handler at: %s", base_redirect_uri.Path)

		login_uri := path.Join(config.Web_Path_Prefix, "login", cluster.Name)
		http.HandleFunc(login_uri, cluster.handleLogin)
		log.Printf("Registered login handler at: %s", login_uri)
	}

	// Index page
	http.HandleFunc(config.Web_Path_Prefix, config.handleIndex)

	// Serve static html assets
	fs := http.FileServer(http.Dir("html/static/"))
	static_uri := path.Join(config.Web_Path_Prefix, "static") + "/"
	log.Printf("Registered static assets handler at: %s", static_uri)

	http.Handle(static_uri, http.StripPrefix(static_uri, fs))

	// Determine whether to use TLS or not
	switch listenURL.Scheme {
	case "http":
		log.Printf("Listening on %s", config.Listen)
		err := http.ListenAndServe(listenURL.Host, nil)
		log.Fatal(err)
	case "https":
		log.Printf("Listening on %s", config.Listen)
		err := http.ListenAndServeTLS(listenURL.Host, config.TLS_Cert, config.TLS_Key, nil)
		log.Fatal(err)

	default:
		log.Fatalf("Listen address %q is not using http or https", config.Listen)
	}
}

func substituteEnvVarsRecursive(copy, original reflect.Value) {
	switch original.Kind() {

	case reflect.Ptr:
		originalValue := original.Elem()
		if !originalValue.IsValid() {
			return
		}
		copy.Set(reflect.New(originalValue.Type()))
		substituteEnvVarsRecursive(copy.Elem(), originalValue)

	case reflect.Interface:
		originalValue := original.Elem()
		copyValue := reflect.New(originalValue.Type()).Elem()
		substituteEnvVarsRecursive(copyValue, originalValue)
		copy.Set(copyValue)

	case reflect.Struct:
		for i := 0; i < original.NumField(); i += 1 {
			substituteEnvVarsRecursive(copy.Field(i), original.Field(i))
		}

	case reflect.Slice:
		copy.Set(reflect.MakeSlice(original.Type(), original.Len(), original.Cap()))
		for i := 0; i < original.Len(); i += 1 {
			substituteEnvVarsRecursive(copy.Index(i), original.Index(i))
		}

	case reflect.Map:
		copy.Set(reflect.MakeMap(original.Type()))
		for _, key := range original.MapKeys() {
			originalValue := original.MapIndex(key)
			copyValue := reflect.New(originalValue.Type()).Elem()
			substituteEnvVarsRecursive(copyValue, originalValue)
			copy.SetMapIndex(key, copyValue)
		}

	case reflect.String:
		replacedString := substituteEnvVars(original.Interface().(string))
		copy.SetString(replacedString)

	default:
		copy.Set(original)
	}

}

var RootCmd = &cobra.Command{
	Use:   "dex-k8s-authenticator",
	Short: "Dex Kubernetes Authenticator",
	Long:  `Dex Kubernetes Authenticator provides a web-interface to generate a kubeconfig file based on a selected Kubernetes cluster. One or more clusters can be defined in the configuration file.`,
	Run: func(cmd *cobra.Command, args []string) {

		var config Config
		err := viper.Unmarshal(&config)
		if err != nil {
			log.Fatalf("Unable to decode configuration into struct, %v", err)
		}

		original := reflect.ValueOf(config)
		copy := reflect.New(original.Type()).Elem()
		substituteEnvVarsRecursive(copy, original)

		// Start the app
		start_app(copy.Interface().(Config))

		// Fallback if no args specified
		cmd.HelpFunc()(cmd, args)

	},
}

// Read in config file
func initConfig() {

	if config_file != "" {
		//viper.SetConfigFile(config_file)
		// get the filepath
		abs, err := filepath.Abs(config_file)
		if err != nil {
			log.Fatalf("Error reading config file, %s", err)
		}

		// get the config name
		base := filepath.Base(abs)

		// get the path
		path := filepath.Dir(abs)

		viper.SetConfigName(strings.Split(base, ".")[0])
		viper.AddConfigPath(path)
		viper.SetDefault("web_path_prefix", "/")

		config, err := os.ReadFile(config_file)
		if err != nil {
			log.Fatalf("Error reading config file, %s", err)
		}

		origConfigStr := bytes.NewBuffer(config).String()

		if err := viper.ReadConfig(bytes.NewBufferString(origConfigStr)); err != nil {
			log.Fatalf("viper.ReadConfig failed to read config, %s", err.Error())
		}

		log.Printf("Using config file: %s", viper.ConfigFileUsed())
	}
}

// Initialization
func init() {
	cobra.OnInitialize(initConfig)

	if err := viper.BindPFlags(RootCmd.Flags()); err != nil {
		log.Fatal(err)
	}

	RootCmd.Flags().StringVar(&config_file, "config", "", "./config.yml")
	RootCmd.PersistentFlags().BoolVarP(&debug, "debug", "d", false, "Enable debug logging")
}

// Let's go!
func main() {
	if err := RootCmd.Execute(); err != nil {
		fmt.Println(err)
		os.Exit(-1)
	}
}
