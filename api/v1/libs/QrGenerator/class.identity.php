<?php
require ('vendor/autoload.php');
class IDENTITY{

private $barcode;

private $targetPath = "qr-code/";

private $encrypt_method = "AES-256-CBC";

private $secret_key = 'XyVkdHuTeGhEjKsTlk';

private $secret_iv = 'tkjpq45gfdkgs9ghyt';

private $randomID;

public function __construct(){
$this->barcode = new \Com\Tecnick\Barcode\Barcode();
$this->randomID = rand(1000, 1000000);
}

public function getQRCode($QrValue, $backgroundColor = '#f0f0f0'){
if (! is_dir($this->targetPath)) {
mkdir($this->targetPath, 0777, true);
}
$bobj = $this->barcode->getBarcodeObj('QRCODE,H', $QrValue, - 16, - 16, 'black', array(
        - 2,
        - 2,
        - 2,
        - 2
))->setBackgroundColor($backgroundColor);
$imageData = $bobj->getPngData();
$timestamp = time();
file_put_contents($this->targetPath . $timestamp . '.png', $imageData);
$imagePath = $this->targetPath . $timestamp.".png";
return $imagePath;
}

public function getBarCodeProduct($MRP,  $MFGDate, $EXPDate){
    //mrp must be in numerics
    if (! is_dir($this->targetPath)) {
        mkdir($this->targetPath, 0777, true);
        }
        $productData = "098{$MRP}10{$MFGDate}55{$EXPDate}";
        $bobj = $this->barcode->getBarcodeObj('C128C', "{$productData}", 450, 70, 'black', array(
            0,
            0,
            0,
            0
        ));
        $imageData = $bobj->getPngData();
        $timestamp = time();
        file_put_contents($this->targetPath . $timestamp . '.png', $imageData);
        $imagePath = $this->targetPath . $timestamp.".png";
        return $imagePath;
}
    


public function getRandom(){
return $this->randomID;
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



}
?>