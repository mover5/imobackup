<?php
require('./wp-blog-header.php');
require('layout.php');
require('connection.inc.php');
imo_top();
?>
<iframe src="http://www.highschoolcube.com/embed/364150" width="960" height="640" frameborder="0" scrolling="no" allowtransparency="true" allowfullscreen mozallowfullscreen webkitallowfullscreen></iframe><div><a style="font-size:11px" href="http://www.highschoolcube.com">Watch, Share and Broadcast High School Events LIVE - in HD - for free!</a></div>
<h2>Pontiac Schedule</h2>
<?php
$FestivalID = 596;
include("display_schedule.php");
?>
<?php
imo_bottom();
?>

