<?php

define('DB_DATABASE', 'wada_demo');
define('DB_USERNAME', 'ishi');
define('DB_PASSWORD', 'vj2sw3v1');
define('PDO_DSN', 'mysql:dbhost=localhost;dbname='. DB_DATABASE);

function filter($s) {
    return $s != "";
}

function get_latest_shipdata() {
    $url = "https://raw.githubusercontent.com/b4b4r07/gr-autopilot/master/data/extract/fukushima/2015/08/03/fukushima_120001.txt";
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $html =  curl_exec($ch);

    $array = split("\n", $html);
    $array = array_filter($array, "filter");

    curl_close($ch);

    return $array[count($array)-1];
}

function judge_ma() {
    return false;
}

function judge_ap() {
    return false;
}

function insert_db() {
    try {
        // connect
        $db = new PDO(PDO_DSN, DB_USERNAME, DB_PASSWORD);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // select
        foreach ($db->query('select max(speed) from gps') as $row) {
            var_dump($row[0]);
        }
        #$ret = $db->query($sql);

        // disconnect
        $db->null;

    } catch (PDOException $e) {
        echo $e->getMessage();
        exit;
    }
}

insert_db();
echo "abc";
