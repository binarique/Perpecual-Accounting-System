<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];
require_once("../config/languages/class.lang.php");

require_once("../config/helper.class.php");

require_once("class.search.php");

$help = new HELPER();

$language = new LANG($help);

$config  = new CONFIG();

$lang_code = $language->getISOCode();

include_once($language->getInstance());

if($REQUEST_METHOD ==  "GET"){
    
if(!isset($_GET["q"]) || !isset($_GET["client"])){

http_response_code(404);
$responseMessage = array(
"message"=>$lang["SEARCH_ENGINE_EMPTY_REQUEST"],
"status"=>"failed"); 
echo json_encode($responseMessage);

}else{
$search = new SEARCH($help);
$categoryId = isset($_GET["category"]) ? $_GET["category"]:"";
$pageno = (isset($_GET["page"]) && is_numeric($_GET["page"])) ? $_GET["page"]:1;
$key_word = $_GET["q"];
$client = isset($_GET["client"]) ? $_GET["client"] : "webapp";
$perpage = (!empty($_GET["perpage"]) && is_numeric($_GET["perpage"])) ? $_GET["perpage"] : 0;
echo json_encode($search->generalSearch($key_word, $client, $help->decryptID($categoryId), $lang_code, $pageno, $perpage));
}   
}else{//REQUEST_METHOD

$responseMessage = array(
"message"=>$lang["REQUEST_METHOD_ERROR"],
"status"=>"failed"
);

http_response_code(400);
echo json_encode($responseMessage);

}


?>