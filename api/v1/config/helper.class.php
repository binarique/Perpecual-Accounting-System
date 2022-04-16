<?php
require_once("config.php");
require_once("database/class.db.php");
/********************************* 
E-COMMERCE MANAGEMENT SYSTEM
Author : WALULYA FRANCIS
Company: ©CodingAnthem 2012-2020
EMAIL:walulyafrancis@gmail.com
PAYPAL:walulyafrancis@gmail.com
Mobile:0756743152
*********************************/

class HELPER{

public $conn;

public $config;

private $encrypt_method;

private $secret_key;

private $secret_iv;

private $service;

public function __construct(){
$db = new DB();
$connection = $db->dbConnection();
$this->conn = $connection;
$this->config  = new CONFIG();
$this->service = $db->inService();
//encrption
$this->encrypt_method = $this->config->ENCRYPTION_METHOD;
$this->secret_key = $this->config->SECRET_KEY;
$this->secret_iv = $this->config->SECRET_PASSWORD;
}

public function isDBInService(){
return $this->service;   
}

public function runQuery($sql){
$query = $this->conn->prepare($sql);
return $query;
}


public function getPageLimit($page_no){
$limit = 'LIMIT '. ($page_no-1) * $this->config->RECORDS_PER_PAGE  .','.$this->config->RECORDS_PER_PAGE; 
return $limit;
}

public function getTotalPages($totalitems){
return ceil($totalitems/$this->config->RECORDS_PER_PAGE);
}
    
public function encryptID($request){
$output = false;  
// hash
$key = hash('sha256', $this->secret_key);
// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
$iv = substr(hash('sha256', $this->secret_iv), 0, 16);
$output = openssl_encrypt($request, $this->encrypt_method, $key, 0, $iv);
$output = base64_encode($output);
return $output;
}

public function get($request){
$RES = trim($_GET[$request]);
return $RES;
}

public function getUserIp(){
return $_SERVER['REMOTE_ADDR'];   
}
    
public function post($request){
$RES = trim($_POST[$request]);
return $RES;
}

public function clean($data){
return htmlspecialchars(strip_tags(trim($data)));
}

public function getHostURL(){
//$protol = $_SERVER['HTTPS'] ? "https://" : "http://";
$url = "http://".$_SERVER['HTTP_HOST']."/"; 
return $url;   
}

//create category
public function createColumn($column, $data){
$keys = array_keys($data);
$dataString = "";
for($i = 0; $i < count($keys); $i++){
$colum_name = $keys[$i]."_".$column; 
$colum_key = ":u".$keys[$i]; 
$dataString .= $colum_name."=".$colum_key.", ";
} 
return trim($dataString, ", ");  
}

public function createColumn2($column, $data, $keyDiff){
    $keys = array_keys($data);
    $dataString = "";
    for($i = 0; $i < count($keys); $i++){
    $colum_name = $keys[$i]."_".$column; 
    $colum_key = ":u".$keyDiff.$keys[$i]; 
    $dataString .= $colum_name."=".$colum_key.", ";
    } 
    return trim($dataString, ", ");  
    }

public function getLangCode($headers){
$langPresent = !empty($headers["lang"]) ? true: false;
if($langPresent){
return   htmlspecialchars(strip_tags(trim($headers["lang"])));
}else{
return "en";
}
}

public function getLangPath(){
return $this->config->LANGUAGES_FOLDER;    
}

public function decryptID($request){
$output = false;  
// hash
$key = hash('sha256', $this->secret_key);
// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
 $iv = substr(hash('sha256', $this->secret_iv), 0, 16);
//decrypt the given text/string/number
 $output = openssl_decrypt(base64_decode($request), $this->encrypt_method, $key, 0, $iv);
return $output;
}
        
public function passwordEncrypt($request){
$newRequest  = hash('sha256', $request);
return $newRequest;
}

public function redirect($link){
header("Location:$link");
}

public function getAllRows($table){
$query = $this->conn->prepare("SELECT * FROM  $table ");
return $query;
}

public function getRows($table, $condition){
$query = $this->conn->prepare("SELECT * FROM  $table WHERE $condition");
return $query;
}
                
public function runDeleteQuery($table, $condition){
$query = $this->conn->prepare("DELETE FROM  $table WHERE $condition");
return $query;
}
                
public function runUpdateQuery($table, $condition){
$query = $this->conn->prepare("UPDATE  $table WHERE $condition");
return $query;
}
                
public function runInsertQuery($table, $rows, $values){
$query = $this->conn->prepare("INSERT INTO  $table($rows) VALUES($values)");
return $query;
}


public function generateUUID() {
return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
// 32 bits for "time_low"
mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

// 16 bits for "time_mid"
mt_rand( 0, 0xffff ),

// 16 bits for "time_hi_and_version",
// four most significant bits holds version number 4
mt_rand( 0, 0x0fff ) | 0x4000,

// 16 bits, 8 bits for "clk_seq_hi_res",
// 8 bits for "clk_seq_low",
// two most significant bits holds zero and one for variant DCE1.1
mt_rand( 0, 0x3fff ) | 0x8000,

// 48 bits for "node"
mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
);
}

public function decodeHeaders(){
    $headers = array();
    foreach (getallheaders() as $name => $value) {
    $headers[$name] = $value;
    }
    return $headers;     
    }
    
    
    public function execPostRequest($url, $data, $headers = array())
    {
    $requestHeaders = array('Content-Type: application/json','Content-Length: '.strlen($data));
    for($i = 0; $i < count($headers); $i++){
    array_push($requestHeaders, $headers[$i]);    
    }
     $ch = curl_init($url);
     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
     curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
     curl_setopt($ch, CURLOPT_TIMEOUT, 5);
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
     //execute post
     $result = curl_exec($ch);
     //close connection
     curl_close($ch);
     $jsonResult = json_decode($result,true); 
     return  $jsonResult;
    }

    public function execGetRequest($url, $data, $headers = array())
    {
    $requestHeaders = array('Content-Type: application/json','Content-Length: '.strlen($data));
    for($i = 0; $i < count($headers); $i++){
    array_push($requestHeaders, $headers[$i]);    
    }
     $ch = curl_init($url);
     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
     curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
     curl_setopt($ch, CURLOPT_TIMEOUT, 5);
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
     //execute post
     $result = curl_exec($ch);
     //close connection
     curl_close($ch);
     $jsonResult = json_decode($result,true); 
     return  $jsonResult;
    }

public function getRandom(){
return rand(1000,1000000);    
}
    
public function execUploadRequest($url, $params, $filePath, $type, $fileName, $newheaders)
{
$ch = curl_init();
$headers =  array_merge(array('Content-Type: multipart/form-data', 'Content-Type: application/json'), $newheaders);
$data = array_merge(array($params["upload_name"] => curl_file_create($filePath, $type, $fileName)), $params);
//start curl request
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$response = curl_exec($ch);
curl_close($ch);
$jsonResult = json_decode($response, true); 
return $response;
}

public function toString($arrayToConvert){
$dataString = "";    
foreach($arrayToConvert as $x){
$dataString .= $x.", ";
}
return trim($dataString, ", ");
}


}
?>