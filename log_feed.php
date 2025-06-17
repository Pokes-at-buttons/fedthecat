<?php
// Require database connection
require_once 'db.php';
require_once 'includes/functions.php';
// 1. Sanitize and capture $_POST['name'], $_POST['note'], $_POST['cats']

if (isset($_POST)) {
    $human_name = sanitize_text_inputs($_POST['human_name']);
    $note = sanitize_text_inputs($_POST['note']);
    $cats = $_POST['cats'];
}


// Put relevant $_POST data into array

$feed_data = [];
foreach ($_POST as $key => $entry) {
    $feed_data[$key] = $entry;
}

$_POST = array(); // Done with these now.

// 2. Insert a row into feed_log (name + note) and feed_log_cats (insert feed_log insert ID and cats loop)

$been_fed = log_a_feed_to_db($feed_data, $mysqli);

// IF no error, return to <s>index</s> stats and display something nice.
if ($been_fed == true) {
    header('Location: /stats.php');
} else header('Location: /error.php');
