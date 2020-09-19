package main

import (
	"fmt"
	"html/template"
	"net/http"
	"os"
	"time"
)

func logf(f string, a ...interface{}) {
	fmt.Printf("%s    %s\n", time.Now().Format("2006-01-02T15:04:05"), fmt.Sprintf(f, a...))
}

func errf(f string, a ...interface{}) {
	fmt.Fprintf(os.Stderr, "%s !! %s\n", time.Now().Format("2006-01-02T15:04:05"), fmt.Sprintf(f, a...))
}

func dief(f string, a ...interface{}) {
	errf(f, a...)
	os.Exit(1)
}

func webError(w http.ResponseWriter, data interface{}) {
	w.WriteHeader(http.StatusInternalServerError)
	t := template.Must(template.ParseFiles("views/error.html"))
	t.Execute(w, data)
}

func webRender(w http.ResponseWriter, tmpl string, data interface{}) {
	t := template.New(tmpl)
	template.Must(t.ParseGlob("views/*.html"))
	template.Must(t.ParseGlob("views/*.css"))
	t.ExecuteTemplate(w, tmpl+".html", data)
}
