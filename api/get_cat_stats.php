<?php
require_once '../db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json'); // Set the content type to JSON so the client knows what to expect.

//$cats = get_all_cats($mysqli);
$cat_stats = get_cat_last_fed_stats($mysqli);
echo json_encode($cat_stats);
