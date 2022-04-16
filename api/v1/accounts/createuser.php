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

require_once("class.accounts.php");

$help = new HELPER();

$language = new LANG($help);

$config  = new CONFIG();

$lang_code = $language->getISOCode();

include_once($language->getInstance());

if($REQUEST_METHOD ==  "POST"){

$data_object = file_get_contents("php://input");

if(!empty($data_object)){

$account = new Accounts($help);

//covert json object to array   
$data = json_decode($data_object);

//fields required for all account types

$first_name = $data->first_name;

$second_name = $data->second_name;

$user_email = $data->user_email;

$country_id = $help->decryptID($data->country_id);

$phone_no = $data->phone_no;

$user_address = $data->user_address;

$user_password = $data->user_password;

$second_password = $data->second_password;

$account_type_id = $help->decryptID($data->account_type_id);

//field that are no required by all account types
$base_currency_id = $help->decryptID($data->base_currency_id);//farmer: true, user: false, deliver: true

$bussiness_name = $data->bussiness_name;//farmer: true, delivery: true, user: false

$nin_no = $data->nin_no;//farmer: true, delivery: true, user: false

$id_front_pic = $data->id_front_pic;//farmer: true, delivery: true, user: false

$id_back_pic = $data->id_back_pic;//farmer: true, delivery: true, user: false

$cover_photo = empty($data->cover_photo) ? $config->DEFAULT_COVER_PHOTO : $data->cover_photo;//farmer: true, delivery: true, user: false

$profile_picture = empty($data->profile_picture) ? $config->DEFAULT_PROFILE_PHOTO : $data->profile_picture;//has a default value
//garbage collection  here 
if(empty($first_name)){

// set response code - 400 bad request
http_response_code(400);
// tell the user
echo json_encode(array(
    "message" => $lang["REG_FIRST_NAME_ERROR"],
    "status" => "failed"
));

}else if(empty($second_name)){

// set response code - 400 bad request
http_response_code(400);
// tell the user
echo json_encode(array(
    "message" => $lang["REG_SECOND_NAME_ERROR"],
    "status" => "failed"
));

}else if(empty($user_email)){

// set response code - 400 bad request
http_response_code(400);
// tell the user
echo json_encode(array(
    "message" => $lang["REG_EMAIL_FIELD_ERROR"],
    "status" => "failed"
));

}else if(!filter_var($user_email, FILTER_VALIDATE_EMAIL)){

// set response code - 400 bad request
http_response_code(400);
// tell the user
echo json_encode(array(
    "message" => $lang["REG_EMAIL_FIELD_INVALID_ERROR"],
    "status" => "failed"
));

}else if(empty($country_id)){

// set response code - 400 bad request
http_response_code(400);
// tell the user
echo json_encode(array(
    "message" => $lang["REG_COUNTRY_FIELD_ERROR"],
    "status" => "failed"
));

}else if(empty($phone_no)){

// set response code - 400 bad request
http_response_code(400);
// tell the user
echo json_encode(array(
    "message" => $lang["REG_PHONENO_FIELD_ERROR"],
    "status" => "failed"
));

}else if(empty($user_address)){

// set response code - 400 bad request
http_response_code(400);
// tell the user
echo json_encode(array(
    "message" => "Your address is required",
    "status" => "failed"
));

}else if(empty($user_password)){

// set response code - 400 bad request
http_response_code(400);
// tell the user
echo json_encode(array(
    "message" => $lang["REG_PASSWORD_FIELD_ERROR"],
    "status" => "failed"
));

}else if(empty($second_password)){
// set response code - 400 bad request
http_response_code(400);
// tell the user
echo json_encode(array(
    "message" => $lang["REG_PASSWORD2_FIELD_ERROR"],
    "status" => "failed"
));   
}else if(strlen($user_password) < 8){

// set response code - 400 bad request
http_response_code(400);
// tell the user
echo json_encode(array(
    "message" => $lang["REG_PASSWORD_LENGTH_ERROR"],
    "status" => "failed"
));

}else if($user_password !== $second_password){
// set response code - 400 bad request
http_response_code(400);
// tell the user
echo json_encode(array(
    "message" => $lang["REG_PASSWORD_NOTEQUAL_ERROR"],
    "status" => "failed"
));
}else if(empty($account_type_id)){

// set response code - 400 bad request
http_response_code(400);
// tell the user
echo json_encode(array(
    "message" => $lang["REG_ACCTYPE_ERROR"],
    "status" => "failed"
));

}else{
echo json_encode($account->registerUser(
    $first_name,
    $second_name,
    $user_email,
    $country_id,
    $phone_no,
    $user_address,
    $user_password,
    $account_type_id,
    $base_currency_id,
    $bussiness_name,
    $nin_no,
    $id_front_pic,
    $id_back_pic,
    $cover_photo,
    $profile_picture,
    $lang 
));
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