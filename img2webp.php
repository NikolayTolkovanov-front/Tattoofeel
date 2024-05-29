<?php

set_time_limit(0);

$dir = "/var/www/TATTOOFEEL/storage/web/source/1";
$extensions = ['jpg','jpeg','png'];

if ($handle = opendir($dir)) {
    //read directory
    while (($file = readdir($handle)) !== false) {
        if ($file != "." && $file != "..") {
            $arTitle = explode('.', $file);
            if (count($arTitle) > 1) {
                $ext = strtolower(array_pop($arTitle));
                if (in_array($ext, $extensions)) {
                    array_push($arTitle, 'webp');
                    $command = 'convert '.'"'.$dir.'/'.$file.'" -quality 70 "'.$dir.'/'.implode('.', $arTitle).'"';
                    shell_exec($command);
                }
            }
        }
    }

    closedir($handle);
}
