<?php

# Returns the number of results based on search term
function getImageNumResults($term) {
	global $conn;
	$query = $conn->prepare("SELECT COUNT(*) as total 
						FROM `images` WHERE (
						`imageTitle` LIKE :term 
						OR `altText` LIKE :term 
						OR `imageURL` LIKE :term
						OR `pageURL` LIKE :term) 
						AND `broken`=0");

	$searchTerm = "%". $term . "%"; //Don't get exact term!
	$query->bindParam(":term", $searchTerm,PDO::PARAM_STR);
	
	$query->execute();

	return $query->fetch(PDO::FETCH_ASSOC)["total"];
}

# Displays all results based on search term
function displayImageResults($term,$currentPage){
	echo t(4)."<div class='imageResults'>\n";
	$images=retrieveImageResults($term,$currentPage);
	showImages($term,$images);
	echo t(4)."</div>\n";
	// echo t(4)."<input type='hidden' id='pageno' value='1'>\n";
	// echo t(4)."<img id='loader' src='assets/images/svg/oval_loader.svg'/>\n";
}

function showImages($term,&$images,$count){
	
	foreach ($images as $row){
		$count+=1;
		$id=$row['id']; //For data-id parameter!
		$title=$row['imageTitle']!=""?$row['imageTitle']:$row['altText'];

		$url=getURL($row['imageURL']); //replace all //../ to /
		$page=getURL($row['pageURL']); //replace all //../ to /

		if (strcmp($title,$row['imageURL'])==0) $title=$url;
		$title=trimField($title,TITLE_LIMIT);
		$desc=trimField($row['description'],DESC_LIMIT);
		$desc=boldify($term,$desc);		
		echo
			t(5)."<div class='gridItem image$count'>\n".
			t(6)."<a href='$url' data-id='$id' data-fancybox data-caption='$title'>\n".
			t(7)."<script>\n".
			t(8)."$(document).ready(function(){\n".
			t(9)."loadImage(\"$url\", \"image$count\")\n".
			t(8)."})\n".
			t(7)."</script>\n".
			
			t(6)."</a>\n".
			t(6)."<span class='caption'>$title</span>\n".
			t(5)."</div>\n";
	}
}

# Displays all results based on search term
function retrieveImageResults($term,$currentPage){
	global $conn;
	$images=array();
	$fromLimit=($currentPage-1)*IMAGES_PER_PAGE;
	$query = $conn->prepare("SELECT * FROM `images`
						WHERE (`imageTitle` LIKE :term 
						OR `altText` LIKE :term 
						OR `imageURL` LIKE :term) 
						AND `broken`=0
						ORDER BY `clicks` DESC
						LIMIT :fromLimit,".IMAGES_PER_PAGE);

	$searchTerm = "%". $term . "%"; //Don't get exact term!
	$query->bindParam(":term", $searchTerm,PDO::PARAM_STR);
	$query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
	$query->execute();
	
	while($row=$query->fetch(PDO::FETCH_ASSOC))
		$images[]=$row;
	
	return $images;
}

?>