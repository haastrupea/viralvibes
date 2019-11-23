<?php
namespace Viralvibes\Test;

/**
 *
 */
trait databasetrait
{
    static public function createCourseTable(){
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
    
    static public function createCourseLinkTable(){
        $db=self::$dbcon->getConnection();
        $query="CREATE TABLE `dl_Course_link` (
            `dl_id` int,
            `dl_link` varchar(255) NOT NULL UNIQUE,
            `course_id` int NOT NULL,
            `external_link` TINYINT(1) NOT NULL DEFAULT '1',
            `dl_count` int DEFAULT '0',
            PRIMARY KEY (`dl_id`),
            FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE
          );";
          $db->exec($query);
    } 
    
    static public function createCourseUpdateRequestTable(){
        $db=self::$dbcon->getConnection();
        $query="CREATE TABLE `update_request` (
            `req_id` int,
            `course_id` int NOT NULL,
            `user_id` int NOT NULL,
            `date_requested` text NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `resolved` tinyint(1) NOT NULL DEFAULT '0',
            `date_resolved` text NULL DEFAULT NULL,
            `resolved_by` int DEFAULT NULL,
            `reason_for_req` varchar(255) NOT NULL,
            PRIMARY KEY (`req_id`)
            );";
          $db->exec($query);
    }

    static public function buildCourseDataSet(){
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
    
    static public function buildCourseLinkDataSet(){
        $db=self::$dbcon->getConnection();
        $query="INSERT INTO `dl_Course_link` (`dl_id`, `dl_link`, `course_id`, `external_link`, `dl_count`) VALUES 
        (1, 'https://linkt.to/download/a/file.ext', 1, 1, 5),
        (2, 'http://dowmloadmaterials.com/dl.php?dl=123', 2, 1, 0),
        (3, 'http://dowmloadmaterials.com/dl.php?dl=125', 2, 1, 0),
        (4, 'http://dowmloadmaterials.com/dl.php?dl=127', 2, 1, 0),
        (5, 'http://dowmloadmaterials.com/dl.php?dl=12', 1, 1, 4),
        (6, 'http://dowmloadmaterials.com/dl.php?dl=120', 5, 1, 0),
        (7, 'upload/coursematerials/sem001_2019session.pdf', 5, 0, 0),
        (8, 'upload/coursematerials/sem004_2019secondsemeter.pdf', 1, 0, 5);";
          $db->exec($query);
    } 
    
    static public function buildCourseUpdateRequestDataSet(){
        $db=self::$dbcon->getConnection();
        $query="INSERT INTO `update_request` (`course_id`, `user_id`, `date_requested`, `resolved`, `date_resolved`, `resolved_by`, `reason_for_req`) VALUES ('1', '1', CURRENT_TIMESTAMP, '0', NULL, NULL, 'it was released over 2 years ago');";

          $db->exec($query);
    }
}
