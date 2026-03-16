<?php
class Conexion{
	public function get_conexion(){
		include ("config.php");
		$conexion = new PDO(
			"mysql:host=$host;dbname=$db;charset=utf8mb4",
			$user,
			$pass,
			[
				PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES   => false,
			]
		);
		return $conexion;
	}
}
?>