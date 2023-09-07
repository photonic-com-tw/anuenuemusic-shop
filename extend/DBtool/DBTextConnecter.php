<?php
 /*
 *
 * @author: MazeR
 * @email: mazer0701@gmail.com
 * @lastUpdate: Set 12 2017
 * @Description: DBTool Class for CU Text Data
 * @depend: (1)thinkPHP5.x think\Db static class
 *
*/
namespace DBTool;

use think\Db;
class DBTextConnecter
{
    //variable area
    private $dataArray = null;
    private $tableName = null;
    private $configDb    = "";
    private $privateKeyName = 'id';

    //overload construct by Static Factory Method Pattern
    public function __construct() {}
    public static function withTableName($tableName, $configDb="") {
        $instance = new self();
        $instance->setTableName($tableName, $configDb);
        return $instance;
    }
    public static function withFileArray($dataArray) {
        $instance = new self();
        $this->setDataArray($dataArray);
        return $instance;
    }
    public static function withFileArrayAndTableName($tableName, $dataArray) {
        $instance = new self();
        $instance->setTableName($tableName);
        $instance->setDataArray($dataArray);
        return $instance;
    }
    public static function withAll($tableName, $dataArray, $privateKeyName) {
        $instance = new self();
        $instance->setTableName($tableName);
        $instance->setDataArray($dataArray);
        $instance->setPrivateKeyName($privateKeyName);
        return $instance;
    }

    //methon area
    public function upTextRow(){
        if($this->dataArray == null){
            throw new \Exception('update data is null');
        }
        if($this->dataArray['id'] == null){
            throw new \Exception('update cannot without id');
        }
        try{
            Db::connect($this->configDb)->table($this->tableName)->where('id', $this->dataArray['id'])->update($this->dataArray);
        }catch (\Exception $e) {
            throw $e;
        }
    }

    public function createTextRow(){
        if($this->dataArray == null){
            throw new \Exception('new data is null');
        }
        try{
            return Db::connect($this->configDb)->table($this->tableName)->insertGetId($this->dataArray);
        }catch (\Exception $e) {
            throw $e;
        }
    }

    //getter and setter area
    public function getTableName(){return $this->tableName;}
    public function setTableName($tableName, $configDb=""){
        if(is_string($tableName)){
            $this->tableName = $tableName;
            $this->configDb   = $configDb;
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
    
    public function getDataArray(){return $this->dataArray;}
    public function setDataArray($dataArray){
        if(is_array($dataArray)){
            $this->dataArray = $dataArray;
        }else{
            throw new \Exception('dataArray must be Array');
        }
    }
}

