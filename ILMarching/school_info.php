<?php

echo "<div style='padding:5px 0px 2px 0px'><b>Director(s)</b></div>";
echo "<div style='padding:0px 0px 0px 8px'>$directors</div>";
echo "<div style='padding:5px 0px 2px 0px'><b>School Colors</b></div>";
echo "<div style='padding:0px 0px 0px 8px'>$colors</div>";

if(($website != "none")&&($website != "")){
	echo "<div align=center style='padding:10px 0px 5px 0px'><a href=$website target=_blank>Band web site</a></div>";
}


?>