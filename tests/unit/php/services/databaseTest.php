<?php
namespace Viralvibes\Test;
require dirname(__FILE__,5).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
use PHPUnit\Framework\TestCase;
use Viralvibes\database;

class databaseTest extends TestCase{
    static protected $dbcon;
    static public function setUpBeforeClass(): void
    {
        self::$dbcon=database::getInstance('sqlite',':memory:');
    }

}