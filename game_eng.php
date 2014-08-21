<?php
session_start(); // ovo bas mora biti prije svega zbog watashijevog wtf servera... inace na mom serveru moze stajati i dolje.
$igra = explode("/",$_SESSION["logged"]);
$to      = 'studiospektar@gmail.com';
$subject = 'Game:' . $igra[1] . ' @ CyclopsChess';
$message = 'On lagcity.net/cyclopschess a game started with the name ' . $igra[1] . '.';
$headers = 'From: CyclopsChess@Lagcity.net' . "\r\n" .
    'Reply-To: aleksandar.gavrilovich@gmail.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
mail($to, $subject, $message, $headers);
$to      = 'aleksandar.gavrilovich@gmail.com';
mail($to, $subject, $message, $headers);
?>
<!DOCTYPE html>
<html lang="hr">	
 <head>
  <meta charset="utf-8">
  <link rel=StyleSheet href="style.css" type="text/css" media=screen> 
  <?php
  if ($_COOKIE["jasamigracbroj"] == 1)
    echo '<link rel="shortcut icon" href="slike/s.png">';
  else
    echo '<link rel="shortcut icon" href="slike/w.png">';
  ?>
  <title>CyclopsChess</title>
 </head>
 <body onload="start();">
  <?php
  include("passwords.php");
  check_logged();
   
  // dinamicno stvara tablicu 8x8 (moglo je i van phpa)
  echo "<table id='divZaTablicu'>" . "\r\n"; 
  for($i=1;$i<9;$i++) {
    echo "<tr>" . "\r\n";
    for($j=1;$j<9;$j++) {
        echo "<td>" . "\r\n";
        if ((($i % 2 == 1) && ($j % 2 == 0)) || (($i % 2 == 0) && ($j % 2 == 1)))
            echo "<div class='poljeBelo' id='" . $i . $j . "'></div>";
        else
            echo "<div class='poljeCrno' id='" . $i . $j . "'></div>";
        echo "</td>";
    }
    echo "</tr>" . "\r\n";
    }
    echo "</table>" . "\r\n";
   
    // dinamicno stvara popis karata (moglo je i van phpa)
    echo "<div id='divZaKarte'>" . "\r\n"; 
    if ($_COOKIE["jasamigracbroj"] == 1) {
       echo "<div>" . "\r\n";
       echo "<span class='divZaJednuKartu' id='spil'></span>"  . "\r\n";
       for($i=1;$i<=6;$i++) {
        echo '<span class="divZaJednuKartu" id="k1' . $i . '"></span>' . "\r\n"; 
       }
       echo "</div>" . "\r\n";
    }
    else {
       echo "<div>" . "\r\n";
       echo "<span class='divZaJednuKartu' id='spil'></span>"  . "\r\n";
       for($i=1;$i<=6;$i++) {
           echo '<span "divZaJednuKartu" id="k2' . $i . '"></span> ' . "\r\n"; 
       }
       echo "</div>" . "\r\n";
    }
   echo "</div>" . "\r\n";
   echo '<div id="gumb"><a href="logout.php"><button>Save and exit</button></a></div>' . "\r\n";
   
   ?>
  <script>
 
 // ******************
 // GLOBALNE VARIJABLE 
 // ******************
 var thebigstring = "1 w0er0000 qqqq0000 00000000 00000000 00000000 00000000 0000aaaa 0000fd0s 000000 000000 yxcvyc yxcvyc 1";
 var maxTimeLeft = 6000;
 var selektiranaKarta = "0";
 var timeLeft = maxTimeLeft;
 var kraj = false; // kraj igre, ako je true nema vise nicega
 var nekiString = "tik"; // tik-tak - sat
 var igracbroj = <?php echo $_COOKIE["jasamigracbroj"] ?>; // koji si ti igrac, 1 ili 2
 var naPotezu = 0; // koji igrac je trenutno na potezu, 1 ili 2
 var odabranaFigurica = new Array(3); // x,y os i vrsta selektirane figurice
 var selectedFigure = false; // je li selektirana neka figurica trenutno
 var selectedCard = false; // je li selektirana neka figurica trenutno
 var odabranaDestinacija = new Array(3); // x,y os i vrsta figurice na odabranoj destinaciji
 odabranaFigurica[2] = '0'; // nista nije selektirano u pocetku
 var pomakJeDopusten = false; // ako je micanje figurice dopusteno, krece se s pomakni.php
 var zutoX = -1; // y koordinata polja za osvijetliti kad je selected
 var zutoY = -1; // y koordinata polja za osvijetliti kad je selected
 var ZutoK = -1; // karta koja se pozuti
 var alldata; // tu se sprema sav data iz xml fajla na serveru
 var polje = new Array(9); // tu se sprema samo 8x8 sahovska ploca
 for (var i = 0; i < 10; i++) {
            polje[i] = new Array(9);
        }
 var karte = new Array(3);
 
 var pravila = new Array(10);
 pravila[0] = "<p>Each player uses his pieces and cards to defeat his opponent by destroying all his pieces. The player who ends with only one chess piece in the game loses.</p><p>A player can use his turn to either draw a card or make a move, A move goes like this:</p><ol><li>use a card (optional)</li><li>move a piece</li></ol>";
 
 pravila[1] = "<p>Goblins are the basis of any good army for two reasons: They are many and they can turn into Cyclops. There are legends of people who had armies without goblins, but none of those people lived to the end of the story.</p><p>A goblin can move 1 or 2 spaces ahead. When it passes to the opponents half of the board, it can only move one space ahead. A goblin can attack an opponent one diagonal space in front of himself. If one of the goblins makes it alive to the end of the board, he becomes a Cyclops, the most powerful piece <del>on earth</del> in the game.</p>";
 
 pravila[2] = "<p>That is a pirate.</p><p>Slow, but merciless, a pirate is the master of coordination as long as he has enough troops around and the sweat from his forehead doesn't fall in his eyes, which he can't wipe off because he has a hook for a hand.</p><p>The pirate can move or eat in any direction, but only one space.</p>";

 pravila[3] = "<p>That is a ninja.</p><p>Quiet and mistical, ninjas are masters of sudden attack when least expected. Unless they are wearing yellow or red uniforms, then you can see them a mile away.</p><p>A ninja can move in any direction diagonally. The amount of space is limited by the board borders, the other pieces and your tactical genious.</p>";
 
 pravila[4] = "<p>That is a robot.</p><p>A golem, an automaton, a robot, call him any way you wish, but me careful because when he starts stomping, nobody is safe, except maybe your own pieces and the ones not in the game.</p><p>The robot can move vertically or horisontally until it reaches an impass or an opponent to attack.</p>"
 
 pravila[5] = "<p>That is a cyclops</p><p><p>The fear of his surroundings, a cyclops is an obvious danger and everybody should keep an eye on him because he can use the situation to his advantage in the blink of an eye. </p><p>He can move in any direction. We do not answer for trauma induced by the cyclops.</p>";
 
 pravila[6] = "<p>This card replaces the pirates wooden leg for a steam engine which doesn't really work, but has a small timemachine which sometimes works.</p><p>Activating this card makes the pirate go wild and he can make a double move, that means move two spaces instead of one. If he fights an opponent piece, he has to stop.</p>";
 
 pravila[7] = "<p>This card displays itself to the goblin who then starts running faster. Do not display upside down because he will run slower!</p><p>A goblin can move forward one space more than he would normally be able to move. For example, if he can move 2 spaces, with this card he can move 3 spaces.</p>";
 
 pravila[8] = "<p>The ninjas are not only quiet and deadly like a fart, they are masters of disguise. A ninja once tried to disguise himself as a goblin, but while the goblin started turning into a cyclops, the ninja turned into a dead gepard.</p><p>The ninja can switch places with any other piece you own.</p>";
 
 pravila[9] = "<p>Deep from inside the goblin hell, ghosts of crazed robots return goblins to their unfinished duty. This ability is dangerous and is not recommended for use near elderly and pregnant. Luckily, few people are both elderly and pregnant.</p><p>A dead goblin returns to one of the spaces where your goblins start on the board. He can not be put on a place that is already taken by another piece or that directly endangers an opponents piece.</p>";
 
 // ****************
 // funkcija start() se pokrece èim se uèita BODY html-a
 // ****************
 
 function start() { 
    document.getElementById("selected").innerHTML = pravila[0];
    refreshHandle = setTimeout(refreshaj, 1000);
    setTimeout(svakihParSekundi, 500+Math.random()*1000); // pokrene se funkcija koja svakih par sekundi provjeri je li protivnik odigrao potez
 }
 
 // **************** 
 // funkcija svakihParSekundi() je ajax koji asinhrono pokreæe verify_eng.php (u POST izdanju) i provjerava tko je na potezu (citajuci xml file...)
 // ****************
 
 function svakihParSekundi() {
    var xhr;
    var xmlhttp;
    xmlhttp=new XMLHttpRequest();
    if (window.XMLHttpRequest) {
        xhr = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) {
        xhr = new ActiveXObject("Msxml2.XMLHTTP");
    }
    else {
        throw new Error("Ajax is not supported by this browser");
    }
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if ((xhr.status == 200) && (xhr.status < 300)) {
                document.getElementById('content_status').innerHTML = xhr.responseText;
            }
        }
    }
    xhr.open('POST', 'verify_eng.php');
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("");
    
    if (igracbroj == naPotezu) {
        timeLeft -= 1;
        document.getElementById("vrijeme").innerHTML = "Your move is over in " + timeLeft + " seconds. ";
    }
    else {
        document.getElementById("vrijeme").innerHTML = "";
    }
    
    if (timeLeft == 0) {

        saljiPomak();
        timeLeft = maxTimeLeft;
    }
    
    if (kraj == false)
        verifyHandle = setTimeout(svakihParSekundi, 2000);
 }
 
 function saljiPomak() {
    var xhr3;
    if (window.XMLHttpRequest) {
        xhr3 = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) {
        xhr3 = new ActiveXObject("Msxml2.XMLHTTP");
    }
    else {
        throw new Error("Ajax is not supported by this browser");
    }
    xhr3.open('POST', 'pomakni.php');
    xhr3.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr3.send("tip=1");
    if (naPotezu == 1)
        naPotezu = 2;
    else
        naPotezu = 1;
 }
 
 
 // **************** 
 // funkcija refreshaj() je ajax koji asinhrono GET-a xml file trenutne igre i time popuni npr. array zvan polje[][] koji kaze gdje je koja figurica
 // ****************
 
 function refreshaj() {
    var xmlhttp;
    xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function() {
        if ((xmlhttp.readyState==4) && (xmlhttp.status==200)) {
            thebigstring = xmlhttp.responseText;
            alldata = thebigstring.split("");
            
            //alfa i omega najvaznije, ajmo odmah
            naPotezu = alldata[102];
            var brojigraca = alldata[0];
            
            var k = 2;
            for (i = 1; i < 9; i++) {
                for (j = 1; j < 9; j++) {
                    polje[i][j] = alldata[k];
                    if (selectedFigure == false) {
                        document.getElementById(""+i+j).innerHTML = "<input type='image' onClick=\"klikNaPolje(" + i + "," + j + "," + "'" + alldata[k] + "'" + ");\" src="    + "'slike/" + alldata[k] + ".png'" + " />"; 
                    }
                    else {
                        if ((zutoX == j)&&(zutoY==i)) {
                            document.getElementById(""+i+j).innerHTML = "<input type='image' onClick=\"klikNaPolje(" + i + "," + j + "," + "'" + alldata[k] + "'" + ");\" src="    + "'slike/" + alldata[k] + "2.png'" + " />"; 
                        }
                        else {
                            document.getElementById(""+i+j).innerHTML = "<input type='image' onClick=\"klikNaPolje(" + i + "," + j + "," + "'" + alldata[k] + "'" + ");\" src="    + "'slike/" + alldata[k] + ".png'" + " />"; 
                        }
                    }
                    k++;
                }
                k++;  
            }
            var karte = new Array(5);
            karte[1] = new Array(7);
            karte[2] = new Array(7);
            karte[3] = new Array(7);
            karte[4] = new Array(7);
            var brojKarata = new Array(5);
            brojKarata[1] = 0; // karte zutog
            brojKarata[2] = 0; // karte crvenog
            brojKarata[3] = 0; // spil zutog
            brojKarata[4] = 0; // spil crvenog
            for (i = 1; i < 5; i++) {
                for (j = 1; j <= 6; j++) {
                    karte[i][j] = alldata[k];
                    if (karte[i][j]!="0") {
                        brojKarata[i]+=1;
                        if (igracbroj == i) {
                            console.log(karte[i][j]);
                            if (selectedCard == false) {
                                document.getElementById("k"+i+j).innerHTML = "<input type='image' onClick=\"klikNaKartu(" + j + ", '" + alldata[k] + "');\" src=" + "'slike/" + alldata[k] + ".png'" + " />"; 
                            }
                            else {
                                if (zutoK == j) {
                                    document.getElementById("k"+i+j).innerHTML = "<input type='image' onClick=\"klikNaKartu(" + j + ", '" + alldata[k] + "');\" src=" + "'slike/" + alldata[k] + "2.png'" + " />"; 
                                }
                                else {
                                    document.getElementById("k"+i+j).innerHTML = "<input type='image' onClick=\"klikNaKartu(" + j + ", '" + alldata[k] + "');\" src=" + "'slike/" + alldata[k] + ".png'" + " />"; 
                                }
                            }
                        }
                    }
                    else if (igracbroj == i) {
                        document.getElementById("k"+i+j).innerHTML = "";
                    }
                    k++;
                }
                k++;
            }
            console.log(brojKarata[igracbroj]);
            if (brojKarata[igracbroj] != 6) {
                document.getElementById("spil").innerHTML = "<input type='image' onClick=\"klikNaSpil();\" src='slike/spil.png' />";
            }
            else {
                document.getElementById("spil").innerHTML = "";
            }
        }
    }
    xmlhttp.open("GET","<?php echo $_SESSION['logged']; ?>.xml?t=" + Math.random(),true);
    xmlhttp.send();
    
    if (igracbroj == 1)
        if (naPotezu == 1)
            nekaslikaigraca = '<img src="slike/g2.png" />';
        else
            nekaslikaigraca = '<img src="slike/g.png" />';
    else
        if (naPotezu == 1)
            nekaslikaigraca = '<img src="slike/t.png" />';
        else
            nekaslikaigraca = '<img src="slike/t2.png" />';
    document.getElementById('slikaigraca').innerHTML = nekaslikaigraca;
    
    kraj = false;
    crveniGubi = -1+thebigstring.split("q").length-1+thebigstring.split("w").length-1+thebigstring.split("e").length-1+thebigstring.split("r").length-1+thebigstring.split("t").length-1+thebigstring.split("z").length;
    if (crveniGubi<2) {
        kraj = true;
        if (igracbroj == 1)
            document.getElementById('divZaTablicu').innerHTML = "<img src='slike/victory.gif' /><br /><p>Bravo, you win!</p>";
        else
            document.getElementById('divZaTablicu').innerHTML = "<img src='slike/lose2.gif' /><br /><p>Better luck next time!</p>";
    }
    zutiGubi = -1+thebigstring.split("a").length-1+thebigstring.split("s").length-1+thebigstring.split("d").length-1+thebigstring.split("f").length-1+thebigstring.split("g").length-1+thebigstring.split("h").length;
    if (zutiGubi<2) {
        kraj = true;
        if (igracbroj == 1)
            document.getElementById('divZaTablicu').innerHTML = "<img src='slike/lose.gif' /><br /><p>Better luck next time!</p>";
        else
            document.getElementById('divZaTablicu').innerHTML = "<img src='slike/victory2.gif' /><br /><p>Bravo, you win!</p>";
    }
    
    clearTimeout(refreshHandle);
    if (kraj == false)
        refreshHandle = setTimeout(refreshaj, 1000);
}

 function klikNaKartu(jot,karta) {
    if (selektiranaKarta == karta) {
        selectedCard = false;
        selektiranaKarta = "0";
        document.getElementById("selected").innerHTML = pravila[0];
    }
    else {
        selectedCard = true;
        zutoK = jot;
        selektiranaKarta = karta;
        
        if (karta == "x") {
            ispis = pravila[6];
        }
        else if (karta == "c") {
            ispis = pravila[7];
        }
        else if (karta == "v") {
            ispis = pravila[8];
        }
        else if (karta == "y") {
            ispis = pravila[9];
        }
        ispis = "<p>You selected a powerful card.</p>" + ispis;
        document.getElementById("selected").innerHTML = ispis;
    }
 }

 function klikNaSpil() {
    if (naPotezu == igracbroj) {
        document.getElementById("selected").innerHTML = "You drew a card from the deck.";
    var xhr4;
    if (window.XMLHttpRequest) {
        xhr4 = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) {
        xhr4 = new ActiveXObject("Msxml2.XMLHTTP");
    }
    else {
        throw new Error("Ajax is not supported by this browser");
    }
    xhr4.open('POST', 'pomakni.php');
    xhr4.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr4.send("tip=2");
    if (naPotezu == 1)
        naPotezu = 2;
    else
        naPotezu = 1;
    }
    else {
        document.getElementById("selected").innerHTML = "You can only draw cards during your turn.";
    }
 }

 // **************** 
 // funkcija klikNaPolje() postavlja tekst u okvir za tekst, po potrebi (de)selektira figure i ako je selektirano odrediste pokrece funkciju pomicaj()
 // ****************

 function klikNaPolje(iii,jjj,kkk) {
    if (((selektiranaKarta == "y") && (kkk == "0") && (igracbroj == 1) && ((jjj==1)||(polje[6][jjj-1]=="0")) && ((jjj==8)||(polje[6][jjj+1]=="0")) && ((-1+thebigstring.split("a").length) < 4) && (iii == 7) && (jjj > 4)) || ((igracbroj == 2) && ((-1+thebigstring.split("q").length) < 4)  && ((jjj==1)||(polje[3][jjj-1]=="0")) && ((jjj==8)||(polje[3][jjj+1]=="0")) && (iii == 2) && (jjj < 5))) {
        unistiKartu(selektiranaKarta);
        var xhr6;
        if (window.XMLHttpRequest) {
            xhr6 = new XMLHttpRequest();
        }
        else if (window.ActiveXObject) {
            xhr6 = new ActiveXObject("Msxml2.XMLHTTP");
        }
        else {
            throw new Error("Ajax is not supported by this browser");
        }
        xhr6.open('POST', 'pomakni.php');
        xhr6.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        
        praznoPoljeJ = jjj-1;
        if (igracbroj == 1) {
            praznoPoljeI = 6;
            goblin = "a";
        }
        else {
            praznoPoljeI = 3;
            goblin = "q";
        }
        
        xhr6.send("tip=0&odabranaFigurica0=" + praznoPoljeI + "&odabranaFigurica1=" + praznoPoljeJ + "&odabranaFigurica2='" + goblin + "'&odabranaDestinacija0=" + iii + "&odabranaDestinacija1=" + jjj + "&odabranaDestinacija2='" + "0" + "'");        
        clearTimeout(refreshHandle);
        refreshHandle = setTimeout(refreshaj, 100+Math.random()*200);
        odabranaFigurica[0] = 0;
        odabranaFigurica[1] = 0;
        odabranaFigurica[2] = "0";
        odabranaDestinacija[0] = 0;
        odabranaDestinacija[1] = 0;
        odabranaDestinacija[2] = "0";
        selektiranaKarta = "0";
        selectedCard = false;
        if (naPotezu == 1)
            naPotezu = 2;
        else
            naPotezu = 1;
    
        document.getElementById("selected").innerHTML = "You created a new goblin!";
        
        return;
    }
    if (kraj == true)
        return;
    if (iii == odabranaFigurica[0] && jjj == odabranaFigurica[1]) {
        // klik na isto polje - tj. deselekcija
        odabranaFigurica[0] = 0;
        odabranaFigurica[1] = 0;
        odabranaFigurica[2] = "0";
        selectedFigure = false;
        document.getElementById("selected").innerHTML = pravila[0];
    }
    else if (kkk != '0') {
        // klik na figuricu
        if (odabranaFigurica[2] == "0") {
            odabranaFigurica[0] = iii;
            odabranaFigurica[1] = jjj;
            odabranaFigurica[2] = kkk;
            zutoY = iii;
            zutoX = jjj;
            selectedFigure = true;
            pozicija = "<p>You selected a figure on these coordinates: " + iii+"-"+jjj+".</p>";
            if (kkk == "q" || kkk == "a") // goblin
                odgovarajucePravilo = 1;
            else if (kkk == "f" || kkk == "r") // gusari
                odgovarajucePravilo = 2;
            else if (kkk == "d" || kkk == "e") // ninje
                odgovarajucePravilo = 3;
            else if (kkk == "w" || kkk == "s") // roboti
                odgovarajucePravilo = 4;
            else if (kkk == "t" || kkk == "g") // kiklopi
                odgovarajucePravilo = 5;
            else if (kkk == "z" || kkk == "h") // konji
                odgovarajucePravilo = 6;
            ispis = pozicija + pravila[odgovarajucePravilo];
            document.getElementById("selected").innerHTML = ispis;
        }
        else {
            odabranaDestinacija[0] = iii;
            odabranaDestinacija[1] = jjj;
            odabranaDestinacija[2] = kkk;
            selectedFigure = false;
            pomicaj(odabranaFigurica, odabranaDestinacija);
        }
    }
    else if (odabranaFigurica[2] != '0') {
        odabranaDestinacija[0] = iii;
        odabranaDestinacija[1] = jjj;
        odabranaDestinacija[2] = kkk;
        selectedFigure = false;
        pomicaj(odabranaFigurica, odabranaDestinacija);
    }
 }
 function unistiKartu(unistenakarta) {
    var xhr5;
    if (window.XMLHttpRequest) {
        xhr5 = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) {
        xhr5 = new ActiveXObject("Msxml2.XMLHTTP");
    }
    else {
        throw new Error("Ajax is not supported by this browser");
    }
    xhr5.open('POST', 'pomakni.php');
    xhr5.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr5.send("tip=3&karta='" + unistenakarta + "'");
    document.getElementById("k"+igracbroj+zutoK).innerHTML = "";
    console.log("k"+igracbroj+zutoK);
    zutoK = -1;
 }
 
 function pomicaj(odabranaFigurica, odabranaDestinacija) { 
    
    pomakJeDopusten = false;
    
    // *******************
    //  MICANJE FIGURICA
    // *******************
    
    if ((igracbroj == 1) && ((odabranaDestinacija[2]=="q") || (odabranaDestinacija[2]=="w") || (odabranaDestinacija[2]=="e") || (odabranaDestinacija[2]=="r") || (odabranaDestinacija[2]=="t") || (odabranaDestinacija[2] == "z") || (odabranaDestinacija[2]=="0"))) { // ako si ti na redu i ako pucaš na tuðe / prazno
        if (odabranaFigurica[2] == "a") { // zuti pijun
            if ((odabranaFigurica[1] == odabranaDestinacija[1])&&(odabranaDestinacija[2]=="0")) { // vertikalno
                if (odabranaFigurica[0] - 1 == odabranaDestinacija[0]) {
                    pomakJeDopusten = true; // default kretanje prema gore
                }
                else if (odabranaFigurica[0] - 2 == odabranaDestinacija[0]) {
                    if (odabranaFigurica[0] == 7) {
                        pomakJeDopusten = true; // 2 gore ak pocinjes na 7
                    }
                    else if (selektiranaKarta == "c") {
                        pomakJeDopusten = true; // 2 gore ak si selektirao kartu
                        unistiKartu(selektiranaKarta);
                    }
                }
                else if (odabranaFigurica[0] - 3 == odabranaDestinacija[0]) {
                    if (selektiranaKarta == "c") {
                        if (odabranaFigurica[0] == 7) {
                            pomakJeDopusten = true;
                            unistiKartu(selektiranaKarta);
                        }
                    }
                }
            }
            else if ((odabranaFigurica[1] == odabranaDestinacija[1]+1)||(odabranaFigurica[1] == odabranaDestinacija[1]-1)) { // jedan ukoso ako nije prazno
                if ((odabranaFigurica[0] - 1 == odabranaDestinacija[0])&&(odabranaDestinacija[2] != "0")) {
                    pomakJeDopusten = true;
                }
            }
        }
        if (odabranaFigurica[2] == "f") { // zuti kralj
            if (((odabranaFigurica[0] == odabranaDestinacija[0])||(odabranaFigurica[0] == odabranaDestinacija[0]+1)||(odabranaFigurica[0] == odabranaDestinacija[0]-1))&&((odabranaFigurica[1] == odabranaDestinacija[1]+1)||(odabranaFigurica[1] == odabranaDestinacija[1]-1)||(odabranaFigurica[1] == odabranaDestinacija[1]))) {
                pomakJeDopusten = true;
            }
            else if (selektiranaKarta == "x") {
                if ((Math.abs(odabranaFigurica[0]-odabranaDestinacija[0]) <= 2 ) && (Math.abs(odabranaFigurica[1]-odabranaDestinacija[1]) <= 2)){
                    pomakJeDopusten = true;
                    unistiKartu(selektiranaKarta);
                }
            }
        }
        else if (odabranaFigurica[2] == "d") { // zuti lovac
            if (Math.abs(odabranaDestinacija[0]-odabranaFigurica[0])==Math.abs(odabranaDestinacija[1]-odabranaFigurica[1])) { // ide ukoso -> to je dobro
                
                if ((odabranaDestinacija[0]<odabranaFigurica[0])&&(odabranaDestinacija[1]<odabranaFigurica[1])) { // ide gore lijevo
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]-n][odabranaFigurica[1]-n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
                if ((odabranaDestinacija[0]<odabranaFigurica[0])&&(odabranaDestinacija[1]>odabranaFigurica[1])) { // ide gore desno
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]-n][odabranaFigurica[1]+n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                            
                        }
                    }
                }
                if ((odabranaDestinacija[0]>odabranaFigurica[0])&&(odabranaDestinacija[1]<odabranaFigurica[1])) { // ide dolje lijevo
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]+n][odabranaFigurica[1]-n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
                if ((odabranaDestinacija[0]>odabranaFigurica[0])&&(odabranaDestinacija[1]>odabranaFigurica[1])) { // ide dolje desno
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]+n][odabranaFigurica[1]+n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
            }
        }
        else if (odabranaFigurica[2] == "s") { // zuti top
            if ((odabranaDestinacija[0]==odabranaFigurica[0])||(odabranaDestinacija[1]==odabranaFigurica[1])) { // ide ravno -> to je dobro
                if (odabranaDestinacija[0]<odabranaFigurica[0]) { // ide gore
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]-n][odabranaFigurica[1]] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
                if (odabranaDestinacija[0]>odabranaFigurica[0]) { // ide dolje
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]+n][odabranaFigurica[1]] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                            
                        }
                    }
                }
                if (odabranaDestinacija[1]<odabranaFigurica[1]) { // ide lijevo
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[1]-odabranaFigurica[1]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]][odabranaFigurica[1]-n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                            
                        }
                    }
                }
                if (odabranaDestinacija[1]>odabranaFigurica[1]) { // ide desno
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[1]-odabranaFigurica[1]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]][odabranaFigurica[1]+n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                            
                        }
                    }
                }
            }
        }
        else if (odabranaFigurica[2] == "g") { // zuta kraljica
            if ((odabranaDestinacija[0]==odabranaFigurica[0])||(odabranaDestinacija[1]==odabranaFigurica[1])) { // ide ravno -> to je dobro
                if (odabranaDestinacija[0]<odabranaFigurica[0]) { // ide gore
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]-n][odabranaFigurica[1]] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
                if (odabranaDestinacija[0]>odabranaFigurica[0]) { // ide dolje
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]+n][odabranaFigurica[1]] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
                if (odabranaDestinacija[1]<odabranaFigurica[1]) { // ide lijevo
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[1]-odabranaFigurica[1]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]][odabranaFigurica[1]-n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
                if (odabranaDestinacija[1]>odabranaFigurica[1]) { // ide desno
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[1]-odabranaFigurica[1]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]][odabranaFigurica[1]+n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
            }
            else if (Math.abs(odabranaDestinacija[0]-odabranaFigurica[0])==Math.abs(odabranaDestinacija[1]-odabranaFigurica[1])) { // ide ukoso -> to je dobro
                
                if ((odabranaDestinacija[0]<odabranaFigurica[0])&&(odabranaDestinacija[1]<odabranaFigurica[1])) { // ide gore lijevo
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]-n][odabranaFigurica[1]-n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
                if ((odabranaDestinacija[0]<odabranaFigurica[0])&&(odabranaDestinacija[1]>odabranaFigurica[1])) { // ide gore desno
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]-n][odabranaFigurica[1]+n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
                if ((odabranaDestinacija[0]>odabranaFigurica[0])&&(odabranaDestinacija[1]<odabranaFigurica[1])) { // ide dolje lijevo
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]+n][odabranaFigurica[1]-n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
                if ((odabranaDestinacija[0]>odabranaFigurica[0])&&(odabranaDestinacija[1]>odabranaFigurica[1])) { // ide dolje desno
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]+n][odabranaFigurica[1]+n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
            }
        }
        else if (odabranaFigurica[2] == "h") { // zuti konj
            if (((Math.abs(odabranaDestinacija[1]-odabranaFigurica[1])==2)&&(Math.abs(odabranaDestinacija[0]-odabranaFigurica[0])==1)) || ((Math.abs(odabranaDestinacija[1]-odabranaFigurica[1])==1)&&(Math.abs(odabranaDestinacija[0]-odabranaFigurica[0])==2))) {
                pomakJeDopusten = true;
            }
        }
    }
    else if ((igracbroj == 2) && ((odabranaDestinacija[2]=="a") || (odabranaDestinacija[2]=="s") || (odabranaDestinacija[2]=="d") || (odabranaDestinacija[2]=="f") || (odabranaDestinacija[2]=="g") || (odabranaDestinacija[2]) == "h" || (odabranaDestinacija[2]=="0"))) { // ako si ti na redu i ako pucaš na tuðe / prazno
        if (odabranaFigurica[2] == "q") { // crveni pijun
            if ((odabranaFigurica[1] == odabranaDestinacija[1])&&(odabranaDestinacija[2]=="0")) { // vertikalno
                if (odabranaFigurica[0] + 1 == odabranaDestinacija[0]) {
                    pomakJeDopusten = true; // default kretanje prema dolje
                }
                else if (odabranaFigurica[0] + 2 == odabranaDestinacija[0]) {
                    if (odabranaFigurica[0] == 2) {
                        pomakJeDopusten = true; // 2 dolje ak pocinjes na 2
                    }
                    else if (selektiranaKarta == "c") {
                        pomakJeDopusten = true; // 2 dolje ak si selektirao kartu
                        unistiKartu(selektiranaKarta);
                    }
                }
                else if (odabranaFigurica[0] + 3 == odabranaDestinacija[0]) {
                    if (selektiranaKarta == "c") {
                        if (odabranaFigurica[0] == 2) {
                            pomakJeDopusten = true;
                            unistiKartu(selektiranaKarta);
                        }
                    }
                }
            }
            else if ((odabranaFigurica[1] == odabranaDestinacija[1]+1)||(odabranaFigurica[1] == odabranaDestinacija[1]-1)) { // jedan ukoso ako nije prazno
                if ((odabranaFigurica[0] + 1 == odabranaDestinacija[0])&&(odabranaDestinacija[2] != "0")) {
                    pomakJeDopusten = true;
                }
            }
        }
        else if (odabranaFigurica[2] == "r") { // crveni kralj
            if (( (odabranaFigurica[0] == odabranaDestinacija[0]) || (odabranaFigurica[0] == odabranaDestinacija[0]+1) || (odabranaFigurica[0] == odabranaDestinacija[0]-1) ) && ( (odabranaFigurica[1] == odabranaDestinacija[1]+1) || (odabranaFigurica[1] == odabranaDestinacija[1]-1) || (odabranaFigurica[1] == odabranaDestinacija[1]) )) {
                    pomakJeDopusten = true;
            }
            else if (selektiranaKarta == "x") {
                if ((Math.abs(odabranaFigurica[0]-odabranaDestinacija[0]) <= 2 ) && (Math.abs(odabranaFigurica[1]-odabranaDestinacija[1]) <= 2)){
                    pomakJeDopusten = true;
                    unistiKartu(selektiranaKarta);
                }
            }
        }
        else if (odabranaFigurica[2] == "e") { // crveni lovac
            if (Math.abs(odabranaDestinacija[0]-odabranaFigurica[0])==Math.abs(odabranaDestinacija[1]-odabranaFigurica[1])) { // ide ukoso -> to je dobro
                if ((odabranaDestinacija[0]<odabranaFigurica[0])&&(odabranaDestinacija[1]<odabranaFigurica[1])) { // ide gore lijevo
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]-n][odabranaFigurica[1]-n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
                if ((odabranaDestinacija[0]<odabranaFigurica[0])&&(odabranaDestinacija[1]>odabranaFigurica[1])) { // ide gore desno
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]-n][odabranaFigurica[1]+n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
                if ((odabranaDestinacija[0]>odabranaFigurica[0])&&(odabranaDestinacija[1]<odabranaFigurica[1])) { // ide dolje lijevo
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]+n][odabranaFigurica[1]-n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
                if ((odabranaDestinacija[0]>odabranaFigurica[0])&&(odabranaDestinacija[1]>odabranaFigurica[1])) { // ide dolje desno
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]+n][odabranaFigurica[1]+n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
            }
        }
        else if (odabranaFigurica[2] == "w") { // crveni top
            if ((odabranaDestinacija[0]==odabranaFigurica[0])||(odabranaDestinacija[1]==odabranaFigurica[1])) { // ide ravno -> to je dobro
                if (odabranaDestinacija[0]<odabranaFigurica[0]) { // ide gore
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]-n][odabranaFigurica[1]] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
                if (odabranaDestinacija[0]>odabranaFigurica[0]) { // ide dolje
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]+n][odabranaFigurica[1]] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
                if (odabranaDestinacija[1]<odabranaFigurica[1]) { // ide lijevo
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[1]-odabranaFigurica[1]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]][odabranaFigurica[1]-n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
                if (odabranaDestinacija[1]>odabranaFigurica[1]) { // ide desno
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[1]-odabranaFigurica[1]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]][odabranaFigurica[1]+n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
            }
        }
        else if (odabranaFigurica[2] == "t") { // crvena kraljica
            if ((odabranaDestinacija[0]==odabranaFigurica[0])||(odabranaDestinacija[1]==odabranaFigurica[1])) { // ide ravno -> to je dobro
                if (odabranaDestinacija[0]<odabranaFigurica[0]) { // ide gore
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]-n][odabranaFigurica[1]] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
                if (odabranaDestinacija[0]>odabranaFigurica[0]) { // ide dolje
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]+n][odabranaFigurica[1]] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
                if (odabranaDestinacija[1]<odabranaFigurica[1]) { // ide lijevo
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[1]-odabranaFigurica[1]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]][odabranaFigurica[1]-n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
                if (odabranaDestinacija[1]>odabranaFigurica[1]) { // ide desno
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[1]-odabranaFigurica[1]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]][odabranaFigurica[1]+n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
            }
            else if (Math.abs(odabranaDestinacija[0]-odabranaFigurica[0])==Math.abs(odabranaDestinacija[1]-odabranaFigurica[1])) { // ide ukoso -> to je dobro
                
                if ((odabranaDestinacija[0]<odabranaFigurica[0])&&(odabranaDestinacija[1]<odabranaFigurica[1])) { // ide gore lijevo
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]-n][odabranaFigurica[1]-n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
                if ((odabranaDestinacija[0]<odabranaFigurica[0])&&(odabranaDestinacija[1]>odabranaFigurica[1])) { // ide gore desno
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]-n][odabranaFigurica[1]+n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
                if ((odabranaDestinacija[0]>odabranaFigurica[0])&&(odabranaDestinacija[1]<odabranaFigurica[1])) { // ide dolje lijevo
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]+n][odabranaFigurica[1]-n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
                if ((odabranaDestinacija[0]>odabranaFigurica[0])&&(odabranaDestinacija[1]>odabranaFigurica[1])) { // ide dolje desno
                    pomakJeDopusten = true; //pretpostavimo da je sve ok
                    for(n = 1; n < Math.abs(odabranaDestinacija[0]-odabranaFigurica[0]); n++) { // provjeravam putanju ima li prepreka
                        if (polje[odabranaFigurica[0]+n][odabranaFigurica[1]+n] != "0") { // ako nije ok postavimo zastavicu na false
                            pomakJeDopusten = false;
                        }
                    }
                }
            }
        }
        else if (odabranaFigurica[2] == "z") { // crveni konj
            if (((Math.abs(odabranaDestinacija[1]-odabranaFigurica[1])==2)&&(Math.abs(odabranaDestinacija[0]-odabranaFigurica[0])==1)) || ((Math.abs(odabranaDestinacija[1]-odabranaFigurica[1])==1)&&(Math.abs(odabranaDestinacija[0]-odabranaFigurica[0])==2))) {
                pomakJeDopusten = true;
            }
        }
    }
    
    // zamjena figurica kod lovca je drugaèiji tip (ne ostavlja prazno na mjestu gdje je bila figurica nego ono kaj je bilo prije)
    if (((igracbroj == 1)&&(selektiranaKarta == "v")&&(odabranaFigurica[2]=="d")&&((odabranaDestinacija[2]=="a") || (odabranaDestinacija[2]=="s") || (odabranaDestinacija[2]=="f") || (odabranaDestinacija[2]=="g") || (odabranaDestinacija[2]=="h")))||((igracbroj == 2)&&(selektiranaKarta == "v")&&(odabranaFigurica[2]=="e")&&((odabranaDestinacija[2]=="q") || (odabranaDestinacija[2]=="w") || (odabranaDestinacija[2]=="r") || (odabranaDestinacija[2]=="t") || (odabranaDestinacija[2]=="z")))) {
        tip = 4;
        pomakJeDopusten = true;
        unistiKartu(selektiranaKarta);
    }
    else {
        tip = 0;
    }
    
    if (pomakJeDopusten == true) {
        if (naPotezu == igracbroj) {
            document.getElementById("selected").innerHTML = "You moved a piece from "+odabranaFigurica[1]+"-"+odabranaFigurica[0]+" to "+odabranaDestinacija[1]+"-"+odabranaDestinacija[0]+".";
        }
        else {
            document.getElementById("selected").innerHTML = "Wait for your turn.";
        }
        
        var xhr2;
        if (window.XMLHttpRequest) {
            xhr2 = new XMLHttpRequest();
        }
        else if (window.ActiveXObject) {
            xhr2 = new ActiveXObject("Msxml2.XMLHTTP");
        }
        else {
            throw new Error("Ajax is not supported by this browser");
        }
        xhr2.open('POST', 'pomakni.php');
        xhr2.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr2.send("tip=" + tip + "&odabranaFigurica0=" + odabranaFigurica[0] + "&odabranaFigurica1=" + odabranaFigurica[1] + "&odabranaFigurica2='" + odabranaFigurica[2] + "'&odabranaDestinacija0=" + odabranaDestinacija[0] + "&odabranaDestinacija1=" + odabranaDestinacija[1] + "&odabranaDestinacija2='" + odabranaDestinacija[2] + "'");

        clearTimeout(refreshHandle);
        refreshHandle = setTimeout(refreshaj, 100+Math.random()*200);
    }
    else { 
        document.getElementById("selected").innerHTML = "Moving a piece from "+odabranaFigurica[1]+"-"+odabranaFigurica[0]+" to "+odabranaDestinacija[1]+"-"+odabranaDestinacija[0]+" is not a valid move."
    }
    odabranaFigurica[0] = 0;
    odabranaFigurica[1] = 0;
    odabranaFigurica[2] = "0";
    odabranaDestinacija[0] = 0;
    odabranaDestinacija[1] = 0;
    odabranaDestinacija[2] = "0";
    selektiranaKarta = "0";
    selectedCard = false;
    if (naPotezu == 1)
        naPotezu = 2;
    else
        naPotezu = 1;
    
 }
  </script>
  <div id="content">
    <div id="slikaigraca">
        <br />
    </div>
    <div id="content_status">
        <br />Connecting to server...
    </div> 
    <div id="selected">
        <br />Choose a piece.
    </div>
    <div id="vrijeme">
        Elapsed time.
    </div>
  </div>
 </body>
</html>