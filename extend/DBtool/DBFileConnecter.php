<?php

 /*
 *
 * @author: MazeR
 * @email: mazer0701@gmail.com
 * @lastUpdate: Set 12 2017
 * @Description: DBTool Class for CU File Data
 * @depend: (1)thinkPHP5.x think\Db static class
 *
*/

namespace DBTool;

use think\Db;
use Gregwar\Image\Image;

class DBFileConnecter
{
    //variable area
    private $fileArray = null;
    private $tableName = null;
    private $privateKeyId = null;
    private $privateKeyName = 'id';
    private $uploadPath = ROOT_PATH . 'public' . DS . 'static' . DS . 'index' . DS . 'upload';

    //overload construct by Static Factory Method Pattern
    public function __construct() {}
    public static function withTableName($tableName) {
        $instance = new self();
        $instance->setTableName($tableName);
        return $instance;
    }
    public static function withFileArray($fileArray) {
        $instance = new self();
        $this->setFileArray($fileArray);
        return $instance;
    }
    public static function withTableName_FileArray($tableName, $fileArray) {
        $instance = new self();
        $instance->setTableName($tableName);
        $instance->setFileArray($fileArray);
        return $instance;
    }
    public static function withTableName_FileArray_PrivateKeyId($tableName, $fileArray, $privateKeyId) {
        $instance = new self();
        $instance->setTableName($tableName);
        $instance->setFileArray($fileArray);
        $instance->setPrivateKeyId($privateKeyId);
        return $instance;
    }
    public static function withAll($tableName, $fileArray, $privateKeyId, $privateKeyName) {
        $instance = new self();
        $instance->setTableName($tableName);
        $instance->setFileArray($fileArray);
        $instance->setPrivateKeyId($privateKeyId);
        $instance->setPrivateKeyName ($privateKeyName);
        return $instance;
    }

    //methon area
    public function upFileRow(){
        if($this->fileArray == null){
            return;
        }
        if($this->privateKeyId == null){
            throw new \Exception('update cannot without id');
        }
        try{
            $updateData = array();
            foreach($this->fileArray as $key => $value ) {
                $info = $value->rule('md5')->move($this->uploadPath);
                if($info) {
                    $logoPath = DS .'upload' . DS . $info->getSaveName();
                    $updateData[$key] = $logoPath;
                }else {
                    throw new \Exception($file->getError());
                }
            }
            if (!empty($updateData)) {
                Db::table($this->tableName)->where('id', $this->privateKeyId)->update($updateData);
            }
        }catch (\Exception $e) {
            throw $e;
        }
    }

    public function createFileRow(){
        if($this->fileArray == null){
            return;
        }
        try{
            $updateData = array();
            foreach($this->fileArray as $key => $value ) {
                $info = $value->rule('md5')->move($this->uploadPath);
                if($info) {
                    $logoPath = DS .'upload' . DS . $info->getSaveName();
                    $updateData[$key] = $logoPath;
                }else {
                    throw new \Exception($file->getError());
                }
            }
            if (!empty($updateData)) {
                return Db::table($this->tableName)->insertGetId($updateData);
            }
        }catch (\Exception $e) {
            throw $e;
        }
    }

    public function fixedFileUp($image, $fileName, $width = false, $height = false){
        try{
            if($image){
                $info = $image->move($this->uploadPath, $fileName , true);
                if($info){
                    $imgPath = '/' .'upload' . '/' . $info->getSaveName();
                    // if($width && $height){
                    //     Image::open($this->uploadPath . '/' . $info->getSaveName())
                    //             ->scaleResize($width, $height, 'white')
                    //             ->save($this->uploadPath . '/' . $info->getSaveName(), 'guess', 100);
                    // }
                    Image::open($this->uploadPath . '/' . $info->getSaveName())
                                ->save($this->uploadPath . '/' . $info->getSaveName(), 'guess', 100);
                    return $imgPath.'?'.rand();
                }else{
                    throw $image->getError();
                }
            }
        }catch (\Exception $e) {
            throw $e;
        }
    }

    //getter and setter area
    public function getTableName(){return $this->tableName;}
    public function setTableName($tableName){
        if(is_string($tableName)){
            $this->tableName = $tableName;
        }else{
            throw new \Exception('tableName must be String');
        }
    }
    
    public function getPrivateKeyName(){return $this->privateKeyName;}
    public function setPrivateKeyName($privateKeyName){
        if(is_string($privateKeyName)){
            $this->privateKeyName = $privateKeyName;
        }else{
            throw new \Exception('privateKeyName must be String');
        }
    }
    
    public function getPrivateKeyId(){return $this->privateKeyId;}
    public function setPrivateKeyId($privateKeyId){$this->privateKeyId = $privateKeyId;}

    public function getFileArray(){return $this->fileArray;}
    public function setFileArray($fileArray){
        if(is_array($fileArray)){
            $this->fileArray = $fileArray;
        }else{
            throw new \Exception('fileArray must be Array');
        }
    }

    public function getUploadPath(){return $this->uploadPath;}
    public function setUploadPath($uploadPath){
        if(is_string($uploadPath)){
            $this->uploadPath = $uploadPath;
        }else{
            throw new \Exception('uploadPath must be string');
        }
    }
}

