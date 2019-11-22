<?php
namespace Viralvibes\Test;
require dirname(__FILE__,5).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

use PHPUnit\Framework\TestCase;
use Viralvibes\database;
use \PDO;
use Viralvibes\download\course\search;

class dataBaseTest extends TestCase{
    static protected $dbcon;
    static public function setUpBeforeClass(): void
    {
        self::$dbcon=database::getInstance('sqlite',':memory:');

        //set up DATABASE TABLE
        self::createDbTable();
        //populate table
        self::buildDataSet();
    }
    static public function tearDownAfterClass(): void
    {
        $qry="DROP TABLE IF EXISTS courses";
        self::$dbcon->getConnection()->exec($qry);
        self::$dbcon=null;
    }

    static public function createDbTable(){
        $db=self::$dbcon->getConnection();
        $query="CREATE TABLE IF NOT EXISTS `courses` (
            `course_id` int NOT NULL,
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
          $db->exec($query);
    }
    static public function buildDataSet(){
        $db=self::$dbcon->getConnection();
        $query="INSERT INTO `courses` (`course_id`, `institution`, `course_code`, `course_title`, `department`, `session`, `semester`, `view_count`, `published`, `when_added`, `last_update`, `description`, `course_type`, `course_unit`, `name_is_acronym`) VALUES
        (1, 'Obafemi Awolowo University', 'SEM001', 'MAN AND HIS ENVIRONMENT', 'animal science', '2018/2019', '2', 0, 1, '2019-11-01 10:47:17', '2019-11-01 10:47:17', 'no description for now', 'special elective', 2, 0),
        (2, 'obafemi Awolowo University', 'SEM002', 'man and people', 'Estate mangement', '2018/2019', '1', 0, 1, '2019-11-06 00:23:16', '2019-11-06 00:23:16', 'compostry for all student that wants to graduate', 'restricted elective', 4, 0),
        (3, 'obafemi Awolowo University', 'seroo1', 'introduction to English', 'all department', NULL, NULL, 0, 1, '2019-11-06 00:23:16', '2019-11-06 00:23:16', '', 'special elective', 0, 0),
        (4, 'obafemi Awolowo University', 'SEM004', 'asking question', 'a.b.c.d', '2018/2019', '1', 0, 1, '2019-11-06 00:28:41', '2019-11-06 00:28:41', 'wonder but easy to pass', 'restricted elective', 4, 0),
        (5, 'obafemi Awolowo University', 'ans301', 'introduction to ruminant', 'animal science, agricultural economics', '2018/2019', '1', 0, 1, '2019-11-06 00:28:41', '2019-11-06 00:28:41', 'for all department except fncs', 'core', 3, 0),
        (6, 'obafemi Awolowo University', 'ans302', 'introduction to non-ruminant', 'animal science, agricultural economics', '2018/2019', '1', 0, 1, '2019-11-06 00:28:41', '2019-11-06 00:28:41', 'for all department except fncs', 'core', 3, 0);";
          $db->exec($query);
    }

    public function test_database_connection_throw_PDOException(){
        $this->expectException('PDOException');
        database::getInstance()
    }
    
    public function  test_database_connected_successfully(){
        $output=$this->dbConnection->getConnection();
        $this->assertInstanceOf(PDO::class,$output,"The object returned is not PDO object as expected");
    }
    public function  test_swapping_database_connection(){
        $db=new database();
        $dbold=$db->getConnection()->getAttribute(PDO::ATTR_DRIVER_NAME);
        $con=new PDO('sqlite::memory:');
        $db->swapDbConnection($con);
        $dbnew=$db->getConnection()->getAttribute(PDO::ATTR_DRIVER_NAME);
        $this->assertNotEquals($dbold,$dbnew,"Could not connect to the swapped database from:{$dbold} to: {$dbnew}");
    }
    public function  test_db_select_query(){
        $search= new search('wonder');
        $search->setDbconnection($this->dbConnection);
        $search->select();
        $search->buildQuery();
        $query=$search->get_sql_query_string();
       $result=$this->dbConnection->queryDb($query);
       $this->assertIsArray($result,"expect db_query to return array");
    }
}