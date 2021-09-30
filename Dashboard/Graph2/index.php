<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect2.inc.php");
header ("Content-type: image/jpeg");

//-------------------------------------------------------------------------------------

$largeur_destination=530;
$hauteur_destination=300;
$Marge=10;
$NbBar=12;
$EpaisseurLigne=3;
$Coord_xText =2;

//-------------------------------------------------------------------------------------

if (isset($_POST['annee'])) {
   $_SESSION['annee']=$_POST['annee'];
}

if (!isset($_SESSION['annee'])) {
   $_SESSION['annee']="2018";
}

$Annee=$_SESSION['annee'];
$AnneeSup=$Annee+1;
$Janvier=mktime(0,0,0,1,1,$Annee);
$Fevrier=mktime(0,0,0,2,1,$Annee);
$Mars=mktime(0,0,0,3,1,$Annee);
$Avril=mktime(0,0,0,4,1,$Annee);
$Mai=mktime(0,0,0,5,1,$Annee);
$Juin=mktime(0,0,0,6,1,$Annee);
$Juillet=mktime(0,0,0,7,1,$Annee);
$Aout=mktime(0,0,0,8,1,$Annee);
$Septembre=mktime(0,0,0,9,1,$Annee);
$Octobre=mktime(0,0,0,10,1,$Annee);
$Novembre=mktime(0,0,0,11,1,$Annee);
$Descembre=mktime(0,0,0,12,1,$Annee);
$JanvierSup=mktime(0,0,0,1,1,$AnneeSup);

$Mois=array('1' => 'Janvier', '2' => 'Fevrier', '3' => 'Mars', '4' => 'Avril', '5' => 'Mai', '6' => 'Juin', '7' => 'Juillet', '8' => 'Aout', '9' => 'Septembre', '10' => 'Octobre', '11' => 'Novembre', '12' => 'Decembre');
$Date=array('1' => $Janvier, '2' => $Fevrier, '3' => $Mars, '4' => $Avril, '5' => $Mai, '6' => $Juin, '7' => $Juillet, '8' => $Aout, '9' => $Septembre, '10' => $Octobre, '11' => $Novembre, '12' => $Descembre);

for ($y=1;$y<=$NbBar;$y++) {
      $o=$y+1;
    if ($y==$NbBar) {
        $SelectArticle2=$cnx->prepare("SELECT * FROM ".$Prefix."devis_recu WHERE date BETWEEN :date1 AND :date2 AND hash=:hash");
        $SelectArticle2->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $SelectArticle2->bindParam(':date1', $Date[$y], PDO::PARAM_STR);
        $SelectArticle2->bindParam(':date2', $JanvierSup, PDO::PARAM_STR);    
        $SelectArticle2->execute();   
    }
    else {
        $SelectArticle2=$cnx->prepare("SELECT * FROM ".$Prefix."devis_recu WHERE date BETWEEN :date1 AND :date2 AND hash=:hash");
        $SelectArticle2->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $SelectArticle2->bindParam(':date1', $Date[$y], PDO::PARAM_STR);
        $SelectArticle2->bindParam(':date2', $Date[$o], PDO::PARAM_STR);    
        $SelectArticle2->execute();
    }
    for ($u=0;$Article2=$SelectArticle2->fetch(PDO::FETCH_OBJ);$u++) {  
        $Total[$y]+=$Article2->montant;
    }  
    $ToTVA[$y]=($Total[$y]/100)*20;
    $hauteur[$y]+=$ToTVA[$y];
}

//-------------------------------------------------------------------------------------

$ValeurMax=max($Total);
$bar_largeur=round((($largeur_destination-($Marge*2))-$Marge)/$NbBar, 0, PHP_ROUND_HALF_DOWN);
$destination_y=$hauteur_destination-$Marge;
$HauteurLigne=$hauteur_destination-($Marge*2);
$LargeurLigne=$largeur_destination-($Marge*2);
$destination_x=$Marge+$EpaisseurLigne;

//-------------------------------------------------------------------------------------

$fond = imagecreate($largeur_destination,$hauteur_destination);
$blanc = imagecolorallocate($fond, 255, 255, 255);

//-------------------------------------------------------------------------------------

$LigneBas=imagecreate($LargeurLigne, $EpaisseurLigne);
$LigneBasBleu = imagecolorallocate($LigneBas, 0, 128, 150);
imagecopymerge($fond, $LigneBas, $Marge, $destination_y, 0, 0, $LargeurLigne, $EpaisseurLigne, 100);

$LigneGauche=imagecreate(2, $HauteurLigne);
$LigneGaucheBleu = imagecolorallocate($LigneGauche, 0, 128, 150);
imagecopymerge($fond, $LigneGauche, $Marge, $Marge, 0, 0, $EpaisseurLigne, $HauteurLigne, 100);

//-------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------

$i=1;

for ($i=1;$i<=$NbBar;$i++) {    
    $HauteurMax[$i]=round((((($hauteur_destination-($Marge*2))-$Marge)/$ValeurMax)*$hauteur[$i]), 0, PHP_ROUND_HALF_DOWN);
    $Bar[$i] = imagecreate($bar_largeur,$HauteurMax[$i]);
        
    if ($i % 2 != 1) {
        $Gris = imagecolorallocate($Bar[$i], 60,60, 60);
    }
    else {
        $Gris = imagecolorallocate($Bar[$i], 30, 30, 30);
    }
    
    $TextBleu = imagecolorallocate($Bar[$i], 0, 128, 150);
    $Coord_yText=$Hauteur[$i] + $Marge;
    imagestring($Bar[$i], 1, $Coord_xText, $Coord_yText, $Mois[$i], $TextBleu);
    $Coord_yText2=$Hauteur[$i] + ($Marge*2);
    imagestring($Bar[$i], 1, $Coord_xText, $Coord_yText2, $hauteur[$i], $TextBleu);
    
    if ($i!=1) {
        $destination_x=$destination_x + $bar_largeur;
    }
    
    $Hauteur[$i] = $destination_y - $HauteurMax[$i];
    imagecopymerge($fond, $Bar[$i], $destination_x, $Hauteur[$i], 0, 0, $bar_largeur, $HauteurMax[$i], 100);    
}

//-------------------------------------------------------------------------------------

imagejpeg($fond);
?>