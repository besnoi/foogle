<?php
	include('config/constants.php');
	include("config/db_connect.php");
	include("functions/util.php");
	include('functions/siteSearch.php');
	include('functions/imageSearch.php');
	include('functions/videoSearch.php');
	include('functions/newsSearch.php');
	include('functions/helper.php');
	if (isset($_GET['q']))
		$term=$_GET['q'];
	else
		return;
	$type = (isset($_GET['type'])) ? $_GET['type'] : 'web';
	$page = (isset($_GET['page'])) ? $_GET['page'] : 1;
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Foogle - Your Own Search Engine</title>
		<link rel="icon" type="image/ico" href="assets/favicon.ico"/>
		<link rel="stylesheet" type="text/css" href="assets/css/style.css"/>
		<link rel="stylesheet" type="text/css" href="assets/css/nav.css"/>
		<link rel="stylesheet" type="text/css" href="vendor/fancybox/jquery.fancybox.min.css"/>
		<script type="text/javascript" src="vendor/jquery/jquery-3.2.1.min.js"></script>
	</head>
	<body>
		<div class="wrapper">
			<div class="header">
				<div class="headerContent">
					<div class="sideLogo">
						<a href=".">
							<img src="assets/images/foogleLogo.png"/>
						</a>
					</div>
					<div class="searchContainer">
						<form action="search.php" method="GET">
							<div class="searchBarContainer">
								<input type="hidden" name="type" value="<?php echo $type?>"/>
								<input class="searchBox" type="text" name="q"
									value="<?php echo $_GET['q']?>"/>
								<button>
									<img src="assets/images/icons/search.png"/>
								</button>
							</div>							
						</form>
					</div>
				</div>
				<div class="tabContainer">
					<ul class="tabList">
						<li <?php echo $type==='web'?'class="active"':'' ?>>
							<a href="<?php echo 'search.php?q=',$term,'&type=web'; ?>">
								Web
							</a>
						</li>
						<li <?php echo $type==='images'?'class="active"':'' ?>>
							<a href="<?php echo 'search.php?q=',$term,'&type=images'; ?>">
								Images
							</a>
						</li>
						<li <?php echo $type==='videos'?'class="active"':'' ?>>
							<a href="<?php echo 'search.php?q=',$term,'&type=videos'; ?>">
								Videos
							</a>
						</li>
						<li <?php echo $type==='news'?'class="active"':'' ?>>
							<a href="<?php echo 'search.php?q=',$term,'&type=news'; ?>">
								News
							</a>
						</li>
					</ol>
				</div>
			</div>
			<?php
				$numResults=0;
				if ($type=='web')
					$numResults=getSiteNumResults($term);
				elseif ($type=='images')
					$numResults=getImageNumResults($term);
				elseif ($type=='videos')
					$numResults=getVideoNumResults($term);
				elseif ($type=='news')
					$numResults=getNewsNumResults($term);
				# Show Results
				echo "<div class='mainResultsSection'>\n";
				echo t(4)."<p class='resultsCount'>About $numResults results found</p>\n";
				if ($numResults==0)
					searchNotFound($term,$type);
				else
					if ($type=='web')
						displayWebResults($term,$page);
					elseif ($type=='images')
						displayImageResults($term,$page);
					elseif ($type=='videos')
						displayVideoResults($term,$page);
					elseif ($type=='news')
						displayNewsResults($term,$page);
				echo t(3)."</div>";

				# Show Navigation System
				include('functions/nav_sys.php');
				# TODO: Infinite Scroll for Images
			?>
			<div class="pageEnd"></div>
		</div>
		<footer>
			<span class='country'>
				<?php
					include('functions/showCountry.php');
				?>
			</span>
		</footer>
		<script type="text/javascript" src="vendor/masonry/masonry.pkgd.min.js"></script>
		<script type="text/javascript" src="vendor/fancybox/jquery.fancybox.min.js"></script>
		<!-- <script type="text/javascript" src="vendor/imagesLoaded/imagesloaded.pkgd.min.js"></script> -->
		<!-- <script type="text/javascript" src="vendor/inview/jquery.inview.min.js"></script> -->
		<script type="text/javascript" src="assets/js/main.js"></script>
	</body>
</html>