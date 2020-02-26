<?php
ob_start();
$db='foogle';
$host='localhost';
$user='root';
$password='hello';
try{
	$conn=new PDO(
		"mysql:dbname=$db;host=$host;charset=utf8",
		"$user",
		"$password",
		array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
	);
	$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);
	
}catch(PDOException $pdo){
	echo "Something went wrong! ".$pdo->getMessage()."<br/>";
}
?>