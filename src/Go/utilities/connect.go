package utilities

import (
	"database/sql"
	_ "github.com/go-sql-driver/mysql"
	_ "github.com/godror/godror"
)

var db *sql.DB

func Mysql(conn Connection)(baseDB *sql.DB, err error)  {
	driver := conn.USERNAME+":"+conn.PASSWORD+"@"+"tcp("+conn.HOST+":"+conn.PORT+")/"+conn.DATABASE+"?charset="+conn.CHARSET
	if conn.PARSETIME != "" {
		driver += "&parseTime="+conn.PARSETIME
	}
	db, err = sql.Open("mysql",driver)
	if err != nil {
		WriteLog("Mysql数据库连接错误!","ERROR")
		return
	}
	db.SetMaxOpenConns(20)
	db.SetMaxIdleConns(10)
	return db,nil
}

func Oracle(conn Connection)(baseDB *sql.DB, err error){
	db, err := sql.Open("godror",`user="`+conn.USERNAME+`" password="`+conn.PASSWORD+`" connectString="`+conn.HOST+`:`+conn.PORT+`/`+conn.SID+`"`)
	if err != nil {
		WriteLog("Oracle数据库连接错误!","ERROR")
		return
	}
	db.SetMaxOpenConns(20)
	db.SetMaxIdleConns(10)
	return db,nil
}
