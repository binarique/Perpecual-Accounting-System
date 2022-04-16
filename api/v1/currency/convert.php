<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
$REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];

require_once("../config/helper.class.php");

require_once("class.currency.php");

require_once("../config/languages/class.lang.php");

$help = new HELPER();

$headers = $help->decodeHeaders();

$language = new LANG($help);

$langValid = $language->islanguageValid($headers);

if($REQUEST_METHOD ==  "GET"){
$currency = new CURRENCY($help);
if(!isset($_GET["amount"]) || !isset($_GET["symbol"]) || !isset($_GET["float"])){

http_response_code(404);
$responseMessage = array(
"message"=>"Failed to process request, incomplete data",
"status"=>"failed"); 
echo json_encode($responseMessage);

}else if(empty($_GET["amount"]) || empty($_GET["symbol"]) || empty($_GET["float"])){

http_response_code(404);
$responseMessage = array(
"message"=>"Failed to process request, incomplete data",
"status"=>"failed"); 
echo json_encode($responseMessage);

}else if(!is_numeric($_GET["amount"]) || !is_numeric($_GET["float"])){
    
http_response_code(400);
$responseMessage = array(
"message"=>"Failed to process request, invalid data in your request",
"status"=>"failed"); 
echo json_encode($responseMessage);

}else{

$amount = $help->clean($_GET["amount"]);
$symbol = $help->clean($_GET["symbol"]);
$float = $help->clean($_GET["float"]);

$responseMessage = $currency->convertRequested($amount, $float, $symbol);
echo json_encode($responseMessage);
    
}
}else{
$responseMessage = array(
"message"=>"Unknown request method",
"status"=>"failed"
); 
http_response_code(400);
echo json_encode($responseMessage);
    
}
?>