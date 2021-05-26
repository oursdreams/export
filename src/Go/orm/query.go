package orm

import (
	"export/utilities"

	"database/sql"
)

func query(sqlStr string,db *sql.DB)(list []map[string]string) {
	rows,err := db.Query(sqlStr)
	if err != nil {
		utilities.WriteLog("SQL："+err.Error()+"("+sqlStr+")","ERROR")
		return
	}
	columns, _ := rows.Columns()
	length := len(columns)
	values := make([]sql.RawBytes, length)
	pointer := make([]interface{}, length)
	for i := 0; i < length; i++ {
		pointer[i] = &values[i]
	}
	for rows.Next() {
		err := rows.Scan(pointer...)
		if err != nil {
			utilities.WriteLog("结果集转换错误!("+sqlStr+")","ERROR")
			return
		}
		row := make(map[string]string)
		for i := 0; i < length; i++ {
			row[columns[i]] = string(values[i])
		}
		list = append(list, row)
	}
	_ = rows.Close()
	return
}
func GetDBData(conn utilities.Connection, sqlStr string)(list []map[string]string) {
	var db *sql.DB
	if conn.DRIVER == "oracle" {
		db, _ = utilities.Oracle(conn)
	}else{
		db, _ = utilities.Mysql(conn)
	}
	return query(sqlStr, db)
}
