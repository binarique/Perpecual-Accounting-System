<?php
class UPLOADER{

private $helper;

public function ___construct($help){
$this->helper = $help;    
}

public function moveUpload($temploryPath, $destinationPath){
if(move_uploaded_file($temploryPath, $destinationPath)){
return true;
}else{
return false;
}
}

public function isFileValid($file_name, $valid_extensions){
$File_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
$hasExtension = in_array($File_type, $valid_extensions) ? true:false;
return $hasExtension;
}

public function newFileName($file_name){
$File_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));    
$base_file_name  = trim(pathinfo($file_name, PATHINFO_FILENAME));   
$Format_name = str_replace(" ","_",$base_file_name);
$new_name = $Format_name."_".md5(rand(1000,1000000)).".".$File_type;
return $new_name;
}

public function getDimensions($tmpdir, $tempdir, $file_name){
$TEMP_FOLDER = $tempdir.$file_name;
if(move_uploaded_file($tmpdir, $TEMP_FOLDER)){
list($width, $height, $type, $attr) = getimagesize($TEMP_FOLDER);
return array("width"=>$width, "height"=>$height);
}else{
return array();
}
}

public function delete($dir, $file_name){
unlink($dir.$file_name);  
}

public function copyPaste($original_file_location, $destination_location){
    $file_extension = pathinfo($original_file_location,PATHINFO_EXTENSION);
    $file_name_without_extension = pathinfo($original_file_location,PATHINFO_FILENAME);
    $byets = file_get_contents($original_file_location);
    $myfile = fopen($destination_location.$file_name_without_extension.".".$file_extension, "w") or die("Unable to create file!");
    fwrite($myfile, $byets);
    fclose($myfile);
    }

public function cutPaste($original_file_location, $destination_location){
    if(file_exists($original_file_location)){
    $file_extension = pathinfo($original_file_location,PATHINFO_EXTENSION);
    $file_name_without_extension = pathinfo($original_file_location,PATHINFO_FILENAME);
    $byets = file_get_contents($original_file_location);
    $myfile = fopen($destination_location.$file_name_without_extension.".".$file_extension, "w") or die("Unable to create file!");
    fwrite($myfile, $byets);
    fclose($myfile);   
    unlink($original_file_location);
    return true;
    }else{
    return false;
    }
    }

   public function FileSizeConvert($bytes)
    {
        $result = false;
        
        $bytes = floatval($bytes);
            $arBytes = array(
                0 => array(
                    "UNIT" => "TB",
                    "VALUE" => pow(1024, 4)
                ),
                1 => array(
                    "UNIT" => "GB",
                    "VALUE" => pow(1024, 3)
                ),
                2 => array(
                    "UNIT" => "MB",
                    "VALUE" => pow(1024, 2)
                ),
                3 => array(
                    "UNIT" => "KB",
                    "VALUE" => 1024
                ),
                4 => array(
                    "UNIT" => "B",
                    "VALUE" => 1
                ),
            );
    
        foreach($arBytes as $arItem)
        {
            if($bytes >= $arItem["VALUE"])
            {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
                break;
            }
        }
        return $result;
    }

}
?>