<?php
namespace Viralvibes\Test;
require dirname(__FILE__,5).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
require dirname(__FILE__,5).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'services'.DIRECTORY_SEPARATOR.'database.php';

use PHPUnit\Framework\TestCase;
use Viralvibes\database;

class databaseTest extends TestCase{
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
            `id` int,
            `institution` varchar(100) NOT NULL,
            `code` varchar(10) NOT NULL,
            `title` varchar(100) NOT NULL,
            `department` varchar(500) NOT NULL,
            `session` varchar(10) DEFAULT NULL,
            `semester` varchar(10) DEFAULT NULL,
            `view_count` int(11) DEFAULT '0',
            `published` TINYINT(1) DEFAULT '1',
            `when_added` TEXT DEFAULT CURRENT_TIMESTAMP,
            `last_update` TEXT DEFAULT CURRENT_TIMESTAMP,
            `description` varchar(255) NOT NULL,
            `type` varchar(50) NOT NULL,
            `unit` int NOT NULL,
            `school_name_is_acronym` TINYINT(1) DEFAULT '0',
            PRIMARY KEY (`id`)
          );";
          $db->exec($query);
    }
    static public function buildDataSet(){
        $db=self::$dbcon->getConnection();
        $query="INSERT INTO `courses` (id,`institution`, `code`, `title`, `department`, `session`, `semester`,`description`, `type`, `unit`, `school_name_is_acronym`) VALUES
        (1,'Obafemi Awolowo University', 'SEM001', 'MAN AND HIS ENVIRONMENT', 'animal science', '2018/2019', '2', 'no description for now', 'special elective', 2, 0),
        (2,'obafemi Awolowo University', 'SEM002', 'man and people', 'Estate mangement', '2018/2019', '1','compostry for all student that wants to graduate', 'restricted elective', 4, 0),
        (3,'obafemi Awolowo University', 'seroo1', 'introduction to English', 'all department', NULL, NULL,'', 'special elective', 0, 0),
        (4,'obafemi Awolowo University', 'SEM004', 'asking question', 'a.b.c.d', '2018/2019', '1', 'wonder but easy to pass', 'restricted elective', 4, 0),
        (5,'obafemi Awolowo University', 'ans301', 'introduction to ruminant', 'animal science, agricultural economics', '2018/2019', '1', 'for all department except fncs', 'core', 3, 0),
        (6,'obafemi Awolowo University', 'ans302', 'introduction to non-ruminant', 'animal science, agricultural economics', '2018/2019', '1', 'for all department except fncs', 'core', 3, 0);";
          $db->exec($query);
    }

    public function test_database_connect_successfully()
    {
        $out=self::$dbcon->getConnection();
        $this->assertInstanceOf(\PDO::class,$out,"failed to connnect to database");
    }

    public function test_database_connection_throw_PDOException(){
        $this->expectException('PDOException');
        database::getInstance('mysql',"nonexisting");
    }

   
    /**
     * @dataProvider selectQueryProvider
     */
    public function test_select_from_database($qry,$param,$expect){
        $result=self::$dbcon->crudQuery($qry,$param);
        $this->assertCount($expect,$result,"was expecting 6 results");
    }

    /**
     * dataprovider for test_database_select_query
     */
    public function selectQueryProvider()
    {
        return [
            'just query arg'=>['select * from courses',null,6],
            'just query and unamned param arg'=>['select * from courses where id=?',[1],1],
            'just query and namned param arg'=>['select * from courses where id=:id',[':id'=>1],1],
        ];
    }

    public function test_insert_into_database(){

        $qry="INSERT INTO `courses` (`institution`, `code`, `title`, `department`, `session`, `semester`,`description`, `type`, `unit`) VALUES
        (:inst,:code,:title,:dept,:ses,:sem,:disc,:typ,:unit);";

            self::$dbcon->crudQuery($qry,[':inst'=>'Obafemi Awolowo University', ':code'=>'SEM005', ':title'=>'MAN AND HIS ENVIRONMENT2', ':dept'=>'geology', ':ses'=>'2018/2019',':sem'=>'2',':disc'=>'no description for now', ':typ'=>'special elective', ':unit'=>2]);

            $result=self::$dbcon->crudQuery('select * from courses where code=?',['SEM005']);
            $this->assertNotEmpty($result,"something went wrong, could not insert into database");
    }

    public function test_update_database_table_row()
    {
        $qry="UPDATE courses set `description`=? where id=?";
        self::$dbcon->crudQuery($qry,['just testing table update via unit testing',3]);

        $result=self::$dbcon->crudQuery('select `description` from courses where id=?',[3]);
        $this->assertEquals('just testing table update via unit testing',$result[0]['description'],"something went wrong, could not insert into database");
    }

    public function test_delete_database_entry()
    {
        //insert a course
        $qry="INSERT INTO `courses` (`institution`, `code`, `title`, `department`, `session`, `semester`,`description`, `type`, `unit`) VALUES
        (:inst,:code,:title,:dept,:ses,:sem,:disc,:typ,:unit);";

            self::$dbcon->crudQuery($qry,[':inst'=>'Obafemi Awolowo University', ':code'=>'SEM250', ':title'=>'MAN AND HIS ENVIRONMENT core', ':dept'=>'chemistry', ':ses'=>'2018/2019',':sem'=>'2',':disc'=>'i was never here', ':typ'=>'core', ':unit'=>2]);

        //test if the course has been truelly added
        $result=self::$dbcon->crudQuery('select * from courses where code=?',['SEM250']);
        $this->assertNotEmpty($result,"cold not insert SEM250");
        
        //delete the just added course
        $qry="DELETE FROM courses where code=?";
        self::$dbcon->crudQuery($qry,['SEM250']);
        
        //check if it has been truely deleted
        $result=self::$dbcon->crudQuery('select * from courses where code=?',['SEM250']);
        $this->assertEmpty($result,"cold not insert SEM250");

    }

}