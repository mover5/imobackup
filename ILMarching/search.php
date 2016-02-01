<?php
require('./wp-blog-header.php');
require('./layout.php');
include('connection.inc.php');
?>
<?php imo_top(); ?>
<!--***************Start Page Content***************-->

<?php $search = $_REQUEST['search'];
$searchAND = explode(" AND ", $search);?>
<h3>Search Results - "<?php echo $search; ?>" </h3>


<?php
  $database = "ilmarch_wordpress";

  mysql_select_db($database, $connect) or die( "Unable to select database");


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
  $count = 0;
  if ($rows > 10) {
    echo "<h2><a href='searchIndiv.php?search=$search&type=blog'>Blog Posts - $rows Results</a></h2>";
  } else {
    echo "<h2>Blog Posts - $rows Results</h2>";
  }
  while ($row = mysql_fetch_array($result)) {
    if ($count < 10) {
        echo "<a href='http://www.ilmarching.com/?p=".$row['ID']."&highlight=".$search."'>".$row['post_title']."</a><br />";
        $count++;
    }
  }
  echo "<p>";
//BANDS
	$database = "ilmarch_library";
    mysql_select_db($database, $connect) or die( "Unable to select database");

    $query = "SELECT * FROM Bands WHERE ";

    foreach ($searchAND as $value) {
      $query .= "(School LIKE '%$value%'
      OR BandName LIKE '%$value%'
      OR Town LIKE '%$value%'
      OR Directors LIKE '%$value%'
      OR Colors LIKE '%$value%'
      OR WebsiteURL LIKE '%$value%'
      OR PicURL LIKE '%$value%'
      OR Address LIKE '%$value%'
      OR Notes LIKE '%$value%') AND ";

    }
    $query .= "1 ORDER BY School ASC";

    $result = mysql_query($query, $connect);

  $rows = mysql_num_rows($result);
  $count = 0;
  if ($rows > 10) {
    echo "<h2><a href='searchIndiv.php?search=$search&type=band'>Bands - $rows Results</a></h2>";
  } else {
    echo "<h2>Bands - $rows Results</h2>";
  }
  while ($row = mysql_fetch_array($result)) {
    if ($count < 10) {
        echo "<a href='bands_indiv.php?BandID=".$row['BandID']."&highlight=".$search."'>".$row['School']."</a><br />";
        $count++;
    }
  }
   echo "<p>";
//Reps
       $query = "SELECT BandShows.BandID, School, Title, Year";


    $query .= " FROM BandShows, Bands WHERE BandShows.BandID = Bands.BandID AND ";

    foreach ($searchAND as $value) {
      $query .= "(Title LIKE '%$value%'";
      $query .= ") AND ";
    }


    $query .= "1 ORDER BY School ASC";
    $result = mysql_query($query, $connect);

  $rows = mysql_num_rows($result);
  $count = 0;
  if ($rows > 10) {
    echo "<h2><a href='searchIndiv.php?search=$search&type=show'>Shows - $rows Results</a></h2>";
  } else {
    echo "<h2>Shows - $rows Results</h2>";
  }
  while ($row = mysql_fetch_assoc($result)) {
    foreach ($row as $key => $value) {
      if ($key <> "School" && strstr(strtoupper($value), strtoupper($search))) {
            if ($count < 10) {
                $showyear = $row['Year'];
                echo "<a href='bands_indiv.php?BandID=".$row['BandID']."&year=".$showyear."&highlight=".$search."'>".$row['School']. " - " . $showyear ."</a><br />";
                $count++;
            }
      }
    }
  }

  echo "<p>";

  //Awards
    $query = "SELECT BandShows.BandID, School, Awards, Year";


    $query .= " FROM BandShows, Bands WHERE BandShows.BandID = Bands.BandID AND ";

    foreach ($searchAND as $value) {
      $query .= "(Awards LIKE '%$value%'";
      $query .= ") AND ";
    }


    $query .= "1 ORDER BY School ASC";

    $result = mysql_query($query, $connect);

  $rows = mysql_num_rows($result);
  $count = 0;
  if ($rows > 10) {
    echo "<h2><a href='searchIndiv.php?search=$search&type=award'>Awards - $rows Results</a></h2>";
  } else {
    echo "<h2>Awards - $rows Results</h2>";
  }
  while ($row = mysql_fetch_assoc($result)) {
    foreach ($row as $key => $value) {
      if ($key <> "School" && strstr(strtoupper($value), strtoupper($search))) {
            if ($count < 10) {
                $showyear = $row['Year'];
                echo "<a href='bands_indiv.php?BandID=".$row['BandID']."&year=".$showyear."&highlight=".$search."'>".$row['School']. " - " . $showyear ."</a><br />";
                $count++;
            }
      }
    }
  }

  echo "<p>";
  $query = "SELECT * FROM Festivals WHERE ";
    foreach ($searchAND as $value) {
        $query .= "(Name LIKE '%$value%'
        OR Date LIKE '%$value%'
        OR Location LIKE '%$value%'
        OR WebsiteURL LIKE '%$value%'
        OR Contact LIKE '%$value%'
        OR Details LIKE '%$value%') AND ";
    }

 $query .= "1 ORDER BY Name ASC, FestivalYear DESC";
 $result = mysql_query($query, $connect);

  $rows = mysql_num_rows($result);
  $count = 0;
  if ($rows > 10) {
    echo "<h2><a href='searchIndiv.php?search=$search&type=festival'>Festivals - $rows Results</a></h2>";
  } else {
    echo "<h2>Festivals - $rows Results</h2>";
  }
  while ($row = mysql_fetch_array($result)) {
    if ($count < 10) {
        echo "<a href='/festivals_detail.php?FestivalID=".$row['FestivalID']."&highlight=".$search."'>".$row['FestivalYear'] . " " .$row['Name']."</a><br />";
        $count++;
    }
  }
   
?>




<!--***************End Page Content***************-->

<?php imo_bottom(); ?>
