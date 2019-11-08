<?php
namespace Viralvibes\download\course;
use Viralvibes\database;
class courseView{
    protected $courseLink=[];
    protected $dbcon;
    protected $course_id;
    
    public function __construct($course_id)
    {
        $this->course_id=$course_id;
    }

    public function setDbconnection(database $connection){
        $this->dbcon=$connection;
    }

    public function fetchAllLinks(){
        $qry='SELECT * FROM dl_Course_link WHERE course_id=?';
            $this->courseLink=$this->dbcon->queryDb($qry,[$this->course_id]);
    }
    
    public function getLinksAsArray(){
        return $this->courseLink;
    }
    
    public function getLinksAsJson(){
        $arr= $this->getLinksAsArray();
        return json_encode($arr);
    }
    
    public function fetchCourseWithId($columns='*'){
        $qry="SELECT {$columns} FROM courses WHERE course_id=?";
        $this->course=$this->dbcon->queryDb($qry,[$this->course_id]);
    }
    
    public function getCourseAsArray(){
        return $this->course;
    }

    public function getCourseAsJson(){
        $arr= $this->getCourseAsArray();
        return json_encode($arr);
    }
}