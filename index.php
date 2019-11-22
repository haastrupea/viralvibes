<?php
require 'models'. DIRECTORY_SEPARATOR. 'php'.DIRECTORY_SEPARATOR. 'services'.DIRECTORY_SEPARATOR.'database.php';

use Viralvibes\database;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <?php
// $file = 'http://localhost:3000/phpunit.xml';
// // $file = 'monkey.gif';


//     header('Content-Description: File Transfer');
//     header('Content-Type: application/octet-stream');
//     header('Content-Disposition: attachment; filename="'.'phpunit.xml'.'"');
//     header('Expires: 0');
//     header('Cache-Control: must-revalidate');
//     header('Pragma: public');
//     // // header('Content-Length: ' . filesize($file));
//     // readfile($file);
//     exit;

// print_r(\PDO::getAvailableDrivers());
// echo password_hash('Undercover',PASSWORD_DEFAULT);
// $me=[];
// $arr=$me["word"]['life'];
// print_r(empty($arr));

$db=database::getInstance('sqlite',':memory:');
// $db=$this->_dbcon;
$query="CREATE TABLE `courses` (
    `course_id` int NOT NULL,
    `institution` varchar(100) NOT NULL,
    `course_code` varchar(10) NOT NULL,
    `course_title` varchar(100) NOT NULL,
    `department` varchar(500) NOT NULL,
    `session` varchar(10) DEFAULT NULL,
    `semester` varchar(10) DEFAULT NULL,
    `view_count` int(11) DEFAULT '0',
    `published` TINYINT(1) NOT NULL DEFAULT '1',
    `when_added` TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_update` TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `description` varchar(255) NOT NULL,
    `course_type` varchar(50) NOT NULL,
    `course_unit` int NOT NULL,
    `name_is_acronym` TINYINT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`course_id`)
  );";

$query2="INSERT INTO `courses` (`course_id`, `institution`, `course_code`, `course_title`, `department`, `session`, `semester`, `view_count`, `published`, `when_added`, `last_update`, `description`, `course_type`, `course_unit`, `name_is_acronym`) VALUES
        (1, 'Obafemi Awolowo University', 'SEM001', 'MAN AND HIS ENVIRONMENT', 'animal science', '2018/2019', '2', 0, 1, '2019-11-01 10:47:17', '2019-11-01 10:47:17', 'no description for now', 'special elective', 2, 0),
        (2, 'obafemi Awolowo University', 'SEM002', 'man and people', 'Estate mangement', '2018/2019', '1', 0, 1, '2019-11-06 00:23:16', '2019-11-06 00:23:16', 'compostry for all student that wants to graduate', 'restricted elective', 4, 0),
        (3, 'obafemi Awolowo University', 'seroo1', 'introduction to English', 'all department', NULL, NULL, 0, 1, '2019-11-06 00:23:16', '2019-11-06 00:23:16', '', 'special elective', 0, 0),
        (4, 'obafemi Awolowo University', 'SEM004', 'asking question', 'a.b.c.d', '2018/2019', '1', 0, 1, '2019-11-06 00:28:41', '2019-11-06 00:28:41', 'wonder but easy to pass', 'restricted elective', 4, 0),
        (5, 'obafemi Awolowo University', 'ans301', 'introduction to ruminant', 'animal science, agricultural economics', '2018/2019', '1', 0, 1, '2019-11-06 00:28:41', '2019-11-06 00:28:41', 'for all department except fncs', 'core', 3, 0),
        (6, 'obafemi Awolowo University', 'ans302', 'introduction to non-ruminant', 'animal science, agricultural economics', '2018/2019', '1', 0, 1, '2019-11-06 00:28:41', '2019-11-06 00:28:41', 'for all department except fncs', 'core', 3, 0),
        (7, 'obafemi Awolowo University', 'ans304', 'introduction to non-ruminants', 'animal science, agricultural economics', '2018/2019', '1', 0, 1, '2019-11-06 00:28:41', '2019-11-06 00:28:41', 'for all department except fncs', 'core', 3, 0);";


  var_dump($db->queryDb($query));
  echo "<pre>";
  var_dump($db->queryDb($query2));
  echo "</pre>";
  echo "<br>";
  echo "<pre>";
  $q="select * from courses";
  var_dump($db->queryDb($q));
  echo "</pre>";

    ?>
</body>
</html>