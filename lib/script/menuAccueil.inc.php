<nav>
<div id="Center">
    <label for="NavButton" class="LabelNavButton"></label>
    <input class="NavButton" id="NavButton" type="checkbox"/>

    <ul class='sub'>
        <li <?php if ($PageActu==$Home."/") { echo "class='Up'"; } ?>><a href="<?php echo $Home; ?>">Accueil</a></li>
        <?php
        while ($MenuPage=$SelectPageActif->fetch(PDO::FETCH_OBJ)) {
        ?>
        <li <?php if ($PageActu==$MenuPage->lien) { echo "class='Up'"; } ?>><a href="<?php echo $MenuPage->lien ?>"><?php echo $MenuPage->libele ?></a></li>
        <?php } ?>
        <li <?php if ($PageActu==$Home."/Dashboard/") { echo "class='Up'"; } ?>><a href="<?php echo $Home; ?>/Dashboard/">Dashboard</a></li>
        <li <?php if ($PageActu==$Home."/Contact/") { echo "class='Up'"; } ?>><a href="<?php echo $Home; ?>/Contact/">Contact</a></li>
    </ul>
</div>
</nav>