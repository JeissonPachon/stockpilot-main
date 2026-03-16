<?php
include("models/mmen.php");

$mmen = new Mmen();
$datm = [];
if (isset($_SESSION['idper'])) {
    $datm = $mmen->menu($_SESSION['idper']);
}
?>