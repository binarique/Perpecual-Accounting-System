<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];

require_once("../config/languages/class.lang.php");

require_once("../config/helper.class.php");

require_once("class.category.php");

$help = new HELPER();

$language = new LANG($help);

$lang_code = $language->getISOCode();

include_once($language->getInstance());

if($REQUEST_METHOD ==  "GET"){
if(isset($_GET["cat_id"])){
$catid = $help->clean($help->get("cat_id"));
$category = new Category($help);
http_response_code(200);
echo json_encode($category->readOneCategory($lang_code,$help->decryptID($catid)));

}else{//REQUEST_METHOD
    
$responseMessage = array(
"message" => $lang["GET_USER_DATA_ERROR"],
"status" => "failed"
);
        
http_response_code(400);
echo json_encode($responseMessage);
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
    