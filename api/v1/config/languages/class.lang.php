<?php
class LANG{

public $helper;
public $headers;

public function __construct($helper){
$this->helper = $helper;
$this->headers = $helper->decodeHeaders();
}

public function islanguageValid(){
$langPresent = !empty($this->headers["lang"]) ? true: false;
if($langPresent){

$lang = $this->helper->clean($this->headers["lang"]);

$query = $this->helper->runQuery("SELECT * FROM languages WHERE lang_iso_code = :ulang");

$query->bindParam(":ulang", $lang);

$query->execute();

if($query->rowCount() > 0){
return true;    
}else{
return false;
}
}else{
return true;
}
}

public function getISOCode(){
return $this->helper->getLangCode($this->headers);    
}

public function getInstance(){
if($this->islanguageValid()){
$langcode = strtolower($this->getISOCode());
return $this->helper->getLangPath()."lang.".$langcode.".php";
}else{
return $this->helper->getLangPath()."lang.en.php";
}
}

public function read(){
$response = array();
$stmt = $this->helper->runQuery("SELECT * FROM languages WHERE use_lang = true");
$stmt->execute();
$counter = $stmt->rowCount();
if($counter > 0){
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
$category = array(
"lang_id" => $row["lang_id"],
"lang_iso_code"=> $row["lang_iso_code"],
"lang_name" => $row["lang_name"],
"isDefault" => $row["isDefault"],
"message" => "",
"status" => "success"
);
array_push($response, $category);
}
}
return $response;
}

public function isLangFieldGiven($resarray){
$isGiven = false;
$dblangs = $this->read();
for($i = 0; count($dblangs) > $i; $i++){
$dblang = $dblangs[$i];
if($dblang["isDefault"]){
if(array_key_exists($dblang["lang_iso_code"], $resarray)){
$isGiven = true; 
}
}
}
return $isGiven;
}

public function languageFieldsProvided($data){
$query = $this->helper->getAllRows("languages");
$query->execute();  
$langcount = $query->rowCount();
$error = "";
if(count($data) > $langcount){
$error = "BAD_REQUEST";  
}else if($langcount !== count($data)){  
$error =  "INCOMPLETE_REQUEST";   
}else{
$keys = array_keys($data);
for($i = 0; $i < count($keys); $i++){
$lang = $this->helper->clean($keys[$i]);
$query2 = $this->helper->runQuery("SELECT * FROM languages WHERE lang_iso_code = :ulang");
$query2->bindParam(":ulang", $lang);
$query2->execute();  
$row = $query2->fetch(PDO::FETCH_ASSOC);
$enable = $row["use_lang"];
$langcount = $query2->rowCount();
if($langcount == 0){
$error = "BAD_REQUEST"; 
}
if($enable && empty($data[$lang])){
$error =  "INCOMPLETE_REQUEST";      
}
}
}//else
return $error;
}


}
?>