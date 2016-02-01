<?php 
require('./wp-blog-header.php');
require('./layout.php');
require('connection.inc.php');
?>
<!--***************Start Page Content***************-->
<?php imo_top(); ?>

<script type="text/javascript">

function collapse(id) {
	table=document.getElementById(id + '_table');

	for(var i=1;i<table.rows.length;i++){
		table.rows[i].style.display='none';
	}
	
	document.getElementById(id+'_button').src = 'images/expand.gif';
	document.getElementById(id+'_button').onclick = new Function('expand("'+id+'")');
}

function expand(id) {
	table=document.getElementById(id + '_table');

	for(var i=1;i<table.rows.length;i++){
		table.rows[i].style.display='';
	}
	
	document.getElementById(id+'_button').src = 'images/collapse.gif';
	document.getElementById(id+'_button').onclick = new Function('collapse("'+id+'")');
}

</script>
<?php 
    $letters = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
 
 	 echo "<p><h2><a name='top'>Illinois Marching Online Bands Library</a></h2><p>";

     foreach ($letters as $value) {

       $link = "<font size='+2'><a href='#$value'>".$value."</a></font> ";
       echo $link;
     }
     echo "<p>";
     
     
	  $query = "SELECT * FROM Bands WHERE 1 ORDER BY School ASC";
	  $result=mysql_query($query);
	  
	$num = mysql_num_rows($result);

		
	$prevletter = "";
	$count = 0;
	$initial = 1;
	$headings = 0;
	echo "<table cellspacing='5' border='0' width='100%'>";
	while ($row = mysql_fetch_array($result)) {
		$school = $row["School"];
 		$band_name = $row["BandName"];
		$city_town = $row["Town"];
		$website 	= $row["WebsiteURL"];
		if(($website != "none")&&($website != "")){
			$website = "<a href=$website target=_blank>Website</a>";
		} else {
			$website = "No Website";
		}
		$BandID 	= $row["BandID"];
		$letter = strtoupper(substr($school, 0, 1));
		
		if ($prevletter <> $letter) {
			if ($initial == 0) {
				echo "</tbody><tbody class='divider'><tr><td colspan='4'></td></tr></tbody>";
			}
			$prevletter = $letter;
			echo "<tbody name='".$headings."_table' id='".$headings."_table' class='data'>";

			if ($initial == 1) {
				$contentHead = "contentHeader1";
				$initial = 0;
			} else {
				$contentHead = "contentHeader2";
			}
			
			echo "<tr><td colspan='4'><div class='cat_bar'>";
			echo "<div class='hbg'>";
			echo "<div class='collapse'>
			<a href='#top'><img src='images/collapse.gif'></a>
			</div>"; // Button
			echo "<a name='$letter'>$letter</a>"; // Date
			$count = 0;
			$headings++;
		}
		if ($count % 2 == 0) {
			$content = "windowbg";	
		} else {
			$content = "h";	
		}
		echo "<tr>
		<td class='$content'><a href=\"./bands_indiv.php?BandID=$BandID\">$school</a></td>
		<td class='$content'>$band_name</td>
		<td class='$content'>$city_town</td>
		<td class='$content'>$website</td></tr>";

		$count++;
	}
	echo "</table>";

?>

<!--***************End Page Content***************-->

<?php imo_bottom(); ?>
