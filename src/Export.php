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
    public function datum($column)
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
     * setting request data to json.
     *
     * @param array $row
     * @param array $list
     * @throws \Exception|GuzzleException
     * @return Response
     */
    public function json(array $row = [], array $list = [])
    {
        $this->setData($row, $list);

        $this->verifyJson();

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
        return new Response($response->getStatusCode(),[
            "Content-type"=>"application/octet-stream",
            "Content-Disposition"=>"attachment; filename=".$this->name,
            "Msg"   => $response->hasHeader("Msg") ? $response->getHeader("Msg") : ""
        ],$response->getBody());
    }

    /**
     * @throws
     */
    private function verifyJson()
    {
        if (!$this->data["row"] && !$this->data["list"])throw new \Exception("Data is empty!");

        if (!$this->name)$this->name = "excel.xlsx";
        $arr = explode(".",$this->name);
        if (count($arr) == 1)$this->name .= ".xlsx";
        if ($arr[count($arr)-1] != 'xlsx'){
            $arr[count($arr)-1] = 'xlsx';
            $this->name = implode(".",$arr);
        }
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