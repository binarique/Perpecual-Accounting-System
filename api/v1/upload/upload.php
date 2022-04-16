<?php

echo "\n";
echo '<pre>';
       if (move_uploaded_file($_FILES["file_upl"]["tmp_name"], "images/".$_FILES["file_upl"]["name"])) {

           echo "File is valid, and was successfully uploaded.\n";
       } else {
           echo "Possible file upload attack!\n";
       }
?>