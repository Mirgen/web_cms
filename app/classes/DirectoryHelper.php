<?php

/**
 * Description of Directory
 */
class DirectoryHelper
{

    public static function recurseCopy($src,$dst) { 
        $dir = opendir($src); 
        @mkdir($dst); 
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 
                    DirectoryHelper::recurseCopy($src . '/' . $file,$dst . '/' . $file); 
                } 
                else { 
                    copy($src . '/' . $file,$dst . '/' . $file); 
                } 
            } 
        } 
        closedir($dir); 
    }

    /**
     * public function deleteDirectory, recursively deletes directory and all its content
     * 
     * @param string $dirPath
     * @return bool
     */
    public static function deleteDirectory($dirPath){
        if (is_dir($dirPath)) {
            $files = array_diff(scandir($dirPath), array('.','..'));
            foreach ($files as $file) {
              (is_dir($dirPath . $file)) ? DirectoryHelper::deleteDirectory($dirPath . "/" . $file) : unlink($dirPath . "/" . $file);
            }
            return rmdir($dirPath);
        }
        return true;
    }
}