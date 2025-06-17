<?php
// Safety First

/**
 * Trim, strip slashes, and convert special characters to HTML Entities
 * @param string $data 
 * @return string 
 */
function sanitize_text_inputs(string $data): string {
    // Trim whitespace from the beginning and end
    $data = trim($data);
    // Remove backslashes
    $data = stripslashes($data);
    // Convert special characters to HTML entities
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}
/**
 * 
 * @param mixed $test 
 * @return bool 
 */
function check_for_number($test) {
    if (is_numeric($test)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Returns array of cats
 * @param object $mysqli 
 * @return array 
 */
function get_all_cats(object $mysqli): array {

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
function log_a_feed_to_db(array $feed_data, object $mysqli) {
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

/**
 * Displays the checkboxes for each cat in array
 * @param array $cats_arr 
 * @return string 
 */
function display_cat_selectors(array $cats_arr): string {
    $html = '';


    $html .= "<label for='cats[]'>Which cat(s)?</label>";
    foreach ($cats_arr as $cat) {

        $html .= "$cat[name]<input type='checkbox' name='cats[]' value='$cat[id]'>";
    }

    return $html;
}

/**
 * Builds table containing latest stats for each cat
 * @param array $cats_array 
 * @return string 
 */
function build_latest_cat_stats_table(array $cats_array): string {

    $html_latest_cat_stats = '';

    // Remove unneccesary keys - could probably just not select these in database but I'm not fussing about data overheads right now, and seems more logical to fetch once and then shape as needed, given small db tables.
    $ready_cats_array = remove_by_keys($cats_array, array('cat_id', 'feed_log_id'));

    // Get the keys to make headers
    $cat_stats_headers = get_keys_for_headers($ready_cats_array);


    // Begin Table
    $html_latest_cat_stats .= '<table id="latest-cat-stats"><tr>';

    foreach ($cat_stats_headers as $header) {

        $html_latest_cat_stats .= "<th>$header</th>";
    }
    $html_latest_cat_stats .= "</tr>";


    foreach ($ready_cats_array as $cats) {
        $html_latest_cat_stats .= "<tr>";

        foreach ($cats as $key => $cat_entry) {




            if ($key == 'fed_at') {

                $cat_entry =  date_and_time_format($cats['fed_at']);
                // echo ($cat_entry);
            }
            // Build table headers

            $html_latest_cat_stats .= "<td>$cat_entry</td>";
        }
        $html_latest_cat_stats .= "</tr>";
    }


    $html_latest_cat_stats .= "</table>";



    return $html_latest_cat_stats;
}

// 
/**
 * Unsets inner array entries based on the inputed keys.

 * @param array $the_cats_array 
 * @param array $unset_these_keys 
 * @return array 
 */
function remove_by_keys(array $the_cats_array, array $unset_these_keys): array {


    foreach ($the_cats_array as $id => $cat) {
        foreach ($unset_these_keys as $key) {
            unset($cat[$key]);
        }
        $the_cats_array[$id] = $cat;
    }

    // Now $the_cats_array has those keys removed from each sub-array
    return $the_cats_array;
}


/**
 * Creates an array of headers for the table by using the Keys of the cat array
 * @param array $the_cats_array 
 * @return array 
 */
function get_keys_for_headers(array $the_cats_array): array {
    // Get the first array, assuming uniform structure
    $first_cat = reset($the_cats_array);
    // Create an array of keys
    $header_keys = array_keys($first_cat);

    // Replace underscores with spaces and capitalize each word
    $header_keys = array_map(function ($key) {
        return ucwords(str_replace('_', ' ', $key));
    }, $header_keys);



    return $header_keys;
}


// 
/**
 * Get the cats and last fed from tables cats, feed_log, and feed_log_cats
 * @param object $mysqli The mysql object
 * @return array Array of cats and their feeding stats
 */

function get_cat_last_fed_stats(object $mysqli) {
    $query = "SELECT
    c.id AS cat_id,
    c.name AS cat_name,
    f.id AS feed_log_id,
    f.human_name AS human_name,
    f.note,
    f.fed_at
FROM cats c
JOIN feed_log_cats flc ON c.id = flc.cat_id
JOIN feed_log f ON flc.feed_log_id = f.id
JOIN (
    SELECT
        flc.cat_id,
        MAX(f.fed_at) AS last_fed_at
    FROM feed_log_cats flc
    JOIN feed_log f ON flc.feed_log_id = f.id
    GROUP BY flc.cat_id
) latest ON latest.cat_id = c.id AND latest.last_fed_at = f.fed_at
ORDER BY c.id
";

    $result = $mysqli->query($query);
    // error_log(print_r($result, true));
    if (!$result) {
        die("Query failed: " . $mysqli->error);
    }

    $cats = [];

    while ($row = $result->fetch_assoc()) { // fetch_assoc will fetch as an associative array
        $cats[] = $row;
    }
    return $cats;
}

function date_and_time_format(string $date_time) {

    $date_formatted =  date('d M Y, H:i', strtotime($date_time));
    return $date_formatted;
}
