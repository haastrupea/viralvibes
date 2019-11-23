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
    
    public function test_fetch_all_course_materials(){
        $this->courseSearch->search('');

        $result=$this->courseSearch->getResult('array');
        $this->assertIsArray($result,"expect to fetch all course material in database as array");
        $this->assertCount(6,$result,"expecting all 6 course materials in the database");
    } 
    
    public function test_no_result_found_for_course_materials(){
        $this->courseSearch->search('non existing course material');

        $result=$this->courseSearch->getResult('array');
        $this->assertIsArray($result,"expect empty array when no rsult is found");
        $this->assertEmpty($result,"expecting no result found i.e empty result");
    }
    
    /**
     * @dataProvider searchFilterProvider
     */
    public function test_filter_course_materials_search_result($filter,$value,$msg)
    {
    
        $this->courseSearch->setSqlfilter($filter,$value);//filter
        $this->courseSearch->search('');//instantiate search term

        $results=$this->courseSearch->getResult();//build query and Default to array
        $this->assertNotEmpty($results,"expect non empty {$value} filtered result");

        foreach ($results as $result) {
            $this->assertEquals($value,$result[$filter],$msg);
        }
    }

    public function searchFilterProvider()
    {
        return [
            'first semester filter'=>['semester','1',"expect to 1 for first semester filter"],
            'second semester filter'=>['semester','2',"expect to 2 for second semester filter"],
            'school filter'=>['institution','Obafemi Awolowo University',"expect obafemi awolowo University as filter result"],
            'session filter'=>['session','2019/2020','expect 2019/2020 for session filter']
        ];
    }

    /**
     * @dataProvider sortResultProvider
     */
    public function test_sort_course_materials_search_result($sortBy,$direction,$expected)
    {
    
        $this->courseSearch->sortResultBy($sortBy);//sort
        $this->courseSearch->sortDirection($direction);//sort
        $this->courseSearch->search('');//instantiate search term and build query
        
        $results=$this->courseSearch->getResult();//Default to array
        
        if($direction==="DESC"){
            $this->assertLessThan($results[0][$expected],$results[5][$expected],"expecting {$sortBy} {$direction}: sorted result first entry to be greater than last entry");
        }else{  
            $this->assertGreaterThan($results[0][$expected],$results[5][$expected],"expecting {$sortBy} {$direction}: sorted result last entry to be greater than first entry");
        }

    }

       /**
     * dataProvider for sortResultByProvider
     */
    public function sortResultProvider()
    {
        return [
            'Date ascending'=>['date','ASC','when_added'],
            'Date descending'=>['date','DESC','when_added'],
            'views ascending'=>['views','ASC','view_count'],
            'views descending'=>['views','DESC','view_count'],
            'download ascending'=>['download','ASC',"download"],
            'download descending'=>['download','DESC',"download"]
        ];
    }


}