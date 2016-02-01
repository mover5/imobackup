<?php
echo "<p align='left'>";
echo "<div id='ddblueblockmenu'>";

echo "<div class='menutitle'>Console Actions</div>";
echo "<ul>";
echo "<li><a href='http://www.ilmarching.com'>ILMarching Home</a></li>";
echo "<li><a href='http://www.ilmarching.com/console'>Console Home</a></li>";
echo "<li><a href='changepassword.php'>Change Your Password</a></li>";
if (isset($_SESSION['role']) && get_username() == "moverholt") {
	echo "<li><a href='http://ilmarching.com:2082/3rdparty/phpMyAdmin/index.php?' target='_blank'>phpMyAdmin</a></li>";
}
echo "</ul>";
if (isset($_SESSION['role'])) {
	if ($_SESSION['role'] == "admin" || $_SESSION['role'] == "contributor") {
		echo "<div class='menutitle'>Bands</div>";
		echo "<ul>";
		echo "<li><a href='add_band.php'>Add a Band</a></li>";
		echo "<li><a href='modify_band.php'>Modify a Band</a></li>";
		echo "<li><a href='delete_band.php'>Delete a Band</a></li>";
		echo "<li><a href='add_show.php'>Add Yearly Info</a></li>";
		echo "<li><a href='modify_show.php'>Modify Yearly Info</a></li>";
		echo "<li><a href='delete_show.php'>Delete Yearly Info</a></li>";
		if ($_SESSION['role'] = "admin") {
			echo "<li><a href='batch_enrollment.php'>Batch Load Enrollment</a></li>";
			echo "<li><a href='modify_division.php'>Modify Division Limits</a></li>";
			echo "<li><a href='add_director_email.php'>Add Director Email</a></li>";
			echo "<li><a href='modify_director_email.php'>Modify Director Email</a></li>";
			echo "<li><a href='view_director_email.php'>View Director Emails</a></li>";
		}
		echo "<li><a href='admin_add_images.php'>Add Band Images</a></li>";
		echo "<li><a href='manage_images.php'>Manage Band Images</a></li>";
		echo "<li><a href='approve_images.php'>Approve Band Images</a></li>";
		echo "<li><a href='approve_videos.php'>Approve Band Videos</a></li>";
		echo "</ul>";

		echo "<div class='menutitle'>Festivals</div>";
		echo "<ul>";
		echo "<li><a href='add_fest.php'>Add a Festival</a></li>";
		echo "<li><a href='modify_fest.php'>Modify a Festival</a></li>";
		echo "<li><a href='delete_fest.php'>Delete a Festival</a></li>";
		echo "<li><a href='add_schedule.php'>Add a Schedule</a></li>";
		echo "<li><a href='modify_schedule.php'>Modify a Schedule</a></li>";
		echo "<li><a href='delete_schedule.php'>Delete a Schedule</a></li>";
		echo "</ul>";

		echo "<div class='menutitle'>Scores</div>";
		echo "<ul>";
		echo "<li><a href='add_scores.php'>Add Scores</a></li>";
		echo "<li><a href='modify_scores.php'>Modify Scores</a></li>";
		echo "<li><a href='delete_scores.php'>Delete Scores</a></li>";
		echo "</ul>";
		
		echo "<div class='menutitle'>Videos</div>";
		echo "<ul>";
		echo "<li><a href='add_video.php'>Add Video</a></li>";
		echo "<li><a href='modify_video.php'>Modify Video</a></li>";
		echo "<li><a href='delete_video.php'>Delete Video</a></li>";
		echo "</ul>";
	}
	if ($_SESSION['role'] == "admin" || $_SESSION['role'] == "moderator" || $_SESSION['role'] == "contributor") {
		echo "<div class='menutitle'>Biographies</div>";
		echo "<ul>";
		echo "<li><a href='add_bio.php'>Add Biography</a></li>";
		echo "<li><a href='modify_bio.php'>Modify Biography</a></li>";
		echo "<li><a href='delete_bio.php'>Delete Biography</a></li>";
		echo "</ul>";
	}
	if ($_SESSION['role'] == "admin") {
		echo "<div class='menutitle'>Users</div>";
		echo "<ul>";
		echo "<li><a href='add_user.php'>Add a User</a></li>";
		echo "<li><a href='modify_user.php'>Modify a User</a></li>";
		echo "<li><a href='delete_user.php'>Delete a User</a></li>";
		echo "</ul>";
	}
	if ($_SESSION['role'] == "admin") {
		echo "<div class='menutitle'>Ads</div>";
		echo "<ul>";
		echo "<li><a href='add_ad.php'>Add an Ad</a></li>";
		echo "<li><a href='modify_ad.php'>Modify an Ad</a></li>";
		echo "<li><a href='delete_ad.php'>Delete an Ad</a></li>";
		echo "</ul>";
	}
	if ($_SESSION['role'] == "admin") {
		echo "<div class='menutitle'>Bands of the Year</div>";
		echo "<ul>";
		echo "<li><a href='boty_results.php'>View Results!</a></li>";
		echo "<li><a href='boty_results_public.php'>View Public Results!</a></li>";
		echo "<li><a href='view_ballots.php'>View Ballots</a></li>";
		echo "<li><a href='add_voter.php'>Add a Voter</a></li>";
		echo "<li><a href='modify_boty_settings.php'>Modify BotY Settings</a></li>";
		echo "<li><a href='reset_boty.php'>Reset BotY Results</a></li>";
		echo "</ul>";
	}
}
echo "</div>";
?>
