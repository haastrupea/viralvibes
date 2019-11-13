<?php
namespace Viralvibes\download\course;

use Viralvibes\database;
class downloadLink{
    protected $link_id;
    protected $dbCon;
    protected $link;
    public function __construct($link_id,database $dbCon)
    {
        $this->link_id=$link_id;
        $this->dbCon=$dbCon;
        $this->fetchLink($this->link_id);
    }

    public function fetchLink()
    {
        
        $qry="select * from dl_Course_link where dl_id=?";
        $this->link=$this->dbCon->queryDb($qry,[$this->link_id]);
        
        return $this->link;
    }

    public function get_dl_count(){
        if($this->is_valid()){
            return $this->link[0]['dl_count'];
        }
        return null;
    }

    public function is_valid()
    {      
       return  empty($this->link)?false:true;
    }
    public function updateDownloadCount()
    {
        if($this->is_valid()){
            $qry="UPDATE dl_Course_link SET dl_count=dl_count+1 where dl_id=?";
            $this->dbCon->queryDb($qry,[$this->link_id]);
            $this->fetchLink();
        }
    }
}