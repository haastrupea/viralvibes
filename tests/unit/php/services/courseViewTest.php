<?php
namespace Viralvibes\Test;
require dirname(__FILE__,5).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

use PHPUnit\Framework\TestCase;
use Viralvibes\database;
use Viralvibes\Test\databasetrait;

class courseViewTest extends TestCase{
    static protected $dbcon;
    use databasetrait;
    static public function setUpBeforeClass(): void
    {
        self::$dbcon=database::getInstance('sqlite',':memory:');

        //set up DATABASE TABLE
        self::createCourseTable();
        self::createCourseLinkTable();
        //populate table
        self::buildCourseDataSet();
        self::buildCourseLinkDataSet();
    }
    static public function tearDownAfterClass(): void
    {
        $db=self::$dbcon->getConnection();
        $qry="DROP TABLE IF EXISTS courses";
        $qry2="DROP TABLE IF EXISTS dl_Course_link";
        $db->exec($qry);
        $db->exec($qry2);
        self::$dbcon=null;
    }

    public function setUp():void
    {
     
    }

}