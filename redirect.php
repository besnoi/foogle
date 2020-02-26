<?php
/*
	PHP Script (AJAX) for Incrementing clicks in the database!
*/

include 'config/db_connect.php';

if (!(isset($_GET['id']) and isset($_GET['type']))) return;
$db=$_GET['type'];
if ($db=='web') $db='sites';

$query=$conn->prepare("UPDATE `$db` SET clicks=clicks+1 WHERE id=:id");
$query->bindParam(':id',$_GET['id']);
$query->execute();

$urlField="url"; //for sites
if ($db=='videos') $urlField="videoURL";
if ($db=='news' or $db=='images') $urlField="pageURL";

$query=$conn->prepare("SELECT * FROM `$db` WHERE id=:id");
$query->bindParam(':id',$_GET['id']);
$query->execute();

header("Location: ".$query->fetch(PDO::FETCH_ASSOC)[$urlField]);
// echo $query->fetch(PDO::FETCH_ASSOC)[$urlField];

?>