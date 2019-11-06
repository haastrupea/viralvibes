<?php
namespace peter\paul\adeoti;
// ini_set('allow_url_fopen','1');
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

print_r(\PDO::getAvailableDrivers());

    ?>
</body>
</html>