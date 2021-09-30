<footer>
<div id="Center">
<?php 
$Lien=$cnx->prepare("SELECT * FROM ".$Prefix."devis_partenaire");
$Lien->execute();
?>
<div id="cadreFooter">
Besoin d'une assistance technique ? <BR />
<a href="<?php echo $Home; ?>/Contact/">Contactez-nous</a><BR />
</div>

<div id="cadreFooter">
<li><a href="<?php echo $Home; ?>/Contact/">Contact</a></li>
<li><a href="<?php echo $Home; ?>/Mentions-legales/">Mentions Legales</a></li>
<li><a href="http://www.3donweb.fr/" target="_blank">Qui somme nous ?</a></li>
</div>

<div id="cadreFooter">
<a title="Site déposé sur CopyrightFrance.com" href="http://www.copyrightfrance.com/certificat-depot-copyright-france-TPY91EB.htm" target="_blank"><img border="0" src="<?php echo $Home; ?>/lib/img/TPY91EB-1.gif" alt=" CopyrightFrance.com "></a>
</div>

</div>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-57493378-11', 'auto');
  ga('send', 'pageview');

</script>
</footer>