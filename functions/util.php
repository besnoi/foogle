<?php
/*
	These are the functions which are not necessarily written by me!!
	Rather I stole them from the internet
*/


# Get IP of the client
function getIP()
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    } else if (isset($_SERVER['HTTP_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    } else if (isset($_SERVER['REMOTE_ADDR'])) {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    } else {
        $ipaddress = 'UNKNOWN';
    }

    return $ipaddress;
}

# Titles need to be trimmed down in a special manner
function trimTitle($string,$limit){
	$title=$string;
	$len=mb_strlen($string,'UTF-8');
	if ($len > $limit){
		$title=mb_substr($string, 0, $limit/2,"utf-8")."...";
		$title.=mb_substr($string, $len-$limit/10,$limit/10,"utf-8");
	}
	return $title;
}

# Remove the extension from a string
function removeExtension($string) { return $string; }

function t($n){ return str_repeat("\t",$n); }

# Replace a case-insensitive string with another string in the same fashion!
function transformCase($string,$subject){
	return $subject;
}

# Trim a UTF-8 string
function trimField($string, $limit) {
	$dots = mb_strlen($string,'UTF-8') > $limit ? "..." : "";
	return mb_substr($string, 0, $limit,"utf-8") . $dots;
}

# URLs will be trimmed down in a special manner
function trimURL($url){
	$url=parse_url($url);
	$trimmedURL=$url['scheme'].'://'.$url['host'];
	if (strlen($url['path'])>0 and $url['path']!='/'){
		if ($url['path'][0]=='/'){
			if (substr($trimmedURL,-3)!=' › ')
				$trimmedURL.=' › ';
			$url['path']=substr($url['path'],1);
		}
		if ($url['path'][strlen($url['path'])-1]=='/')
			$url['path']=substr($url['path'],0,-1);
		$trimmedURL.=str_replace('/',' › ',$url['path']);
	}
	return trimField($trimmedURL,URL_LIMIT);
}


# Sanitize a URL
function getURL($url){
	$url=parse_url($url);
	return $url['scheme'].'://'.$url['host'].
		preg_replace('/\/+/', '/',$url['path']).
		(isset($url['query'])?'?'.$url['query']:'').
        (isset($url['fragment'])?'#'.$url['fragment']:'');
}

# Boldify a term in a string
function boldify($term,$string){
	$term=stripslashes($term);
	return preg_replace_callback(
		"/$term/i",
		function ($matches){
			return '<strong>'.$matches[0].'</strong>';
		},
		$string
	);
}

# Convert relative URL to absolute URL
function rel2abs( $rel, $base ) {
	// parse base URL  and convert to local variables: $scheme, $host,  $path
	extract( parse_url( $base ) );

	if (strpos( $rel,"//" ) === 0 )
		return $scheme . ':' . $rel;

	// return if already absolute URL
	if ( parse_url( $rel, PHP_URL_SCHEME ) != '' )
		return $rel;

	// queries and anchors
	if ( $rel[0] == '#' || $rel[0] == '?' )
		return $base . $rel;

	// remove non-directory element from path
	$path = preg_replace( '#/[^/]*$#', '', $path );

	// destroy path if relative url points to root
	if ( $rel[0] ==  '/' )
		$path = '';

	// dirty absolute URL
	$abs = $host . $path . "/" . $rel;

	// replace '//' or  '/./' or '/foo/../' with '/'
	$abs = preg_replace( "/(\/\.?\/)/", "/", $abs );
	$abs = preg_replace( "/\/(?!\.\.)[^\/]+\/\.\.\//", "/", $abs );

	// absolute URL is ready!
	return $scheme . '://' . $abs;
	}
?>