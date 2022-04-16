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

$default = $currency->getDefaultCurrency();
if(count($default) > 0){

http_response_code(404);
$responseMessage = array(
    "message"=>"No Currencies",
    "status"=>"failed");
echo json_encode($responseMessage);


}else{

http_response_code(200);
echo json_encode($default);

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