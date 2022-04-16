<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];

require_once("../config/languages/class.lang.php");

require_once("../config/helper.class.php");

require_once("class.currency.php");

$help = new HELPER();

$language = new LANG($help);

$lang_code = $language->getISOCode();

include_once($language->getInstance());

if($REQUEST_METHOD ==  "GET"){

$currency = new CURRENCY($help);

if(count($currency->getSupportedCurrencies()) > 0){
http_response_code(200);
$response = $currency->getSupportedCurrencies();
echo  json_encode($response);
}else{
http_response_code(404);
echo json_encode(array("message" => $lang["SUPPORT_CURRENCIES_EMPTY"], "status" => "failed"));
}

}else{//REQUEST_METHOD
    
$responseMessage = array(
"message" => $lang["REQUEST_METHOD_ERROR"],
"status" => "failed"
);
        
http_response_code(400);
echo json_encode($responseMessage);
        
}
    
?>
    