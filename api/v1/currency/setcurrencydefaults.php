<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];

require_once("../config/helper.class.php");

require_once("class.currency.php");

require_once("../config/languages/class.lang.php");

$help = new HELPER();

$headers = $help->decodeHeaders();

$language = new LANG($help);

$langValid = $language->islanguageValid($headers);

if($REQUEST_METHOD ==  "POST"){
$currency = new CURRENCY($help);

http_response_code(201);

$currency->setDefaultCurrency(false, null);

$responseMessage = array(
    "message"=>"Defaults created",
    "status"=>"success");
echo json_encode($responseMessage);

}else{
$responseMessage = array(
"message"=>"Unknown request method",
"status"=>"failed"
); 
http_response_code(400);
echo json_encode($responseMessage);
    
}
?>