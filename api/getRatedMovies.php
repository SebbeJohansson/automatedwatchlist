<?php

    require_once "../classes/db.php";

    $db = Database::getInstance();

    
    header('Content-Type: application/json');

    echo json_encode($db->advancedReturnQuery("SELECT * FROM movies WHERE enabled = 1 ORDER BY created_date, movie_slug"));

    exit();