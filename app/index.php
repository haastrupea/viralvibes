<?php
include_once 'controller/php/controllersLoader.php';
include_once 'models/php/modelsLoader.php';


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

$app->get('/download[/{action}][/{cat}][/{q}]',function($args){

    // $action=$args['action'];
    // $cat=$args['cat'];
    // $query=$args['q'];

    // echo "Action=".$action;
    // echo "<br>";
    // echo "Category=".$cat;
    // echo "<br>";
    // echo "Search query=".$query;
});
$app->run();