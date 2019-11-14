<?php
namespace Viralvibes;
class database{
    protected $dbconnection;
    public function __construct($dbname='',$usr="",$psw=""){
        $dsn="mysql:host=127.0.0.1;dbname={$dbname};charset=utf8";
            $dbcon=new \PDO($dsn,$usr,$psw);
                if(is_object($dbcon)){
                    $this->dbconnection=$dbcon;
                }
        }
    public function swapDbConnection($dbcon){
        if(is_object($dbcon)){
            $this->dbconnection=$dbcon;
        }
    }
    public function getConnection(){
            return $this->dbconnection;
    }
    
    public function queryDb($query,array $param=null,$fetchStyle=\PDO::FETCH_ASSOC){    
        $qry=$this->dbconnection->prepare($query);
        if(is_null($param)){
            $qry->execute();
        }else{
            $qry->execute($param);
        }

        //return result if any
        return $qry->fetchAll($fetchStyle);
    }
}