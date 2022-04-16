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

require_once("class.slides.php");

$help = new HELPER();

$language = new LANG($help);

$config  = new CONFIG();

$lang_code = $language->getISOCode();

include_once($language->getInstance());

if($REQUEST_METHOD ==  "POST"){
$data_object = file_get_contents("php://input");

if(!empty($data_object)){
$slides = new Slides($help);
//covert json object to array   
$data = json_decode($data_object);
if(empty($data->title)){
$responseMessage = array(
    "message" => "Post title is required",
    "status" => "failed"
    );    
http_response_code(400);
echo json_encode($responseMessage);

}else if(empty($data->body)){
$responseMessage = array(
        "message" => "Body text is required",
        "status" => "failed"
        );    
http_response_code(400);
echo json_encode($responseMessage);

}else if(empty($data->featured_image)){
$responseMessage = array(
        "message" => "Featured image is required",
        "status" => "failed"
        );    
http_response_code(400);
echo json_encode($responseMessage);
}else{
// DO SOMETHING
if($slides->createSlide($data)){
    $responseMessage = array(
        "message" => "Post created successfully",
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
}
}else{
http_response_code(400);
// tell the user that all fields are required
echo json_encode(array(
"message" => "All fields are required",
"status" => "failed")
);
}
}else{
    http_response_code(400);
    // tell the user that all fields are required
    echo json_encode(array(
    "message" => "Invalid request method",
    "status" => "failed")
    );
}
?>