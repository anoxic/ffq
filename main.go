package main

import (
	//"time"
	"io/ioutil"
	"net/http"
	"strings"
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
	logf("http listener binding to %s", host)
	err := http.ListenAndServe(host, handler)
	if err != nil {
		dief("http listener unable to bind to %s", host)
	}
}

func handler() http.HandlerFunc {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		logf("http %v", r.URL.String())

		if r.URL.String()[len(r.URL.String())-1:] == "/" {
			listingHandler(w, r)
		} else {
			webError(w, "NotFound")
		}
	})
}

func listingHandler(w http.ResponseWriter, r *http.Request) {
	dirname := "pages"

	files, err := ioutil.ReadDir(dirname)
	if err != nil {
		webError(w, err)
		return
	}

	var files2 []ListedPage

	for _, file := range files {
		if r.URL.String() != "/" {
			if !strings.HasPrefix(toSlugPath(file.Name()), r.URL.String()[1:]) {
				continue
			}
		}
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
