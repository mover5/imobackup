<?php
require('./wp-blog-header.php');
require('./layout.php');
include('connection.inc.php');
?>
<?php imo_top(); ?>
<!--***************Start Page Content***************-->
<p>
<h3>IL Marching Online Message Board</h3></p>
<h2><a href="http://s8.zetaboards.com/ilmarching/index/">Click here to continue to the Message Boards!</a></h2>
<p>
<?php
				
				mysql_select_db("ilmarch_wordpress", $connect);
				$result = mysql_query("SELECT * FROM wp_posts WHERE ID=396");
				$row = mysql_fetch_array($result);
				echo $row['post_content'];
				mysql_close($connect);
		?>
<!--***************End Page Content***************-->

<?php imo_bottom(); ?>
