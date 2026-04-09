package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"log"
	"net/http"
	"path"
	"time"

	oidc "github.com/coreos/go-oidc"
	"os"
	"github.com/spf13/cast"
	"golang.org/x/oauth2"
)

const exampleAppState = "Vgn2lp5QnymFtLntKX5dM8k773PwcM87T4hQtiESC1q8wkUBgw5D3kH0r5qJ"

func (cluster *Cluster) oauth2Config() *oauth2.Config {

	return &oauth2.Config{
		ClientID:     cluster.Client_ID,
		ClientSecret: cluster.Client_Secret,
		Endpoint:     cluster.Provider.Endpoint(),
		Scopes:       cluster.Scopes,
		RedirectURL:  cluster.Redirect_URI,
	}
}

func (config *Config) handleIndex(w http.ResponseWriter, r *http.Request) {

	if len(config.Clusters) == 1 && r.URL.String() == config.Web_Path_Prefix {
		http.Redirect(w, r, path.Join(config.Web_Path_Prefix, "login", config.Clusters[0].Name), http.StatusSeeOther)
	} else {
		renderIndex(w, config)
	}
}

func (cluster *Cluster) handleLogin(w http.ResponseWriter, r *http.Request) {
	log.Printf("Handling login-uri for: %s", cluster.Name)
	authCodeURL := cluster.oauth2Config().AuthCodeURL(exampleAppState, oauth2.AccessTypeOffline)
	if cluster.Connector_ID != "" {
		log.Printf("Using dex connector with id %#q", cluster.Connector_ID)
		authCodeURL = fmt.Sprintf("%s&connector_id=%s", authCodeURL, cluster.Connector_ID)
	}
	log.Printf("Redirecting post-loginto: %s", authCodeURL)
	http.Redirect(w, r, authCodeURL, http.StatusSeeOther)
}

func (cluster *Cluster) handleCallback(w http.ResponseWriter, r *http.Request) {
	var (
		err      error
		token    *oauth2.Token
		IdpCaPem string
	)

	// An error message to that presented to the user
	userErrorMsg := "Invalid token request"

	log.Printf("Handling callback for: %s", cluster.Name)

	ctx := oidc.ClientContext(r.Context(), cluster.Client)
	oauth2Config := cluster.oauth2Config()
	switch r.Method {
	case "GET":
		// Authorization redirect callback from OAuth2 auth flow.
		if errMsg := r.FormValue("error"); errMsg != "" {
			cluster.renderHTMLError(w, userErrorMsg, http.StatusBadRequest)
			log.Printf("handleCallback: request error. error: %s, error_description: %s", errMsg, r.FormValue("error_description"))
			return
		}
		code := r.FormValue("code")
		if code == "" {
			cluster.renderHTMLError(w, userErrorMsg, http.StatusBadRequest)
			log.Printf("handleCallback: no code in request: %q", r.Form)
			return
		}
		if state := r.FormValue("state"); state != exampleAppState {
			cluster.renderHTMLError(w, userErrorMsg, http.StatusBadRequest)
			log.Printf("handleCallback: expected state %q got %q", exampleAppState, state)
			return
		}
		token, err = oauth2Config.Exchange(ctx, code)
	case "POST":
		// Form request from frontend to refresh a token.
		refresh := r.FormValue("refresh_token")
		if refresh == "" {
			cluster.renderHTMLError(w, userErrorMsg, http.StatusBadRequest)
			log.Printf("handleCallback: no refresh_token in request: %q", r.Form)
			return
		}
		t := &oauth2.Token{
			RefreshToken: refresh,
			Expiry:       time.Now().Add(-time.Hour),
		}
		token, err = oauth2Config.TokenSource(ctx, t).Token()
	default:
		// Return non-HTML error for non GET/POST requests which probably wasn't executed by browser
		http.Error(w, fmt.Sprintf("Method not implemented: %s", r.Method), http.StatusBadRequest)
		return
	}

	if err != nil {
		cluster.renderHTMLError(w, userErrorMsg, http.StatusBadRequest)
		log.Printf("handleCallback: failed to get token: %v", err)
		return
	}

	rawIDToken, ok := token.Extra("id_token").(string)
	if !ok {
		cluster.renderHTMLError(w, userErrorMsg, http.StatusBadRequest)
		log.Printf("handleCallback: no id_token in response: %q", token)
		return
	}

	idToken, err := cluster.Verifier.Verify(r.Context(), rawIDToken)
	if err != nil {
		cluster.renderHTMLError(w, userErrorMsg, http.StatusBadRequest)
		log.Printf("handleCallback: failed to verify ID token: %q, err: %v", rawIDToken, err)
		return
	}
	var claims json.RawMessage
	if err = idToken.Claims(&claims); err != nil {
		cluster.renderHTMLError(w, userErrorMsg, http.StatusBadRequest)
		log.Printf("handleCallback: failed to unmarshal json payload of ID token into claims: %v", err)
		return
	}

	buff := new(bytes.Buffer)
	if err = json.Indent(buff, []byte(claims), "", "  "); err != nil {
		cluster.renderHTMLError(w, userErrorMsg, http.StatusBadRequest)
		log.Printf("handleCallback: failed to indent json:  %v", err)
		return

	}

	if cluster.Config.IDP_Ca_Pem != "" {
		IdpCaPem = cluster.Config.IDP_Ca_Pem
	} else if cluster.Config.IDP_Ca_Pem_File != "" {
		content, err := os.ReadFile(cluster.Config.IDP_Ca_Pem_File)
		if err != nil {
			log.Fatalf("Failed to load CA from file %s, %s", cluster.Config.IDP_Ca_Pem_File, err)
		}
		IdpCaPem = cast.ToString(content)
	}

	cluster.renderToken(w, rawIDToken, token.RefreshToken,
		cluster.Config.IDP_Ca_URI,
		IdpCaPem,
		cluster.Config.Logo_Uri,
		cluster.Config.Web_Path_Prefix,
		cluster.Config.Kubectl_Version,
		buff.Bytes())
}
