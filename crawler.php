<?php
	/*
		A stupid crawler!! I suggest you use the better crawlers available out there!
	*/
	include("config/db_connect.php");
	include("config/constants.php");
	include("src/util.php");

	$crawledLinks=array();
	$crawledImages=array();
	
	$count=0;

	function followLink($url,$origURL,$depth=0){
		global $crawledLinks;
		global $count;

		if (!filter_var($url, FILTER_VALIDATE_URL)) return; //not a valid URL

		if (!isset($origURL)) $origURL=$url;
		if ($depth>MAX_DEPTH){
			echo "<div style='color:red;'>The Crawler is giving up!</div>";
			return;
		}
		
		if ($count>MAX_COUNT) return;
		$crawling=array();

		$options=array(
			'http'=>array(
				'method'=>"GET",
				'user-agent'=>"foogleBot/0.1\n"
			)
		);
		if (ignoreLink($url)) return;
		$url=convertLink($origURL,$url);

		// if (alreadyExistsInDatabase($url)) return;

		$context=stream_context_create($options);
		$doc=new DomDocument();
		@$doc->loadHTML(file_get_contents($url,false,$context));
        $links=$doc->getElementsByTagName('a');
		$pageTitle=getDocTitle($doc,$url);
		// $metaData=get_meta_tags($url);
		$metaData=getDocMetaData($doc);
		
		$headers=@get_headers($url,1);

		if($headers and strpos($headers['Content-Type'],'text/html')!==false){
			# All Fine! Can Continue
		}else{
			#Oops URL is not a HTML-file!!
			return;
		}

		if (!alreadyExistsInDatabase($url)){
			$count=$count+1;
			insertIntoDatabase($url,$pageTitle,$metaData,$depth);
		}

		getAllImages($doc,$url);
		foreach ($links as $i){
			$link=$i->getAttribute('href');
			if (!in_array($link,$crawledLinks)){
				$crawledLinks[]=$link;
				$crawling[]=$link;
			}
		}
		foreach ($crawling as $crawlURL)
			if (!alreadyExistsInDatabase($crawlURL))
				followLink($crawlURL,$origURL,$depth+1);
	}
	
	function convertLink($site,$path){
		return getURL(rel2abs( $path, $site));
		if (substr_compare($path,"//",0,2)==0 and strpos("http://",$path))
			return parse_url($site)['scheme'].$path;
		elseif (strpos($path,"http://",0,7)==0 or
			substr_compare($path,"https://",0,8)==0 or 
			substr_compare($path,"www.",0,4)==0)
			return $path;
		else
			return $site.'/'.$path;
	}

	function ignoreLink($url){
		return $url[0]=="#" or substr($url, 0, 11) == "javascript:" or
		substr($url, 0, 5) == "data:";
	}

	function alreadyExistsInDatabase($link){
		global $conn;
		$query=$conn->prepare("SELECT * FROM sites WHERE url = :url");
		$query->bindParam(":url",$link);
		$query->execute();
		return $query->rowCount()!=0;
	}

	function imageAlreadyExistsInDatabase($link){
		global $conn;
		$query=$conn->prepare("SELECT * FROM images WHERE imageURL=:url");
		$query->bindParam(":imageURL",$link);
		$query->execute();
		return $query->rowCount()!=0;
	}

	function insertIntoDatabase($link,$title,&$metaData,$depth){
		global $crawledLinks;

		$crawledLinks[]=$link;

		# In facebook it shows update your browser!!
		if (strpos($title,"Update Your Browser")!== false){
			echo '<div style="color:red">Crawler is blocked!!!</div>';
			return;
		}
		# Very stupid Crawler, I told you!
		elseif (strpos($link,"//accounts.google.com/")!== false or strpos($title,"Sign in")!== false){
			echo '<div style="color:purple">User Login required!!</div>';
			return;
		}
		elseif (strpos(parse_url($link)['host'],"google.")!== false and strpos($link,"?")!== false){
			echo '<div style="color:purple">This is Google Search!!</div>';
			return;
		}

		global $conn;
		echo (
			"Inserting new record {URL= $link".
			", Title = '$title'".
			", Description = '".$metaData['description'].
			"', Keywords = ' ".$metaData['keywords'].
			"'}<br/><br/><br/>"
		);
		$query=$conn->prepare("INSERT INTO sites(url,title,description,keywords)
							VALUES(:url, :title, :description, :keywords);");
		$query->bindParam(":url",$link);
		$query->bindParam(":title",$title);
		$query->bindParam(":description",$metaData['description']);
		$query->bindParam(":keywords",$metaData['keywords']);
		$query->execute();
	}

	function insertImageIntoDatabase($imageURL,$pageURL,$imageTitle,$altText){
		global $conn;
		/*If image doesn't exist then add it to database other-wise update the database*/
		if (!imageAlreadyExistsInDatabase($imageURL)){
			// print('should work'.$imageURL."<br/>".$pageURL."<br/>".$imageTitle."<br/>".$altText);
			$query=$conn->prepare("INSERT INTO images(imageURL,pageURL,imageTitle,altText)
								VALUES(:imageURL, :pageURL, :imageTitle, :altText)");
			$query->bindParam(":imageURL",$imageURL);
			$query->bindParam(":pageURL",$pageURL);
			$query->bindParam(":imageTitle",$imageTitle);
			$query->bindParam(":altText",$altText);
			// echo 'i am';
			$query->execute();
		}else{
			
		}
	}

    function getDocTitle(&$doc,$url){
        $titleNodes=$doc->getElementsByTagName('title');
        if (count($titleNodes)==0 or !isset($titleNodes[0]->nodeValue))
            return $url;
        $title=str_replace('','\n',$titleNodes[0]->nodeValue);
        return (strlen($title)<1)?$url:$title;
    }

    function getDocMetaData(&$doc){
        $metaData=array();
        $metaNodes=$doc->getElementsByTagName('meta');
        foreach ($metaNodes as $node)
            $metaData[$node->getAttribute("name")]=$node->getAttribute("content");
        if (!isset($metaData['description']))
			$metaData['description']='No Description Available';
		if (!isset($metaData['keywords']))
			$metaData['keywords']='';
        return array(
            'keywords'=>str_replace('','\n',$metaData['keywords']),
            'description'=>str_replace('','\n',$metaData['description'])
        );
	}
	
	function getAllImages(&$doc,$pageURL){
		print('atleast i am here');
		global $crawledImages;
		/*Gets all the images in the document- Doesn't get CSS Images though*/
		$imgNodes=$doc->getElementsByTagName('img');
		
		foreach($imgNodes as $node){
			echo 'okay';
			$imageURL=$node->getAttribute("src");
			if (!isset($imageURL) or ignoreLink($imageURL)){
				echo 'continuing';
				continue;
			}
			$imageURL=convertLink($pageURL,$imageURL);
			$imageTitle=$node->getAttribute("title");
			$altText=$node->getAttribute("alt");

			if (!in_array($imageURL,$crawledImages)){
				$crawledImages[]=$imageURL;
				/*Do not add nameless images into the database*/
				echo "UI:".strlen($imageTitle)."<br>";
				echo ($altText and strlen($altText)>=3) or
				($imageTitle and strlen($imageTitle)>=3);
				echo 'Alt:'.$altText.">".$imageTitle."<br>";
				
				if (($altText and strlen($altText)>=3) or
				  ($imageTitle and strlen($imageTitle)>=3)){
					echo '<br>INSERGING INTO DATABASE<br>';
					$imageTitle=strlen($imageTitle)>0?$imageTitle:'';
					$altText=strlen($altText)>0?$altText:'';
					insertImageIntoDatabase($imageURL,$pageURL,$imageTitle,$altText);
				}
			}
		}
		
	}
	if (isset($_GET[url]))
		followLink($_GET[url],$_GET[url]);

	// followLink("https://www.reecekenney.com","https://www.reecekenney.com")
	// insertImageIntoDatabase("hello","world","title","alttes");
?>