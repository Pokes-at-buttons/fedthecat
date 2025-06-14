<?php
// Include the database connection file
require_once 'db.php';
require_once 'includes/functions.php';
$cat_list = get_all_cats($mysqli);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php
    include('templates/navigation.php');
    ?>