<?php 
$Section= explode("?",$_SERVER['REQUEST_URI']);

$PageActu=$Home.$Section[0];

//echo print_r($_SERVER);

$RecupArticle=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Article WHERE page=:page AND statue='1' ORDER BY position ASC");
$RecupArticle->bindParam(':page', $PageActu, PDO::PARAM_STR);
$RecupArticle->execute();
$Count=$RecupArticle->rowcount();

$SelectPageActif=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE statue='1' ORDER BY position ASC");
$SelectPageActif->execute();

$SelectPageSOE=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE lien=:page");
$SelectPageSOE->bindParam(':page', $PageActu, PDO::PARAM_STR);
$SelectPageSOE->execute();
$SOEPage=$SelectPageSOE->fetch(PDO::FETCH_OBJ);

$HOME=$Home."/";
$SelectLibeleAccueil=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE lien=:lien");
$SelectLibeleAccueil->bindParam(':lien', $HOME, PDO::PARAM_STR);
$SelectLibeleAccueil->execute();
$PageLibeleAccueil=$SelectLibeleAccueil->fetch(PDO::FETCH_OBJ);

$CONTACT=$Home."/Contact/";
$SelectLibeleContact=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE lien=:lien");
$SelectLibeleContact->bindParam(':lien', $CONTACT, PDO::PARAM_STR);
$SelectLibeleContact->execute();
$PageLibeleContact=$SelectLibeleContact->fetch(PDO::FETCH_OBJ);

$SelectParamLogoFooter=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE id='1'");    
$SelectParamLogoFooter->execute(); 
$ParamLogoFooter=$SelectParamLogoFooter->fetch(PDO::FETCH_OBJ);

$SelectParamLogoHeader=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE id='2'");    
$SelectParamLogoHeader->execute(); 
$ParamLogoHeader=$SelectParamLogoHeader->fetch(PDO::FETCH_OBJ);
?>