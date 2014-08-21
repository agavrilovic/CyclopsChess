<?php 
function createRandomWord() {
    list($usec, $sec) = explode(' ', microtime());
    srand((float) $sec + ((float) $usec * 100000));
    $randval = rand(0,19);
    $rijeci = array("vjeverica", "pas", "krava", "gitara", "stolac", "bumbar", "majica", "vjetar", "sunce", "more", "jastuk", "pijetao", "papir", "igra", "tajna", "pobjeda", "kava", "knjiga", "kopno", "svemir");
    $rijec = $rijeci[$randval] . rand(10,99);
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
                fclose($fp);
                $igraci = (int)$igraciString;
                $USERS["sessioni/" . $naziv[0]] = $igraci;
            }
        }
    }
    if (!array_key_exists($rijec,$USERS))
         echo $rijec;
     else
        createRandomWord();
};

function listSessions() {
    $myDir = getcwd() . "/sessioni/";
    $files = scandir($myDir);
    $k = 0;
    $kasnijeZatvoriUl = false;
    foreach ($files as $f) {
        $k++;
        if ($k>2) {
            $naziv = explode(".", $f);
            if (($k == 3)&&($naziv[0]!="z_statistika")) {
                echo "<h2>Popis otvorenih igara</h2><ul>";
                $kasnijeZatvoriUl = true;
            }
            if ($naziv[1]=="xml") {
                $myFile = "sessioni/" . $naziv[0] . ".xml";
                $fp = fopen($myFile,"r+");
                $igraciString = fread($fp, 1);
                fclose($fp);
                $igraci = (int)$igraciString;
                $USERS["sessioni/" . $naziv[0]] = $igraci;
                if ($naziv[0] == "z_statistika")
                    echo "";
                else if ($igraci == 2)
                    echo "<li>" . $naziv[0] . " s " . $igraci . " igraca - igra u tijeku</li>";
                else
                    echo "<li><a href='passwords.php?ac=klik&password=" . $naziv[0] . "'>" . $naziv[0] . " - ceka se na igraca!</a></li>";
            }
        }
    }
    if ($kasnijeZatvoriUl == true)
        echo "</ul>";
};
?>
<!DOCTYPE html>
<html lang="hr">
 <head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="keywords" content="chess" >
    <meta name="author" content="Aleksandar Gavrilovic" >
    <meta name="description" content="Igra" >
	<link rel="stylesheet" href="style.css" />
    <link rel="shortcut icon" href="slike/w.png">
        <title>CyclopsChess</title>
 </head>
<body>
    <!--h1><img src="slike/s.png" />CyclopsChess<img src="slike/w.png" /></h1-->
    <img src="slike/login.jpg" />
    <p>Izmedu saha i jave, nalazi se CyclopsChess! Mozda ima duplo manje figura, ali zato ima duplo vise zabave!</p>
     <form action="passwords.php" method="get" >
        <input type="hidden" name="ac" value="log" >
        <input type="hidden" name="password" value=<?php createRandomWord(); ?> >
        <center><input class="myButton" type="submit" value="Zapocni igru"></input></center>
    </form>
    <h2>Pravila</h2>
    <p>Svaki igrac ima neke figure i karte pomocu kojih pokusava pobjediti drugog igraca tako da svojim figurama unisti sve protivnicke figure. Onaj kojem ostane samo jedna figura gubi igru.</p>
    <p>Igrac na potezu moze povuci kartu ili napraviti potez. Potez ide po sljedecem redu:</p>
    <ol><li>aktiviranje karte (opcionalno)</li>
    <li>micanje jedne od figura</li></ol>
    <p>Ako jedan od goblina dogura ziv do kraja ploce, postaje Kiklop koji je najmocnija figura <del>na svijetu</del> u igri.</p>
    <?php listSessions(); ?>
    
 </body>
</html>