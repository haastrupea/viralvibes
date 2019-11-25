<?php
namespace Viralvibes\download\course;
class courseView{
    protected $dbcon;
    protected $course_id;
    protected $course=[];
    
    public function __construct($course_id,$connection)
    {
        $this->course_id=$course_id;
        $this->dbcon=$connection;
    }

    /**
     * @todo make this function private
     */
    private function fetchLinks($type=null){
        $qry="SELECT * FROM dl_Course_link WHERE course_id=?";
        if(isset($type)){
            if($type=='ext'){
                $qry.= " AND external_link=1";
            }else if($type=="int"){
                $qry.= " AND external_link=0";
            }
        }
            return $this->dbcon->crudQuery($qry,[$this->course_id]);
    }
    
    /**
     * @todo call FetchLinks before 
     */
    public function getLinksAs($type){
        switch (strtolower($type)) {
            case 'json':
                return json_encode($this->fetchLinks());
                break;
            default:
            return $this->fetchLinks();
        }
    }
    
    private function fetchCourseWithId(){
        $qry="SELECT * FROM courses WHERE id=?";
        return $this->dbcon->crudQuery($qry,[$this->course_id]);
    }
    public function fetchCourse(){
        $this->course=$this->fetchCourseWithId()[0];
    }

    public function getCourseAs($type,$col='*'){
        $result=$this->fetchCourseWithId($col);
        switch (strtolower($type)) {
            case 'json':
                return json_encode($result);
                break;
            default:
                return $result;//return array as default
        }
    }

    public function getExternalLinkAs($type){
        switch (strtolower($type)) {
            case 'json':
                return json_encode($this->fetchLinks('ext'));
                break;
            default:
                return $this->fetchLinks('ext');
        }
    }
    
    public function getInternalLinkAs($type){
        switch (strtolower($type)) {
            case 'json':
                return json_encode($this->fetchLinks('int'));
                break;
            default:
                return $this->fetchLinks('int');
        }
    }

    public function getDownloadCount()
    {
        $qry="SELECT SUM(dl_count) as download_count FROM dl_Course_link WHERE course_id=?";
        return $this->dbcon->crudQuery($qry,[$this->course_id])[0]['download_count'];  
    }
    public function getViewCount()
    {
        $course=$this->course;
        return $course['view_count'];
    }
    public function updateViewCount()
    {
        $qry="UPDATE courses SET view_count=view_count+1 where id=?";
        $this->dbcon->crudQuery($qry,[$this->course_id]);
        $this->fetchCourse();
    }

    public function relatedCourses($limit='10')
    {
        $course=$this->course;
        $param=[':school'=>'%'.$course['institution'].'%',':dept'=>"%".$course['department']."%",':id'=>$course['id']];
        
        $qry="SELECT * FROM courses WHERE (institution like :school AND department like :dept ) AND NOT id=:id order by view_count DESC LIMIT $limit";
            return $this->dbcon->crudQuery($qry,$param);
    }

    public function getCourseDesc()
    {
        return $this->course['description'];
    }

    public function getCourseSession()
    {
        return $this->course['session'];
    }
    
    public function getCourseSemester()
    {
        return $this->course['semester'];
    }
    public function getCourseUnit()
    {
        return $this->course['unit'];
    }

    /**
     * @todo : request for update functionality
     */

     public function areadyRequestUpdate($user_id)
     {
        $param=[':id'=>$this->course_id,':user'=>$user_id];
        $qry="SELECT COUNT(*) as req_count FROM update_request where course_id=:id AND `user_id`=:user";
            return $this->dbcon->crudQuery($qry,$param)[0]["req_count"]==='0'?false:true;
     }
     /**
      * @todo : remove 2 and req_id from query in production
      */
     public function RequestUpdate($user_id,$reasonForRequest)
     {
         if(!$this->areadyRequestUpdate($user_id)){
            $param=[':course_id'=>$this->course_id,':user'=>$user_id,':reason'=>$reasonForRequest];
            $qry="INSERT INTO `update_request` (`course_id`, `user_id`,`reason_for_req`) VALUES (:course_id,:user,:reason)";
            $this->dbcon->crudQuery($qry,$param);
         }
     }
}