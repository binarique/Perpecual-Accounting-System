<?php
require_once("../config/config.php");
class Slides{

private $helper;

private $config;

public function __construct($help){
$this->helper = $help;
$this->config = new CONFIG();
}

public function getSlidesAds(){
$response = array();
$stmt = $this->helper->getAllRows("sliderads ORDER BY AD_ID DESC");
$stmt->execute();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
$slide = array(
"AD_ID" => $this->helper->encryptID($row["AD_ID"]),
"BIG_HEADING" => $row["BIG_HEADING"],
"AD_BODY_TEXT" => $row["AD_BODY_TEXT"],
"AD_IMAGE" => $row["AD_IMAGE"],
"BTN_LINK" => $row["BTN_LINK"],
"IMAGE_LINK" =>  $this->helper->getHostURL().$this->config->PROJECT_FOLDER."images/sliders/".$row["AD_IMAGE"],
"POST_DATE" => $row["POST_DATE"]
);
array_push($response, $slide);
}
return $response;
}

public function createSlide($data){
$title = $this->helper->clean($data->title);
$body = $this->helper->clean($data->body);
$featured_image = $this->helper->clean($data->featured_image);
$stmt = $this->helper->runQuery("
INSERT INTO sliderads SET
BIG_HEADING = :utitle,
AD_BODY_TEXT = :ubody,
AD_IMAGE = :ufeaturedimage,
BTN_LINK = ''");
$stmt->bindParam(":utitle", $title);
$stmt->bindParam(":ubody", $body);
$stmt->bindParam(":ufeaturedimage", $featured_image);
if($stmt->execute()){
return true;
}else{
return false;
}
}

}

?>