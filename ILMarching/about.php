<?php
require('./wp-blog-header.php');
require('./layout.php');
include('connection.inc.php');
imo_top();
?>
<p>
<h3>About Illinois Marching Online</h3>

<?php
    mysql_select_db("ilmarch_wordpress", $connect);
    $query = "SELECT * FROM wp_posts WHERE ID=386";
    $result = mysql_query($query, $connect);
    $row = mysql_fetch_array($result);
    echo $row['post_content'];

?>

<p></p>
<hr>
<?php
    $database = "ilmarch_library";
    mysql_select_db($database, $connect);
    $query = "SELECT * FROM Users ORDER BY Role, RealName";
    $result = mysql_query($query, $connect);
    $admin = "";
    $contrib = "";
    $mod = "";
    while ($row = mysql_fetch_array($result)) {
        if ($row['Role'] == 'admin') {
            $admin = $admin . "<a href=\"bio.php?UserID=".$row['UserID']."\">".$row['RealName']."</a><br />";
        } elseif ($row['Role'] == 'moderator') {
            $mod = $mod . "<a href=\"bio.php?UserID=".$row['UserID']."\">".$row['RealName']."</a><br />";
        } elseif ($row['Role'] == 'contributor') {
            $contrib = $contrib . "<a href=\"bio.php?UserID=".$row['UserID']."\">".$row['RealName']."</a><br />";
        }
    }
?>

<p>
<center>
<h2>Meet the IL Marching Staff</h2>
<table border=0>
<tr><td align='center' width='200'><b><u>Administrators</u></b><br /></td><td align='center' width='200'><b><u>Contributors</u></b><br /></td><td align='center' width='200'><b><u>Moderators</u></b><br /></td></tr>
<tr><td valign='top' align='center' width='200'><?php echo $admin;?></td><td valign='top' align='center' width='200'><?php echo $contrib;?></td><td valign='top' align='center' width='200'><?php echo $mod;?></td></tr>
</table>
</center>

<?php imo_bottom(); ?>
