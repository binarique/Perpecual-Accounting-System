<?php
require_once("../libs/locations/geoplugin.class.php");

class CURRENCY{

public $helper;

public $geo;

public function __construct($help, $baseCurrency = 'USD'){
$this->helper = $help;
$this->geo = new geoPlugin($baseCurrency);
$this->geo->locate();
}



public function getMyCurrencyId(){
$ip = $_SERVER['REMOTE_ADDR'];
$stmt1 = $this->helper->runQuery("SELECT * FROM currency_confing WHERE user_ip = :uuserip");
$stmt1->bindParam(":uuserip", $ip);
$stmt1->execute();
$row = $stmt1->fetch(PDO::FETCH_ASSOC);
return $row["currency_id"];
}

public function getDefaultCurrency(){
    $currency_id  = $this->getMyCurrencyId();
    $stmt1 = $this->helper->runQuery("SELECT * FROM supported_currency WHERE currency_id = :ucurrency");
    $stmt1->bindParam(":ucurrency", $currency_id);
    $stmt1->execute();
    $row = $stmt1->fetch(PDO::FETCH_ASSOC);
    if($stmt1->rowcount() > 0){
    return array(
    "id" =>$this->helper->encryptID($row["currency_id"]),
    "iso_code" =>$row["currency_iso_code"],
    "symbol" =>$row["currency_symbol"],
    "isBaseCurrency" =>$row["isBaseCurrency"]);
    }else{
    return array();
    }
    }

    public function getSupportedCurrencies(){
        $response = array();
        $stmt1 = $this->helper->runQuery("SELECT * FROM supported_currency");
        $stmt1->execute();
        if($stmt1->rowcount() > 0){
        while($row = $stmt1->fetch(PDO::FETCH_ASSOC)){
       array_push($response, array(
        "currency_id" => $this->helper->encryptID($row["currency_id"]),
        "currency_iso_code" => $row["currency_iso_code"],
        "currency_symbol" => $row["currency_symbol"],
        "inUse" => $row["inUse"],
        "prefixed" => $row["prefixed"],
        "indented" => $row["indented"],
        "message" => "",
        "status" => "success"
        )
        );
        }
        }else{
        //nothing found
        }
        return $response;
        }

        public function getCurrencyById($currency_id){
            $stmt1 = $this->helper->runQuery("SELECT * FROM supported_currency WHERE currency_id = :ucid");
            $stmt1->bindParam(":ucid", $currency_id);
            $stmt1->execute();
            if($stmt1->rowcount() > 0){
            $row = $stmt1->fetch(PDO::FETCH_ASSOC);
            return array(
            "currency_id" => $this->helper->encryptID($row["currency_id"]),
            "currency_iso_code" => $row["currency_iso_code"],
            "currency_symbol" => $row["currency_symbol"],
            "inUse" => $row["inUse"],
            "prefixed" => $row["prefixed"],
            "indented" => $row["indented"],
            "message" => "",
            "status" => "success"
            );
            }else{
            //nothing found
            return array();
            }
            }


public function clearBaseCurrency(){
$stmt1 = $this->helper->runQuery("UPDATE supported_currency SET isBaseCurrency = false");
if($stmt1->execute()){
return true;
}else{
return false;
}
}

public function setBaseCurrency($currency_id){
$currency_id = $this->helper->decryptID($this->helper->clean($currency_id));   
if($this->clearBaseCurrency()){
$stmt1 = $this->helper->runQuery("UPDATE supported_currency SET isBaseCurrency = true WHERE currency_id = :ucurrencyid");
$stmt1->bindParam(":ucurrencyid", $currency_id);
if($stmt1->execute()){
return true;
}else{
return false;
}
}else{
return false;    
}
}

public function setDefaultCurrency($manual = false, $currency_id2){    
//set all currencies supported 
$ip = $_SERVER['REMOTE_ADDR'];

if(!$manual){
if($this->geo->isLocalHost()){
//offline set default base currency
$stmt4 = $this->helper->runQuery("SELECT * FROM supported_currency WHERE isBaseCurrency = true");
$stmt4->execute();
$row = $stmt4->fetch(PDO::FETCH_ASSOC);
$currency_id1 = $row["currency_id"];

if(!$this->isCurrencyConfigAvailable($ip)){
$this->saveCurrencyConfig($currency_id1, $ip);
}

}else{
//get default currency basing on the location
$CURRENCY_CODE =  strtoupper($this->geo->currencyCode);

$stmt2 = $this->helper->runQuery("SELECT * FROM supported_currency WHERE currency_iso_code = :ucurrencycode");
$stmt2->bindParam(":ucurrencycode", $CURRENCY_CODE);
$stmt2->execute();
$row1 = $stmt2->fetch(PDO::FETCH_ASSOC);
$currency_id = $row1["currency_id"];


if(!$this->isCurrencyConfigAvailable($ip)){
$this->saveCurrencyConfig($currency_id, $ip);
}

}
}else{
$currency_id2 = $this->helper->decryptID($this->helper->clean($currency_id));
if(!$this->isCurrencyConfigAvailable($ip)){
$this->saveCurrencyConfig($currency_id2, $ip);
}

}
}


public function isCurrencyConfigAvailable($ip){
$stmt5 = $this->helper->runQuery("SELECT * FROM currency_confing WHERE user_ip = :uip");
$stmt5->bindParam(":uip", $ip);
$stmt5->execute();
$counter = $stmt5->rowCount();
if($counter > 0){
return true;
}else{
return false;
}
}


public function saveCurrencyConfig($currency_id, $ip){
$stmt5 = $this->helper->runQuery("INSERT INTO currency_confing SET user_ip = :uip, currency_id = :ucurrency_id");
$stmt5->bindParam(":ucurrency_id", $currency_id);
$stmt5->bindParam(":uip", $ip);
if($stmt5->execute()){
return true;
}else{
return false;
}
}

public function convertRequested($amount, $float = 2, $symbol = true){
if($this->geo->isLocalHost()){
$stmt1 = $this->helper->runQuery("SELECT * FROM supported_currency WHERE isBaseCurrency = true");
$stmt1->execute();
$row = $stmt1->fetch(PDO::FETCH_ASSOC);
if($symbol == "true"){
return array(
    "amount"=>$row["currency_symbol"]." ".round($amount, $float),
    "message"=>"",
    "status"=>"success"
);
}else{
return array(
        "amount"=>round($amount, $float),
        "symbol"=>$row["currency_symbol"],
        "message"=>"",
        "status"=>"success"
);   
}
}else{

return array(
    "amount"=>$this->geo->convert($amount, $float, $symbol),
    "symbol"=>$row["currency_symbol"],
    "message"=>"",
    "status"=>"success"
); 

}
}

public function convert($amount, $float = 2, $symbol=true){
if($this->geo->isLocalHost()){
$stmt1 = $this->helper->runQuery("SELECT * FROM supported_currency WHERE isBaseCurrency = true");
$stmt1->execute();
$row = $stmt1->fetch(PDO::FETCH_ASSOC);
if($symbol){
return $row["currency_symbol"]." ".round($amount, $float);
}else{
return $this->geo->convert($amount, $float, $symbol);
}
}
}

}
?>