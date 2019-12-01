<?php
$officialName=$model->officialName();
$logoImg=$model->logoImg();

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="assets/imgs/logo.svg" type="image/svg">
    <title><?php echo $officialName ?> Home page</title>
</head>
<body>
    
</body>
</html>
<h1><?php echo $officialName;?></h1>
<img src="<?php echo $logoImg ?>" alt="<?php echo $officialName?> logo">


