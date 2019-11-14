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
        $this->search= new search();
        $this->search->setDbconnection($db);
    }

    public function tearDown():void{
        unset($this->dbConnection);
    }

    public function createDbTable(){
        $db=$this->dbConnection;
        $query="CREATE TABLE `courses` (
            `id` int NOT NULL,
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
            PRIMARY KEY (`id`)
          );";
          $db->queryDb($query);
    }

    public function buildDataSet(){
        $db=$this->dbConnection;
        $query="INSERT INTO `courses` (`id`, `institution`, `course_code`, `course_title`, `department`, `session`, `semester`, `view_count`, `published`, `when_added`, `last_update`, `description`, `course_type`, `course_unit`, `name_is_acronym`) VALUES
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
        $output=$this->search->getSearchTerm();
        $this->assertFalse($output,"was expecting no search term");      
    }

    public function test_empty_database_query_array()
    {
        $output=$this->search->sql_query_array();
        $this->assertEmpty($output,"Expecting the query array to be empty");

    }

    /**
     * @dataProvider selectColumnProvider
     */
    public function test_column_to_select_from_course_table($col,$expected)
    {
        $this->search->select($col);
        $this->search->buildQuery();       
        $output=$this->search->get_sql_query_string(); 
        $this->assertStringContainsStringIgnoringCase($expected,$output,"Expecting {$expected} column(s)");

    }

    public function selectColumnProvider()
    {
        return [
            'select all column'=>[['*'],'*'],
            'select course id,institution,course_code and department columns as array'=>[['id','institution','course_code','department'],'course_code,department,id,institution'],
            'select id column as array'=>[['id'],'id'],
        ];
    }

     /**
     * @dataProvider sortResultByProvider
     */
    public function test_sort_result_by($orderby,$expected)
    {
        $this->search->sortResultBy($orderby);
        //build search string
        $this->search->select(['*']);
        $this->search->buildQuery();       
        $output=$this->search->get_sql_query_string();       
        $output2=$this->search->get_sql_query_param_array();       
        $this->assertStringContainsStringIgnoringCase(':sortby',$output,"Expect query string to contain :sortby");
        $this->assertEquals($expected,$output2[':sortby'],"Expect sql_query_param_array[':sortby'] to equal {$output}");
    }

     /**
     * dataProvider for sortResultByProvider
     */
    public function sortResultByProvider()
    {
        return [
            'Date'=>['date','when_added'],
            'views'=>['views','view_count'],
            'download'=>['download',"download"]
        ];
    }

    /**
     * @dataProvider sortResultProvider
     */
    public function test_sort_result_direction($direction,$msg)
    {
        $this->search->sortDirection($direction);
        //build search string
        $this->search->select();       
        $this->search->buildQuery();       
        $output=$this->search->get_sql_query_string();   
        $this->assertStringContainsStringIgnoringCase($direction,$output,$msg);

    }

 /**
     * dataprovider for test_sort_result
     */
    public function sortResultProvider()
    {
       return [
           'Ascending'=>['ASC',"Expecting ASC i.e sort in ASCending order"],
           'Descending'=>['DESC',"Expecting DESC i.e sort in DESCending order"]
       ];
    }

    /**
     * @dataProvider filterDataProvider
     */
    public function test_filter_result($filter,$filterValue,$msg)
    {
        $this->search->setSqlfilter($filter,$filterValue);
        //build search string
        $this->search->select();
        $this->search->buildQuery();       
        $output=$this->search->getSqlFilter($filter);
        $this->assertStringContainsStringIgnoringCase($filterValue,$output,$msg);

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

    /**
     * @dataProvider resultLimitProvider
     */
    public function test_set_search_result_limit($limit)
    {
        
        $this->search->setResultLimit($limit);
        //build search string
        $this->search->select();     
        $this->search->buildQuery();
        $output=$this->search->getResultAsArray();
        $this->assertCount($limit,$output,"search result was limited {$limit} results");
    }

    public function resultLimitProvider()
    {
        return [
            'result to 3'=>[3],
            'result to 4'=>[4],
        ];

    }

    public function test_set_search_result_offset()
    {
        $this->search->setOffset(2);
        //build search string
        $this->search->select();       
        $this->search->buildQuery();
        $output=$this->search->get_sql_query_string();
        $this->assertStringContainsStringIgnoringCase('offset',$output,'search result was expected to contain "offset"');
    }

    public function test_build_sql_search_query(){
        //create instance of search
        $search= new search('sem001');
        //select all column
        $search->select();       
        //filter
        $search->setSqlfilter('institution','Obafemi Awolowo University');
        //sort
        $search->sortResultBy('date');
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
         $search->select();
         //build search string
         $search->buildQuery();
         $output=$search->getResultAsArray();
         //assert
         $this->assertIsArray($output,"the search result was expected to be array");
         $this->assertCount($expect,$output,$msg);
    }

    /**
     * dataprovider for test_fetch_search_result_as_array function
     */
    public function searchResultProvider()
    {
        return [
            ['','6','expect to return all the courses in the database as array when there is no search term and filter'],
            ['monday','0','expected no result to be found'],
        ];
    }

    public function test_return_Course_Search_Result_as_json(){
        //select all column
        $this->search->select();
        //build search string
        $this->search->buildQuery();
        $output=$this->search->getResultAsJson();
        $this->assertJson($output,'was expecting search result in array');
    }
    }