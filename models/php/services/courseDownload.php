<?php
namespace Viralvibes\download\course;

use Viralvibes\database;

class downloadLink{
    protected $link_id;
    protected $dbCon;
    public function __construct($link_id)
    {
        $this->link_id=$link_id;
    }
    public function setDbconnection(database $dbCon){
        $this->dbCon=$dbCon;
    }
    public function is_valid()
    {
        $qry="select COUNT(link_id) as count from dl_Course_link where link_id=?";
       return  $this->dbCon->queryDb($qry,[$this->link_id])[0]['count']===0?false:true;
    }
}