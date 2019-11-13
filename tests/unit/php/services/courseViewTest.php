<?php
namespace Viralvibes\Test;
require dirname(__FILE__,5).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
require dirname(__FILE__,5).DIRECTORY_SEPARATOR. 'models'. DIRECTORY_SEPARATOR. 'php'.DIRECTORY_SEPARATOR. 'services'.DIRECTORY_SEPARATOR.'courseView.php';

use PHPUnit\Framework\TestCase;
use Viralvibes\download\course\courseView;
use Viralvibes\database;
use PDO;

class courseViewTest extends TestCase{
    protected $dbConnection;
    protected $courseView;

    public function setUp():void{
        //setup db connection
        $db=new database();
        // $db=new database('viralvibes_course_materials','root','Undercover');
        $con=new PDO('sqlite::memory:');
        $db->swapDbConnection($con);
        $this->dbConnection=$db;
        //create dbtables
        $this->createDbTable();
        //populate the tables
        $this->buildDataSet();

        //search 
        $this->courseView=new courseView('1',$db);
        $this->courseView->fetchCourse();
    }

    public function tearDown():void{
        unset($this->dbConnection);
        unset($this->courseView);
    }

    public function createDbTable(){
        $db=$this->dbConnection;
        $query1="CREATE TABLE `courses` (
            `course_id` int,
            `institution` varchar(100) NOT NULL,
            `course_code` varchar(10) NOT NULL,
            `course_title` varchar(100) NOT NULL,
            `department` varchar(500) NOT NULL,
            `session` varchar(10) DEFAULT NULL,
            `semester` varchar(10) DEFAULT NULL,
            `view_count` int(11) DEFAULT '0',
            `published` TINYINT(1) NOT NULL DEFAULT '1',
            `when_added` TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `last_update` TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `description` varchar(255) NOT NULL,
            `course_type` varchar(50) NOT NULL,
            `course_unit` int NOT NULL,
            `name_is_acronym` TINYINT(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (`course_id`)
          );";
          $db->queryDb($query1);

        $query="CREATE TABLE `dl_Course_link` (
            `dl_id` int,
            `dl_link` varchar(255) NOT NULL UNIQUE,
            `course_id` int NOT NULL,
            `external_link` TINYINT(1) NOT NULL DEFAULT '1',
            `dl_count` int DEFAULT '0',
            PRIMARY KEY (`dl_id`),
            FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE
          );";
          $db->queryDb($query);

        $query3="CREATE TABLE `update_request` (
            `req_id` int,
            `course_id` int NOT NULL,
            `user_id` int NOT NULL,
            `date_requested` text NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `resolved` tinyint(1) NOT NULL DEFAULT '0',
            `date_resolved` text NULL DEFAULT NULL,
            `resolved_by` int DEFAULT NULL,
            `reason_for_req` varchar(255) NOT NULL,
            PRIMARY KEY (`req_id`)
            );";
          $db->queryDb($query3);
    }

    public function buildDataSet(){
        $db=$this->dbConnection;
        $query1="INSERT INTO `courses` (`course_id`, `institution`, `course_code`, `course_title`, `department`, `session`, `semester`, `view_count`, `published`, `when_added`, `last_update`, `description`, `course_type`, `course_unit`, `name_is_acronym`) VALUES
        (1, 'Obafemi Awolowo University', 'SEM001', 'MAN AND HIS ENVIRONMENT', 'animal science', '2018/2019', '2', 20, 1, '2019-11-01 10:47:17', '2019-11-01 10:47:17', 'no description for now', 'special elective', 2, 0),
        (2, 'obafemi Awolowo University', 'SEM002', 'man and people', 'Estate mangement', '2018/2019', '1', 0, 1, '2019-11-06 00:23:16', '2019-11-06 00:23:16', 'compostry for all student that wants to graduate', 'restricted elective', 4, 0),
        (3, 'obafemi Awolowo University', 'seroo1', 'introduction to English', 'all department', NULL, NULL, 0, 1, '2019-11-06 00:23:16', '2019-11-06 00:23:16', '', 'special elective', 0, 0),
        (4, 'obafemi Awolowo University', 'SEM004', 'asking question', 'a.b.c.d', '2018/2019', '1', 0, 1, '2019-11-06 00:28:41', '2019-11-06 00:28:41', 'wonder but easy to pass', 'restricted elective', 4, 0),
        (5, 'obafemi Awolowo University', 'ans301', 'introduction to ruminant', 'animal science, agricultural economics', '2018/2019', '1', 0, 1, '2019-11-06 00:28:41', '2019-11-06 00:28:41', 'for all department except fncs', 'core', 3, 0),
        (6, 'obafemi Awolowo University', 'ans302', 'introduction to non-ruminant', 'animal science, agricultural economics', '2018/2019', '1', 0, 1, '2019-11-06 00:28:41', '2019-11-06 00:28:41', 'for all department except fncs', 'core', 3, 0),
        (7, 'obafemi Awolowo University', 'cpp301', 'introduction to non-ruminant', 'animal science, agricultural economics', '2018/2019', '1', 0, 1, '2019-11-06 00:28:41', '2019-11-06 00:28:41', 'for all department except fncs', 'core', 3, 0),
        (8, 'obafemi Awolowo University', 'cpp302', 'introduction to non-ruminant', 'animal science, agricultural economics', '2018/2019', '1', 0, 1, '2019-11-06 00:28:41', '2019-11-06 00:28:41', 'for all department except fncs', 'core', 3, 0),
        (9, 'obafemi Awolowo University', 'axd301', 'introduction to non-ruminant', 'animal science, agricultural economics', '2018/2019', '1', 0, 1, '2019-11-06 00:28:41', '2019-11-06 00:28:41', 'for all department except fncs', 'core', 3, 0);";
         $db->queryDb($query1);

        $query="INSERT INTO `dl_Course_link` (`dl_id`, `dl_link`, `course_id`, `external_link`, `dl_count`) VALUES 
        (1, 'https://linkt.to/download/a/file.ext', 1, 1, 5),
        (2, 'http://dowmloadmaterials.com/dl.php?dl=123', 2, 1, 0),
        (3, 'http://dowmloadmaterials.com/dl.php?dl=125', 2, 1, 0),
        (4, 'http://dowmloadmaterials.com/dl.php?dl=127', 2, 1, 0),
        (5, 'http://dowmloadmaterials.com/dl.php?dl=12', 1, 1, 4),
        (6, 'http://dowmloadmaterials.com/dl.php?dl=120', 5, 1, 0),
        (7, 'upload/coursematerials/sem001_2019session.pdf', 5, 0, 0),
        (8, 'upload/coursematerials/sem004_2019secondsemeter.pdf', 1, 0, 5);";
          $db->queryDb($query);

        $query3="INSERT INTO `update_request` (`course_id`, `user_id`, `date_requested`, `resolved`, `date_resolved`, `resolved_by`, `reason_for_req`) VALUES ('1', '1', CURRENT_TIMESTAMP, '0', NULL, NULL, 'it was released over 2 years ago');";
          $db->queryDb($query3);
    }

    /**
     * @dataProvider fetchLinkProvider
     */
    public function test_fetch_course_link_from_database($col,$msg){
        $result=$this->courseView->getLinksAs('Array',$col);
        $this->assertNotEmpty($result,"{$msg}: could not get download links course with id 1");
        if($col!=='*'){
            $cols=explode(',',$col);
            foreach ($cols as $value) {
                $this->assertArrayHasKey($value,$result[0],"was expecting the array to have {$col} in the column(S) returned");
            }
        }
    }

    public function fetchLinkProvider()  
    {
        return [
            'all columns'=>['*',"all Column"],
            'download_id columns'=>['dl_id',"dl_id Column"],
            'course_id columns'=>['course_id',"course_id Column"],
            'download_link column'=>['dl_link',"dl_link Column"],
            'download link,id,course id columns'=>['dl_link,dl_link,course_id',"download link,id,course id columns expected in result returned"]
        ];
    }

    
    public function test_fetch_course_link_from_database_as_array(){
        $result=$this->courseView->getLinksAs('Array','*');
        $this->assertIsArray($result,'Expect links as array');
    }
    
    public function test_fetch_course_link_from_database_as_json(){
        $result=$this->courseView->getCourseAs('Json','*');
        $this->assertJson($result,'Expect links as Json');
    }
    /**
     *@dataProvider fetchCourseProvider
     */
    public function test_fetch_course_from_database_with_id($col,$msg){
        $result=$this->courseView->getCourseAs('Array',$col);
        $this->assertNotEmpty($result,"{$msg}: could not get course with id 1");
        if($col!=='*'){
            $cols=explode(',',$col);
            foreach ($cols as $value) {
                $this->assertArrayHasKey($value,$result[0],"was expecting the array to have {$col} in the column(S) returned");
            }
        }
    }

    /**
     * data Provider for test_fetch_course_from_database_with_id
     */
    public function fetchCourseProvider()
    {
        return [
            'all columns'=>['*',"all Column"],
            'institution columns'=>['institution',"institution Column"],
            'course_id columns'=>['course_id',"course_id Column"],
            'semester columns'=>['semester',"semester Column"],  
            'semester,institution columns'=>['semester,institution',"semester,institution Column"],  
        ];
    }
    
    /**
     * @dataProvider fetchResultAsProvider
     */
    public function test_fetch_course_from_database_as($type,$col){
        $result=$this->courseView->getCourseAs($type,$col);
        switch (strtolower($type) ) {
            case 'json':
            $this->assertJson($result,"Expect course {$col} columns as Json");
            break;           
            default:
            $this->assertIsArray($result,"Default: Expect Course {$col} columns as array");      
        }
    }
    /**
     * provider fn for test_fetch_course_from_database_as
     */
    public function fetchResultAsProvider(){
        return [
            'all columns as array'=>['array','*',"all Column as Array"],
            'all columns as json'=>['json','*',"all Column as Json"],
            'institution columns as array'=>['array','institution',"institution Column as array"],
            'institution column as Json'=>['Json','institution',"institution Column as json"],
            'semester,institution columns as array'=>['Array','semester,institution',"semester,institution Column as Array"], 
            'semester,institution columns as Json'=>['Json','semester,institution',"semester,institution Column as Json"], 
        ];
    }

    public function test_fetch_external_course_link_from_database(){
        $result=$this->courseView->getExternalLinkAs('array');
        $this->assertNotEmpty($result,'could not get download links course with id 1');
        foreach ($result as $value) {
            $this->assertEquals(1,$value['external_link'],"Expecting external links only");
        }
    }
    public function test_fetch_internal_course_link_from_database(){
        $result=$this->courseView->getInternalLinkAs('array');
        $this->assertNotEmpty($result,'could not get download links course with id 1');
        foreach ($result as $value) {
            $this->assertEquals(0,$value['external_link'],"Expecting external links only");
        }
    }

    public function test_compute_download_count()
    {
        $result=$this->courseView->getDownloadCount();
        $this->assertEquals(14,$result);
    }
    public function test_get_total_views_count()
    {
        $result=$this->courseView->getViewCount();
        $this->assertEquals(20,$result,"Expect view count to be 20");
    }
    public function test_update_views_count()
    {
        $this->courseView->updateViewCount();
        $result=$this->courseView->getViewCount();
        $this->assertEquals(21,$result,"Expect view count to increase from 20 to 21");
    }

    public function test_related_Courses()
    {
        $result=$this->courseView->relatedCourses();
        $this->assertCount(5,$result," expect Related Courses to be 5");
    }
    public function test_show_course_description()
    {
        $result=$this->courseView->getCourseDesc();
        $this->assertEquals('no description for now',$result," expect course description to be 'no description for now'");
    }
    public function test_show_course_session()
    {
        $result=$this->courseView->getCourseSession();
        $this->assertEquals('2018/2019',$result," expect course description to be '2018/2019'");
    }
    public function test_show_course_semester()
    {
        $result=$this->courseView->getCourseSemester();
        $this->assertEquals('2',$result," expect course description to be '2'");
    }
    public function test_show_course_unit()
    {
        $result=$this->courseView->getCourseUnit();
        $this->assertEquals('2',$result," expect course description to be '2'");
    }

    public function test_user_already_request_for_course_update()
    {
        $result=$this->courseView->areadyRequestUpdate(1);
        $this->assertTrue($result," The user was expected to have made request for update already");
    }

    public function test_request_for_course_update()
    {
        $this->courseView->RequestUpdate(2,"just testing update feature");
        $result=$this->courseView->areadyRequestUpdate(2);
        $this->assertTrue($result," expect to have added '1' more request");
    }
}