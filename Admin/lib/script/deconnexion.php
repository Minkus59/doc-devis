<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");

session_start();
session_unset();
session_destroy();
header("location:".$Home."/Admin/");
?>