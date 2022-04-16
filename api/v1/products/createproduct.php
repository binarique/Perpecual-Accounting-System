<?php
//required headers do not remove
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];

require_once("../config/languages/class.lang.php");

require_once("../config/helper.class.php");

require_once("class.products.php");

$help = new HELPER();

$language = new LANG($help);

$config  = new CONFIG();

$lang_code = $language->getISOCode();

include_once($language->getInstance());

if($REQUEST_METHOD ==  "POST"){

$data_object = file_get_contents("php://input");

if(!empty($data_object)){

$product = new Products($help);

//covert json object to array   
$data = json_decode($data_object);

if(empty($data->product_no)){

$responseMessage = array(
    "message" => $lang["PRODUCT_CREATE_NO_ERROR"],
    "status" => "failed"
    );    
http_response_code(400);
echo json_encode($responseMessage);

}else if(empty($data->product_name)){

$responseMessage = array(
        "message" => $lang["PRODUCT_CREATE_NAME_ERROR"],
        "status" => "failed"
        );    
http_response_code(400);
echo json_encode($responseMessage);

}else if(empty($help->decryptID($data->cat_id))){

$responseMessage = array(
        "message" => $lang["PRODUCT_CAT_ID_ERROR"],
        "status" => "failed"
        );    
http_response_code(400);
echo json_encode($responseMessage);

}else if(empty($help->decryptID($data->units_id))){

$responseMessage = array(
        "message" => $lang["PRODUCT_UNIT_ID_ERROR"],
        "status" => "failed");    
http_response_code(400);
echo json_encode($responseMessage);    

}else if(empty($data->stock_units)){
        $responseMessage = array(
                "message" => $lang["PRODUCT_STOCK_ERROR"],
                "status" => "failed");    
        http_response_code(400);
        echo json_encode($responseMessage);
}else if(empty($data->price_per_unit)){

$responseMessage = array(
        "message" => $lang["PRODUCT_PRICE_PER_UNIT_ERROR"],
        "status" => "failed"
        );    
http_response_code(400);
echo json_encode($responseMessage);  

}else if(!is_numeric($data->price_per_unit)){
    $responseMessage = array(
        "message" => $lang["PRODUCT_PPU_INVALID_CHARS_ERROR"],
        "status" => "failed"
        );    
http_response_code(400);
echo json_encode($responseMessage);  
}else if(empty($data->min_unit_order)){

$responseMessage = array(
        "message" => $lang["PRODUCT_MIN_UNIT_ERROR"],
        "status" => "failed"
        );    
http_response_code(400);
echo json_encode($responseMessage);   

}else if(!is_numeric($data->min_unit_order)){
    $responseMessage = array(
        "message" => $lang["PRODUCT_MINPO_ERROR"],
        "status" => "failed"
        );    
http_response_code(400);
echo json_encode($responseMessage);  
}else if(empty($help->decryptID($data->currency_id))){

$responseMessage = array(
        "message" => $lang["PRODUCT_CURRENCY_ERROR"],
        "status" => "failed"
        );    
http_response_code(400);
echo json_encode($responseMessage);

}else if(empty($data->product_description)){
  
$responseMessage = array(
        "message" => $lang["PRODUCT_DESC_ERROR"],
        "status" => "failed"
        );    
http_response_code(400);
echo json_encode($responseMessage);

}else if(empty($data->product_thumbnail)){

$responseMessage = array(
        "message" => $lang["PRODUCT_THUMB_ERROR"],
        "status" => "failed"
        );    
http_response_code(400);
echo json_encode($responseMessage);

}else if(empty($data->product_details)){
$responseMessage = array(
        "message" => $lang["PRODUCT_DETAILS_ERROR"],
        "status" => "failed"
        );    
http_response_code(400);
echo json_encode($responseMessage);
    
}else{
//TOMMORROW WORK
$pro_array = json_decode($data_object, true);

$retv = $product->createProduct($pro_array, $lang);
echo json_encode($retv);
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