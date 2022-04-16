<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];

require_once("../config/languages/class.lang.php");

require_once("../config/helper.class.php");

require_once("class.orders.php");

$help = new HELPER();

$language = new LANG($help);

$config  = new CONFIG();

$lang_code = $language->getISOCode();

include_once($language->getInstance());

if($REQUEST_METHOD ==  "GET"){
 if(isset($_GET["userid"])){

  $order = new ORDERS($help);

$pageno = (isset($_GET["page"]) && is_numeric($_GET["page"])) ? $_GET["page"]:1;
$perpage = (!empty($_GET["perpage"]) && is_numeric($_GET["perpage"])) ? $_GET["perpage"] : 12;
$userid = $help->decryptID($help->clean($_GET["userid"]));

$response =   $order->getMyTraderOrders($lang_code, $userid, $pageno, $perpage);
echo json_encode($response);

}else{
$responseMessage = array(
   "message"=>$lang["GET_USER_DATA_ERROR"],
   "status"=>"failed"
     );
http_response_code(400);
echo json_encode($responseMessage);     
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



