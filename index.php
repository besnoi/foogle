<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Foogle - Your Own Search Engine</title>
		<link rel="icon" type="image/ico" href="assets/favicon.ico"/>
		<link rel="stylesheet" type="text/css" href="assets/css/style.css"/>
	</head>
	<body>
		<div class="wrapper indexPage">
			<div class="mainSection">
				<div class="logoContainer">
					<img src="assets/images/foogleLogo.png"/>
				</div>
				<div class="searchContainer">
					<form action="search.php" method="GET">
						<input class="searchBox" type="text" name="q"/>
						<div>
							<input class="searchBtn" type="submit" value="Foogle Search"/>
							<input class="searchBtn" type="submit" value="I'm Feeling Lucky"/>
						</div>
					</form>
				</div>
			</div>
		</div>
		<footer>
			<?php
				require 'functions/util.php';
				printCountry();
			?>
		</footer>
	</body>

</html>