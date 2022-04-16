<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];

require_once("../config/languages/class.lang.php");

require_once("../config/helper.class.php");

require_once("class.messager.php");

$help = new HELPER();

$language = new LANG($help);

$lang_code = $language->getISOCode();

$config = new CONFIG();

include_once($language->getInstance());

if($REQUEST_METHOD ==  "GET"){
if(!empty($_GET["order_id"]) && !empty($_GET["user1_id"]) && !empty($_GET["user2_id"])){
///SEND MESSAGE
$msg = new MESSAGER($help);

$order_id = $help->decryptID($help->clean($_GET["order_id"]));
$user1_id = $help->decryptID($help->clean($_GET["user1_id"]));
$user2_id = $help->decryptID($help->clean($_GET["user2_id"]));

$responseMessage = $msg->getMessages($order_id, $user1_id, $user2_id, $lang_code);
http_response_code(200);
echo json_encode($responseMessage);
}else{
    $responseMessage = array(
        "message" => $lang["GET_USER_DATA_ERROR"],
        "status" => "failed"
        );
        http_response_code(400);
        echo json_encode($responseMessage);
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
