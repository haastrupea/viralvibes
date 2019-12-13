<?php
class defaultController{
    public function run(String $action='home',Array $param=null)
    {
     if(!method_exists($this,$action)){
         $action='home';
     }

     call_user_func_array([$this,$action],$param);
    }
    
    protected function home($section)
    {
        $view=new homePageView($section);

        $view->render();
    }
}