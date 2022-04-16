<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];

require_once("../config/languages/class.lang.php");

require_once("../config/helper.class.php");

require_once("class.accounts.php");

$help = new HELPER();

$language = new LANG($help);

$lang_code = $language->getISOCode();

include_once($language->getInstance());

$config = new CONFIG();

if($REQUEST_METHOD ==  "GET"){
$account = new ACCOUNTS($help);
if(isset($_GET["admin_id"])){
$adminid = $help->decryptID($help->clean($_GET["admin_id"]));
if(!empty($adminid)){

echo json_encode($account->getAdminDetails($adminid, $config, $lang));

}else{
    $responseMessage = array(
        "message" => $lang["GET_USER_DATA_ERROR"],
        "status" => "failed"
        );
        http_response_code(400);
        echo json_encode($responseMessage);   
}
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
