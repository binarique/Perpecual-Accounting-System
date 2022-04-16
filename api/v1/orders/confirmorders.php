<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];

require_once("../config/languages/class.lang.php");

require_once("../config/helper.class.php");

require_once("class.orders.php");

$help = new HELPER();

$language = new LANG($help);

$config  = new CONFIG();
$lang_code = $language->getISOCode();
include_once($language->getInstance());
if($REQUEST_METHOD ==  "GET"){
 if(isset($_GET["order_id"])){
$order = new ORDERS($help);
$orderid = $help->decryptID($help->clean($_GET["order_id"]));
if($order->confirmOrder($orderid)){
    $responseMessage = array(
        "message" => "Order confirmed successfully",
        "status" => "success"
        );
        http_response_code(200);
        echo json_encode($responseMessage);   
}else{
    $responseMessage = array(
        "message" => "Something went wrong, please try again laiter",
        "status" => "failed"
        );
        http_response_code(400);
        echo json_encode($responseMessage);   
}
}else{
$responseMessage = array(
   "message"=>$lang["GET_USER_DATA_ERROR"],
   "status"=>"failed"
     );
http_response_code(400);
echo json_encode($responseMessage);     
}
}else{//REQUEST_METHOD
    $responseMessage = array(
    "message"=>$lang["REQUEST_METHOD_ERROR"],
    "status"=>"failed"
    );
    
    http_response_code(400);
    echo json_encode($responseMessage);
    
    }
?>