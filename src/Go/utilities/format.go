package utilities

import (
	"time"
)

func DateFormat(date string, parseType string)string{
	if date == ""{
		return ""
	}
	var LOC, _ = time.LoadLocation("Asia/Shanghai")
	dateUTC,_ := time.ParseInLocation(parseType,date,LOC)
	return  dateUTC.Local().Format("2006-01-02 15:04:05")
}
