<?php
    require_once "../classes/db.php";

    $db = Database::getInstance();

    echo "This is the loadarings file";

    $file = fopen("../backupthelist.csv", "r");
            
    while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
        if($column[0] != "Title" && $column[8] != ""){
            var_dump($column);
            $sql = "
                UPDATE movies SET enjoyment_rating = :enjoyment, cinematography_rating = :cinematography, music_rating = :music, sound_mix_rating = :sound, story_rating = :story, cast_rating = :cast
                WHERE imdb_id = '".$column[2]."'
            ";
            $values = [
                'enjoyment' => $column[4],
                'cinematography' => $column[5],
                'music' => $column[6],
                'sound' => $column[7],
                'story' => $column[8],
                'cast' => $column[9],
            ];
            var_dump($values);
            var_dump($sql);
            $db->advancedPushQuery($sql, $values);
            var_dump($db->dbErrors);
            
        }
    }
    ?>

