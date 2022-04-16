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

require_once("class.orders.php");

$help = new HELPER();

$language = new LANG($help);

$config  = new CONFIG();

$lang_code = $language->getISOCode();

include_once($language->getInstance());

if($REQUEST_METHOD ==  "POST"){

$data_object = file_get_contents("php://input");

if(!empty($data_object)){

$order = new ORDERS($help);

//covert json object to array   
$data = json_decode($data_object);

//fields required for all account types

$farmer_id = $help->decryptID($data->farmer_id);

$product_id = $help->decryptID($data->product_id);

$trader_id = $help->decryptID($data->trader_id);

$stock_units_required = $data->stock_units_required;

$order_description = $data->order_description;

//garbage collection  here 
if(empty($farmer_id)){

// set response code - 400 bad request
http_response_code(400);
// tell the user
echo json_encode(array(
    "message" => "Incomplete data request",
    "status" => "failed"
));

}else if(empty($product_id)){


// set response code - 400 bad request
http_response_code(400);
// tell the user
echo json_encode(array(
    "message" => "Incomplete data request",
    "status" => "failed"
));


}else if(empty($trader_id)){

// set response code - 400 bad request
http_response_code(400);
// tell the user
echo json_encode(array(
    "message" => "Incomplete data request",
    "status" => "failed"
));


}else if(empty($stock_units_required)){

// set response code - 400 bad request
http_response_code(400);
// tell the user
echo json_encode(array(
    "message" => "Quantity to stock is required",
    "status" => "failed"
));

}else if(!is_numeric($stock_units_required)){

// set response code - 400 bad request
http_response_code(400);
// tell the user
echo json_encode(array(
    "message" => "Quantity to stock must be numeric",
    "status" => "failed"
));

}else if($stock_units_required <= 0){

//set response code - 400 bad request
http_response_code(400);
// tell the user
echo json_encode(array(
    "message" => "Quantity to stock is invalid",
    "status" => "failed"
));

}else{

if($order->orderItem($farmer_id, $product_id, $stock_units_required, $order_description, $trader_id)){

//set response code - 200 OK request
http_response_code(200);
// tell the user
echo json_encode(array(
    "message" => "Order placed successfully",
    "status" => "success"
));

}else{
//set response code - 400 bad request
http_response_code(400);
// tell the user
echo json_encode(array(
    "message" => "Something went wrong please try agin laiter",
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