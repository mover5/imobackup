<?php

function help_balloon($text) {
    echo "<div class='help'><img src='images/icon-help.gif'><span>$text</span></div>";
}

function print_band_select($name) {
    require('connection.inc.php');
    $query = "SELECT * FROM Bands ORDER BY School ASC";
    $result = mysql_query($query, $connect);
    echo "<select name='$name' id='$name'>";
    echo "<option value='...'>...</option>";
        while ($row = mysql_fetch_array($result)) {
            $bandid = $row['BandID'];
            $school = $row['School'];
            echo "<option value='$bandid'>$school</option>";
        }
    echo "</select>";
}

function print_band_select_custom($name, $custom) {
    require('connection.inc.php');
    $query = $custom;
    $result = mysql_query($query, $connect);
    echo "<select name='$name' id='$name'>";
        while ($row = mysql_fetch_array($result)) {
            $bandid = $row['BandID'];
            $school = $row['School'];
            echo "<option value='$bandid'>$school</option>";
        }
    echo "</select>";
}

function print_festival_select($name) {
    require('connection.inc.php');
    $query = "SELECT * FROM Festivals ORDER BY FestivalYear DESC, Name ASC";
    $result = mysql_query($query, $connect);
    echo "<select name='$name' id='$name'>";
        while ($row = mysql_fetch_array($result)) {
            $festid = $row['FestivalID'];
            $name = $row['Name'];
            $festyear = $row['FestivalYear'];
            echo "<option value='$festid'>$festyear - $name</option>";
        }
    echo "</select>";
}

function print_festival_select_custom($name, $custom) {
    require('connection.inc.php');
    $query = $custom;
    $result = mysql_query($query, $connect);
    echo "<select name='$name' id='$name'>";
        while ($row = mysql_fetch_array($result)) {
            $festid = $row['FestivalID'];
            $name = $row['Name'];
            $festyear = $row['FestivalYear'];
            echo "<option value='$festid'>$festyear - $name</option>";
        }
    echo "</select>";
}



?>
