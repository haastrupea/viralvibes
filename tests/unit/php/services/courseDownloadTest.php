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
    }

    /**
     * @dataProvider validLinkProvider
     */
    public function test_link_is_valid($link_id,$expect)
    {
        $link=new downloadLink($link_id);
        $output=$link->is_valid();
        $this->assertEquals($expect,$output,"the link id is {$expect}");
        
    }

    public function validLinkProvider()
    {
        return [
        'link is invalid'=>[1,false],
        'link is valid'=>[100,true]
        ];
    }
}