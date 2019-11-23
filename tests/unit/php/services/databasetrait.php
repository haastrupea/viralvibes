<?php
namespace Viralvibes\Test;

/**
 *
 */
trait databasetrait
{
    static public function createDbTable(){
        $db=self::$dbcon->getConnection();
        $query="CREATE TABLE IF NOT EXISTS `courses` (
            `id` int,
            `institution` varchar(100) NOT NULL,
            `code` varchar(10) NOT NULL,
            `title` varchar(100) NOT NULL,
            `department` varchar(500) NOT NULL,
            `session` varchar(10) DEFAULT NULL,
            `semester` varchar(10) DEFAULT NULL,
            `view_count` int(11) DEFAULT '0',
            `published` TINYINT(1) DEFAULT '1',
            `when_added` TEXT DEFAULT CURRENT_TIMESTAMP,
            `last_update` TEXT DEFAULT CURRENT_TIMESTAMP,
            `description` varchar(255) NOT NULL,
            `type` varchar(50) NOT NULL,
            `unit` int NOT NULL,
            `school_name_is_acronym` TINYINT(1) DEFAULT '0',
            PRIMARY KEY (`id`)
          );";
          $db->exec($query);
    }
    static public function buildDataSet(){
        $db=self::$dbcon->getConnection();
        $query="INSERT INTO `courses` (id,`institution`, `code`, `title`, `department`, `session`, `semester`,`description`, `type`, `unit`, `school_name_is_acronym`) VALUES
        (1,'Obafemi Awolowo University', 'SEM001', 'MAN AND HIS ENVIRONMENT', 'animal science', '2018/2019', '2', 'no description for now', 'special elective', 2, 0),
        (2,'obafemi Awolowo University', 'SEM002', 'man and people', 'Estate mangement', '2018/2019', '1','compostry for all student that wants to graduate', 'restricted elective', 4, 0),
        (3,'obafemi Awolowo University', 'seroo1', 'introduction to English', 'all department', NULL, NULL,'', 'special elective', 0, 0),
        (4,'obafemi Awolowo University', 'SEM004', 'asking question', 'a.b.c.d', '2018/2019', '1', 'wonder but easy to pass', 'restricted elective', 4, 0),
        (5,'obafemi Awolowo University', 'ans301', 'introduction to ruminant', 'animal science, agricultural economics', '2018/2019', '1', 'for all department except fncs', 'core', 3, 0),
        (6,'obafemi Awolowo University', 'ans302', 'introduction to non-ruminant', 'animal science, agricultural economics', '2018/2019', '1', 'for all department except fncs', 'core', 3, 0);";
          $db->exec($query);
    }
}
