<?php
namespace Viralvibes\download\course;
use Viralvibes\database;
use Viralvibes\pagination;
class courseSearch{
    protected $searchTerm;
    protected $dbcon;
    protected $sql_query_arr=[];
    protected $sql_param_arr=[];
    protected $sql_query_string='';
    protected $sql_query_table='courses';

    public function __construct(database $connection){
        $this->dbcon=$connection;
    }

    public function search($search=""){
        if(!empty($search)){
            $this->searchTerm=$search;
        }
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
    
    public function getResult($dataType="array"){

            $this->buildQuery();//build query

        switch (strtolower($dataType)) {
            case 'array':
                return $this->getResultAsArray();
                break;  
            case 'json':
                return $this->getResultAsJson();
                break;
        }
    }

    private function getResultAsArray()
    {
        if(!empty($this->sql_query_string)){
           return $this->dbcon->crudQuery($this->sql_query_string,$this->sql_param_arr);
        }
    }

    private function getResultAsJson()
    {
        $arrayResult=$this->getResultAsArray();
        return json_encode($arrayResult);
    }
    public function selectColumn(array $column=['*'])
    {
        $arr=[];
        if(empty($this->sql_query_arr['select']['columns'])){
            $arr=$column;
        }else{ 
            $arr=array_merge($this->sql_query_arr['select']['columns'],$column);
        }
        sort($arr);
        $this->sql_query_arr['select']['columns']=$arr;
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

    public function resultFilter($filter,$value)
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

    /**Pagination methods */
    public function setPageLimit($limit)
    {
        $this->sql_query_arr['limit']=$limit;
    }
    
    public function getPageLimit()
    {
        return $this->sql_query_arr['limit'];
    }
    public function setPageOffset($offset)
    {
        $this->sql_query_arr['offset']=$offset;
    }
    /**Pagination methods  end*/

    public function joinTable(string $table,string $on,string $type="LEFT"){
        $this->sql_query_arr['join']['type']=$type;
        $this->sql_query_arr['join']['table']=$table;
        $this->sql_query_arr['join']['on']=$on;
    }
    private  function sortByDownload(){
        $this->selectColumn(['SUM(link.dl_count) as download']);
        $this->sql_query_arr['sortby']='download';
        $this->joinTable('dl_Course_link as link','link.course_id','left');
        $this->groupBy('id');
    }
    public function groupBy($col){
        $this->sql_query_arr['groupby']=$col;
    }
    protected function buildQuery(){
        $query='select ';
        //query column
        $col=isset($this->sql_query_arr['select'])?implode(',',$this->sql_query_arr['select']['columns']):"*";
        $query.= $col;
        //query table
        $query.=" from {$this->sql_query_table}";

        //joining table to courses 
        if(!empty($this->sql_query_arr['join'])){
            $join=$this->sql_query_arr['join'];
            $query.=" {$join['type']} join {$join['table']} on id={$join['on']}";            
        }

        //search term control
        $query.=" where (institution like :searchterm or code like :searchterm or title like :searchterm or department like :searchterm)";
        //search term param arr entry
        $this->sql_param_arr[":searchterm"]="%{$this->searchTerm}%";

        //filter query
        if(!empty($this->sql_query_arr['filter'])){
            foreach ($this->sql_query_arr['filter'] as $key => $value) {
                $query.=" and {$key}=:{$key}";
                //parameter array
                $this->sql_param_arr[":{$key}"]=$value;                
            }
        }

        // building "group by" query
        if(isset($this->sql_query_arr['groupby'])){
            $query.=" GROUP BY {$this->sql_query_arr['groupby']}";
        }

        //building sort query
        if(isset($this->sql_query_arr['sortby'])){
                $query.=" order by {$this->sql_query_arr['sortby']}";


            //building sort direction query
            if(isset($this->sql_query_arr['sortDirection'])){
                $query.=" {$this->sql_query_arr['sortDirection']}";
            }
        }

        
        //building limit query
        if(isset($this->sql_query_arr['limit'])){
            $query.=" LIMIT :limit";
            $this->sql_param_arr[":limit"]=$this->sql_query_arr['limit'];
        }

        //building offset query
        if(isset($this->sql_query_arr['offset'])){
            $query.=" OFFSET :offset";
            $this->sql_param_arr[":offset"]=$this->sql_query_arr['offset'];
        }

        //saving the completed query
        $this->sql_query_string=$query;
    }
}