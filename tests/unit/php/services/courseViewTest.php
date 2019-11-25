<?php
namespace Viralvibes\Test;
require dirname(__FILE__,5).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

use PHPUnit\Framework\TestCase;
use Viralvibes\database;
use Viralvibes\Test\databasetrait;
use Viralvibes\download\course\courseView;

class courseViewTest extends TestCase{
    static protected $dbcon;
    protected $courseView;
    use databasetrait;
    static public function setUpBeforeClass(): void
    {
        self::$dbcon=database::getInstance('sqlite',':memory:');

        //set up DATABASE TABLE
        self::createCourseTable();
        self::createCourseLinkTable();
        self::createCourseUpdateRequestTable();
        //populate table
        self::buildCourseDataSet();
        self::buildCourseLinkDataSet();
        self::buildCourseUpdateRequestDataSet();
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
      //search 
      $this->courseView=new courseView('1',self::$dbcon);
      $this->courseView->fetchCourse();
    }
    public function tearDown():void{
        unset($this->courseView);
    }

    public function test_get_all_links_type_for_a_course_as_array(){
        $result=$this->courseView->getLinksAs('Array');
        $this->assertNotEmpty($result,"expects non empty result of links for course with id 1 as array");
        $this->assertCount(3,$result,"expects all 3 links for course with id 1");
    }
    
    public function test_get_all_links_type_for_a_course_as_json(){
        $result=$this->courseView->getLinksAs('json');
        $this->assertJson($result,"expect links data as json");
    }

    public function test_get_course_as_array(){
        $result=$this->courseView->getCourseAs('Array');

        $this->assertNotEmpty($result,"expects course data as array");
    } 
    
    public function test_get_course_as_Json(){
        $result=$this->courseView->getCourseAs('json');

        $this->assertJson($result,"expect course data as json");
    }

    public function test_fetch_external_course_link_only(){
        $result=$this->courseView->getExternalLinkAs('array');
        $this->assertNotEmpty($result,'could not get download links course with id 1');
        foreach ($result as $value) {
            $this->assertEquals(1,$value['external_link'],"Expecting external links only");
        }
        $result=$this->courseView->getExternalLinkAs('json');

        $this->assertJson($result,"expect external course link data as json");
    }
    public function test_fetch_internal__course_link_only(){
        $result=$this->courseView->getInternalLinkAs('array');
        $this->assertNotEmpty($result,'could not get download links course with id 1');
        foreach ($result as $value) {
            $this->assertEquals(0,$value['external_link'],"Expecting external links only");
        }

        $result=$this->courseView->getInternalLinkAs('json');

        $this->assertJson($result,"expect internal course link data as json");

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
        $this->assertCount(2,$result," expect Related Courses to be 2");
    }
    public function test_show_course_description()
    {
        $result=$this->courseView->getCourseDesc();
        $this->assertEquals('no description for now',$result," expect course description to be 'no description for now'");
    }
    public function test_show_course_session()
    {
        $result=$this->courseView->getCourseSession();
        $this->assertEquals('2019/2020',$result," expect course description to be '2019/2020'");
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