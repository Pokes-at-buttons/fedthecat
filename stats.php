<?php
// At some point ⏱️ would like to add a widget like system - whatever a widget is - so that I can create stat displays on the fly and have them incorporated into this page.

include('templates/header.php');
?>
<div id="last-fed-times">
    <?php

    // List the Cats with their last fed times and who fed them.

    // Get the cats and last fed from tables cats, feed_log, and feed_log_cats

    // Will move this to functions when built

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
    error_log(print_r($result, true));
    if (!$result) {
        die("Query failed: " . $mysqli->error);
    }

    $cats = [];

    while ($row = $result->fetch_assoc()) { // fetch_assoc will fetch as an associative array
        $cats[] = $row;
    }

    // Table with Columns Cat Name, Fed At time, Time since last feed, Fed By, Notes



    echo (build_latest_cat_stats_table($cats));


    ?>


</div>
</body>

</html>