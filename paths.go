package main

import (
	"regexp"
	"strings"
)

var reHumanPathSeparator *regexp.Regexp
var reHumanPathSpace *regexp.Regexp
var reFilePathTrimBefore *regexp.Regexp
var reFilePathTrimAfter *regexp.Regexp
var reFilePathSpace *regexp.Regexp

func init() {
	reHumanPathSeparator = regexp.MustCompile(`[-_]{2}`)
	reHumanPathSpace = regexp.MustCompile(`[-_]`)
	reFilePathTrimBefore = regexp.MustCompile(`^[^a-z0-9#]+`)
	reFilePathTrimAfter = regexp.MustCompile(`[^a-z0-9]+$`)
	reFilePathSpace = regexp.MustCompile(`[^a-z0-9/(__)]+`)
}

func toHumanPath(s string) string {
	s = reHumanPathSeparator.ReplaceAllLiteralString(s, `/`)
	s = reHumanPathSpace.ReplaceAllLiteralString(s, ` `)
	return s
}

func toFilePath(s string) string {
	s = strings.ToLower(s)
	s = reFilePathTrimBefore.ReplaceAllLiteralString(s, ``)
	s = reFilePathTrimAfter.ReplaceAllLiteralString(s, ``)
	s = reFilePathSpace.ReplaceAllLiteralString(s, `_`)
	return s
}

func toSlugPath(s string) string {
	s = toFilePath(s)
	s = strings.ReplaceAll(s, `__`, `/`)
	s = strings.ReplaceAll(s, `_`, `-`)
	if s[0] == '#' {
		s = `-` + s[1:]
	}
	return s
}
