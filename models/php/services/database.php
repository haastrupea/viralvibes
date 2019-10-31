<?php
namespace Viralvibes;

class database{
  
    public function connect($dsn,$usr,$psw){
            $dbcon=new \PDO($dsn,$usr,$psw);
            var_dump($dbcon);
            if($dbcon){
                return true;
            }
        }
    }