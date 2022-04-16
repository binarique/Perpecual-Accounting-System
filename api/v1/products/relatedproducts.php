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
 if(!isset($_GET["category"])){
    $responseMessage = array(
        "message"=>$lang["GET_USER_DATA_ERROR"],
        "status"=>"failed"
          );
     http_response_code(400);
     echo json_encode($responseMessage); 
 }else if(!isset($_GET["product_id"])){
    $responseMessage = array(
        "message"=>$lang["GET_USER_DATA_ERROR"],
        "status"=>"failed"
          );
     http_response_code(400);
     echo json_encode($responseMessage); 
 }else{
$catid = $help->decryptID($help->clean($_GET["category"]));
$productid = $help->decryptID($help->clean($_GET["product_id"]));

$product = new Products($help);
$pageno = (isset($_GET["page"]) && is_numeric($_GET["page"])) ? $_GET["page"]:1;
$perpage = (!empty($_GET["perpage"]) && is_numeric($_GET["perpage"])) ? $_GET["perpage"] : 0;
$response = $product->getRelatedProducts($lang_code, $catid, $productid, $pageno, $perpage);

http_response_code(200);
echo json_encode($response);

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



