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

$config = new CONFIG();

include_once($language->getInstance());

if($REQUEST_METHOD ==  "GET"){
$account = new ACCOUNTS($help);
if(isset($_GET["user_id"])){
$userid = $help->decryptID($help->clean($_GET["user_id"]));
$verify = $help->clean($_GET["verify"]);
if(!empty($userid)){
    if($account->verifyAccount($userid, $verify)){
        $responseMessage = array(
            "message" => "Account verified successfully",
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
