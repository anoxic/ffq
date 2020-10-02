package main

import (
	//"time"
	"bufio"
	"errors"
	"fmt"
	"io/ioutil"
	"net/http"
	"os"
	"strings"
)

type Page struct {
	Path       string
	Title      string
	Rev        string
	RevSummary string
	RevAuthor  string
	Body       string
	RevTime    string
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
		path := r.URL.String()

		logf("http %v", path)

		if path[len(path)-1:] == "/" {
			listingHandler(w, r)
		} else {
			page, err := loadPage(path)
			webRender(w, "view", map[string]interface{}{"page": page, "err": err})
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

func attemptOpen(path string) (*os.File, error) {
	file, err := os.Open(path)

	if os.IsNotExist(err) {
		return nil, errors.New(fmt.Sprintf("NotFound: %s", path))
	}

	if err != nil {
		return nil, err
	}

	return file, nil
}

func loadPage(path string) (*Page, error) {
	filename := "pages/" + toFilePath(path)

	file, err := attemptOpen(filename)
	if err != nil {
		return nil, err
	}
	b1 := make([]byte, 8)
	n1, err := file.Read(b1)
	if err != nil {
		return nil, err
	}
	file.Close()
	//return &Page{Path: path, Body: string(b1[0:n1]), Title: string(n1)}, nil

	filename = "rev/" + toFilePath(path) + "~" + string(b1[0:n1-1])
	file, err = attemptOpen(filename)

	if err != nil {
		return nil, err
	}

	page := &Page{}

	scanner := bufio.NewScanner(file)

	find := func(haystack string, needle string, i *string) {
		needle = needle + " "
		if strings.Index(haystack, needle) != -1 {
			*i = haystack[len(needle):]
		}
	}

	for scanner.Scan() {
		logf(scanner.Text())

		find(scanner.Text(), `path`, &page.Path)
		find(scanner.Text(), `title`, &page.Title)
		find(scanner.Text(), `rev`, &page.Rev)
		find(scanner.Text(), `rev_summary`, &page.RevSummary)
		find(scanner.Text(), `rev_author`, &page.RevAuthor)
		find(scanner.Text(), `rev_time`, &page.RevTime)
	}

	logf("%s", page)

	for scanner.Scan() {
		//load into a bytes.Buffer
	}

	if err := scanner.Err(); err != nil {
		return nil, err
	}

	return &Page{Path: path}, nil
}

// p := &Page{Title: title, Body: []byte(body)}
// err := p.save()
func (p *Page) save() error {
	filename := p.Title + ".txt"
	return ioutil.WriteFile(filename, p.Body, 0600)
}
