<?php
// required headers do not remove
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];

require_once("../config/languages/class.lang.php");

require_once("../config/helper.class.php");

require_once("class.messager.php");

$help = new HELPER();

$language = new LANG($help);

$config  = new CONFIG();

$lang_code = $language->getISOCode();

include_once($language->getInstance());

if($REQUEST_METHOD ==  "POST"){

$data_object = file_get_contents("php://input");

if(!empty($data_object)){
//covert json object to array   
$data = json_decode($data_object);
//fields required for all account types
$order_id = $help->decryptID($data->order_id);
$user1 = $help->decryptID($data->user1);
$user2 = $help->decryptID($data->user2);
$message = $data->message;
//garbage collection  here 
if(empty($message)){
// set response code - 400 bad request
http_response_code(400);
// tell the user
echo json_encode(array(
    "message" => "Incomplete data request",
    "status" => "failed"
));

}else{
///SEND MESSAGE
$msg = new MESSAGER($help);
if($msg->sendMSG($order_id, $user1, $user2, $message)){
// set response code - 200 OK request
http_response_code(200);
// tell the user
echo json_encode(array(
    "message" => "Message sent successfuly",
    "status" => "success"
));
}else{
//set response code - 400 bad request
http_response_code(400);
// tell the user
echo json_encode(array(
    "message" => "Something went wrong, please try again laiter",
    "status" => "failed"
));

}
}
}else{
// set response code - 400 bad request
http_response_code(400);
// tell the user that all fields are required
echo json_encode(array(
"message" => $lang["ALL_LOGIN_EMPTY_ERROR"],
"status" => "failed")
);
}
}else{
$responseMessage = array(
"message" => $lang["REQUEST_METHOD_ERROR"],
"status" => "failed"
);
http_response_code(400);
echo json_encode($responseMessage);
}
    
    ?>