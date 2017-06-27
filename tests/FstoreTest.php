<?php

require 'vendor/autoload.php';
use FlSouto\Fstore;
use FlSouto\FstoreTable;

class FstoreTest extends PHPUnit\Framework\TestCase{

/* 
### Accessing a Database


*/
    function testCreateDatabase(){
        $db = new Fstore(__DIR__.'/test_db');
        $this->assertEquals(__DIR__.'/test_db', $db->dir());
    }

    function testExceptionInvalidDir(){
        $this->expectException(\Exception::class);
        new Fstore('sdfdsf');
    }

    function testExceptionNonWritableDir(){
        $this->expectException(\Exception::class);
        new Fstore(__DIR__.'/protected_dir');
    }

/* 
### Accessing a Table

*/    
    function testGetTable(){
        $db = new Fstore(__DIR__.'/test_db');
        // creates users table on the fly
        $products = $db->table('products');
        $this->assertInstanceOf(FstoreTable::class, $products);
    }

/* 
### Inserting and Retrieving a Row
*/    
    
    function testInsertAndGet(){
        $db = new Fstore(__DIR__.'/test_db');
        $products = $db->table('products');
        $id = $products->insert([
            'name' => 'Pencil',
            'description' => 'Can be used to write things down.',
            'price' => 1.99
        ]);

        $new_row = $products->get($id);

        $this->assertEquals([
            'name' => 'Pencil',
            'description' => 'Can be used to write things down.',
            'price' => 1.99
        ],$new_row);
    }

/* 
### Getting the Insert Date Based on the Row's ID

*/    
    function testGetDateBasedOnId(){
        $db = new Fstore(__DIR__.'/test_db');
        $products = $db->table('products');
        $id = $products->insert([
            'name' => 'Pencil',
            'description' => 'Can be used to write things down.',
            'price' => 1.99
        ]);
        
        $date = $products->date('d/m/Y H:i', $id);
        $this->assertEquals($date, date('d/m/Y H:i'));
    }

/* 
### Updating a Record
*/    
    function testUpdate(){

        $db = new Fstore(__DIR__.'/test_db');
        $products = $db->table('products');

        $id = $products->insert([
            'name' => 'Pencil',
            'description' => 'Can be used to write things down.',
            'price' => 1.99
        ]);

        $products->update(['price'=>1.5], $id);

        $row = $products->get($id);

        $this->assertEquals(1.5, $row['price']);

    }

/*
### Deleting a Record

*/    
    function testDelete(){

        $db = new Fstore(__DIR__.'/test_db');
        $products = $db->table('products');

        $id = $products->insert([
            'name' => 'Pencil',
            'description' => 'Can be used to write things down.',
            'price' => 1.99
        ]);

        $products->delete($id);

        $this->expectException(\InvalidArgumentException::class);

        $products->get($id);

    }
/* 
### Retrieving all IDs from a Table
*/
    function testGetAllIds(){

        $db = new Fstore(__DIR__.'/test_db');
        $table = $db->table('alphabet');

        $inserted_ids = [];
        foreach(['a','b','c','d'] as $letter){
            $inserted_ids []= $table->insert([
                'letter' => $letter
            ]);
        }

        $ids = $table->ids();

        $this->assertEquals($ids, $inserted_ids);

    }
/* 
### Retrieving only first 10 IDs from a Table
*/
    function testGetFirstXIds(){

        $db = new Fstore(__DIR__.'/test_db');
        $table = $db->table('alphabet');

        $inserted_ids = [];
        foreach(range('a','z') as $letter){
            $inserted_ids []= $table->insert([
                'letter' => $letter
            ]);
        }

        $first_10_ids = $table->ids(10);

        $this->assertEquals(array_slice($inserted_ids,0,10), $first_10_ids);

    }

/* 
### Retrieving only the LAST 10 IDs from a Table
*/    
    function testGetLastXIds(){

        $db = new Fstore(__DIR__.'/test_db');
        $table = $db->table('alphabet');

        $inserted_ids = [];
        foreach(range('a','z') as $letter){
            $inserted_ids []= $table->insert([
                'letter' => $letter
            ]);
        }

        $last_10_ids = $table->ids(-10);

        $this->assertEquals(array_slice(array_reverse($inserted_ids),0,10), $last_10_ids);

    }

/* 
### Querying a Table using Filters

*/    
    function testFetchRowsWithFilter(){

        $db = new Fstore(__DIR__.'/test_db');
        $table = $db->table('alphabet');

        $inserted_ids = [];
        foreach(range('a','z') as $letter){
            $inserted_ids []= $table->insert([
                'letter' => $letter
            ]);
        }

        $q = $table->query();
        
        // only rows that satisfy certain conditions
        $q->filter(function($row){
            return in_array($row['letter'], ['f','a','b','i','o']);
        });
        // fetch those rows
        $rows = $q->rows();
        
        $this->assertEquals(array_column($rows,'letter'),['a','b','f','i','o']);
        
    }

/* 
### Querying The Last X Rows...
*/    
    
    function testLastXRows(){

        $db = new Fstore(__DIR__.'/test_db');
        $table = $db->table('alphabet');

        $inserted_ids = [];
        foreach(range('a','z') as $letter){
            $inserted_ids []= $table->insert([
                'letter' => $letter
            ]);
        }

        $q = $table->query();

        // the last ten
        $q->limit(-10);

        // fetch them
        $rows = $q->rows();

        $this->assertEquals(
            array_reverse(['q','r','s','t','u','v','w', 'x', 'y', 'z']),
            array_slice(array_column($rows,'letter'),-10)
        );

    }

/*
### Fetching only one column from the result

*/
    function testFetchValues(){

        $db = new Fstore(__DIR__.'/test_db');
        $table = $db->table('alphabet');

        foreach(range('a','z') as $letter){
            $table->insert([
                'letter' => $letter
            ]);
        }

        $q = $table->query();

        // first ten
        $q->limit(10);

        // fetch values of 'letter' column
        $values = $q->values('letter');

        $this->assertEquals(
            range('a','j'),
            array_values($values)
        );
    }

/*
### Fetching only the ids from a result

*/
    function testFetchIds(){

        $db = new Fstore(__DIR__.'/test_db');
        $table = $db->table('alphabet');

        $insert_ids = [];
        foreach(range('a','z') as $letter){
            $insert_ids[$letter] = $table->insert([
                'letter' => $letter
            ]);
        }

        $q = $table->query();

        // only rows that satisfy certain conditions
        $q->filter(function($row){
            return in_array($row['letter'], ['f','a','b','i','o']);
        });

        // fetch only the ids
        $ids = $q->ids();

        $expected = [];
        foreach(str_split('fabio') as $letter){
            $expected[] = $insert_ids[$letter];
        }
        sort($ids);
        sort($expected);

        $this->assertEquals(
            $expected,
            $ids
        );
    }

    function tearDown(){
        $db = new Fstore(__DIR__.'/test_db');
        $table = $db->table('alphabet');
        if(is_dir($table->dir())){
            foreach($table->ids() as $id){
                $table->delete($id);
            }
        }

    }

    function __destruct(){

        foreach(scandir($path=__DIR__.'/test_db/') as $dir){
            if(is_dir($path.$dir) && $dir!='.' && $dir!='..'){
                shell_exec("rm {$path}{$dir} -R");
            }
        }

    }

}