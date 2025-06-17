<?php
// At some point ⏱️ would like to add a widget like system - whatever a widget is - so that I can create stat displays on the fly and have them incorporated into this page.

include('templates/header.php');
?>
<div id="last-fed-times">
    <?php

    // Get cat last fed stats 
    $cats = get_cat_last_fed_stats($mysqli);
    // echo (print_r($cats, true));

    // List the Cats with their last fed times and who fed them.

    // Prints table with Columns Cat Name, Fed At time, Time since last feed, Fed By, and Notes



    echo (build_latest_cat_stats_table($cats));


    ?>


</div>
</body>

</html>