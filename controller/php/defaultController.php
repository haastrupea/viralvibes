<?php
class defaultController implements controller{
    public function run(String $action='home',Array $param)
    {
     if(!method_exists($this,$action)){
         $action='home';
     }

        call_user_func_array([$this,$action],$param);
    }
    
    function home($section)
    {
        //load model if any
        $model=new homePageModel($section);

        $view=new homePageView;

        //pass model into it if any
        $view->render($model);
    }
}