<?php
error_reporting(0);
// required headers donot remove
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];

require_once("../config/languages/class.lang.php");

require_once("../config/helper.class.php");

require_once("class.accounts.php");

$help = new HELPER();

$language = new LANG($help);

$lang_code = $language->getISOCode();

include_once($language->getInstance());

if($REQUEST_METHOD ==  "POST"){

$data_object = file_get_contents("php://input");

if(!empty($data_object)){

$data = json_decode($data_object);

$account = new Accounts($help);

$email = $data->email;

$password = $data->password;

$login = $account->isAdminLogin($email, $password, $lang);

if(!empty($email) && !empty($password)){

if($login["status"] == "success"){

http_response_code(200);
echo json_encode($login);

}else{

http_response_code(400);
echo json_encode($login);

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