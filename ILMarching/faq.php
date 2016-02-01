<?php 
require('./wp-blog-header.php'); 
require('./layout.php'); 
include('connection.inc.php'); 
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
    drillxmlhttp.open("GET", "ajax/faqcontent.php?id="+id, true);
    drillxmlhttp.send();
}

function changeToFAQ(id) {
    document.getElementById("etitab").className = "imo_tab_down";
    document.getElementById("faqtab").className = "imo_tab_up";
    document.getElementById("title").innerHTML = "Marching Band FAQ";
    switchContent(id);
}

function changeToETI(id) {
    document.getElementById("etitab").className = "imo_tab_up";
    document.getElementById("faqtab").className = "imo_tab_down";
    document.getElementById("title").innerHTML = "Marching Band Etiquette";
    switchContent(id);
}
</script>

<h2 id='title' name='title'>Marching Band FAQ</h2>
<table border='0'>
    <tr style='margin-bottom:0px;'>
    <td id='faqtab' name='faqtab' class='imo_tab_up' align='center' onclick='changeToFAQ(871)'>FAQ</td>
    <td id='etitab' name='etitab' class='imo_tab_down' align='center' onclick='changeToETI(873)'>Etiquette</td>
</tr>
</table>
<table border='4'>
    <tr><td id='content' name='content'>
    </td></tr>
</table>
<script type='text/javascript'>
changeToFAQ(871);
</script>


<!--***************End Page Content***************-->
<?php imo_bottom(); ?>
