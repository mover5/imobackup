<?php
	function imo_top() {
		//include('forum_old/SSI.php');		
		//$cpath = "http://ilmarching.com" . $_SERVER["SCRIPT_NAME"];	
		//$_SESSION['login_url'] = $cpath;
		//$_SESSION['logout_url'] = $cpath;
		htmlhead();
		contentbody();
		contentheader();
		start_content();
		
	} 
	//Content Here
	function imo_bottom() {
		end_content();
		footer();
		close_body();
	}

	function htmlhead() {
		echo '<html xmlns="http://www.w3.org/1999/xhtml">';
		echo '<head>';
		echo '<link rel="stylesheet" type="text/css" href="css/index.css?rc3" /> ';
		echo '<link rel="stylesheet" type="text/css" href="css/imo_page.css" /> ';
		echo '<link rel="stylesheet" type="text/css" href="css/wp.css" /> ';
		echo '<link rel="stylesheet" type="text/css" href="css/buttons.css" /> ';
		echo '<link rel="stylesheet" type="text/css" href="css/help.css" /> ';
		echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
		echo '<meta name="description" content="Illinois Marching Online" />'; 
		echo '<title>Illinois Marching Online</title> ';
		define_javascripts();		
		echo '</head>';
		
	}
	
	function define_javascripts() {
		include('connection.inc.php');
		$date = date("Y-m-d");
		$query = "SELECT * FROM Ads WHERE Banner = 1 and ExpirationDate > '$date'";
		$result = mysql_query($query, $connect);
		$bannernumads = mysql_num_rows($result);
		$bannerimagearray = array();
		$bannerurlarray = array();
		$bannertitlearray = array();
		$count = 0;
		while ($row = mysql_fetch_array($result)) {
			$bannerimagearray[$count] = "ads/" . $row['Image'];
			$bannerurlarray[$count] = $row['Link'];
			$count++;
		}
		
		$query = "SELECT * FROM Ads WHERE Banner = 0 and ExpirationDate > '$date'";
		$result = mysql_query($query, $connect);
		$sidenumads = mysql_num_rows($result);
		$sideimagearray = array();
		$sideurlarray = array();
		$sidetitlearray = array();
		$count = 0;
		while ($row = mysql_fetch_array($result)) {
			$sideimagearray[$count] = "ads/" . $row['Image'];
			$sideurlarray[$count] = $row['Link'];
			$count++;
		}
			
		echo "\n<script type=\"text/javascript\">
	
		var i = 0;
		var bannerimagepreload = new Array();\n
		var bannerimage = new Array();\n
		var bannerurl = new Array();\n
		var bannernumads = $bannernumads;";
		for ($i = 0; $i < $bannernumads; $i++) {
			echo "bannerimagepreload[$i] = new Image();
				  bannerimagepreload[$i].src = \"".$bannerimagearray[$i]."\";\n
				  bannerimage[$i] = \"".$bannerimagearray[$i]."\"\n
				  bannerurl[$i] = \"".$bannerurlarray[$i]."\"\n";
		}
		
		echo "var sideimagepreload = new Array();\n
		var sideimage = new Array();\n
		var sideurl = new Array();\n
		var sidenumads = $sidenumads;";
		for ($i = 0; $i < $sidenumads; $i++) {
			echo "sideimagepreload[$i] = new Image();
				  sideimagepreload[$i].src = \"".$sideimagearray[$i]."\";\n
				  sideimage[$i] = \"".$sideimagearray[$i]."\"\n
				  sideurl[$i] = \"".$sideurlarray[$i]."\"\n";
		}

		echo "	    
		var bannerimageNum;
		var sideimageNum;
		intervalID = setInterval(\"changeBannerAd()\", 8000);
		function changeBannerAd()
		{
			bannerimageNum++;
			if (bannerimageNum >= bannernumads) bannerimageNum = 0;

			document.getElementById(\"adimage\").src = bannerimage[bannerimageNum];
			document.getElementById(\"adlink\").href = bannerurl[bannerimageNum];	
			
			sideimageNum++;
			if (sideimageNum >= sidenumads) sideimageNum = 0;

			document.getElementById(\"sideadimage\").src = sideimage[sideimageNum];
			document.getElementById(\"sideadlink\").href = sideurl[sideimageNum];			
		}

		
		function changeImage(src, obj) {
			obj.src = src;
		}
		</script>";
	}
	
	function contentbody() {
		echo '<body>';
        echo '<script src="js/jquery.js"></script>';
		echo '<div id="wrapper" style="width: 90%">';
	}
	
	function contentheader() {
		echo '<div id="header">';
		echo '<div class="frame">';
		echo '<div id="top_section">'; 
		banner("");
		menu("");
		echo '</div>';
		echo '<div id="upper_section" class="middletext">';
		ad();
		//user();
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
	
	function user() {
		global $context;
		if ($context['user']['is_logged']) {
			echo '<div class="user">';
			echo '<table border=0 cellpadding=5><tr>';

			if (!empty($context['user']['avatar'])) {
				echo '<td valign="top"><p class="avatar">', $context['user']['avatar']['image'], '</p></td>';
			}
				
			echo '<td valign="top"><b>Hello <span>', $context['user']['name'], '</span></b><br />';
			ssi_logout();
			echo '</td>'; 
			echo '</tr>';
			echo '</table><br />';
			echo '</div>';
		} else {
			ssi_login();
		}
	}
	
	function banner($host) {
		echo '<center><a href="http://ilmarching.com"><img src="'.$host.'images/Main_Banner.jpg" /></a></center> ';
	}
	
	function menu($host) {
		echo '<div class="navblock">';
		echo '<a title="" href="'.$host.'index.php"><div class="navbutton">HOME/NEWS</div></a>';
		echo '<a title="" href="'.$host.'bands.php"><div class="navbutton">BANDS</div></a>';
		echo '<a title="" href="'.$host.'festivals.php"><div class="navbutton">FESTIVALS</div></a>'; 
//		echo '<a title="" href="scores.php"><div class="navbutton">SCORES</div></a>';
		echo '<a title="" href="'.$host.'forum"><div class="navbutton">FORUMS</div></a>';
		echo '<a title="" href="'.$host.'videos.php"><div class="navbutton">VIDEOS</div></a>'; 
		echo '<a target="_blank" title="" href="http://cafepress.com/ilmarching"><div class="navbutton">STORE</div></a>'; 
		echo '<a title="" href="'.$host.'faq.php"><div class="navbutton">FAQ</div></a>';
		echo '<a title="" href="'.$host.'archive.php"><div class="navbutton">ARCHIVE</div></a>';
		echo '<a title="" href="'.$host.'about.php"><div class="navbutton">ABOUT</div></a>';
		echo '</div>'; 
	}
	
	function ad() {	
		include('connection.inc.php');
		$date = date("Y-m-d");
		$query = "SELECT * FROM Ads WHERE Banner = 1 and ExpirationDate > '$date'";
		$result = mysql_query($query, $connect);
		$bannernumads = mysql_num_rows($result);
		$bannerimagearray = array();
		$bannerurlarray = array();
		$bannertitlearray = array();
		$count = 0;
		while ($row = mysql_fetch_array($result)) {
			$bannerimagearray[$count] = "ads/" . $row['Image'];
			$bannerurlarray[$count] = $row['Link'];
			$bannertitlearray[$count] = $row['Title'];
			$count++;
		}
		$id = rand(0, ($bannernumads-1));
		if (mysql_num_rows($result) > 0) {
    		echo "<center><a href=\"".$bannerurlarray[$id]."\" name=\"adlink\" id=\"adlink\" ><img src=\"".$bannerimagearray[$id]."\" name=\"adimage\" id=\"adimage\" width='797' height='130'></a></center>";
	    	echo "\n<script type=\"text/javascript\">
		    bannerimageNum = $id;
		    </script>";
		}
	}
	
	function start_content() {
		echo '<div id="content_section">';
		echo '<div class="frame">';
		echo '<div id="main_content_section">';
		echo '<table class="main_table">';
		echo '<tr>';
		echo '<td id="sidebar">';
		//user();
		sidebar("");		
		echo '<div class="sidenode">';
		twitter();
		echo '</div>';
		
//		side_ad();
		
		echo '</td>';
		echo '<td id="main_content">';
		
	}
	
	function end_content() {
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}

	function sidebar($host) {
		echo '<div class="side_box">';
		echo '<h3>Submit Info To IMO</h3>';
		echo '<a href="'.$host.'submit.php"><div class="imo_button">Submit Info</div></a>';
		echo '<a href="'.$host.'upload_video.php"><div class="imo_button">Submit Band Video</div></a>';
		echo '<a href="'.$host.'upload_image_web.php"><div class="imo_button">Upload Band Image</div></a>';
		//echo '<a href="submit_band_info.php"><img onmouseout="return changeImage("images/th_BandInfo.png", this)" onmouseover="return changeImage("images/th_BandInfo2.png",this)" src="images/th_BandInfo.png"></a>';
		//echo '<br /><br /><a href="submit_festival_info.php"><img onmouseout="return changeImage("images/th_FestivalInfo.png", this)" onmouseover="return changeImage("images/th_FestivalInfo2.png",this)" src="images/th_FestivalInfo.png"></a>';
		//echo '<br /><br /><a href="submit_score_info.php"><img onmouseout="return changeImage("images/th_SubmitScores.png", this)" onmouseover="return changeImage("images/th_SubmitScores2.png",this)" src="images/th_SubmitScores.png"></a>';
		//echo '<br /><br /><a href="upload_image_web.php"><img onmouseout="return changeImage("images/th_BandImage.png", this)" onmouseover="return changeImage("images/th_BandImage2.png",this)" src="images/th_BandImage.png"></a>';
		
		echo '<hr>';
		echo '<h3>Donate to IMO</h3>';
		echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">';
		echo '<input type="hidden" name="cmd" value="_s-xclick">';
		echo '<input type="hidden" name="hosted_button_id" value="6625171">';
		echo '<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">';
		echo '<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">';
		echo '</form>';
		echo '<br />';
		echo '<hr>';
		echo '<br /><a href="http://www.facebook.com/home.php?#!/pages/Illinois-Marching-Online/64742541413?ref=ts" target="_blank"><img src="'.$host.'images/facebook.gif" /></a>';
		echo '<br />&nbsp;';
		echo '</div>';
	}
	
	function twitter() {
	echo '<a class="twitter-timeline" href="https://twitter.com/ilmarching" data-widget-id="253737838363938816">Tweets by @ilmarching</a>
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
	}
	
	function side_ad() {
		
		
		include('connection.inc.php');
		$date = date("Y-m-d");
		$query = "SELECT * FROM Ads WHERE Banner = 0 and ExpirationDate > '$date'";
		$result = mysql_query($query, $connect);
		$sidenumads = mysql_num_rows($result);
		$sideimagearray = array();
		$sideurlarray = array();
		$sidetitlearray = array();
		$count = 0;
		while ($row = mysql_fetch_array($result)) {
			$sideimagearray[$count] = "ads/" . $row['Image'];
			$sideurlarray[$count] = $row['Link'];
			$sidetitlearray[$count] = $row['Title'];
			$count++;
		}
		$id = rand(0, ($sidenumads-1));
		if ($sidenumads > 0) {
			echo '<div class="sidenode">';
			echo "<center><a href=\"".$sideurlarray[$id]."\" name=\"sideadlink\" id=\"sideadlink\" ><img src=\"".$sideimagearray[$id]."\" name=\"sideadimage\" id=\"sideadimage\" width='180' height='300'></a></center>";
			echo "\n<script type=\"text/javascript\">";
			echo "sideimageNum = " . $id . ";";
			echo "</script>";
			echo '</div>';
		}
	}
	
	function footer() {	
		echo '<div id="footer_section">';
		echo '<div class="frame">';
		echo '<ul class="reset">';
		echo '<li class="copyright">©2003-'.Date("Y").' Illinois Marching Online. All Rights Reserved</li>';
		echo '<li class="copyright">';
		echo '<a href="wp-login.php">Wordpress Login</a>&nbsp;&nbsp;';
		echo '<a href="console">Console Login</a>';
		echo '</li>';
		echo '</ul>';
		echo '</div>';
		echo '</div>';
	}
	
	function close_body() {
		echo '</div>';
		echo "<script type=\"text/javascript\"> var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\"); 
   document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));
   </script>
   <script type=\"text/javascript\">
   try {
      var pageTracker = _gat._getTracker(\"UA-6858601-1\");
      pageTracker._trackPageview();
   } catch(err) {}
   </script>";
		echo '</body>';
		echo '</html>';
	}
?>
