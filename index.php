<?php
include('templates/header.php');
?>
<form action="log_feed.php" method="post">
    <label for='human_name'>Who fed the cat(s)?</label>
    <input type='text' name='human_name' required>
    <?php
    $all_cats = get_all_cats($mysqli);
    error_log("Index all cats: " . print_r($all_cats, true));

    echo (display_cat_selectors($all_cats));
    ?>
    <label for="note">Notes</label><br>
    <textarea name="note"></textarea><br>

    <button type="submit">Submit Feeding</button>
</form>

</body>

</html>