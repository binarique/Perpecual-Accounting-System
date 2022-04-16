<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];

require_once("../../config/helper.class.php");

require_once("../../config/config.php");

require_once("../class.upload.php");

$helper = new HELPER();

$TEMP_FOLDER = "../temp/";

$VALID_EXTENSIONS = array( 'jpeg', 'jpg', 'png');

$config = new CONFIG();

$DESTINATION_FOLDER = $config->PRODUCT_THUMBNAILS_UPLOAD_FOLDER;

$uploader = new UPLOADER($helper);

$headers = $helper->decodeHeaders();

if($REQUEST_METHOD ==  "POST"){
if(!isset($_POST["upload_name"])){

$responseMessage = array(
"message"=>"Unable to upload image, incomplete data",
"status"=>"failed"
);      
http_response_code(400);
echo json_encode($responseMessage); 

}else if(!isset($_FILES[$_POST["upload_name"]]['name'])){

$responseMessage = array(
"message"=>"Unable to upload image, incomplete data",
"status"=>"failed"
);      
http_response_code(400);
echo json_encode($responseMessage); 

}else{
$File_Name = $_FILES[$_POST["upload_name"]]['name'];
$tmp_dir = $_FILES[$_POST["upload_name"]]['tmp_name'];
$fileSize = $_FILES[$_POST["upload_name"]]['size'];

if(!$uploader->isFileValid($File_Name , $VALID_EXTENSIONS)){

$responseMessage = array(
"message"=>"Unable to upload image, invalid file mime type, please check for extensions allowed (".$helper->toString($VALID_EXTENSIONS).")",
"status"=>"failed"
);      
http_response_code(400);
echo json_encode($responseMessage); 

}else if($fileSize > 5242880){

$responseMessage = array(
"message"=>"Unable to upload image, your file size is greater than 5MB",
"status"=>"failed"
);      
http_response_code(400);
echo json_encode($responseMessage); 

}else{
$final_file_name = $uploader->newFileName($File_Name);

$image_data = $uploader->getDimensions($tmp_dir, $TEMP_FOLDER, $final_file_name);

if(count($image_data) > 0){
//700px by 700px;
$width = $image_data["width"];
$height = $image_data["height"];

if($width < 500 && $height < 500){
$responseMessage = array(
"message"=>"Unable to upload image, your image does not meet the requirements of 500x500 pixels and above, your image is ".$width."x".$height." pixels",
"status"=>"failed"
);      

http_response_code(400);
echo json_encode($responseMessage);  
unlink($TEMP_FOLDER.$final_file_name);

}else{

if($uploader->cutPaste($TEMP_FOLDER.$final_file_name, $DESTINATION_FOLDER)){

    $responseMessage = array(
        "image_name"=>$final_file_name,
        "image"=>$helper->getHostURL().$config->PRODUCT_THUMBNAILS_FOLDER.$final_file_name,
        "message"=>"",
        "type"=>"product",
        "status"=>"success"); 
        http_response_code(200);
        echo json_encode($responseMessage); 

}else{

    $responseMessage = array(
        "message"=>"Unable to upload image, service is not available",
        "status"=>"failed");      
        http_response_code(503);
        echo json_encode($responseMessage);
unlink($TEMP_FOLDER.$final_file_name);

}
}
}else{

$responseMessage = array(
"message"=>"File not supported, file is not a valid image",
"status"=>"failed"
);      
http_response_code(400);
echo json_encode($responseMessage);

}
}
}
}else{
$responseMessage = array(
"message"=>"Unknown request method",
"status"=>"failed"
);      
http_response_code(400);
echo json_encode($responseMessage);          
}

?>