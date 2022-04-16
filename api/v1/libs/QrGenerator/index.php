
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>BarCode/qr/reader</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" media="screen" href="main.css" />
<script src="main.js"></script>
</head>
<body>

<?php
require_once("class.identity.php");
$ID = new IDENTITY();
$randm = $ID->encryptID($ID->getRandom());
$image = $ID->getQRCode($randm);
?>
<div class="result-heading">Output:</div>
<img src="<?php echo $image; ?>"  width="150px"  height="150px">
</body>
</html>