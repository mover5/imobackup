<?php session_start(); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
  <title>ILMarching Console Login</title>
  <link rel="stylesheet" type="text/css" href="css/login.css" />
  <link rel="stylesheet" type="text/css" href="css/IMOconsole.css" />
  <link rel="stylesheet" type="text/css" href="css/anylinkcssmenu.css" />
	<script type="text/javascript" src="js/anylinkcssmenu.js"></script>
	<script type="text/javascript">
	//anylinkcssmenu.init("menu_anchors_class") ////Pass in the CSS class of anchor links (that contain a sub menu)
	anylinkcssmenu.init("anchorclass")
	</script>
  <script src="js/equalcolumns.js" type="text/javascript"></script> 
</head>
<body>
<div id="maincontainer">
	
	<!--HEADER-->
	<div id="topsection">
		<div class="innertube">
			<?php include('header.html'); ?>
		</div>
	</div>

	<!--MAIN CONTENT-->
	<div id="contentwrapper">
		<div id="contentcolumn">
			<div class="innertube"><p>
			<?php
			if (isset($_SESSION['error'])) {
	echo "<center><font size=+1 color=#FF0000>" . $_SESSION['error'] . "</font></center><p>";
	unset($_SESSION['error']);
}
			?>
			 <form id="login-form" method="post" action="includes/login.inc.php">
				<fieldset>
				  <legend>Login to the ILMarching Console</legend>
				  <p>Please enter your username and password to access the administrator's panel</p>
				  <label for="username">
					<input type="text" name="username" id="username" />Username:
				  </label>
				  <label for="password">
					<input type="password" name="password" id="password" />Password:
				  </label>
				  <label for="submit">
					<input type="submit" name="submit" id="submit" value="Login" />
				  </label>
				</fieldset>
			  </form>
			</div>
		</div>
	</div>



	<!--MENU-->
	<div id="leftcolumn">
		<div class="innertube">
			<?php include('menu.php'); ?>
		</div>
	</div>

	<div id="footer"><?php include('footer.html');?><div>

</div>
 
</body>
</html>