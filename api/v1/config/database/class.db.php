<?php
class DB{

private $DB_HOST;

private $DB_USER;

private $DB_PASSWORD;

private $DB_DATABASE;

private $connection;

private $isConnected = false;

public function __construct(){

$configuration = new CONFIG();

$this->DB_HOST = $configuration->DB_HOST;

$this->DB_USER = $configuration->DB_USER;

$this->DB_PASSWORD = $configuration->DB_PASSWORD;

$this->DB_DATABASE = $configuration->DB_DATABASE;
}

public function dbConnection(){
$this->connection = null;      
try{
$this->connection = new PDO('mysql:dbhost='.$this->DB_HOST.';dbname='.$this->DB_DATABASE, $this->DB_USER, $this->DB_PASSWORD);
$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$this->isConnected = true;
}catch(PDOException $e){
echo "CONNECTION FAILED: ".$e->getMessage();
$this->isConnected = false;
}
return $this->connection;
}

public function inService(){
return $this->isConnected;    
}

}
?>