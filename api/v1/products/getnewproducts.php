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
$product = new Products($help);
$pageno = (isset($_GET["page"]) && is_numeric($_GET["page"])) ? $_GET["page"]:1;
$perpage = (!empty($_GET["perpage"]) && is_numeric($_GET["perpage"])) ? $_GET["perpage"] : 0;

$response = $product->getNewProducts($lang_code, $lang, $pageno, $perpage);
echo json_encode($response);

}else{//REQUEST_METHOD
    $responseMessage = array(
    "message"=>$lang["REQUEST_METHOD_ERROR"],
    "status"=>"failed"
    );
    
    http_response_code(400);
    echo json_encode($responseMessage);
    
    }
?>



