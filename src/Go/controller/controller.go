package controller

import (
    "export/excel"
    "export/orm"
    "export/utilities"

	"encoding/json"
    "net/http"
    "strconv"
)

func Export(w http.ResponseWriter,req *http.Request)  {
	decoder := json.NewDecoder(req.Body)
	param := utilities.SetParam(decoder)
	p := param.GetParam()
	errorMsg := utilities.NewError(p.PATH.LOG)
	file := excel.CreateFile(selectModule(p))
	if errorMsg.GetMsg() != "" {
		w.Header().Set("Msg",errorMsg.GetMsg())
		return
	}
	if p.PATH.FILE != "" {
		err := file.SaveAs(p.PATH.FILE)
		if err != nil {
			utilities.WriteLog("文件保存失败!","ERROR")
		}
		return
	}
	if _, err := file.WriteTo(w); err != nil {
		utilities.WriteLog("返回二进制流失败!","ERROR")
		return
	}
}

func selectModule(param *utilities.Param) (row []interface{}, list [][]interface{}, mergeRow map[string]string, mergeColumn []string, method string) {
	var data []map[string]string
	switch param.TYPE{
	case "base":
		row = param.DATA.ROW
		list = param.DATA.LIST
		if param.FORMAT.METHOD == "merge" {
			mergeRow = param.FORMAT.MERGEROW
			mergeColumn = param.FORMAT.MERGECOLUMN
		}
		break
	case "unify":
		data = orm.GetDBData(param.CONNECTION, param.DATA.SQL)

        row = param.DATA.ROW
		datum := param.FORMAT.DATUM
		if param.FORMAT.METHOD != "merge" {
			datum = ""
		}else{
			mergeColumn = param.FORMAT.MERGECOLUMN
		}
		list, mergeRow = unifyFormat(data, param.DATA.COLUMN, param.DATA.RULE, datum)
	}
	return row,list,mergeRow,mergeColumn,param.FORMAT.METHOD
}

func unifyFormat(data []map[string]string, row []string, rule map[string]string, datum string) ([][]interface{}, map[string]string){
	rowLen := len(row)
	column :=  make(map[string]int,rowLen)
	for i,c := range row {
		column[c] = i
	}

	list := make([][]interface{},len(data))
    mergeRow := make(map[string]string)
    var columnName string
    var startIndex int
    rowAmount := 0
	for k,v := range data{
        if datum != "" {
            if columnName == v[datum] {
                rowAmount++
            }else{
                if rowAmount != 0 {
                    mergeRow[strconv.Itoa(startIndex+2)] = strconv.Itoa(startIndex+2+rowAmount)
                }
                columnName = v[datum]
                startIndex = k
                rowAmount = 0
            }
            if rowAmount != 0 {
                mergeRow[strconv.Itoa(startIndex+2)] = strconv.Itoa(startIndex+2+rowAmount)
            }
        }
		line := make([]interface{},rowLen)
		for key,value := range column {
			if rule[key] != ""{
				line[value] = dataFormat(rule[key], v[key])
				continue
			}
			line[value] = v[key]
		}
		list[k] = line
	}
	return list, mergeRow
}

func dataFormat(ruleType string,value string)string{
	switch ruleType {
		case "oracleDate":
			value = utilities.DateFormat(value, "2006-01-02T15:04:05+08:00")
		case "mysqlDate":
			value = utilities.DateFormat(value,"2006-01-02T15:04:05Z")
		case "percent":
			rate,_ := strconv.ParseFloat(value,64)
			if rate != 0 {
				value = strconv.FormatFloat(rate * 100, 'f', 2, 64)+"%"
			}
	}
	return value
}
