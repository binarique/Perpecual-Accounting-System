<?php
require_once("../config/helper.class.php");
class TransactionTypes{

private $helper;

public function __construct(){
 $this->helper = new Helper();
}

// Read All Inventory
public function readTransactionType($trx_type_id){
    $stmt2 = $this->helper->runQuery("SELECT * FROM  transaction_types WHERE id = :trxid");
    $stmt2->bindParam(":trxid", $trx_type_id);
    $stmt2->execute();
    $row2 = $stmt2->fetchObject();
    return $row2;
}
// Read unposted inventory To Accounts
// public function readUnpostInvetoryToAccounts(){
//     $response = array();
//     $stmt = $this->helper->runQuery("SELECT * FROM inventory WHERE 	account_posting = false");
//     if($stmt->execute()){
//         while($row = $stmt->fetchObject()){
//             array_push($response, $row);
//         }
//     }
//     return $response;
// }
}
?>