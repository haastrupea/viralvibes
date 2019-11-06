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
    protected $searchResult;

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
            $this->searchResult=$this->dbcon->selectData($this->sql_query_string,$this->sql_param_arr);
            return $this->searchResult;
        }
    }
    public function getResultAsJson()
    {
        $arrayResult=$this->getResultAsArray();
        return json_encode($arrayResult);
    }
    public function select($column)
    {
        if(is_string($column)){
            $this->sql_query_arr['select']="select {$column} from courses";
        }
        if(is_array($column)){
            $column=implode(',',$column);
            $this->sql_query_arr['select']="select {$column} from courses";
        }
        return $this->sql_query_arr['select'];
    }
    public function sortResultBy($column)
    {
        $this->sql_query_arr['sortby']=$column;
        return $this->sql_query_arr['sortby'];
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
        return $this->sql_query_arr['sortDirection'];
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
        return $this->sql_query_arr['filter'];
    }
    public function setResultLimit($limit)
    {
        $this->sql_query_arr['limit']=$limit;

        return $this->sql_query_arr['limit'];
    }
    public function setOffset($offset)
    {
        $this->sql_query_arr['offset']=$offset;

        return $this->sql_query_arr['offset'];
    }

    public function buildQuery(){
        $query='';
        $query.=$this->sql_query_arr['select'];
        $query.=" where (institution like :searchterm or course_code like :searchterm or course_title like :searchterm or department like :searchterm)";
        //search term param arr entry
        $this->sql_param_arr[":searchterm"]="%{$this->searchTerm}%";

        if(!empty($this->sql_query_arr['filter'])){
            foreach ($this->sql_query_arr['filter'] as $key => $value) {
                $query.=" and {$key}=:{$key}";
                //parameter array
                $this->sql_param_arr[":{$key}"]=$value;                
            }
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