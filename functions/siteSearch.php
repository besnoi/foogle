<?php

# Returns the number of results based on search term
function getSiteNumResults($term) {
	global $conn;
	$query = $conn->prepare("SELECT COUNT(*) as total 
						FROM `sites` WHERE title LIKE :term 
						OR `url` LIKE :term 
						OR `keywords` LIKE :term 
						OR `description` LIKE :term");

	$searchTerm = "%". $term . "%"; //Don't get exact term!
	$query->bindParam(":term", $searchTerm,PDO::PARAM_STR);
	
	$query->execute();

	return $query->fetch(PDO::FETCH_ASSOC)["total"];
}

# Displays all results based on search term
function displayWebResults($term,$currentPage){
	global $conn;
	$fromLimit=($currentPage-1)*RESULTS_PER_PAGE;
	$query = $conn->prepare("SELECT * FROM `sites`
						WHERE `title` LIKE :term 
						OR `url` LIKE :term 
						OR `keywords` LIKE :term 
						OR `description` LIKE :term
						ORDER BY `clicks` DESC
						LIMIT :fromLimit,".RESULTS_PER_PAGE);

	$searchTerm = "%". $term . "%"; //Don't get exact term!
	$query->bindParam(":term", $searchTerm,PDO::PARAM_STR);
	$query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
	$query->execute();
	
	$html=t(4)."<div class='siteResults'>\n";
	while($row=$query->fetch(PDO::FETCH_ASSOC)){
		
		$id=$row['id']; //For data-id parameter!
		$title=$row['title'];

		$url=getURL($row['url']); //replace all //../ to /

		if (strcmp($title,$row['url'])==0) $title=$url;
		$title=trimField($title,TITLE_LIMIT);
		$desc=trimField($row['description'],DESC_LIMIT);
		$desc=boldify($term,$desc);		
		$html.=
			t(5)."<div class='result'>\n".
			t(6)."<h3>\n".
			t(7)."<a href='redirect.php?type=web&id=$id'>\n".
			t(8)."$title\n".
			t(7)."</a>\n".
			t(6)."</h3>\n".
			t(6)."<span class='link'>\n".
			t(7).trimURL($url)."\n".
			t(6)."</span>\n".
			t(6)."<span class='description'>\n".
			t(7)."$desc\n".
			t(6)."</span>\n".
			t(5)."</div>\n";
	}
	$html.=t(4)."</div>\n";
	echo $html;
}

?>