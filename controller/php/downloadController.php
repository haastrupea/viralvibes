<?php
class downloadController extends defaultController{
    function home($args)
    {
        //get model
        $model=new downloadHomeModel();
        //load download index page view
        $view=new downloadHomeView;

        $view->render($model);
    }

    function search($cat,$query){
        $model=new downloadSearchModel($cat,$query);

        $view=new downloadSearchView;

        $view->render($model);
    }
}