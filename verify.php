<?php
session_start();
$myFile = $_SESSION["logged"] . ".xml";
$fp = fopen($myFile,"r");
fseek($fp, 102);
$trenutniIgracString = fread($fp, 1);
fclose($fp);
$trenutniIgrac = (int)$trenutniIgracString;
$igracbroj = $_COOKIE["jasamigracbroj"];
if ($trenutniIgrac == $igracbroj)
    setcookie("igram", 1);
else 
    setcookie("igram", 0);
$igra = explode("/",$_SESSION["logged"]);
echo "<h2>" . $igra[1] . "</h2>";
if ($trenutniIgrac == $igracbroj)
    echo "Na redu si!";
else
    echo "Nisi na redu.";
?>