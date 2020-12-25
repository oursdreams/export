package excel

import (
	"export/utilities"
	"github.com/360EntSecGroup-Skylar/excelize/v2"
)

const SHEET  = "Sheet1"

func CreateFile(row []interface{},list [][]interface{}, mergeRow map[string]string, mergeColumn []string, method string) (excel *excelize.File) {
	file := excelize.NewFile()
	streamWriter, err := file.NewStreamWriter(SHEET)
	if err != nil {
		utilities.WriteLog("文件生成失败!","ERROR")
	}

	if method == "warpText" {
		styleID, err := file.NewStyle(&excelize.Style{
			Alignment: &excelize.Alignment{
				WrapText: true,
			},
		})
		if err != nil {
			utilities.WriteLog("创建换行样式失败!","ERROR")
		}
		end, _ := excelize.ColumnNumberToName(len(row))
		err = file.SetColStyle(SHEET, "A:"+end, styleID)
		if err != nil {
			utilities.WriteLog("设置换行格式失败!","ERROR")
			return
		}
	}

	cell, _ := excelize.CoordinatesToCellName(1, 1)
	if err := streamWriter.SetRow(cell, row); err != nil {
		utilities.WriteLog("写入表头失败!","ERROR")
	}

	for index := 0; index < len(list); index++ {
		cell, _ := excelize.CoordinatesToCellName(1, index+2)
		if err := streamWriter.SetRow(cell, list[index]); err != nil {
			utilities.WriteLog("数据写入文件失败!","ERROR")
		}
	}
	/* 合并操作 */
	if method == "merge" {
		if mergeRow != nil && mergeColumn != nil {
			for start,end := range mergeRow {
				for i := 0;i<len(mergeColumn); i++ {
					err := file.MergeCell(SHEET,mergeColumn[i]+start,mergeColumn[i]+end)
					if err != nil {
						utilities.WriteLog("合并单元格失败!","ERROR")
						return
					}
				}
			}
		}
	}

	if err := streamWriter.Flush(); err != nil {
		utilities.WriteLog("文件流关闭失败!","ERROR")
	}
	return file
}
