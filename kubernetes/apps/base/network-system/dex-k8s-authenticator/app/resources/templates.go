// FIXME: Dislike this file a bit - what's the take on referencing
// viper config values (treat it as a global, or pass values around?)
package main

import (
	"encoding/json"
	"fmt"
	"html/template"
	"log"
	"net/http"
	"strings"
)

// compile all templates and cache them
var templates = template.Must(template.ParseGlob("./templates/*.html"))

func renderIndex(w http.ResponseWriter, config *Config) {
	t, _ := template.ParseFiles("./templates/index.html")
	err := t.Execute(w, config)
	if err != nil {
		log.Println(err)
		http.Error(w, http.StatusText(500), 500)
	}
}

type templateData struct {
	IDToken           string
	RefreshToken      string
	RedirectURL       string
	Claims            string
	Username          string
	Issuer            string
	ClusterName       string
	ShortDescription  string
	ClientSecret      string
	ClientID          string
	K8sMasterURI      string
	K8sCaURI          string
	K8sCaPem          string
	IDPCaURI          string
	IDPCaPem          string
	LogoURI           string
	Web_Path_Prefix   string
	StaticContextName bool
	KubectlVersion    string
	Namespace         string
}

func (cluster *Cluster) renderToken(w http.ResponseWriter,
	idToken,
	refreshToken string,
	idpCaURI string,
	idpCaPem string,
	logoURI string,
	webPathPrefix string,
	kubectlVersion string,
	claims []byte) {

	var data map[string]interface{}
	err := json.Unmarshal(claims, &data)
	if err != nil {
		panic(err)
	}

	unix_username := "user"
	if data["email"] != nil {
		email := data["email"].(string)
		unix_username = strings.Split(email, "@")[0]
	}

	token_data := templateData{
		IDToken:           idToken,
		RefreshToken:      refreshToken,
		RedirectURL:       cluster.Redirect_URI,
		Claims:            string(claims),
		Username:          unix_username,
		Issuer:            data["iss"].(string),
		ClusterName:       cluster.Name,
		ShortDescription:  cluster.Short_Description,
		ClientSecret:      cluster.Client_Secret,
		ClientID:          cluster.Client_ID,
		K8sMasterURI:      cluster.K8s_Master_URI,
		K8sCaURI:          cluster.K8s_Ca_URI,
		K8sCaPem:          cluster.K8s_Ca_Pem,
		IDPCaURI:          idpCaURI,
		IDPCaPem:          idpCaPem,
		LogoURI:           logoURI,
		Web_Path_Prefix:   webPathPrefix,
		StaticContextName: cluster.Static_Context_Name,
		Namespace:         cluster.Namespace,
		KubectlVersion:    kubectlVersion}

	if err := templates.ExecuteTemplate(w, "kubeconfig.html", token_data); err != nil {
		log.Println(err)
		http.Error(w, http.StatusText(500), 500)
	}
}

// renderHTMLError renders an HTML page that presents an HTTP error.
func (cluster *Cluster) renderHTMLError(w http.ResponseWriter, errorMsg string, code int) {
	w.Header().Set("Content-Type", "text/html; charset=utf-8")
	w.Header().Set("X-Content-Type-Options", "nosniff")
	w.WriteHeader(code)

	if err := templates.ExecuteTemplate(w, "error.html", map[string]string{
		"Logo_Uri":          cluster.Config.Logo_Uri,
		"Web_Path_Prefix":   cluster.Config.Web_Path_Prefix,
		"Code":              fmt.Sprintf("%d", code),
		"Error_Description": errorMsg,
	}); err != nil {
		log.Println(err)
		http.Error(w, http.StatusText(500), 500)
	}
}
