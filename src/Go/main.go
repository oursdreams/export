package main

import (
	"export/controller"
	"net/http"
)

func main() {
	http.HandleFunc("/", controller.Export)
	err := http.ListenAndServe("127.0.0.1:9722", nil)
	if err != nil {
		panic("端口号已被占用!")
	}
}