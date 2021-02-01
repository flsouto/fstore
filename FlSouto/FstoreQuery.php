<?php
namespace FlSouto;

class FstoreQuery{

    protected $table;
    protected $limit = null;
    protected $filters = [];
    protected $since = null;
    protected $until = null;
    protected $selid = '';

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

    function selid($key='id'){
        $this->selid = $key;
        return $this;
    }

    function rows(){
        $ids = $this->table->ids();
        $changed = 0;
        if($this->since){
            foreach($ids as $i=>$id){
                if($id/10000 < $this->since){
                    unset($ids[$i]);
                    $changed=true;
                }
            }
        }
        if($this->until){
            foreach($ids as $i=>$id){
                if($id/10000 > $this->until){
                    unset($ids[$i]);
                    $changed=true;
                }
            }
        }
        if($changed){
            $ids = array_values($ids);
        }
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
                if($this->selid){
                    $row[$this->selid] = $id;
                }
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

    static function date2time($date){
        if(!is_object($date) && preg_match("/^\d+$/",$date)){
            return $date;
        } else if(preg_match("/\d{2}\/\d{2}\/\d{4}/",$date)){
            return strtotime(implode('-',array_reverse(explode('/',$date))));
        } else if($date instanceof \DateTime){
            return $date->getTimestamp();
        } else if(is_string($date)) {
            return strtotime($date);
        } else {
            throw new \InvalidArgumentException("FstoreQuery expecting a valid date!");
        }

    }

    function since($start_date){
        $this->since = self::date2time($start_date);
        return $this;
    }

    function until($end_date){
        $this->until = self::date2time($end_date);
        return $this;
    }

    function ids(){
        if(empty($this->filters)){
            return $this->table->ids($this->limit);
        } else {
            return array_keys($this->rows());
        }
    }

}
