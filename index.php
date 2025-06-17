<?php
include('templates/header.php');

// Get cat last fed stats 
$cats = get_cat_last_fed_stats($mysqli);

// List the Cats with their last fed times and who fed them.

// Prints table with Columns Cat Name, Fed At time, Time since last feed, Fed By, and Notes

echo (build_latest_cat_stats_table($cats));





?>
<!-- Add div to house it's been this long since they been fed -->
<div id="time-since-feeding">

</div>
<form action="log_feed.php" method="post">
    <label for='human_name'>Who fed the cat(s)?</label>
    <input type='text' name='human_name' required>
    <?php
    $all_cats = get_all_cats($mysqli);


    echo (display_cat_selectors($all_cats));
    ?>
    <label for="note">Notes</label>
    <textarea name="note"></textarea>

    <button type="submit">Submit Feeding</button>
</form>
<script src="js/script.js?v=1"></script>

</body>

</html>