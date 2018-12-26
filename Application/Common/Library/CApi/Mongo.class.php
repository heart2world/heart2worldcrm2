<?php
namespace Common\Library\CApi;

class Mongo extends  \MongoClient{


    private  $dbnamed;

    private  $tabled;

    public function __construct($server, array $options, array $driver_options)
    {
        parent::__construct($server='mongodb://tank:test@localhost:27017', $options, $driver_options);

    }

    public function setdbnamed($dbnamed){
        $this->dbnamed = $dbnamed;
    }

    public function settabled($tabled){
        $this->tabled = $tabled;
    }

    // 创建数据库
    public function setDB($dbname){
       return $this->selectDB($dbname);
    }

    // 创建集合（表）
    public function setTable($table){
        return $this->selectCollection($table);
    }

    // 选择集合
    public function getTable($dbname,$table){
        if(!property_exists($this,$dbname)){
            $this->setDB($dbname);
        }
        $db = $this->$dbname;            // 选择一个数据库

        if(!property_exists($db,$table)){
            $this->setTable($table);
        }

        $collection = $db->$table; // 选择集合

        return $collection;
    }

    // 生成uuid
    public  function setUuid(){
       return str_replace('.','',uniqid('',true));
    }


    // 插入数据（行）
    public function setdataAll($dbname,$table,$document){

        $collection = $this->getTable($dbname,$table);

        foreach($document as $key => &$docs){
            $collection->insert($docs);
        }
        return  $document;
    }

    // 插入数据（单行）
    public function setdata($dbname,$table,$doc){
        $collection = $this->getTable($dbname,$table);
        $collection->insert($doc);
        return  $doc;
    }

    // 查找数据（行）
    public function getdata($dbname,$table,$fruitQuery = array('uuid' => 'xxxx'),$start=0,$limit=10){

        $db = $this->$dbname;            // 选择一个数据库
        $collection = $db->$table; // 选择集合

        $cursor = $collection->find($fruitQuery)->skip($start)->limit($limit);

        $returns = [];
        foreach ($cursor as $doc) {
            $returns[] =$doc;
        }
        return $returns;
    }

    // 统计数据（行）
    public function getdatacount($dbname,$table,$fruitQuery = array('uuid' => 'xxxx')){

        $db = $this->$dbname;            // 选择一个数据库
        $collection = $db->$table; // 选择集合

        return $collection->find($fruitQuery)->count(true);

    }

    // 更新数据（行）
    public function updatedata($dbname,$table,$where,$data){
        $db = $this->$dbname;            // 选择一个数据库
        $collection = $db->$table; // 选择集合
        // 更新文档
        return $collection->update($where, $data);
    }

    // 删除数据（行）
    public function deletedata($dbname,$table,$where,$data){
        $db = $this->$dbname;            // 选择一个数据库
        $collection = $db->$table; // 选择集合
        // 更新文档
        return $collection->remove($where, $data);
    }




}