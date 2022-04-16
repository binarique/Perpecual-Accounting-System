<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];

require_once("../config/languages/class.lang.php");

require_once("../config/helper.class.php");

require_once("class.products.php");

$help = new HELPER();

$language = new LANG($help);

$config  = new CONFIG();
$lang_code = $language->getISOCode();
include_once($language->getInstance());
if($REQUEST_METHOD ==  "GET"){
 if(isset($_GET["productid"])){
$product = new Products($help);
$productid = $help->decryptID($help->clean($_GET["productid"]));

$response = $product->readOnProduct($productid, $lang_code);
echo json_encode($response);

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