<?php
// include_once 'controller/php/controllersLoader.php';
// include_once 'models/php/modelsLoader.php';


/**
 * Router link structure=/page/action/param
 * page could be download, housing/news/default homepage
 * action could be search/course/music/video/rent/ etc
 * param could be the value that needed to perform an action
 */

// switch ($page) {
//     case 'download':
//         $controller=new downloadController();//download home page
//         break;
//     case 'housing':
//         $controller=new housingController();//housing home page
//         break;
//     case 'news':
//         $controller=new newsController();//news home page
//         break;
//     default:
//         $controller=new defaultController();//site home page
//         break;
// }

// $controller->run($action,$param);

$app=new router;

$app->get('/[home][/{section}]',function($args){

    $section[0]=isset($args['section'])?$args['section']:"";


    $controller=new defaultController();

    $controller->run('home',$section);

});//home page

//services index start
$app->get('/download[/home]',function(){
    echo "welcome to download home page <br>";
    echo "via get";
});//download index page

$app->get('/housing[/home]',function(){
    echo "welcome to housing home page <br>";
    echo "via get";
});//housing index page

$app->get('/news[/home]',function(){
    echo "welcome to news home page <br>";
    echo "via get";
});//news index page

//services index end


$app->get('/download/search[/{cat}]/{q}',function($args){

    $cat=isset($args['cat'])?$args['cat']:"";
    $query=isset($args['q'])?$args['q']:"";

    echo "Action=Search";
    echo "<br>";
    echo "Category=".$cat;
    echo "<br>";
    echo "Search query=".$query;
});//download with action

$app->run();