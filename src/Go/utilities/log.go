package utilities

import (
	"os"
	"time"
)
type Error struct{
	msg string
}
var path string
var err *Error

func NewError(p string)*Error{
	err = &Error{}
	path = p
	return err
}

func (e *Error)GetMsg()string{
	return e.msg
}

func todayFilename()string{
	today := time.Now().Format("2006-01-02")
	return today + ".log"
}
func getLogFile(content string){
	logsPath := path+"/logs"
	if !IsExist(logsPath) {
		ex := os.Mkdir(logsPath,os.ModePerm)
		if ex != nil {
			err.msg = "创建存储日志目录时找不到路径！"
			return
		}
	}
	filename := logsPath+"/"+todayFilename()
	log,ex := os.OpenFile(filename, os.O_CREATE|os.O_WRONLY|os.O_APPEND, 0666)
	defer log.Close()
	if ex != nil {
		err.msg = "日志存储时找不到指定路径！"
		return
	}
	str := []byte(content)
	n, ex := log.Write(str)
	if ex == nil && n != len(str) {
		err.msg = "日志写入发生错误！"
	}
}

func WriteLog(content string, grade string ){
	if path != "" {
		grade = "["+grade+"]"
		createTime := time.Now().Format("15:04:05")
		getLogFile("\n"+grade+"   "+createTime+"\n"+"   "+content)
	}
	err.msg = grade+"："+content
}

func IsExist(f string) bool {
	_, err := os.Stat(f)
	return err == nil || os.IsExist(err)
}
