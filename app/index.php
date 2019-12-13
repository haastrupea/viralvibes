<?php
// include_once 'controller/php/controllersLoader.php';
// include_once 'models/php/modelsLoader.php';

use Viralvibes\Request;

require 'vendor/autoload.php';

include_once 'router/router.php';


$app=new router;

$app->get('/[home][/{section}]',function(Request $request,Array $args){

    $section[0]=isset($args['section'])?$args['section']:"";

    $c=new defaultController();

    $c->run('home',$section);

});//home page


//services index start
$app->get('/download[/home]',function(){

    $c=new downloadController();

    $c->run('home',[null]);
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


$app->get('/download/search[/{cat}][/{q}]',function(Request $request,Array $args){

    $cat=isset($args['cat'])?$args['cat']:"";
    $query=isset($args['q'])?$args['q']:"";
    $c=new downloadController;

    $c->run('search',[$cat,$query]);

});//download with action

$app->run();