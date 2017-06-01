<?php
namespace FlSouto;

class FstoreTable{

    protected $db;
    protected $table;

    protected static $cache_ids = [];

    function __construct(Fstore $db, $table){
        $this->db = $db;
        $this->table = $table;
    }

    function db(){
        return $this->db;
    }

    function name(){
        return $this->table;
    }

    function dir(){
        return $this->db->dir().'/'.$this->table.'/';
    }

    protected function put($id,array $data){
        file_put_contents($this->dir().$id.'.json',json_encode($data, JSON_PRETTY_PRINT));
    }

    function insert(array $data){
        static $called_times = 0;
        $id = ((microtime(true)*10000)+$called_times)."";
        if(!is_dir($this->dir())){
            mkdir($this->dir());
        }
        $this->put($id, $data);
        if(isset(self::$cache_ids[$this->table])){
            self::$cache_ids[$this->table][] = $id;
        }
        $called_times++;
        return $id;
    }

    function update(array $data, $id){
        $row = $this->get($id);
        foreach($data as $k=>$v){
            $row[$k] = $v;
        }
        $this->put($id, $row);
    }

    function delete($id){
        unlink($this->dir().$id.'.json');
    }

    function get($id){
        $file = $this->dir().$id.'.json';
        if(!file_exists($file)){
            throw new \InvalidArgumentException("Row $id not found on table '$this->table'");
        }
        $data = json_decode(file_get_contents($file), true);
        return $data;
    }

    function query(){
        return new FstoreQuery($this);
    }

    function ids($limit=null){
        if(!isset(self::$cache_ids[$this->table])){
            $ids = [];
            foreach(scandir($this->dir()) as $file){
                if(substr($file,-5)!=='.json'){
                    continue;
                }
                $ids[] = substr($file, 0, -5);
            }
            sort($ids);
            self::$cache_ids[$this->table] = $ids;
        }

        $ids = self::$cache_ids[$this->table];

        if(!$limit){
            return $ids;
        } else {
            if(!(int)$limit){
                throw new \InvalidArgumentException("Limit must be an integer!");
            }
            if($limit>0){
                return array_slice($ids, 0, $limit);
            } else {
                $ids = array_slice($ids, $limit);
                rsort($ids);
                return $ids;
            }
        }

    }

}
