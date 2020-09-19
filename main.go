package main

import (
	//"time"
	"io/ioutil"
	"net/http"
)

type Page struct {
	Path       string
	Rev        int
	Title      string
	RevSummary string
	RevAuthor  string
	//RevTime    Time
	Body []byte
}

type ListedPage struct {
	HumanPath string
	SlugPath  string
}

func main() {
	host := ":8080"
	handler := handler()
	err := http.ListenAndServe(host, handler)
	if err != nil {
		dief("http listener unable to bind to %s", host)
	}
}

func handler() http.HandlerFunc {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		logf("http %v", r.URL.String())

		if r.URL.String()[len(r.URL.String())-1:] == "/" {
			dirname := "pages"

			files, err := ioutil.ReadDir(dirname)
			if err != nil {
				webError(w, err)
			}

			var files2 []ListedPage

			for _, file := range files {
				files2 = append(files2, ListedPage{toHumanPath(file.Name()), toSlugPath(file.Name())})
			}

			data := struct {
				Title    string
				Items    []ListedPage
				WikiName string
				Notice   string
				Page     Page
			}{
				Title:    "My page",
				Items:    files2,
				WikiName: "Notes",
			}
			webRender(w, "list", data)
		} else {
			webError(w, "NotFound")
		}
	})
}

func loadPage(title string) (*Page, error) {
	filename := title + ".txt"
	body, err := ioutil.ReadFile(filename)
	if err != nil {
		return nil, err
	}
	return &Page{Title: title, Body: body}, nil
}

// p := &Page{Title: title, Body: []byte(body)}
// err := p.save()
func (p *Page) save() error {
	filename := p.Title + ".txt"
	return ioutil.WriteFile(filename, p.Body, 0600)
}

// TODO
// - make a "check" function to see if an error happened, if so log + w.Exec something
