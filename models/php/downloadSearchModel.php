<?php
class downloadSearchModel{
    private $category;
    private $query;
    public function __construct($cat,$query)
    {
      $this->category=$cat;
      $this->query=$query;
    }
}