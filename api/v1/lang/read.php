<?php
header("Access-Control-Allow-Origin: *");

header("Content-Type: application/json; charset=UTF-8");

$REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];

require_once("../config/helper.class.php");

require_once("../config/languages/class.lang.php");

$help = new HELPER();

$headers = $help->decodeHeaders();

$language = new LANG($help);

if($REQUEST_METHOD ==  "GET"){

$langauages = $language->read();

if(count($langauages) > 0){
 
http_response_code(200);
echo json_encode($langauages);

}else{
// set response code - 400 bad request
http_response_code(404);
// tell the user
echo json_encode(array("message" => "No language found", "status" => "failed"));
}

}else{//REQUEST_METHOD
$responseMessage = array(
"message"=>"Unknown request method",
"status"=>"failed"
);

http_response_code(400);
echo json_encode($responseMessage);

}


?>