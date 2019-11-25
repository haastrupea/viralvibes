<?php
namespace Viralvibes\Test;
require dirname(__FILE__,5).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

use PHPUnit\Framework\TestCase;
use Viralvibes\database;
use Viralvibes\Test\databasetrait;
use Viralvibes\download\course\downloadLink;

class courseDownloadTest extends TestCase{
    static protected $dbcon;
    protected $courseView;
    use databasetrait;
    static public function setUpBeforeClass(): void
    {
        self::$dbcon=database::getInstance('sqlite',':memory:');

        //set up DATABASE TABLE
        self::createCourseTable();
        self::createCourseLinkTable();
        self::createLinkReportTable();
        //populate table
        self::buildCourseDataSet();
        self::buildCourseLinkDataSet();
        self::buildLinkReportDataSet();
    }
    static public function tearDownAfterClass(): void
    {
        $db=self::$dbcon->getConnection();
        $qry="DROP TABLE IF EXISTS courses";
        $qry2="DROP TABLE IF EXISTS dl_Course_link";
        $qry3="DROP TABLE IF EXISTS report_link";
        $db->exec($qry);
        $db->exec($qry2);
        $db->exec($qry3);
        self::$dbcon=null;
    }


        /**
     * @dataProvider validLinkProvider
     */
    public function test_link_is_valid($link_id,$expect)
    {
        $link=new downloadLink($link_id,self::$dbcon);
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
        $link= new downloadLink(1,self::$dbcon);
        $link->updateDownloadCount();
        $out=$link->get_dl_count();
        $this->assertEquals(6,$out,"Expect download count of 6");
    }
    

    public function test_user_already_report_link()
    {
        $link= new downloadLink(5,self::$dbcon);
        $result=$link->areadyReportlink(1);
        $this->assertTrue($result," The user was expected to have reported  this link as broken already");
    }

    public function test_user_report_a_link()
    {
        $link= new downloadLink(5,self::$dbcon);
        $link->reportLink(3,"just testing report link feature");
        $result=$link->areadyReportlink(3);
        $this->assertTrue($result," expect to have added '1' more report for this link");
    }

    
    public function test_internal_file_name(){
        $link= new downloadLink(7,self::$dbcon);
        $fileName=$link->getFileName();
        $this->assertEquals('ans301 introduction to ruminant 2018/2019session.pdf',$fileName,"expects to get file names part of the internal link");
    }

    public function test_get_course_download_url(){
        $link=new downloadLink(1,self::$dbcon);
        $url=$link->getLink();
        $this->assertEquals('https://linkt.to/download/a/file.ext',$url,"expects https://linkt.to/download/a/file.ext");
    } 
    
    public function test_checking_file_permission(){
        $link=new downloadLink(7,self::$dbcon);
        $url=$link->getLink();
        $this->assertFileIsReadable($url,"expects course file to be readable");
        $this->assertFileIsWritable($url,"expects course file to be Writable");
        $this->assertFileExists($url,"expects course file to exist");

    }
    
}