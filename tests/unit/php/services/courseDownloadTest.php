<?php
namespace Viralvibes\Test;
require dirname(__FILE__,5).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
require dirname(__FILE__,5).DIRECTORY_SEPARATOR. 'models'. DIRECTORY_SEPARATOR. 'php'.DIRECTORY_SEPARATOR. 'services'.DIRECTORY_SEPARATOR.'courseDownload.php';

use PHPUnit\Framework\TestCase;
use Viralvibes\download\course\downloadLink;
use Viralvibes\database;
use PDO;
class courseDownloadTest extends TestCase{

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
    }

    public function tearDown():void{
        unset($this->dbConnection);
        unset($this->courseView);
    }

    public function createDbTable(){
        $db=$this->dbConnection;
        $query1="CREATE TABLE `report_link` (
            `report_id` int,
            `link_id` int(11) NOT NULL,
            `user_id` int(11) NOT NULL,
            `report_date` text NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `resolved` tinyint(1) NOT NULL DEFAULT '0',
            `date_resolved` text NULL DEFAULT '0000-00-00 00:00:00',
            `resolved_by` int(11) DEFAULT NULL,
            `reason` varchar(255) NOT NULL,
            PRIMARY KEY (`report_id`)
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
    }

    public function buildDataSet(){
        $db=$this->dbConnection;
        $query1="INSERT INTO `report_link` (`link_id`, `user_id`,`reason`) VALUES
        (5, 1, 'link is broken'),(5, 2,'file has been deleted');";
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
    }

    /**
     * @dataProvider validLinkProvider
     */
    public function test_link_is_valid($link_id,$expect)
    {
        $link=new downloadLink($link_id,$this->dbConnection);
        $output=$link->is_valid();
        $this->assertEquals($expect,$output,"the link id is {$expect}");
    }

    public function validLinkProvider()
    {
        return [
        'link is invalid'=>[100,false],
        'link is valid'=>[1,true]
        ];
    }

    public function test_increase_link_download_count_by_1()
    {
        $link= new downloadLink(1,$this->dbConnection);
        $link->updateDownloadCount();
        $out=$link->get_dl_count();
        $this->assertEquals(6,$out,"Expect download count of 6");
    }
    

    public function test_user_already_report_link()
    {
        $link= new downloadLink(5,$this->dbConnection);
        $result=$link->areadyReportlink(1);
        $this->assertTrue($result," The user was expected to have reported  this link as broken already");
    }

    public function test_user_report_a_link()
    {
        $link= new downloadLink(5,$this->dbConnection);
        $link->reportLink(3,"just testing report link feature");
        $result=$link->areadyReportlink(3);
        $this->assertTrue($result," expect to have added '1' more report for this link");
    }
}