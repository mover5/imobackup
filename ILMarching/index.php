<?php
require('./wp-blog-header.php');
require('layout.php');
require('connection.inc.php');
imo_top();
?>

<?php
 if ($posts) : foreach ($posts as $post) : start_wp(); ?>

<?php
$date = the_date('','<b><div class="post_date">','</b></div>', false);
if (isset($_GET['highlight'])) {
       $search = explode(" AND ", $_GET['highlight']);
       foreach ($search as $value) {
            $replace = "<FONT style=\"BACKGROUND-COLOR: #BBBBBB\">" . $value . "</FONT>";
            $date = str_ireplace($value, $replace, $date);
       }
    }
    echo $date;

?>
	
<div class="post">
	<div class="storytitle" id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php
    $title = the_title("","",false);
    if (isset($_GET['highlight'])) {
       $search = explode(" AND ", $_GET['highlight']);
       foreach ($search as $value) {
            $replace = "<FONT style=\"BACKGROUND-COLOR: #BBBBBB\">" . $value . "</FONT>";
            $title = str_ireplace($value, $replace, $title);
       }
    }
    echo $title;
    ?></a></div>
	<div class="meta"><?php _e("Filed under:"); ?> <?php the_category() ?> &#8212; <?php the_author() ?> @ <?php the_time() ?></div>

	<div class="storycontent">
		<?php 
			if ($_GET['p'] == "") {
				the_excerpt();
			}else{
                if (isset($_GET['highlight'])) {
                    $search = explode(" AND ", $_GET['highlight']);
                    $content = get_the_content();
                    $content = apply_filters('the_content', $content);
	                $content = str_replace(']]>', ']]&gt;', $content);
                    foreach ($search as $value) {
                        $replace = "<FONT style=\"BACKGROUND-COLOR: #BBBBBB\">" . $value . "</FONT>";
                        $content = str_ireplace($value, $replace, $content);
                    }
                    echo $content;

                } else {
                    the_content();
                }
			}
		?> 
		<a href="<?php the_permalink() ?>">(full article)</a><br><br>
	</div>
<p><center>
</center></p>	
	<!--
	<?php trackback_rdf(); ?>
	-->
</div>
<?php endforeach; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif;
imo_bottom();
 ?>

