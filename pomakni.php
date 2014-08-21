<?php
session_start();
if (($_COOKIE["igram"] == 1)&&(((int)$_POST["tip"] == 0)||((int)$_POST["tip"] == 4))) { // tip poteza #0 - povuci potez na polju i promijeni igraca
    
    // POST podaci o X,Y osi figurice koja se mice, tipu figurice, X,Y osi destinacije, tipu figurice na destinaciji
    $odabranaFigurica1 = $_POST["odabranaFigurica0"];
    $odabranaFigurica2 = $_POST["odabranaFigurica1"];
    $odabranaFigurica3 = trim(stripslashes(trim($_POST["odabranaFigurica2"],"'")),"'");
    $odabranaDestinacija1 = $_POST["odabranaDestinacija0"];
    $odabranaDestinacija2 = $_POST["odabranaDestinacija1"];
    $odabranaDestinacija3 = trim(stripslashes(trim($_POST["odabranaDestinacija2"],"'")),"'");
    
    if ((((int)$odabranaDestinacija1) == 1)&&($odabranaFigurica3 == "a")) {
        $odabranaFigurica3 = "g";
    }
    if ((((int)$odabranaDestinacija1) == 8)&&($odabranaFigurica3 == "q")) {
        $odabranaFigurica3 = "t";
    }
    
    if ((int)$_POST["tip"] == 4) {
        file_put_contents('sessioni/z_statistika.xml', "P", FILE_APPEND);
        $zamjena = $odabranaDestinacija3;
    }
    else {
        file_put_contents('sessioni/z_statistika.xml', "G", FILE_APPEND);
        $zamjena = "0";
    }
    
    // Otvaranje fajla za citanje i ucitavanje svog sadrzaja u string $stream
    $myFile = $_SESSION["logged"] . ".xml";
    $fp = fopen($myFile,"r");
    if (!flock($fp, LOCK_EX)) { 
        exit(-1);
    }
    $stream = fread($fp, filesize($myFile));
    fclose($fp);

    // Preracunavanje iz 8x8 sustava POST varijabli u sustav kakav je u fajlu (jedan red, "redovi" odvojeni razmacima
    $placeDestination = 2+9*((int)$odabranaDestinacija1-1)+(int)$odabranaDestinacija2;
    $placeFigure = 2+9*((int)$odabranaFigurica1-1)+(int)$odabranaFigurica2;
    if ($placeDestination < $placeFigure) {
        $placeChangeUp = $placeDestination;
        $placeChangeDown = $placeFigure;
    }
    else {
        $placeChangeDown = $placeDestination;
        $placeChangeUp = $placeFigure;
    }
    $newStreamBeginToUp = substr($stream, 0, $placeChangeUp-1);
    $newStreamBeginToDown = substr($stream, 0, $placeChangeDown);
    $newStreamUpToDown = substr($stream, $placeChangeUp, $placeChangeDown - $placeChangeUp - 1);
    $newStreamDownToEnd = substr($stream,  $placeChangeDown, strlen($stream) - $placeChangeDown - 2);
    $sljedeciIgrac = substr($stream, 102, 1);
    if (substr($stream, 102, 1) == "1")
        $sljedeciIgrac = "2";
    else 
        $sljedeciIgrac = "1";
    $newStreamDownToEnd = $newStreamDownToEnd . " " . $sljedeciIgrac;
    
    // Stvaranje novog stringa $string2 koji sadrzi novi izgled polja (0 umjesto figurice, figurica na destinaciji)
    if ($placeDestination < $placeFigure) {
        $string2 = $newStreamBeginToUp . $odabranaFigurica3 . $newStreamUpToDown . $zamjena . $newStreamDownToEnd;
    }
    else {
        $string2 = $newStreamBeginToUp . $zamjena . $newStreamUpToDown .  $odabranaFigurica3 . $newStreamDownToEnd;
    }
    
    // Upisivanje novih podataka u fajl
    $myFile = $_SESSION["logged"] . ".xml";
    $fp = fopen($myFile,"w");
    if (!flock($fp, LOCK_EX)) { 
        exit(-1);
     }
    fwrite($fp, $string2);
    fclose($fp);
    
    // Cuvanje statistike o pobjedama
    
    $crveniGubi = substr_count($string2,"q") + substr_count($string2,"w") + substr_count($string2,"e") + substr_count($string2,"r") + substr_count($string2,"t") + substr_count($string2,"z");
    if ($crveniGubi<2) {
        setcookie("kraj",true);
        file_put_contents('sessioni/z_statistika.xml', "Z", FILE_APPEND);
    }
    $zutiGubi = substr_count($string2,"a") + substr_count($string2,"s") + substr_count($string2,"d") + substr_count($string2,"f") + substr_count($string2,"g") + substr_count($string2,"h");
    if ($zutiGubi<2) {
        setcookie("kraj",true);
        file_put_contents('sessioni/z_statistika.xml', "C", FILE_APPEND);
    }
}
else if (($_COOKIE["igram"] == 1)&&((int)$_POST["tip"] == 1)) { // tip poteza #1 - samo promjeni igraca jer je isteklo vrijeme...
    file_put_contents('sessioni/z_statistika.xml', "T", FILE_APPEND);
    $myFile = $_SESSION["logged"] . ".xml";
    $fp = fopen($myFile,"r");
    if (!flock($fp, LOCK_EX)) { 
        exit(-1);
    }
    $stream = fread($fp, filesize($myFile));
    fclose($fp);
    if (substr($stream, 102, 1) == "1")
        $sljedeciIgrac = "2";
    else 
        $sljedeciIgrac = "1";
    $newStream = substr($stream,  0, strlen($stream) - 1);
    $newStream = $newStream . $sljedeciIgrac;
    $myFile = $_SESSION["logged"] . ".xml";
    $fp = fopen($myFile,"r+");
    if (!flock($fp, LOCK_EX)) { 
        exit(-1);
     }
    fwrite($fp, $newStream);
    fclose($fp);
}
else if (($_COOKIE["igram"] == 1)&&((int)$_POST["tip"] == 2)) { // tip poteza #2 - povuci kartu iz spila i promijeni igraca
    file_put_contents('sessioni/z_statistika.xml', "K", FILE_APPEND);
    $myFile = $_SESSION["logged"] . ".xml";
    $fp = fopen($myFile,"r");
    if (!flock($fp, LOCK_EX)) { 
        exit(-1);
    }
    $stream = fread($fp, filesize($myFile));
    fclose($fp);
    if (substr($stream, 102, 1) == "1") {
        $sljedeciIgrac = "2";
        $a = 74;
        $b = 79;
        $c = 88;
        $d = 93;
    }
    else {
        $sljedeciIgrac = "1";
        $a = 81;
        $b = 86;
        $c = 95;
        $d = 100;
    }
    
    for($i = $a; $i <= $b; $i++)
        if (substr($stream, $i, 1) == "0") {
            $beginToYourCards = substr($stream, 0, $i);
            $yourCardsToYourDeck = substr($stream, $i+1, $c-$i-1);
            break;
        }
    for($i = $c; $i <= $d; $i++)
        if (substr($stream, $i, 1) != "0") {
            $kartaKojuUzimas = substr($stream, $i, 1);
            $nonEmptyDeckCard = $i;
            $yourDeckToYourDeckCard = substr($stream, $c, abs($c-$nonEmptyDeckCard)) . "0";
            break;
        }
    $yourDeckCardToEnd = substr($stream, $nonEmptyDeckCard+1, strlen($stream)-$nonEmptyDeckCard - 2);
    $newStream = $beginToYourCards . $kartaKojuUzimas . $yourCardsToYourDeck . $yourDeckToYourDeckCard . $yourDeckCardToEnd . $sljedeciIgrac;
    
    $myFile = $_SESSION["logged"] . ".xml";
    $fp = fopen($myFile,"r+");
    if (!flock($fp, LOCK_EX)) { 
        exit(-1);
     }
    fwrite($fp, $newStream);
    fclose($fp);
}
else if (($_COOKIE["igram"] == 1)&&((int)$_POST["tip"] == 3)) { // tip poteza #3 - nije zapravo potez ali ajde - koristis kartu dakle umjesto nje stavi 0
    $karta = trim(stripslashes(trim($_POST["karta"],"'")),"'");
    $myFile = $_SESSION["logged"] . ".xml";
    $fp = fopen($myFile,"r");
    if (!flock($fp, LOCK_EX)) { 
        exit(-1);
    }
    $stream = fread($fp, filesize($myFile));
    fclose($fp);
    if (substr($stream, 102, 1) == "1") {
        $a = 74;
        $b = 79;
    }
    else {
        $a = 81;
        $b = 86;
    }
    
    for($i = $a; $i <= $b; $i++)
        if (substr($stream, $i, 1) == $karta) {
            $beginToYourCards = substr($stream, 0, $i);
            $yourCardsToTheEnd = substr($stream, $i+1, strlen($stream)-$i-1);
            break;
        }
        
    $newStream = $beginToYourCards . "0" . $yourCardsToTheEnd;
    $myFile = $_SESSION["logged"] . ".xml";
    $fp = fopen($myFile,"r+");
    if (!flock($fp, LOCK_EX)) { 
        exit(-1);
     }
    fwrite($fp, $newStream);
    fclose($fp);
}
 ?>