<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];

require_once("../config/languages/class.lang.php");

require_once("../config/helper.class.php");

require_once("class.accounts.php");

$help = new HELPER();

$language = new LANG($help);

$lang_code = $language->getISOCode();

include_once($language->getInstance());

if($REQUEST_METHOD ==  "GET"){

$account = new ACCOUNTS($help);

$config = new CONFIG();

$responseMessage = $account->getAllUsers($config, $lang_code);
http_response_code(200);    
echo json_encode($responseMessage);


}else{//REQUEST_METHOD
    
$responseMessage = array(
"message" => $lang["REQUEST_METHOD_ERROR"],
"status" => "failed"
);
    
http_response_code(400);
echo json_encode($responseMessage);
    
}

?>
