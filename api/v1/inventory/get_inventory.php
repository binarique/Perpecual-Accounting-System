<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once("class.inventory.php");
$inv = new Inventory();
$inventory = $inv->readAllInventory();
//print_r($inventory);
http_response_code(200);
echo json_encode($inventory);
?>