<?php
error_reporting(E_ALL ^ E_NOTICE);

if (($_GET["ac"]=="klik")||($_GET["ac"]=="log")) {
    session_start();
    login();
}

// Funkcija koja provjerava ima li fajla koji opisuje plocu na trenutnom SESSIONu, dodaje +1 igraca i postavlja $_SESSION["logged"] na tu plocu

function login() {
    global $_SESSION, $USERS;
    $myDir = getcwd() . "/sessioni/";
    $files = scandir($myDir);
    $k = 0;
    $igraciString = "";
    foreach ($files as $f) {
        $k++;
        if ($k>2) {
            $naziv = explode(".", $f);
            if ($naziv[1]=="xml") {
                $ime = "sessioni/" . $naziv[0] . ".xml";
                $fp = fopen($ime,"r+");
                $igraciString = fread($fp, 1);
                $USERS["sessioni/" . $naziv[0]] = (int)$igraciString;
                fclose($fp);
            }
        }
    }
    if (($_GET["ac"]=="log")||($_GET["ac"]=="klik")) {
        $ime = "sessioni/" . $_GET["password"] . ".xml";
        if ($USERS["sessioni/" . $_GET["password"]]==2) {
            header("Location: logout_eng.php");
        }
        else if ($USERS["sessioni/" . $_GET["password"]] == 1) {
            $fp = fopen($ime,"r+");
            fwrite($fp, ($USERS["sessioni/" . $_GET["password"]]+1));
            $USERS["sessioni/" . $_GET['password']] = 2;
            
        } else {
              if (file_exists($ime)) {
                $fp = fopen($ime,"r+");
                fwrite($fp, "1");
              }
              else {
                $fp = fopen($ime,"w");
                $shuffled1 = str_shuffle("yxcvyc");
                $shuffled2 = str_shuffle("yxcvyc");
                fwrite($fp, "1 w0er0000 qqqq0000 00000000 00000000 00000000 00000000 0000aaaa 0000fd0s 000000 000000 " . $shuffled1 . " " . $shuffled2 . " 1");    
              }
              $USERS["sessioni/" . $_GET['password']] = 1;
         };
         fclose($fp);
         $_SESSION["logged"]="sessioni/" . $_GET["password"];
         setcookie("jasamigracbroj", $USERS["sessioni/" . $_GET['password']]);
     }
     setcookie("kraj", false);
     if (array_key_exists($_SESSION["logged"],$USERS)) {
        file_put_contents('sessioni/z_statistika.xml', "N", FILE_APPEND);
        header("Location: game_eng.php");
     }
};

// Funkcija koja provjerava ima li fajla koji opisuje plocu na trenutnom SESSIONu, ako ne, vraca te na login

function check_logged() {
    global $_SESSION, $USERS;
    $myDir = getcwd() . "/sessioni/";
    $files = scandir($myDir);
    $k = 0;
    foreach ($files as $f) {
        $k++;
        if ($k>2) {
            $naziv = explode(".", $f);
            if ($naziv[1]=="xml") {
                $myFile = "sessioni/" . $naziv[0] . ".xml";
                $fp = fopen($myFile,"r+");
                $igraciString = fread($fp, 1);
                $igraci = (int)$igraciString;
                $USERS["sessioni/" . $naziv[0]] = $igraci;
                fclose($fp);
            }
        }
    }
    if (!array_key_exists($_SESSION["logged"],$USERS))
         header("Location: logout_eng.php");
};
?>