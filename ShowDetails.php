<?php
/**
 * Created by PhpStorm.
 * User: niraj
 * Date: 29-03-17
 * Time: 05:11 PM
 */
require_once 'core/init.php';

$iRecordID = $_GET['id'];
$index = $_GET['index'];
$type = $_GET['type'];

if(!$iRecordID){
    echo "<h2>Well, that didn't go well</h2>";
    die();
}

$params = [
    'index' => $index,
    'type' => $type,
    'id' => $iRecordID
];

$db = new Database();
$data = $db->get($params);

$arr['name'] = $data['_source']['Song'];
$arr['rank'] = $data['_source']['Rank'];
$arr['artist'] = $data['_source']['Artist'];
$arr['year'] = $data['_source']['Year'];
$arr['lyrics'] = $data['_source']['Lyrics'];
$arr['source'] = $data['_source']['Source'];

echo "<br><b>Name:    </b><br>    ". $arr['name'];
echo "<br><b>Rank:    </b><br>    ". $arr['rank'];
echo "<br><b>Artist:  </b><br>    ". $arr['artist'];
echo "<br><b>Release Year: </b><br>   ". $arr['year'];
echo "<br><b>Lyrics:  </b><br>    ". $arr['lyrics'];
echo "<br><b>Source:  </b><br>    ". $arr['source'];

