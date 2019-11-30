<?php
$action=isset($_GET['a']) ? $_GET['a']: '';//action could be search/rent/read/share/report/request/course/music/video/
$page=isset($_GET['p']) ? $_GET['p']: 'home';
$param=$_GET['value'];//will be the value on which the action needs to be performed on

/**
 * Router link structure=/page/action/param
 * page could be download, housing/news/default homepage
 * action could be search/course/music/video/rent/ etc
 * param could be the value that needed to perform an action
 */

switch ($page) {
    case 'download':
        $controller=new downloadController();//download home page
        break;
    case 'housing':
        $controller=new housingController();//housing home page
        break;
    case 'news':
        $controller=new newsController();//news home page
        break;
    default:
        $controller=new defaultController();//site home page
        break;
}

$controller->run($action,$param);