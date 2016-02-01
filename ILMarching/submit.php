<?php 
require('./wp-blog-header.php'); 
require('./layout.php'); 
require("/home/ilmarch/sendgrid-php/ilmarching_sendgrid.php");
include('connection.inc.php'); 
if (isset($_POST['mailsubmit'])) {
if ($_POST['body'] <> "" && $_POST['subject'] <> "" && strtoupper($_POST['spam']) == "NOSPAM") {
    $subject = $_POST['subject'];
    $body = $_POST['body'];
    $tomark = "overholt.mark@gmail.com";
    $todan = "dbalash@gmail.com";
    $from = "From: Illinois Marching Online";
//    echo nl2br($body);
    sendEmail($tomark, $subject, $body);
    sendEmail($todan, $subject, $body);
    $message = "Thank you for submitting information to ILMarching";
} else if ($_POST['spam'] <> "NOSPAM") {
	$message = "You are a spam bot...";
} 
}
?> 
<?php imo_top(); ?> 
<!--***************Start Page Content***************--> 
<script type='text/javascript'>
function switchContent(id) {
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        drillxmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        drillxmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    drillxmlhttp.onreadystatechange=function()
    {
        if (drillxmlhttp.readyState==4 && drillxmlhttp.status==200)
        {
            document.getElementById("content").innerHTML=drillxmlhttp.responseText;
        }
    }
    drillxmlhttp.open("GET", "ajax/submit_pages.php?page="+id, true);
    drillxmlhttp.send();
}

function changeToBand() {
    document.getElementById("bandtab").className = "imo_tab_up";
    document.getElementById("festivaltab").className = "imo_tab_down";
    document.getElementById("scorestab").className = "imo_tab_down";
    document.getElementById("title").innerHTML = "Marching Band Submissions - Band Info";
    switchContent('BAND');
}

function changeToFestival() {
    document.getElementById("bandtab").className = "imo_tab_down";
    document.getElementById("festivaltab").className = "imo_tab_up";
    document.getElementById("scorestab").className = "imo_tab_down";
    document.getElementById("title").innerHTML = "Marching Band Submissions - Festival Info";
    switchContent('FESTIVAL');
}

function changeToScores() {
    document.getElementById("bandtab").className = "imo_tab_down";
    document.getElementById("festivaltab").className = "imo_tab_down";
    document.getElementById("scorestab").className = "imo_tab_up";
    document.getElementById("title").innerHTML = "Marching Band Submissions - Scores";
    switchContent('SCORES');
}

</script>

<h2 id='title' name='title'>Marching Band Submissions - Band Info</h2>
<?php
if (isset($message)) {
  echo "<font size='+1' color='#FF0000'>$message</font><p>";
}
?>
<table border='0'>
    <tr style='margin-bottom:0px;'>
    <td id='bandtab' name='bandtab' class='imo_tab_up' align='center' onclick='changeToBand()'>Bands</td>
    <td id='festivaltab' name='festivaltab' class='imo_tab_down' align='center' onclick='changeToFestival()'>Festivals</td>
    <td id='scorestab' name='scorestab' class='imo_tab_down' align='center' onclick='changeToScores()'>Scores</td>
</tr>
</table>
<form action='process_submit.php' method='POST'>
<table border='4'>
    <tr><td id='content' name='content'>
    </td></tr>
</table>
</form>
<script type='text/javascript'>
changeToBand();
</script>


<!--***************End Page Content***************-->
<?php imo_bottom(); ?>
