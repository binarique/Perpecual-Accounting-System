<?php
require_once("../config/helper.class.php");
class Inventory{

private $helper;

public function __construct(){
 $this->helper = new Helper();
}

// Read All Inventory
public function readAllInventory(){
    $response = array();
    $stmt = $this->helper->runQuery("SELECT * FROM inventory");
    if($stmt->execute()){
        while($row = $stmt->fetchObject()){
            array_push($response, $row);
        }
    }
    return $response;
}
// Read unposted inventory To Accounts
public function readUnpostInvetoryToAccounts(){
    $response = array();
    $stmt = $this->helper->runQuery("SELECT * FROM inventory WHERE 	account_posting = false");
    if($stmt->execute()){
        while($row = $stmt->fetchObject()){
            array_push($response, $row);
        }
    }
    return $response;
}
}
?>