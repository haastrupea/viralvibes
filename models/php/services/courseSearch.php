<?php
namespace Viralvibes\download\course;
require dirname(__FILE__).DIRECTORY_SEPARATOR.'database.php';

use Viralvibes\database;
class search{
    protected $searchTerm;
    protected $dbcon;
    protected $sql_query_arr=[];
    protected $sql_param_arr=[];
    protected $sql_query_string='';
    protected $sql_query_table='courses';

    public function __construct(String $search=''){
        if(!empty($search)){
            $this->searchTerm=$search;
        }
    }

    public function setDbconnection(database $connection){
        $this->dbcon=$connection;
    }

    public function getSearchTerm(){
        if(!empty($this->searchTerm)){
            return $this->searchTerm;
        }
        return false;
    }
    public function sql_query_array()
    {
        return $this->sql_query_arr;
    }
    public function get_sql_query_string()
    {
        return $this->sql_query_string;
    }
    public function get_sql_query_param_array()
    {
        return $this->sql_param_arr;
    }
    public function getResultAsArray()
    {
        if(!empty($this->sql_query_string)){
           return $this->dbcon->queryDb($this->sql_query_string,$this->sql_param_arr);
        }
    }
    public function getResultAsJson()
    {
        $arrayResult=$this->getResultAsArray();
        return json_encode($arrayResult);
    }
    public function select(array $column=['*'])
    {
        $column=implode(',',$column);
        if(empty($this->sql_query_arr['select']['columns'])){
            $this->sql_query_arr['select']['columns']=$column;
        }else{  
            $this->sql_query_arr['select']['columns'].=$column;
        }
        
    }
    public function sortResultBy($orderBy)
    {
        switch (strtolower($orderBy)) {
            case 'date':
                $this->sql_query_arr['sortby']='when_added';
                break;
            case 'views':
                $this->sql_query_arr['sortby']='view_count';
                break;
            case 'download':
                $this->sortByDownload();
                break;
        }
    }
    public function sortDirection($direction)
    {
            switch (strtoupper($direction)) {
                case 'ASC':
                $this->sql_query_arr['sortDirection']="ASC";
                  break;
                case 'DESC':
                $this->sql_query_arr['sortDirection']="DESC";
                  break;
              }
    }

    public function setSqlfilter($filter,$value)
    {
        switch ($filter) {
            case 'session':
                $this->sql_query_arr['filter']['session']=$value;
                break;
            case 'semester':
                $this->sql_query_arr['filter']['semester']=$value;
                break;
            case 'institution':
                $this->sql_query_arr['filter']['institution']=$value;
                break;
        }

    }
    public function getSqlFilter($key=null){
        if(!is_null($key)){
            return $this->sql_query_arr['filter'][$key];
        }
    }
    public function setResultLimit($limit)
    {
        $this->sql_query_arr['limit']=$limit;
    }
    public function setOffset($offset)
    {
        $this->sql_query_arr['offset']=$offset;
    }

    public function joinTable(string $table,string $on,string $type="LEFT"){
        $this->sql_query_arr['join']['type']=$type;
        $this->sql_query_arr['join']['table']=$table;
        $this->sql_query_arr['join']['on']=$on;
    }
    private  function sortByDownload(){
        $this->select(['SUM(link.dl_count) as download']);
        $this->sql_query_arr['sortby']='download';
        $this->joinTable('dl_Course_link as link','link.course_id','left');
        $this->groupBy('id');
    }
    public function groupBy($col){
        $this->sql_query_arr['groupby']=$col;
    }
    public function buildQuery(){
        $query='select ';
        $query.=$this->sql_query_arr['select']['columns'];
        //query table
        $query.=" from {$this->sql_query_table}";
        //joining table to courses 
        if(!empty($this->sql_query_arr['join'])){
            $join=$this->sql_query_arr['join'];
            $query.=" {$join['type']} join {$join['table']} on {$join['on']}";            
        }

        $query.=" where (institution like :searchterm or course_code like :searchterm or course_title like :searchterm or department like :searchterm)";
        //search term param arr entry
        $this->sql_param_arr[":searchterm"]="%{$this->searchTerm}%";

        //filter
        if(!empty($this->sql_query_arr['filter'])){
            foreach ($this->sql_query_arr['filter'] as $key => $value) {
                $query.=" and {$key}=:{$key}";
                //parameter array
                $this->sql_param_arr[":{$key}"]=$value;                
            }
        }

        //group result with course id
        if(isset($this->sql_query_arr['groupby'])){
            $query.=" GROUP BY {$this->sql_query_arr['groupby']}";
        }

        if(isset($this->sql_query_arr['sortby'])){
                $query.=' order by :sortby';
                $this->sql_param_arr[":sortby"]=$this->sql_query_arr['sortby'];
        }

        if(isset($this->sql_query_arr['sortDirection'])){
            $query.=" {$this->sql_query_arr['sortDirection']}";
        }
        if(isset($this->sql_query_arr['limit'])){
            $query.=" LIMIT :limit";
            $this->sql_param_arr[":limit"]=$this->sql_query_arr['limit'];
        }
        if(isset($this->sql_query_arr['offset'])){
            $query.=" OFFSET :offset";
            $this->sql_param_arr[":offset"]=$this->sql_query_arr['offset'];
        }
        $this->sql_query_string=$query;
    }
}