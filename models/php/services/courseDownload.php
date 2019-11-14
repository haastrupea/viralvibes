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
    
    public function areadyReportlink($user_id)
    {
        $param=[':id'=>$this->link_id,':user'=>$user_id];
        $qry="SELECT COUNT(*) as report_count FROM report_link where link_id=:id AND `user_id`=:user";
            return $this->dbCon->queryDb($qry,$param)[0]["report_count"]==='0'?false:true;
    }

    public function reportLink($user_id,$report_reason)
    {
        if(!$this->areadyReportlink($user_id)){
            $param=[':link_id'=>$this->link_id,':user'=>$user_id,':reason'=>$report_reason];
            $qry="INSERT INTO `report_link` (`link_id`, `user_id`,`reason`) VALUES (:link_id,:user,:reason)";
            $this->dbCon->queryDb($qry,$param);
         }
    }
}