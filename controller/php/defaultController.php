<?php
class defaultController{
    protected $model=null;
    public function __construct()
    {
        $this->model=new homePage();
    }

    public function run($action='home',$param=null)
    {
        if (!method_exists($this,$action)) {
            $action= 'home';
        }

        return $this->$action($param);
    }
    protected function getModel(){
        return $this->model;
    }

    public function home()
    {
        $model=$this->getModel();
        include_once 'views/homePage.php';
    }

}