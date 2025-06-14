<?php

function sanitize_text_inputs($data) {
    // Trim whitespace from the beginning and end
    $data = trim($data);
    // Remove backslashes
    $data = stripslashes($data);
    // Convert special characters to HTML entities
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function check_for_number($test) {
    if (is_numeric($test)) {
        return true;
    } else {
        return false;
    }
}

function get_all_cats($mysqli) {

    // search db for cat details
    $query = "SELECT * FROM cats";
    $result = $mysqli->query($query);
    if (!$result) {
        die("Query failed: " . $mysqli->error);
    }


    // return array of cats 
    $cats = [];
    while ($row = $result->fetch_assoc()) { // fetch_assoc will fetch as an associative array
        $cats[] = $row;
    }
    return $cats;
}

// Log feed data to database
function log_a_feed_to_db(array $feed_data, $mysqli) {
    if (!is_array($feed_data)) {
        return false; // Error, this needs an array.
    }

    // First get the details
    $human_name = $mysqli->real_escape_string($feed_data['human_name']);

    $note = $mysqli->real_escape_string($feed_data['note']);

    $cats = $feed_data['cats'];

    // Make cats safe and definitely an int
    $cats = array_map('intval', $cats);



    $query = "INSERT INTO feed_log (human_name, note) VALUES ('$human_name', '$note')";
    if (!$mysqli->query($query)) {
        error_log("Insert failed: " . $mysqli->error);
        return false;
    }
    // Get the last inserted ID so that I can use it as foreign key in the feed_log_cats table.
    $feed_log_id = $mysqli->insert_id;

    // For each cat ID in $_POST['cats']
    //    → INSERT INTO feed_log_cats (feed_log_id, cat_id)
    foreach ($cats as $cat_id) {
        $link_query = "INSERT INTO feed_log_cats (feed_log_id, cat_id) VALUES ($feed_log_id, $cat_id)";
        if (!$mysqli->query($link_query)) {
            error_log("Link insert failed: " . $mysqli->error);
            // Handle rollback or error here - Not tonight, Josephine. 
        }
    }

    return $feed_log_id;
}
// Get Humans from Database

function display_cat_selectors(array $cats_arr) {
    $html = '';


    $html .= "<label for='cats[]'>Which cat(s)?</label><br>";
    foreach ($cats_arr as $cat) {

        $html .= "$cat[name]<br><input type='checkbox' name='cats[]' value='$cat[id]'>";
    }

    return $html;
}
