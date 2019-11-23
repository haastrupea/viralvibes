<?php
namespace Viralvibes\Test;
require dirname(__FILE__,5).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

use PHPUnit\Framework\TestCase;
use Viralvibes\Test\databasetrait;
use Viralvibes\database;
use Viralvibes\download\course\courseSearch;

class courseSearchTest extends TestCase{
    static protected $dbcon;
    use databasetrait;
    static public function setUpBeforeClass(): void
    {
        self::$dbcon=database::getInstance('sqlite',':memory:');

        //set up DATABASE TABLE
        self::createCourseTable();
        //populate table
        self::buildCourseDataSet();
    }
    static public function tearDownAfterClass(): void
    {
        $qry="DROP TABLE IF EXISTS courses";
        self::$dbcon->getConnection()->exec($qry);
        self::$dbcon=null;
    }

    public function setUp():void
    {
       $this->courseSearch=new courseSearch(self::$dbcon);
    }

    public function test_search_for_course_materials(){
        $this->courseSearch->search('sem001');

        $result=$this->courseSearch->getResult('array');
        $this->assertIsArray($result,"expect search result as array");
        $this->assertNotEmpty($result,"expect non empty array");

        $result1=$this->courseSearch->getResult('json');
        $this->assertJson($result1,"expect search result to be a valid json data");
    }
    
    public function test_filter_course_materials_search_result()
    {
        //filter
        $this->courseSearch->filter;
        $this->courseSearch->search('');
        $result=$this->courseSearch->getResult();//Default to array
    }
}