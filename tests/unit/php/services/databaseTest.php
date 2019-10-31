<?php
namespace Viralvibes\Test;
require dirname(__FILE__,5).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
require dirname(__FILE__,5).DIRECTORY_SEPARATOR. 'models'. DIRECTORY_SEPARATOR. 'php'.DIRECTORY_SEPARATOR. 'services'.DIRECTORY_SEPARATOR.'database.php';

use PHPUnit\Framework\TestCase;
use Viralvibes\database;

class dataBaseTest extends TestCase{
    protected $db;
    protected function setUp():void{
        $this->db=new database();
    }
    
    protected function tearDown():void{
        unset($this->db);
    }
    public function test_db_connection_PDOException(){
        $this->expectException('PDOException');
        $output=$this->db->connect(null,null,null);
    }

    public function test_db_connection_success(){
        $dsn='mysql:host=127.0.0.1;dbname=viralvibes;charset=utf8';
        $psw="Undercover";
        $usr="root";
        $output=$this->db->connect($dsn,$usr,$psw);
        $this->assertTrue($output);
    }
}