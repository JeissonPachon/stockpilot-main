<?php
function tit($tt){
	$html = "";
	$html .= "<h3>".$tt."</h3>";
	$html .= "<hr>";
	echo $html;
}


function generar_hash_contrasena($con) {
    // La lógica de hashing usada en tu control.php
    $pas_hash = sha1(md5($con . "/Pq5@-+")) . "kjahw9";
    return $pas_hash;
}
?>

