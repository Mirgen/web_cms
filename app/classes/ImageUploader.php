<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UploadImages
 *
 * @author Jiri Kvapil
 */
class ImageUploader
{
    private $requestFiles = NULL;

    private $destinationFolder = "";

    private $resize = true;

    private $createThumbnails = false;

    private $formInputName = NULL;

    // default: www/images/
    private $imagesFolder = "images/";

    /*
     * string save new file under this name
     */
    private $newFileName = NULL;

    // constructed in constructor
    private $baseDir = "";

    private $maxWidth = 1000;

    private $maxHeight = 1000;

    private $thumbnailMaxWidth = 400;

    private $thumbnailMaxHeight = 400;

    /*
     * array $files array with files to be aploaded
     */
    private $files = array();

    private $result = array();

    public function __construct($requestFiles, $destinationFolder = "", $resize = true, $createThumbnails = false, $formInputName = NULL)
    {
        $this->requestFiles = $requestFiles;
        $this->destinationFolder = $destinationFolder;
        $this->baseDir = __DIR__ . "/../../www/" . $this->imagesFolder . $this->destinationFolder . "/";

        if($resize !== true){
            $this->resize = $resize;
        }
        if($createThumbnails !== false){
            $this->createThumbnails = $createThumbnails;
        }
        if($formInputName !== NULL){
            $this->formInputName = $formInputName;
        }
    }

    public function setNewFileName($newFileName){
        $this->newFileName = $newFileName;
    }

    public function setMaxWidth($width){
        $this->maxWidth = $width;
    }

    public function setMaxHeight($height){
        $this->maxWidth = $height;
    }

    public function thumbnailMaxWidth($width){
        $this->thumbnailMaxWidth = $width;
    }

    public function thumbnailMaxHeight($height){
        $this->thumbnailMaxWidth = $height;
    }

    public function deleteImage($image){
        if(true === file_exists($this->baseDir . $image)){
            return unlink($this->baseDir . $image);
        }
        return true;
    }

    public function deleteDirectory(){
        if (is_dir($this->baseDir)) {
            $files = array_diff(scandir($this->baseDir), array('.','..'));
            foreach ($files as $file) {
              (is_dir($this->baseDir. $file)) ? deleteDirectory($this->baseDir . $file) : $this->deleteImage($file);
            }
            return rmdir($this->baseDir);
        }
        return true;
    }

    // execute upload action
    public function startUpload(){
        $postFiles = $this->requestFiles;

        // if formInputName was passed upload only images of file input given by formInputName
        if($this->formInputName !== NULL){
            $this->files = array($this->formInputName => $postFiles[$this->formInputName]);
        }
        // else upload all files from all file inputs
        else {
            $this->files = $postFiles;
        }

        // parse file array and upload files
        foreach($this->files as $files){
            if(false === is_array($files)){
                $files = array($files);
            }
            // finaly execute the upload
            $this->upload($files);
        }

        return $this->result;
    }

    private function upload($files){

        foreach ($files as $file) {
            if($file->isImage() && $file->isOk()) {
                $fileExtension = strtolower(mb_substr($file->getSanitizedName(), strrpos($file->getSanitizedName(), ".")));
                if(NULL === $this->newFileName){
                    $this->newFileName = uniqid(rand(0,20), TRUE);
                }
                $wholePath = $this->baseDir . $this->newFileName . $fileExtension;
                $file->move($wholePath);

                // resize main image:
                if($this->resize){
                    $image = \Nette\Image::fromFile($wholePath);
                    if($image->getWidth() > $this->maxWidth) {
                      $image->resize($this->maxWidth, $this->maxHeight);
                    }
                    $image->sharpen();
                    $image->save($wholePath);
                }

                // thumbnail image if needed:
                if(true === $this->createThumbnails){
                    $image_thumb = \Nette\Image::fromFile($wholePath);
                    // resize image to be smaller:
                    if($image_thumb->getWidth() > $this->thumbnailMaxWidth) {
                        $image_thumb->resize($this->thumbnailMaxWidth, NULL);
                    }
                    // resize to square:
                    if($image_thumb->getHeight() > $this->thumbnailMaxHeight){
                        // calculate TOP coordinate to fit needed rectangle to the middle of the image
                        $top = ($image_thumb->getHeight() - $this->thumbnailMaxHeight)/2;
                        $image_thumb->crop(0, $top, $this->thumbnailMaxWidth, $this->thumbnailMaxHeight);
                    }
                    $image_thumb->sharpen();
                    $image_thumb->save($baseDir . $this->newFileName . "_t" . $fileExtension);
                }

                $this->result[] = array("name" => $this->newFileName, "extension" => $fileExtension, "original" => $file->name);
            } else {
                $this->result[] = array("error" => $file->error, "original" => $file->name);
            }
        }
    }
}