<?php
namespace Viralvibes\download\course;
class downloadLink{
    protected $link_id;
    protected $dbCon;
    protected $link;
    protected $uploadFolder='assets/upload/document/course-materials/';
    public function __construct($link_id,$dbCon)
    {
        $this->link_id=$link_id;
        $this->dbCon=$dbCon;
        $this->fetchLink($this->link_id);
    }

    private function fetchLink()
    {
        
        $qry="select * from dl_Course_link where dl_id=?";
        $this->link=$this->dbCon->crudQuery($qry,[$this->link_id]);
            return $this->link;
    }

    public function get_dl_count(){
        return $this->is_valid()?$this->link[0]['dl_count']:null;
    }

    public function is_valid()
    {      
       return  empty($this->link)?false:true;
    }
    public function updateDownloadCount()
    {
        if($this->is_valid()){
            $qry="UPDATE dl_Course_link SET dl_count=dl_count+1 where dl_id=?";
            $this->dbCon->crudQuery($qry,[$this->link_id]);
            $this->fetchLink();
        }
    }
    
    public function areadyReportlink($user_id)
    {
        $param=[':id'=>$this->link_id,':user'=>$user_id];
        $qry="SELECT COUNT(*) as report_count FROM report_link where link_id=:id AND `user_id`=:user";
            return $this->dbCon->crudQuery($qry,$param)[0]["report_count"]==='0'?false:true;
    }

    public function reportLink($user_id,$report_reason)
    {
        if(!$this->areadyReportlink($user_id)){
            $param=[':link_id'=>$this->link_id,':user'=>$user_id,':reason'=>$report_reason];
            $qry="INSERT INTO `report_link` (`link_id`, `user_id`,`reason`) VALUES (:link_id,:user,:reason)";
            $this->dbCon->crudQuery($qry,$param);
         }
    }

    public function isExternal(){
        return $this->link[0]['external_link']?true:false;
    }
    
    public function getLink(){
        $folder=$this->uploadFolder;
        $file=$this->link[0]['dl_link'];
        return $this->isExternal()?$file:$folder.$file;
    }
    
    public function getCourseId(){
        return $this->link[0]['course_id'];
    }

    private function fetchCourse()
    {
        $qry="select * from courses where id=?";
        return $this->dbCon->crudQuery($qry,[$this->getCourseId()])[0];
    }

    public function getFileName(){
        if(!$this->isExternal()){
            $course=$this->fetchCourse();
            $file_name=$course['code']." ";
            $file_name.=$course['title']." ";
            $file_name.=$course['session']."session";
            $file_name.=".".$this->link[0]['fileType'];//extension

            return $file_name;
        }
    }

    private function internalDownload()
    {
        if(!$this->isExternal()){
            //update link download count
            $this->updateDownloadCount();
            $file=$this->getLink();
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.$this->getFileName().'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }
    }

    private function externalDownload()
    {
        if ($this->isExternal()) {
            //update link download count
            $this->updateDownloadCount();

            $exlink=$this->getLink();
            if(filter_var($exlink,FILTER_VALIDATE_URL)){
                header("Location: {$exlink}");
                die();
            }
        }
    }

    public function download()
    {
        if($this->isExternal()){
            $this->externalDownload();
        }else{
            $this->internalDownload();
        }
    }
}