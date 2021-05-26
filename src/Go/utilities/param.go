package utilities

import (
    "encoding/json"
)

type Format struct {
	METHOD 		string 				`json:"method"`
	MERGECOLUMN []string 			`json:"mergeColumn"`
	MERGEROW 	map[string]string 	`json:"mergeRow"`
	DATUM 		string				`json:"datum"`
}
type Connection struct {
	DRIVER 		string `json:"driver"`
	HOST 		string `json:"host"`
	PORT 		string `json:"port"`
	DATABASE 	string `json:"database"`
	USERNAME 	string `json:"username"`
	PASSWORD 	string `json:"password"`
	CHARSET 	string `json:"charset"`
	PARSETIME 	string `json:"parsetime"`
	SID 		string `json:"sid"`
}
type Data struct {
	ROW 	[]interface{} 		`json:"row"`
	LIST 	[][]interface{} 	`json:"list"`
	SQL 	string 				`json:"sql"`
	RULE 	map[string]string 	`json:"rule"`
}
type Path struct {
	LOG 	string `json:"log"`
	FILE 	string `json:"file"`
}

type Param struct{
	TYPE  		string 		`json:"type"`
	FORMAT		Format 		`json:"format"`
	CONNECTION 	Connection 	`json:"connection"`
	DATA		Data		`json:"data"`
	PATH		Path		`json:"path"`
}

var param *Param

func SetParam(request *json.Decoder)*Param{
	e := request.Decode(&param)
	if e != nil{
		panic(e)
	}
	return param
}

func (p *Param)GetParam()*Param{
	return p
}