<?php 
$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];

session_start();
?>

<div id="Content">
<div id="Center">
    
<img src="<?php echo $Home; ?>/lib/img/hotline.jpg" alt="Assistance téléphonique gratuite" width="100%">

<article>
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR />"; } ?>


<H1>Contact</H1>

<?php
if ($Count>0) {

    while($Actu=$RecupArticle->fetch(PDO::FETCH_OBJ)) { 

        echo '
        <article>';

        echo $Actu->message;
        if (($Cnx_Admin==true)||($Cnx_Client==true)) { 
            echo '<a href="'.$Home.'/Admin/Article/Nouveau/?id='.$Actu->id.'"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a><a href="'.$Home.'/Admin/Article/supprimer.php?id='.$Actu->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a>';
        } 
        echo '</article>';
    }
}
?>

<div id="acc_gauche">

Pour toutes questions<BR /><BR />

<!--<b>Téléphone :</b> <?php echo $Telephone; ?><BR />
<b>Adresse :</b> <?php echo $Adresse; ?><BR />
<b>E-mail :</b> <?php echo $Destinataire; ?> ou via le <b>formulaire ci-dessous</b> <BR /><BR />-->

Merci de bien vouloir préciser vos coordonnées et votre demande.<BR /><BR />  
<form name="form_contact" id="form_contact" action="<?php echo $Home; ?>/lib/script/contact.php" method="POST">

<input type="text" value="<?php if (isset($_SESSION['nom'])) { echo $_SESSION['nom']; } ?>" name="nom" placeholder="Nom / Prénom*" required="required"><BR /><BR />
<input type="text" value="<?php if (isset($_SESSION['tel'])) { echo $_SESSION['tel']; } ?>" name="tel" placeholder="Numero de téléphone*" required="required"/><BR /><BR />
<input type="text" value="<?php if (isset($_SESSION['cp'])) { echo $_SESSION['cp']; } ?>" name="cp" placeholder="Code postal*" required="required"/><BR /><BR />
<input type="text" value="<?php if (isset($_SESSION['sujet'])) { echo $_SESSION['sujet']; } ?>" name="sujet" placeholder="Sujet*" required="required"/><BR /><BR />
<textarea cols="40" rows="10" name="message" placeholder="Message ou détailles pour devis*" required="required"><?php if (isset($_SESSION['message'])) { echo $_SESSION['message']; } ?></textarea><BR /><BR />
<input type="email" value="<?php if (isset($_SESSION['email'])) { echo $_SESSION['email']; } ?>" name="email" placeholder="Votre adresse e-mail*" required="required"/><BR /><BR />
<input type="submit" name="Envoyer" value="Envoyer"/>

</form><BR /><BR />

<font color='#FF0000'>*</font> : Informations requises<BR /><BR />
</div>

<!--<div id="acc_droite">
<?php // echo $GoogleMap; ?>
</div>-->
<script type="text/javascript" src="../lib/script/select_contact.js" async></script>

</article>

</div>
</div>

