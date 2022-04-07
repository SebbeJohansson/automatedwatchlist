<?php

    /* Cron job */

    require_once "../classes/trakt.php";
    require_once "../classes/db.php";

    $trakt = Trakt::getInstance();
    $db = Database::getInstance();

    var_dump("thsiu is the cronjob");

    $movies = $trakt->getWatched();
	var_dump($movies);
    $ratedMovies = $trakt->getRated();
    $ratings = [];
    foreach($ratedMovies as $ratedMovie){
        $ratings[$ratedMovie['movie']['ids']['slug']] = $ratedMovie['rating'];
    }
	

    foreach($movies as $movie){

        $sql = "
            INSERT INTO movies (trakt_id, imdb_id, movie_title, movie_year, movie_slug, trakt_rating)
            VALUES (:trakt_id, :imdb_id, :movie_title, :movie_year, :movie_slug, :trakt_rating)
            ON DUPLICATE KEY UPDATE trakt_rating=VALUES(trakt_rating), movie_title = VALUES(movie_title), movie_year = VALUES(movie_year), movie_slug = VALUES(movie_slug)
        ";
        $values = [
            'trakt_id' => $movie['movie']['ids']['trakt'],
            'imdb_id' => $movie['movie']['ids']['imdb'],
            'movie_title' => $movie['movie']['title'],
            'movie_year' => $movie['movie']['year'],
            'movie_slug' => $movie['movie']['ids']['slug'],
            'trakt_rating' => isset($ratings[$movie['movie']['ids']['slug']]) ? $ratings[$movie['movie']['ids']['slug']] : NULL
        ];
        $db->advancedPushQuery($sql, $values);

        var_dump($db->dbErrors);

        var_dump($movie);
    }


