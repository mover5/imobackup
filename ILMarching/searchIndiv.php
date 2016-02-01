<?php
require('./wp-blog-header.php');
require('./layout.php');
?>
<?php imo_top(); ?>
<!--***************Start Page Content***************-->

<?php $search = $_REQUEST['search'];

$type = $_REQUEST['type'];

$searchAND = explode(" AND ", $search);t ?>
<h3>Search Results - "<?php echo $search; ?>" </h3>


<?php
if ($type == "blog") {
  $username = "ilmarch_wordpres";
  $password = "nox4on";
  $database = "ilmarch_wordpress";

  $connect=mysql_connect ("localhost", $username, $password) or die ('I cannot connect to the database because: ' . mysql_error());
  mysql_select_db($database) or die( "Unable to select database");
  $query = "SELECT * FROM `wp_posts` WHERE ";

  foreach($searchAND as $value) {
$query .= "(`post_content` LIKE '%$value%'
OR `post_date` LIKE '%$value%'
OR `post_title` LIKE '%$value%'
OR `post_excerpt` LIKE '%$value%') AND ";

}
$query .= "`post_status` = 'publish' ORDER BY post_title";
  $result = mysql_query($query, $connect);
    $rows = mysql_num_rows($result);

  echo "<h2>Blog Posts - $rows Results</h2>";
  while ($row = mysql_fetch_array($result)) {
        echo "<a href='http://www.ilmarching.com/?p=".$row['ID']."&highlight=".$search."'>".$row['post_title']."</a><br />";
  }

} elseif ($type == "band") {

    $username = "ilmarch_libraria";
	$password = "ilmarching";
	$database = "ilmarch_library";

	$connect=mysql_connect ("localhost", $username, $password) or die ('I cannot connect to the database because: ' . mysql_error());
    mysql_select_db($database) or die( "Unable to select database");

     $query = "SELECT * FROM Bands WHERE ";

    foreach ($searchAND as $value) {
      $query .= "(school LIKE '%$value%'
      OR band_name LIKE '%$value%'
      OR city_town LIKE '%$value%'
      OR directors LIKE '%$value%'
      OR colors LIKE '%$value%'
      OR website LIKE '%$value%'
      OR pic_url LIKE '%$value%'
      OR notes LIKE '%$value%') AND ";

    }
    $query .= "1 ORDER BY school ASC";

    $result = mysql_query($query, $connect);
      $rows = mysql_num_rows($result);

    echo "<h2>Bands - $rows Results</h2>";
  while ($row = mysql_fetch_array($result)) {

        echo "<a href='bands_indiv.php?b=".$row['b']."&highlight=".$search."'>".$row['school']."</a><br />";

  }

} elseif ($type == "show") {
  $username = "ilmarch_libraria";
	$password = "ilmarching";
	$database = "ilmarch_library";

	$connect=mysql_connect ("localhost", $username, $password) or die ('I cannot connect to the database because: ' . mysql_error());
    mysql_select_db($database) or die( "Unable to select database");
  $query = "SELECT b, school";

    for ($i = 2000; $i <= date("Y"); $i++) {
        $query .= ", ";
        $query .= $i;
        $query .= "_rep";
    }

    $query .= " FROM Bands WHERE ";

    foreach ($searchAND as $value) {
      $query .= "(2000_rep LIKE '%$value%'";

      for ($i = 2001; $i <= date("Y"); $i++) {
          $query .= " OR ";
          $query .= $i;
          $query .= "_rep LIKE '%$value%'";
      }
      $query .= ") AND ";
    }


    $query .= "1 ORDER BY school ASC";
    $result = mysql_query($query, $connect);
      $rows = mysql_num_rows($result);
    echo "<h2>Shows - $rows Results</h2>";

  while ($row = mysql_fetch_assoc($result)) {
    foreach ($row as $key => $value) {
      if ($key <> "school" && strstr(strtoupper($value), strtoupper($search))) {
                $showyear = explode("_", $key);
                echo "<a href='bands_indiv.php?b=".$row['b']."&year=".$showyear[0]."&highlight=".$search."'>".$row['school']. " - " . $showyear[0] ."</a><br />";
      }
    }
  }
} elseif ($type == "award") {
  $username = "ilmarch_libraria";
	$password = "ilmarching";
	$database = "ilmarch_library";

	$connect=mysql_connect ("localhost", $username, $password) or die ('I cannot connect to the database because: ' . mysql_error());
    mysql_select_db($database) or die( "Unable to select database");
 //Awards
    $query = "SELECT b, school";

    for ($i = 2000; $i <= date("Y"); $i++) {
        $query .= ", ";
        $query .= $i;
        $query .= "_accomp";
    }

    $query .= " FROM Bands WHERE ";

    foreach ($searchAND as $value) {
      $query .= "(2000_accomp LIKE '%$value%'";

      for ($i = 2001; $i <= date("Y"); $i++) {
          $query .= " OR ";
          $query .= $i;
          $query .= "_accomp LIKE '%$value%'";
      }
      $query .= ") AND ";
    }


    $query .= "1 ORDER BY school ASC";

    $result = mysql_query($query, $connect);
      $rows = mysql_num_rows($result);

    echo "<h2>Awards - $rows Results</h2>";

  while ($row = mysql_fetch_assoc($result)) {
    foreach ($row as $key => $value) {
      if ($key <> "school" && strstr(strtoupper($value), strtoupper($search))) {
                $showyear = explode("_", $key);
                echo "<a href='bands_indiv.php?b=".$row['b']."&year=".$showyear[0]."&highlight=".$search."'>".$row['school']. " - " . $showyear[0] ."</a><br />";
      }
    }
  }
} elseif ($type == "festival") {
  $username = "ilmarch_libraria";
	$password = "ilmarching";
	$database = "ilmarch_library";

	$connect=mysql_connect ("localhost", $username, $password) or die ('I cannot connect to the database because: ' . mysql_error());
    mysql_select_db($database) or die( "Unable to select database");
       $query = "SELECT * FROM Festivals WHERE ";
    foreach ($searchAND as $value) {
        $query .= "(name LIKE '%$value%'
        OR date LIKE '%$value%'
        OR location LIKE '%$value%'
        OR website LIKE '%$value%'
        OR contact LIKE '%$value%'
        OR paradeSched LIKE '%$value%'
        OR fieldSched LIKE '%$value%'
        OR comments LIKE '%$value%') AND ";
    }

 $query .= "1 ORDER BY name ASC, showYear DESC";
    $result = mysql_query($query, $connect);
       $rows = mysql_num_rows($result);
    echo "<h2>Festivals - $rows Results</h2>";

  while ($row = mysql_fetch_array($result)) {

        echo "<a href='/festivals_detail.php?f=".$row['f']."&highlight=".$search."'>".$row['showYear'] . " " .$row['name']."</a><br />";

  }
   echo "<p>";
} elseif ($type == "scores"){
$username = "ilmarch_libraria";
	$password = "ilmarching";
	$database = "ilmarch_library";

	$connect=mysql_connect ("localhost", $username, $password) or die ('I cannot connect to the database because: ' . mysql_error());
    mysql_select_db($database) or die( "Unable to select database");
      $query = "SELECT * FROM Scores
    WHERE ";

    foreach ($searchAND as $value) {
        $query .= "(name LIKE '%$value%'
        OR shortname LIKE '%$value%'
    OR city_town LIKE '%$value%'
    OR date LIKE '%$value%'
    OR class_1 LIKE '%$value%'
    OR class_2 LIKE '%$value%'
    OR class_3 LIKE '%$value%'
    OR class_4 LIKE '%$value%'
    OR class_5 LIKE '%$value%'
    OR class_6 LIKE '%$value%'
    OR class_7 LIKE '%$value%'
    OR class_8 LIKE '%$value%'
    OR class_9 LIKE '%$value%'
    OR class_10 LIKE '%$value%'
    OR grand_champ LIKE '%$value%') AND ";
    }
    $query .= "1 ORDER BY name ASC, date DESC";
    $result = mysql_query($query, $connect);

  $rows = mysql_num_rows($result);

    echo "<h2>Scores - $rows Results</h2>";

  while ($row = mysql_fetch_array($result)) {

        $year = explode("-", $row['date']);
        echo "<a href='/scores_indiv.php?c=".$row['c']."&highlight=".$search."'>".$year[0] . " " .$row['name']."</a><br />";

  }
   echo "<p>";
}


mysql_close($connect);
?>



<!--***************End Page Content***************-->

<?php imo_bottom(); ?>