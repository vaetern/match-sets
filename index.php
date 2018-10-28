<?php

class MyDB extends \SQLite3
{
    function __construct()
    {
        $this->open('rail_pack.410318.db');
    }
}

$db = new MyDB();
if (!$db) {
    echo $db->lastErrorMsg();
} else {
    echo "Opened database successfully\n";
}

function getCsvRow(string $file)
{

    $csvHandle = fopen($file, 'r');

    while (!feof($csvHandle)) {
        yield fgetcsv($csvHandle, null, ',');
    }

    fclose($csvHandle);
}

function selectBinding(MyDB $db, string $code)
{
    $sql = "SELECT CODE_VALUE as code from rail_location_mapper WHERE `DATA_OWNER` = 'FRR' AND `GENERIC_LOCATION_CODE` = '{$code}'";

    $ret = $db->query($sql);

    return $ret->fetchArray(SQLITE3_ASSOC)['code'];
}

//============
$fp = fopen('dataset.csv', 'wb');
foreach (getCsvRow('dataset_dirt.csv') as $row) {
    $clientCode = selectBinding($db, $row[0]);

    if ($clientCode != null) {
        $row[0] = $clientCode;
        $row[8] *= 100;
        $row[9] *= 100;

        fputcsv($fp, $row);
    }
}
fclose($fp);

//============

$fp = fopen('dataset_full.csv', 'wb');
$current = 0;
foreach (getCsvRow('dataset.csv') as $row) {
    $row[10] = 1;
    fputcsv($fp, $row);

    $current++;
    for ($z = 0; $z < 2; $z++) {
        $rand = random_int(0, 1918);
        if ($rand == $current) {
            $rand = (int) (($rand + 3) / 2);
        }

        $randArr[] = $rand;
    }
    $i = 0;
    foreach (getCsvRow('dataset.csv') as $row2) {

        if (in_array($i, $randArr)) {
            $newRow = [
                $row[0],
                $row[1],
                $row[2],
                $row[3],
                $row[4],
                $row2[5],
                $row2[6],
                $row2[7],
                $row2[8],
                $row2[9],
                0,
            ];
            fputcsv($fp, $newRow);
        }

        $i++;
    }
    $randArr = [];

}
fclose($fp);


