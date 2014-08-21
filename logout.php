<?php
session_start(); 
$myFile = $_SESSION["logged"] . ".xml";
$fp = fopen($myFile,"r");
$igraciString = fread($fp, 1);
$igraci = (int)$igraciString;
$igraci = $igraci - 1;
fclose($fp);
if ($_COOKIE["kraj"] == true) {
    unlink($myFile);
    file_put_contents('sessioni/z_statistika.xml', "X", FILE_APPEND);
}
else {
    $fp = fopen($myFile,"r+");
    fwrite($fp, $igraci);
    fclose($fp);
    file_put_contents('sessioni/z_statistika.xml', "L", FILE_APPEND);
}
session_destroy();
header("Location: login_cro.php");
?>