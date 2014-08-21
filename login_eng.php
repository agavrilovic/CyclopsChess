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
                echo "<h2>List of current games</h2><ul>";
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
                    echo "<li>" . $naziv[0] . " s " . $igraci . " players - game in progress</li>";
                else
                    echo "<li><a href='passwords_eng.php?ac=klik&password=" . $naziv[0] . "'>" . $naziv[0] . " - waiting for a player!</a></li>";
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
    <p>Between chess and a something else, there is CyclopsChess! Maybe it has half the chess pieces, but it has double the fun!</p>
     <form action="passwords_eng.php" method="get" >
        <input type="hidden" name="ac" value="log" >
        <input type="hidden" name="password" value=<?php createRandomWord(); ?> >
        <center><input class="myButton" type="submit" value="Start the game"></input></center>
    </form>
    <h2>Rules</h2>
    <p>Each player uses his pieces and cards to defeat his opponent by destroying all his pieces. The player who ends with only one chess piece in the game loses.</p>
    <p>A player can use his turn to either draw a card or make a move, A move goes like this:</p>
    <ol><li>use a card (optional)</li>
    <li>move a piece</li></ol>
    <p>If a goblin moves to the end of the board, he becomes a Cyclops, the most powerful piece in the <del>world</del> game.</p>
    <?php listSessions(); ?>
    
 </body>
</html>