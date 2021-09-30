<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect2.inc.php");

$rep = $_SERVER['DOCUMENT_ROOT']."/lib/logo/";

if (isset($_POST['oui'])) {

    $SelectParam=$cnx->prepare("SELECT * FROM devis_param WHERE hash=:client");
    $SelectParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
    $SelectParam->execute();
    $Param=$SelectParam->fetch(PDO::FETCH_OBJ);

    unlink($rep.basename($Param->logo));

    $Supprlogo=$cnx->prepare("UPDATE devis_param SET logo='', type='0' WHERE hash=:client");
    $Supprlogo->bindParam(':client', $SessionClient, PDO::PARAM_STR);
    $Supprlogo->execute();

    header('Location:'.$Home.'/Parametre/Profil/');
}


if (isset($_POST['non'])) {  
    header('Location:'.$Home.'/Parametre/Profil/');
}

?>

<html>
<head>
<title>Confirmation</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR />"; } 
if (isset($MAJ)) { echo "<font color='#FF6600'>".stripslashes($MAJ)."</font><BR />"; } ?>

Etes-vous sur de vouloir supprimer ce logo ?

<TABLE width="300">
  <form action="" method="POST">
<TR><TD align="center"><input name="oui" type="submit" value="OUI"></TD><TD align="center"><input name="non" type="submit" value="NON"/></TD></TR></form>
</TABLE>
</body>
</html>