<?php
class downloadController extends defaultController{
    public function __construct()
    {
        $this->model=new download();
    }
    public function search($param)
    {
        //load model
        $model=$this->getModel();
        include_once 'views/';
    }
}