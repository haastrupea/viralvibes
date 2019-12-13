<?php
namespace Viralvibes;
class Request{
    public function __construct()
    {
        $this->init();
    }

    protected function init()
    {
        foreach ($_SERVER as $key => $value) {
            $this->{$this->camelCase($key)}=$value;
        }
    }
    protected function camelCase($str){
        preg_match("/_./",$str,$result);

        $str=str_ireplace($result,$result,strtolower($str));
        return str_replace('_',"",$str);
    }

    public function getBody()
    {
        if($this->requestMethod==="GET"){
            return;
        }
        
        if($this->requestMethod==="POST"){
            $body=[];
            foreach ($_POST as $key => $value) {
                $body[$key]=$value;
            }
            return $body;
        }
    }
}