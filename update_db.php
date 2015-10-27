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

function insert_db() {
    try {
        // connect
        $db = new PDO(PDO_DSN, DB_USERNAME, DB_PASSWORD);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // insert
        $stmt = $db->prepare("insert into gps (head1, head2, id, utctime, accuracy, latitude, lat_dir, longitude, lon_dir, speed, compass, utcdate, checksum) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $array = split(",|\*", get_latest_shipdata());
        $array[3]  = preg_replace('/(\d{2})(\d{2})(\d{2})/', '$1:$2:$3',   $array[3]);
        $array[11] = preg_replace('/(\d{2})(\d{2})(\d{2})/', '20$3-$2-$1', $array[11]);

        // select
        $stmt = $db->query('select * from gps order by line desc limit 1');
        if (!$stmt) {
            $info = $db->errorInfo();
            exit($info[2]);
        }

        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            #echo '<p>' . $data['line'] . ':' . $data['id'] . "</p>\n";
            if ($data['utctime'] == $array[3]) {
                exit;
            }
        }

        // insert execute
        $stmt->execute($array);
        echo "inserted " . $db->lastInsertId();

        // disconnect
        $db->null;

    } catch (PDOException $e) {
        echo $e->getMessage();
        exit;
    }
}

insert_db();
