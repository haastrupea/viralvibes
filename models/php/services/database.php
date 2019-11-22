<?php
namespace Viralvibes;
class database{
    /**
     * store instance of the current database connection
     */
    private $_dbconnection;
    private $_dbName;
    static private $_instance=[];
    static public function getInstance($dbdriver,$dbname,$usr=null,$psw=null){
        if(empty(self::$_instance)){
            self::$_instance[$dbname]= new self($dbdriver,$dbname,$usr,$psw);
        }else{
            if(!array_key_exists($dbname,self::$_instance)){
            self::$_instance[$dbname]= new self($dbdriver,$dbname,$usr,$psw);
            }
        }
        return self::$_instance[$dbname];
    }
    
    public function getAllInstance()
    {
      return self::$_instance;
    }

    private function __construct($dbdriver,$dbname,$usr,$psw){
        $this->_dbName=$dbname;
        $dsn='';
        switch (strtolower($dbdriver)) {
            case 'mysql':
                $dsn="mysql:host=127.0.0.1;dbname={$dbname};charset=utf8";
                break;
                case 'sqlite':
                $dsn="sqlite:{$dbname}";
                break;
        }
            $dbcon=new \PDO($dsn,$usr,$psw);
            $this->_dbconnection=$dbcon;
        }

    public function getConnection(){
            return $this->_dbconnection;
    }

    public function queryDb($query,array $param=null,$fetchStyle=\PDO::FETCH_ASSOC){    
        $qry=$this->_dbconnection->prepare($query);
        if(is_null($param)){
            $qry->execute();
        }else{
            $qry->execute($param);
        }

        //return result if any
        return $qry->fetchAll($fetchStyle);
    }

    public function __clone()
    {
        
    }
}