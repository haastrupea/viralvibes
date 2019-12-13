<?php
class downloadHomeView implements views{
  
    function render($model)
    {
        //unpack model here
        
        //load download index template
        include_once 'template/download/index.php';
    }
}