<?php
require_once '../db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

//$cats = get_all_cats($mysqli);
$cat_stats = get_cat_last_fed_stats($mysqli);
echo json_encode($cat_stats);
