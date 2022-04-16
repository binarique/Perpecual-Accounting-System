<?php
include_once("../config/helper.class.php");

$helper = new HELPER();

echo $helper->passwordEncrypt("12345678");

?>