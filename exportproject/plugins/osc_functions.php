<?php

/**
 * Tries to delete the directory recursivaly.
 * @return true on success.
 */
function osc_deleteDir($path) {
    if (!is_dir($path))
        return false;

    $fd = @opendir($path);
    if (!$fd)
        return false;

    while ($file = @readdir($fd)) {
        if ($file != '.' && $file != '..') {
            if (!is_dir($path . '/' . $file)) {
                if (!@unlink($path . '/' . $file)) {
                    closedir($fd);
                    return false;
                } else {
                    osc_deleteDir($path . '/' . $file);
                }
            } else {
                osc_deleteDir($path . '/' . $file);
            }
        }
    }
    closedir($fd);

    return @rmdir($path);
}



/**
 * Common interface to zip a specified folder to a file using ziparchive or pclzip
 *
 * @param string $archive_folder full path of the folder
 * @param string $archive_name full path of the destination zip file
 * @return int
 */
function osc_zip_folder($archive_folder, $archive_name) {
    if (class_exists('ZipArchive')) {
        return _zip_folder_ziparchive($archive_folder, $archive_name);
    }
    // if ZipArchive class doesn't exist, we use PclZip
    return _zip_folder_pclzip($archive_folder, $archive_name);
}

/**
 * Zips a specified folder to a file
 *
 * @param string $archive_folder full path of the folder
 * @param string $archive_name full path of the destination zip file
 * @return int
 */
function _zip_folder_ziparchive($archive_folder, $archive_name) {

    $zip = new ZipArchive;
    if ($zip -> open($archive_name, ZipArchive::CREATE) === TRUE) {
        $dir = preg_replace('/[\/]{2,}/', '/', $archive_folder."/");

        $dirs = array($dir);
        while (count($dirs)) {
            $dir = current($dirs);
            $zip -> addEmptyDir(str_replace(TMP_PATH, '', $dir));

            $dh = opendir($dir);
            while (false !== ($_file = readdir($dh))) {
                if ($_file != '.' && $_file != '..') {
                    if (is_file($dir.$_file)) {
                        $zip -> addFile($dir.$_file, str_replace(TMP_PATH, '', $dir.$_file));
                    } elseif (is_dir($dir.$_file)) {
                        $dirs[] = $dir.$_file."/";
                    }
                }
            }
            closedir($dh);
            array_shift($dirs);
        }
        $zip -> close();
        return true;
    } else {
        return false;
    }

}

/**
 * Zips a specified folder to a file
 *
 * @param string $archive_folder full path of the folder
 * @param string $archive_name full path of the destination zip file
 * @return int
 */
function _zip_folder_pclzip($archive_folder, $archive_name) {

    // first, we load the library
    require_once LIB_PATH . 'pclzip/pclzip.lib.php';

    $zip = new PclZip($archive_name);
    if($zip) {
        $dir = preg_replace('/[\/]{2,}/', '/', $archive_folder."/");

        $v_dir = osc_base_path();
        $v_remove = $v_dir;

        // To support windows and the C: root you need to add the
        // following 3 lines, should be ignored on linux
        if (substr($v_dir, 1,1) == ':') {
            $v_remove = substr($v_dir, 2);
        }
        $v_list = $zip->create($dir, PCLZIP_OPT_REMOVE_PATH, $v_remove);
        if ($v_list == 0) {
            return false;
        }
        return true;
    } else {
        return false;
    }

}




function osc_copy($source, $dest, $options=array('folderPermission'=>0755,'filePermission'=>0755)) {
    $result =true;
    if (is_file($source)) {
        if ($dest[strlen($dest)-1]=='/') {
            if (!file_exists($dest)) {
                cmfcDirectory::makeAll($dest,$options['folderPermission'],true);
            }
            $__dest=$dest."/".basename($source);
        } else {
            $__dest=$dest;
        }
        if(function_exists('copy')) {
            $result = @copy($source, $__dest);
        } else {
            $result=osc_copyemz($source, $__dest);
        }
        @chmod($__dest,$options['filePermission']);

    } elseif(is_dir($source)) {
        if ($dest[strlen($dest)-1]=='/') {
            if ($source[strlen($source)-1]=='/') {
                //Copy only contents
            } else {
                //Change parent itself and its contents
                $dest=$dest.basename($source);
                @mkdir($dest);
                @chmod($dest,$options['filePermission']);
            }
        } else {
            if ($source[strlen($source)-1]=='/') {
                //Copy parent directory with new name and all its content
                @mkdir($dest,$options['folderPermission']);
                @chmod($dest,$options['filePermission']);
            } else {
                //Copy parent directory with new name and all its content
                @mkdir($dest,$options['folderPermission']);
                @chmod($dest,$options['filePermission']);
            }
        }

        $dirHandle=opendir($source);
        $result = true;
        while($file=readdir($dirHandle)) {
            if($file!="." && $file!="..") {
                if(!is_dir($source."/".$file)) {
                    $__dest=$dest."/".$file;
                } else {
                    $__dest=$dest."/".$file;
                }
                //echo "$source/$file ||| $__dest<br />";
                $data = osc_copy($source."/".$file, $__dest, $options);
                if($data==false) {
                    $result = false;
                }
            }
        }
        closedir($dirHandle);

    } else {
        $result=true;
    }
    return $result;
}



function osc_copyemz($file1,$file2){
    $contentx =@file_get_contents($file1);
    $openedfile = fopen($file2, "w");
    fwrite($openedfile, $contentx);
    fclose($openedfile);
    if ($contentx === FALSE) {
        $status=false;
    } else {
        $status=true;
    }

    return $status;
}


?>
