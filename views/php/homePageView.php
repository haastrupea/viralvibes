<?php
class homePageView{
    protected $model;
    public function __construct($section)
    {
        $this->model=new homePageModel();
        $this->section=$section;
    }

    public function render()
    {
        //load model
        $officialName=$this->model->getOfficialName();
        $logoImg=$this->model->getLogo();
        $section=$this->section;
        
        //load template
       include_once 'template/homePage.php';

    }
}