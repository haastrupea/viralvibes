<?php
namespace Viralvibes\Test;
require dirname(__FILE__,5).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
require dirname(__FILE__,5).DIRECTORY_SEPARATOR. 'models'. DIRECTORY_SEPARATOR. 'php'.DIRECTORY_SEPARATOR. 'services'.DIRECTORY_SEPARATOR.'courseSearch.php';

use PDO;
use Viralvibes\Download\course\search;
use PHPUnit\Framework\TestCase;
use Viralvibes\database;

class courseSearchTest extends TestCase{
    protected $dbConnection;

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
    }

    public function createDbTable(){
        $db=$this->dbConnection;
        $query="CREATE TABLE `courses` (
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
          $db->queryDb($query);
    }

    public function buildDataSet(){
        $db=$this->dbConnection;
        $query="INSERT INTO `courses` (`course_id`, `institution`, `course_code`, `course_title`, `department`, `session`, `semester`, `view_count`, `published`, `when_added`, `last_update`, `description`, `course_type`, `course_unit`, `name_is_acronym`) VALUES
        (1, 'Obafemi Awolowo University', 'SEM001', 'MAN AND HIS ENVIRONMENT', 'animal science', '2018/2019', '2', 0, 1, '2019-11-01 10:47:17', '2019-11-01 10:47:17', 'no description for now', 'special elective', 2, 0),
        (2, 'obafemi Awolowo University', 'SEM002', 'man and people', 'Estate mangement', '2018/2019', '1', 0, 1, '2019-11-06 00:23:16', '2019-11-06 00:23:16', 'compostry for all student that wants to graduate', 'restricted elective', 4, 0),
        (3, 'obafemi Awolowo University', 'seroo1', 'introduction to English', 'all department', NULL, NULL, 0, 1, '2019-11-06 00:23:16', '2019-11-06 00:23:16', '', 'special elective', 0, 0),
        (4, 'obafemi Awolowo University', 'SEM004', 'asking question', 'a.b.c.d', '2018/2019', '1', 0, 1, '2019-11-06 00:28:41', '2019-11-06 00:28:41', 'wonder but easy to pass', 'restricted elective', 4, 0),
        (5, 'obafemi Awolowo University', 'ans301', 'introduction to ruminant', 'animal science, agricultural economics', '2018/2019', '1', 0, 1, '2019-11-06 00:28:41', '2019-11-06 00:28:41', 'for all department except fncs', 'core', 3, 0),
        (6, 'obafemi Awolowo University', 'ans302', 'introduction to non-ruminant', 'animal science, agricultural economics', '2018/2019', '1', 0, 1, '2019-11-06 00:28:41', '2019-11-06 00:28:41', 'for all department except fncs', 'core', 3, 0);";
          $db->queryDb($query);
    }

    public function test_student_search_for_a_course_material(){
        $search=new search('sem001');
        $output=$search->getSearchTerm();
        $this->assertEquals('sem001',$output,"the output was expected to be sem001");
    }

    public function test_search_courses_without_keyword(){
        $search=new search("");
        $output=$search->getSearchTerm();
        $this->assertFalse($output,"was expecting no search term");      
    }

    public function test_database_empty_sql_query_array()
    {
        $search=new search();
        $output=$search->sql_query_array();
        $this->assertEmpty($output,"Expecting the query array to be empty");

    }

    public function test_return_all_column_from_course_table()
    {
        $search=new search();
        $output=$search->select('*');
        $this->assertEqualsIgnoringCase('select * from courses',$output,"Expecting select * from courses");

    }

    public function test_return_specified_column_from_course_table()
    {
        $search=new search();
        $output=$search->select('course_id,institution,course_code,department');
        $this->assertEqualsIgnoringCase('select course_id,institution,course_code,department from courses',$output,"Expecting select course_id,institution,course_code,department from courses");

    }

    public function test_specify_column_to_return_as_array()
    {
        $search=new search();
        $output=$search->select(['course_id','institution']);
        $this->assertEqualsIgnoringCase('select course_id,institution from courses',$output,"Expecting select course_id,institution, from courses");

    }

     /**
     * @dataProvider sortResultByProvider
     */
    public function test_sort_result_by($column,$msg)
    {
        $search=new search();
        $output=$search->sortResultBy($column);
        $this->assertEqualsIgnoringCase($column,$output,$msg);
    }

     /**
     * dataProvider for sortResultByProvider
     */
    public function sortResultByProvider()
    {
        return [
            ['date',"Expecting order by 'date' i.e the date the courses were added"]
        ];
    }

    /**
     * @dataProvider sortResultProvider
     */
    public function test_sort_result_direction($direction,$msg)
    {
        $search=new search();
        $output=$search->sortDirection($direction);
        $this->assertEqualsIgnoringCase($direction,$output,$msg);

    }

 /**
     * dataprovider for test_sort_result
     */
    public function sortResultProvider()
    {
       return [
           ['ASC',"Expecting ASC i.e sort in ASCending order"],
           ['DESC',"Expecting DESC i.e sort in DESCending order"]
       ];
    }

    /**
     * @dataProvider filterDataProvider
     */
    public function test_filter_result($filter,$filterValue,$msg)
    {
        $search=new search();
        $search->setSqlfilter($filter,$filterValue);
        $output=$search->getSqlFilter($filter);
        $this->assertEqualsIgnoringCase($filterValue,$output,$msg);

    }

    /**
     * dataprovider for test_filter_result
     */
    public function filterDataProvider(){
        return [
            'first semester filter'=>['semester','1',"expect to 1 for first semester filter"],
            'second semester filter'=>['semester','2',"expect to 2 for second semester filter"],
            'school filter'=>['institution','obafemi awolowo University',"expect obafemi awolowo University as filter result"],
            'session filter'=>['session','2019/2020','expect 2019/2020 for session filter']
        ];
    }

    public function test_set_search_result_limit()
    {
        $search= new search();
        $output=$search->setResultLimit(3);
        $this->assertEquals('3',$output,'Total number of search expected is limited to 3');
    }

    public function test_set_search_result_offset()
    {
        $search= new search();
        $output=$search->setOffset(2);
        $this->assertEquals('2',$output,'search result expected is offset by 2');
    }

    public function test_build_sql_search_query(){
        //create instance of search
        $search= new search('sem001');
        //select all column
        $search->select('*');
        //filter
        $search->setSqlfilter('institution','Obafemi Awolowo University');
        //sort
        $search->sortResultBy('when_added');
        //sort direction
        $search->sortDirection('ASC');
        //limit result to 3
        $search->setResultLimit(3);
        //offset result by 2
        $search->setOffset(2);
        //build search string
        $search->buildQuery();
        $output=$search->get_sql_query_string();
        //expected query
        $query="select * from courses where (institution like :searchterm or course_code like :searchterm or course_title like :searchterm or department like :searchterm) and institution=:institution order by :sortby ASC LIMIT :limit OFFSET :offset";
        //assert
        $this->assertEquals($query,$output,"expecting $query");

        $outputparam=$search->get_sql_query_param_array();
        //expected query
        $param=[':searchterm' => '%sem001%',':institution' => 'Obafemi Awolowo University',':sortby' => 'when_added',':limit' => 3,':offset' => 2];
        //assert
        $this->assertEquals($param,$outputparam,"expecting parameterised array");
    }
  
    /**
     * @dataProvider searchResultProvider
     */
    public function test_fetch_search_result_as_array($searchTerm,$expect,$msg){
         //create instance of search
         $search= new search($searchTerm);
         $search->setDbconnection($this->dbConnection);
         //select all column
         $search->select('*');
         //build search string
         $search->buildQuery();
         $output=$search->getResultAsArray();
         //assert
         $this->assertIsArray($output,"the search result was expected to be array");
         $this->assertCount($expect,$output,$msg);
    }

    public function searchResultProvider()
    {
        return [
            ['','6','expect to return all the courses in the database as array when there is no search term and filter'],
            ['monday','0','no result found'],
        ];
    }

    public function test_return_Course_Search_Result_as_json(){
        $search=new search('sem001');
        $search->setDbconnection($this->dbConnection);
        //select all column
        $search->select('*');
        //build search string
        $search->buildQuery();

        $output=$search->getResultAsJson();
        $this->assertJson($output,'was expecting search result in array');
    }
    }