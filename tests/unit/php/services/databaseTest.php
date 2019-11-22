<?php
namespace Viralvibes\Test;
require dirname(__FILE__,5).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
require dirname(__FILE__,5).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'services'.DIRECTORY_SEPARATOR.'database.php';

use PHPUnit\Framework\TestCase;
use Viralvibes\database;

class databaseTest extends TestCase{
    static protected $dbcon;
    static public function setUpBeforeClass(): void
    {
        self::$dbcon=database::getInstance('sqlite',':memory:');
    }

    public function test_database_connect_successfully()
    {
        $out=self::$dbcon->getConnection();

        $this->assertInstanceOf(\PDO::class,$out,"failed to connnect to database");
    }

}