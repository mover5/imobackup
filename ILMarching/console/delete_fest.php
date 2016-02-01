<?php
session_start();

require_once('includes/functions.inc.php');
require_once('includes/connect.inc.php');

if (check_login_status() == false || (get_login_role() != "admin" && get_login_role() != "contributor")) {
	$_SESSION['error']  = "You do not have the authorization to access this page";
	redirect('login.php');
} else {
	$username = $_SESSION['username'];
	$role = $_SESSION['role'];
	$connect = connectToDB();
}

if (isset($_POST['FestivalID'])) {
	$f = $_POST['FestivalID'];
	$query = "DELETE FROM Festivals WHERE FestivalID='$f'";
	mysql_query($query);
	$message = "Deleted festival successfully";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
<link rel="stylesheet" type="text/css" href="css/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="css/IMOconsole.css" />
<link rel="stylesheet" type="text/css" href="css/anylinkcssmenu.css" />
<script src="js/equalcolumns.js" type="text/javascript"></script> 
<script type="text/javascript" src="js/anylinkcssmenu.js"></script>
<script type="text/javascript" src="js/jquery.js"></script>
<script type='text/javascript' src='js/jquery.autocomplete.js'></script>
<script type="text/javascript">
//anylinkcssmenu.init("menu_anchors_class") ////Pass in the CSS class of anchor links (that contain a sub menu)
anylinkcssmenu.init("anchorclass")
$().ready(function() {
	$("#FestivalName").autocomplete("ajax/get_festival_list_mod_fest.php", {
		width: 260,
		matchContains: true,
		//mustMatch: true,
		//minChars: 0,
		//multiple: true,
		//highlight: false,
		//multipleSeparator: ",",
		selectFirst: false
	});
		$("#FestivalName").result(function(event, data, formatted) {
			$("#FestivalID").val(data[1]);
		});
});

function validateID() {
	if (document.getElementById('FestivalID').value != "") {
		document.SelectFestival.submit();
	} else {
		alert("Please select a Festival from the Autocomplete box");
		return;
	}
}
</script>
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
			<div class="innertube">
				<?php if (isset($message)) {
					echo "<center><font size=+1 color=#FF0000>" . $message . "</font></center><p>";
				}
				?>
				<h1>Delete Festival</h1>
					<form name='SelectFestival' action='delete_fest.php' method='POST' autocomplete="off">
					Festival <label>:</label>
					<input type="text" name="FestivalName" id="FestivalName" size='50'/><input type='hidden' name='FestivalID' id='FestivalID' value=''/> 
					<input type='hidden' name='modifysubmit' id='modifysubmit' value='Submit' />
				<input type='button' id='modifybutton' name='modifybutton' value='Select Festival' onClick='validateID()'/><br />(Just start typing the Festival Name)
				</form>
				</form>
			</div>
		</div>
	</div>
	<!--MENU-->
	<div id="leftcolumn">
		<div class="innertube">
			Welcome, <?php echo ucwords(get_username());?><br />
			<?php echo date("F d, Y");?><br />
			<?php echo ucwords(get_login_role()) . " - <a href=includes/logout.inc.php><font color=#FFFFFF>logout</font></a>";?>
			<?php include("menu.php"); ?>
		</div>	
	</div>

	<div id="footer"><?php include('footer.html');?><div>
</div>

</body>
</html>
