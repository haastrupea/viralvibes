<?php
namespace Viralvibes;

class pagination{
    protected $search;
    protected $perPage;
    protected $totalResult;
    protected $totalPages;
    public function __construct($search,$itemsPerPage)
    {
        $this->perPage=$itemsPerPage;
        $this->totalResult=count($search->getResult());//get result without argument default to array
        $this->totalPages=(int) floor($this->totalResult/$this->perPage);
        $this->search=$search;
        $this->search->setPageLimit($this->perPage);
        
    }
    
    public function getTotalResult(){
        return $this->totalResult;
    }
    public function getTotalPages()
    {
        return $this->totalPages;
    }
    public function gotoPage($page){
        if($page>0 and $page<=$this->totalPages){
            $offset=(int)($this->perPage*($page-1));
            $this->search->setPageOffset($offset);
        }
    }

}