<?php

require('./wp-blog-header.php');
require('layout.php');
require('connection.inc.php');
require('common_functions.php');

imo_top();

include('js/upload_video.jquery');

if (isset($_POST['bandid'])) {
    echo "<script>";
    echo "$(document).ready(function() {var id = $('select#bandid option[value=".$_POST['bandid']."]').attr('selected', 'selected');})";
    echo "</script>";
}
if (isset($_POST['festid'])) {
    echo "<script>";
    echo "$(document).ready(function() {var id = $('select#festid option[value=".$_POST['festid']."]').attr('selected', 'selected');})";
    echo "</script>";
}
?>

<h1>Submit a Video to ILMarching</h1>
<div id='message' class='message'></div>
<div class='grid'>
    <div class='row'>
        <div class='label'>Band:</div>
        <div class='cell'><?php print_band_select("bandid"); ?></div>
        <div class='help'><img src='images/icon-help.gif'><span>Please select the Band performing in the video.</span></div>
    </div>
    <div class='space'></div>
    <div class='row'>
        <div class='label'>Festival:</div>
        <div class='cell'><?php print_festival_select("festid"); ?></div>
        <?php help_balloon("Please select the Festival where this band performed. Remember to select to correct year."); ?>
    </div>
    <div class='space'></div>
    <div class='row'>
        <div class='label'>Type:</div>
        <div class='cell'>
            <select id='type' name='type'>
                <option value='Prelim' selected='selected'>Prelims</option>
                <option value='SemiFinals'>SemiFinals</option>
                <option value='Finals'>Finals</option>
            </select>
        </div>
        <?php help_balloon("If this performance was a Semifinals or a Finals performance, indicate so here. Otherwise, leave this as Prelims."); ?>
    </div>
    <div class='space'></div>
    <div class='row'>
        <div class='label'>Youtube URL:</div>
        <div class='cell'><input style='margin-left:5px;' type='text' name='url' id='url' size='43'/><button id='preview' class='button_submit'>Preview Video</button></div>
        <?php help_balloon("This is the Youtube URL of the video. The video must already be uploaded to Youtube in order to submit it to ILMarching."); ?>
    </div>
    <div class='space'></div>
    <div class='row'>
        <div class='cell'><button id='submit' class='button_submit'>Submit</button></div>
    </div>
    
</div>
<p>
<div>NOTE: Make sure to click the "Preview Video" button. If your video does not play here, it won't be added to our collection.</div>
<div>Please use real YouTube links, not the 'embed' links. Example: "https://www.youtube.com/watch?v=Lnf3ai37A-s"</div>
<p>
<div id="previewvideo">
<iframe id='videoframe' name='videoframe' width='450' height='350' src='' frameborder='0' allowfullscreen></iframe>
</div>

<?php
imo_bottom();
?>
