<?php
namespace FlSouto;

class FstoreQuery{

    protected $table;
    protected $limit = null;
    protected $filters = [];

    function __construct(FstoreTable $table){
        $this->table = $table;
    }

    function limit($limit){
        if($limit && !(int)$limit){
            throw new \InvalidArgumentException("Limit must be an integer!");
        }
        $this->limit = $limit;
        return $this;
    }

    function filter($function){
        if(!is_callable($function)){
            throw new \InvalidArgumentException("Filter must be a callable!");
        }
        $this->filters[] = $function;
        return $this;
    }

    function rows(){
        $ids = $this->table->ids();
        $limit = $this->limit;
        if($limit<0){
            $limit *= -1;
            rsort($ids);
        }
        $rows = [];
        foreach($ids as $id){
            $row = $this->table->get($id);
            $pass = true;
            foreach($this->filters as $filter){
                if(!call_user_func($filter, $row, $id)){
                    $pass = false;
                }
            }
            if($pass){
                $rows[$id] = $row;
                if($limit && count($rows)>=$limit){
                    break;
                }
            }
        }
        return $rows;
    }

    function values($column){
        $values = [];
        foreach($this->rows() as $id => $row){
            $values[$id] = $row[$column];
        }
        return $values;
    }

    function ids(){
        if(empty($this->filters)){
            return $this->table->ids($this->limit);
        } else {
            return array_keys($this->rows());
        }
    }




}