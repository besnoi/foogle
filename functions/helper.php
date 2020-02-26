<?php

$dict = pspell_new("en");

/**
 * This contains functions to help users with did you mean, etc!!
**/

function searchNotFound($term,$type){

	if ($type=="sites") $type="documents";
	elseif ($type=="images") $type="image results";
	elseif ($type=="videos") $type="video results";

	echo t(4)."<div id='helperCard'>\n";
	didYouMean($term,$type,5);
	echo t(5)."<p style='padding-top:.33em'>\n";
	echo t(6)."No results containing all your search terms were found.\n";
	echo t(5)."</p>\n";
	echo t(5)."<p aria-level='3' role='heading' style='padding-top:.33em'>\n";
	echo t(6)."Your search - <b>\"$term\"</b> - did not match any $type\n";
	echo t(5)."</p>\n";
	echo t(5)."<p style='margin-top:1em'>\n";
	echo t(6)."Suggestions:\n";
	echo t(5)."</p>\n";
	echo t(5)."<ul style='margin-left:1.3em;margin-bottom:2em'>\n";
	echo t(6)."<li>Make sure that all words are spelled correctly.</li>\n";
	echo t(6)."<li>Try different keywords.</li>\n";
	echo t(6)."<li>Try more general keywords.</li>\n";
	echo t(5)."</ul>\n";
	echo t(4)."</div>\n";
}

function didYouMean($term,$type,$t){
	global $dict;
	if (pspell_check($dict, $term)) return;
	$suggestions = pspell_suggest($dict, $term);
	if (count($suggestions)==0) return; else $term=$suggestions[0];
	echo t($t)."<p class='dyd' style='padding-top:.33em'>\n";
	echo t($t+1)."<span>Did you mean: </span>\n";
	echo t($t+1)."<a href='search.php?q=$term&type=$type'>\n";
	echo t($t+2)."\"$term\"\n";
	echo t($t+1)."</a>\n";
	echo t($t)."</p>\n";
	echo $html;
}

function showingInsteadOf($term){
	global $dict;
}

?>