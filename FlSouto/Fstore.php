<?php
namespace FlSouto;

class Fstore{

    protected $save_dir = '';
    protected $tables = [];

    function __construct($save_dir){
        if(!is_dir($save_dir)){
            throw new \InvalidArgumentException("Not a directory: $save_dir");
        }
        if(!is_writable($save_dir)){
            throw new \InvalidArgumentException("Directory not writable: $save_dir!");
        }
        $this->save_dir = $save_dir;
    }

    function dir(){
        return $this->save_dir;
    }

    /**
     * @param $table
     * @return FstoreTable
     */
    function table($table){

        if(!isset($this->tables[$table])){
            $this->tables[$table] = new FstoreTable($this, $table);
        }

        return $this->tables[$table];
    }

    function date($format, $id){
        return date($format, $id/10000);
    }

}