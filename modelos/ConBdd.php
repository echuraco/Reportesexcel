<?php 
//Conexion basica a BDD tipo PDO MYSQL
//reemplazar new PDO("mysql:host=aqui direccion mysql ;dbname=Nombre de la BDD", "Usuario_Bddmysql","ClaveUsuariomysql");
//Realizado por Edison Churaco - Soporte glpi
//Grupo: Cabritos
class Conexion{
	public static function conectar(){
		try {
			$conn = new PDO("mysql:host=localhost;dbname=glpi", "root","");
			//echo "Connected to  at  successfully.";
		} catch (PDOException $pe) {
			die("Could not connect to the database  :" . $pe->getMessage());
		}
		return $conn;
	}

}