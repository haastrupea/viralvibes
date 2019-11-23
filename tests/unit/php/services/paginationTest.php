<?php
namespace Viralvibes\Test;
require dirname(__FILE__,5).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

use PHPUnit\Framework\TestCase;
use Viralvibes\download\course\courseSearch;
use Viralvibes\Test\databasetrait;
use Viralvibes\database;

class paginationTest extends TestCase{
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
        $db=self::$dbcon->getConnection();
        $qry="DROP TABLE IF EXISTS courses";
        $db->exec($qry);
        self::$dbcon=null;
    }

    public function setUp():void
    {
       $this->courseSearch=new courseSearch(self::$dbcon);
    }

}