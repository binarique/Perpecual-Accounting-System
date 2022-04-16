<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];

require_once("../config/languages/class.lang.php");

require_once("../config/helper.class.php");

require_once("class.locale.php");

$help = new HELPER();

$language = new LANG($help);

$lang_code = $language->getISOCode();

include_once($language->getInstance());

if($REQUEST_METHOD ==  "GET"){

$locale = new Locale($help);
$response = $locale->getSupportedCountries();

if(count($locale->getSupportedCountries()) > 0){

http_response_code(200);

echo json_encode($response);
}else{
http_response_code(404);
echo json_encode(array("message" => $lang["SUPPORT_COUNTRIES_EMPTY"], "status" => "failed"));
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
        