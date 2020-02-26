<?php
	/*
		Note that I'm adding useless hard-coded tabs just for my own convenience!
		You can remove them if you like!
	*/

	$totalPages=ceil($numResults/RESULTS_PER_PAGE);
	$pagesLeft=min(MAX_PAGES,$totalPages);

	if ($pagesLeft<=1) return;

	// function t($n){ return str_repeat("\t",$n); }
	$indent=3;
	$html="";
	$html.= "<div id='foot'>
				<table id='nav'>
					<tbody>
						<tr valign='top'>\n";

	$html.=t(7)."<td>\n";
	if ($page>1)
		$html.=
			t(8)."<a href='search.php?q=$term&type=$type&page=".($page-1)."'>\n".
			t(9)."<span class='pagePrev'></span>\n".
			t(9)."<div style='margin-right:35px;clear:right;padding-top:5px'>Previous</div>\n".
			t(8)."</a>\n";
	else
		$html.= t(8)."<span class='pageStart'></span>\n";
	$html.=t(7)."</td>\n";

	$startFrom=max(1,$page-floor(MAX_PAGES/2));

	if ($startFrom+$pagesLeft>$totalPages+1)
		$startFrom=$totalPages-$pagesLeft+1;

	for ($i=$startFrom;$i<$startFrom+$pagesLeft and $i<=$totalPages;$i++){
		$html.=t(7)."<td>\n";
		if ($i==$page)
			$html.=
				t(8)."<span class='pageSelected'></span><br/>\n".
				t(8)."<div>$page</div>\n";
		else
			$html.=
				t(8)."<a href='search.php?q=$term&type=$type&page=$i'>\n".
				t(9)."<span class='pageUnselected'></span><br/>\n".
				t(9)."<div>$i</div>\n".
				t(8)."</a>\n";

		$html.=t(7)."</td>\n";
		}
	
	$html.=t(7)."<td>\n";

	if ($page*RESULTS_PER_PAGE<$numResults)
		$html.=
			t(8)."<a href='search.php?q=$term&type=$type&page=".($page+1)."'>\n".
			t(9)."<span class='pageNext'></span>\n".
			t(9)."<div style='margin-left:53px;margin-right:-15px;'>Next</div>\n".
			t(8)."</a>\n";
	else
		$html.= t(8)."<span class='pageEnd'></span>\n";

	$html.=t(7)."</td>\n";

	$html.=t(6)."</tr>\n".t(5)."</tbody>\n".t(4)."</table>\n".t(3)."</div>\n";
	echo $html;
?>