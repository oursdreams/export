<?php
namespace Oursdreams\Export;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Export
{
    /**
     * request url
     */
    protected $url = "localhost:9722";

    /**
     * request data type.
     */
    protected $type;

    /**
     * file name
     */
    protected $name;

    /**
     * format param detail.
     */
    protected $format = [
        "datum" => "id",
    ];

    /**
     * database connection info.
     */
    protected $connection = [
        "charset"   =>  "utf8mb4",
        "parsetime" =>  "True",
        "sid"       => 	"orcl",
    ];

    /**
     * data list.
     */
    protected $data;

    /**
     * export path or log path info.
     */
    protected $path = [
        "log" => "",
        "file"=> "",
    ];

    /**
     * setting format info.
     *
     * @param string $method  default:warpText merge
     * @param array $mergeColumn
     * @param array $mergeRow
     * @param string $datum
     */
    protected function setFormat($method, array $mergeColumn = [], array $mergeRow = [], $datum = "")
    {
        $this->format["method"] = $method;
        $this->format["mergeColumn"] = $mergeColumn;
        if ($mergeRow) $this->format["mergeRow"] = $mergeRow;
        if ($datum)$this->format["datum"] = $datum;
    }

    /**
     * setting format method to warp text.
     *
     * @return $this
     */
    public function setWarpTextFormat()
    {
        $this->setFormat("warpText");

        return $this;
    }

    /**
     * setting format method to merge.
     *
     * @param array $mergeColumn
     * @param array $mergeRow
     * @return $this
     */
    public function setMergeFormat(array $mergeColumn = [], array $mergeRow = [])
    {
        $this->setFormat("merge",$mergeColumn,$mergeRow);

        return $this;
    }

    /**
     * setting format datum.
     *
     * @param string $column
     * @return $this
     */
    public function setDatum($column)
    {
        $this->format["datum"] = $column;
        return $this;
    }

    /**
     * setting connection driver.
     *
     * @param string $driver
     * @param string $host
     * @param string $port
     * @param string $database
     * @param string $username
     * @param string $password
     * @param string $charset
     * @param string $parsetime
     * @param string $sid
     */
    protected function setConnection($driver, $host, $port, $database, $username, $password, $charset = "", $parsetime = "", $sid = "")
    {
        $this->connection["driver"] = $driver;
        $this->connection["host"] = $host;
        $this->connection["port"] = $port;
        $this->connection["database"] = $database;
        $this->connection["username"] = $username;
        $this->connection["password"] = $password;
        if ($charset)$this->connection["charset"] = $charset;
        if ($parsetime)$this->connection["parsetime"] = $parsetime;
        if ($sid)$this->connection["sid"] = $sid;
    }

    /**
     * setting mysql connection driver.
     *
     * @param string $host
     * @param string $port
     * @param string $database
     * @param string $username
     * @param string $password
     * @param string $charset
     * @param string $parsetime
     * @return $this
     */
    public function setMysqlConnection($host, $port, $database, $username, $password, $charset = "utf8mb4", $parsetime = "True")
    {
        $this->setConnection("mysql", $host, $port, $database, $username, $password, $charset, $parsetime);

        return $this;
    }

    /**
     * setting oracle connection driver.
     *  A local client is required to support the connection.
     *
     * @param string $host
     * @param string $port
     * @param string $database
     * @param string $username
     * @param string $password
     * @param string $charset
     * @param string $parsetime
     * @return $this
     */
    public function setOracleConnection($host, $port, $database, $username, $password, $charset = "utf8mb4", $parsetime = "True")
    {
        $this->setConnection("mysql", $host, $port, $database, $username, $password, $charset, $parsetime);

        return $this;
    }

    /**
     * setting file or log path.
     *
     * @param string $log
     * @param string $file
     */
    protected function setPath($log = "", $file = "")
    {
        $this->path["log"] = $log;
        $this->path["file"] = $file;
    }

    /**
     * setting log path.
     *
     * @param string $path
     * @return $this
     */
    public function setLogPath($path)
    {
        $this->setPath($path);

        return $this;
    }

    /**
     * setting file path.
     *
     * @param string $path
     * @return $this
     */
    public function setFilePath($path)
    {
        $this->setPath(null, $path);

        return $this;
    }

    /**
     * setting request data.
     *
     * @param array $row
     * @param array $list
     * @param string $sql
     * @param array $rule
     */
    protected function setData(array $row = [], array $list = [], $sql = "", array $rule = [])
    {
        $this->data["row"] = $row;
        $this->data["list"] = $list;
        $this->data["sql"] = $sql;
        if ($rule) $this->data["rule"] = $rule;
    }

    /**
     * setting export file name.
     *
     * @param string $filename
     *
     * @return $this
     */
    public function setFileName($filename)
    {
        $this->name = $filename;

        return $this;
    }

    /**
     * @param array $row
     * @param array $list
     * @param $fileName
     * @return bool|Response
     */
    static function make(array $row = [], array $list = [], $fileName)
    {
        return (new static)->setFileName($fileName)->json($row,$list)->direct();
    }

    /**
     * setting request data to json.
     *
     * @param array $row
     * @param array $list
     * @throws \Exception|GuzzleException
     * @return Response|bool
     */
    public function json(array $row = [], array $list = [])
    {
        $this->setData($row, $list);

        $this->verifyJson();
        $this->verifyFile();

        $http = new Client(['base_uri' => 'http://localhost:9722','timeout'=>2.0]);
        /** @var  $response */

        try{
            $response = $http->request('POST', '/',[
                'headers'=>[
                    'Accept'     => 'application/json',
                ],
                "json"=>[
                    'type'   => "base",
                    "data"   => $this->data,
                    "path"   => $this->path,
                    "format" => $this->format
                ]
            ]);
        }catch(\Exception $e){
            throw new \Exception("服务异常！");
        }
        if ($response->getStatusCode() == 500){
            throw new \Exception($response->getHeader("Msg"));
        }
        return $this->response($response);
    }

    /**
     * @throws
     */
    private function verifyJson()
    {
        if (!$this->data["row"] && !$this->data["list"])
            throw new \Exception("Data is empty!");
    }


    /**
     * setting request data to json.
     *
     * @param string $sql
     * @param array $rule
     * @throws \Exception|GuzzleException
     * @return Response|bool
     */
    public function sql(string $sql, array $rule)
    {
        $this->setData([], [], $sql, $rule);

        $this->verifySql();
        $this->verifyFile();

        $http = new Client(['base_uri' => 'http://localhost:9722','timeout'=>2.0]);
        /** @var  $response */

        try{
            $response = $http->request('POST', '/',[
                'headers'=>[
                    'Accept'     => 'application/json',
                ],
                "json"=>[
                    'type'   => "unify",
                    "data"   => $this->data,
                    "path"   => $this->path,
                    "format" => $this->format,
                    "connection" => $this->connection,
                ]
            ]);
        }catch(\Exception $e){
            throw new \Exception("服务异常！");
        }
        if ($response->getStatusCode() == 500){
            throw new \Exception($response->getHeader("Msg"));
        }
        return $this->response($response);
    }

    /**
     * @throws
     */
    private function verifySql()
    {
        if (!$this->data["sql"])throw new \Exception("Data is empty!");
        if (!isset($this->connection["driver"])   ||
            !isset($this->connection["host"])     ||
            !isset($this->connection["port"])     ||
            !isset($this->connection["database"]) ||
            !isset($this->connection["username"]) ||
            !isset($this->connection["password"]))throw new \Exception("No connection specified!");
        if (isset($this->data["rule"])){
            foreach ($this->data["rule"] as $key=>$val){
                if ($val == "date"){
                    if ($this->connection["driver"] == "mysql")$this->data["rule"][$key] = "mysqlDate";
                    else $this->data["rule"][$key] = "oracleDate";
                }
            }
        }
    }

    /**
     * 验证文件名
     */
    private function verifyFile()
    {
        if ($this->path["file"]){
            $basePath = base_path();
            if (mb_strpos($basePath,$this->path["file"]) === false){
                $this->path["file"] = base_path($this->path["file"]);
            }
        }
        if (!$this->name)$this->name = "excel.xlsx";
        $arr = explode(".",$this->name);
        if (count($arr) == 1)$this->name .= ".xlsx";
        if (count($arr)>1 && $arr[count($arr)-1] != 'xlsx'){
            $arr[count($arr)-1] = 'xlsx';
            $this->name = implode(".",$arr);
        }
    }

    private function response($response)
    {
        if ($this->path["file"]){
            if (file_exists($this->path["file"]))return true;
            return false;
        }
        return new Response($response->getStatusCode(),[
            "Content-type"=>"application/octet-stream",
            "Content-Disposition"=>"attachment; filename=".$this->name,
            "Msg"   => $response->hasHeader("Msg") ? $response->getHeader("Msg") : ""
        ],$response->getBody());
    }

    /**
     * setting sql column format date.
     *
     * @param string $column
     *
     * @return $this
     */
    public function setDateTimeColumn(string $column)
    {
        if (!isset($this->data["rule"]))$this->data["rule"] = [];
        $this->data["rule"][$column] = "date";
        return $this;
    }

    /**
     * setting sql column format percent.
     *
     * @param string $column
     *
     * @return $this
     */
    public function setPercentColumn(string $column)
    {
        if (!isset($this->data["rule"]))$this->data["rule"] = [];
        $this->data["rule"][$column] = "percent";
        return $this;
    }

    /**
     * PARAMS EXAMPLE
     */
    private function PARAMS()
    {
        [
            "type"      => "base",
            "format"    => [
                "method"        => "warpText",
                "mergeColumn"   => ["column"],
                "mergeRow"      => ["row"=>"row"],
                "datum"         => "datum",
            ],
            "connection"=>[
                "driver"        => "mysql",
                "host"          => "host",
                "port"          => "port",
                "database"      => "databse",
                "username"      => "username",
                "password"      => "password",
                "charset"       => "char",
                "parsetime"     => "pars",
                "sid"           => "",
            ],
            "data"=>[
                "row"           => ["sql-row"],
                "list"          => [["sql-list"]],
                "sql"           => "sql",
                "rule"          => ["sql"=>"rule"],
            ],"path"=>[
                "log"           => "log",
                "file"          => ""
            ]
        ];
    }
}