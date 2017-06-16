<?php

require 'vendor/autoload.php';
use FlSouto\Fstore;
use FlSouto\FstoreTable;

class FstoreTest extends PHPUnit\Framework\TestCase{

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
    
    function testGetTable(){
        $db = new Fstore(__DIR__.'/test_db');
        // creates users table on the fly
        $products = $db->table('products');
        $this->assertInstanceOf(FstoreTable::class, $products);
    }

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

    function testGetAllIds(){

        $db = new Fstore(__DIR__.'/test_db');
        $tmp_tbl = uniqid();
        $table = $db->table($tmp_tbl);

        $inserted_ids = [];
        foreach(['a','b','c','d'] as $letter){
            $inserted_ids []= $table->insert([
                'letter' => $letter
            ]);
        }

        $ids = $table->ids();

        $this->assertEquals($ids, $inserted_ids);

    }

    function testGetFirstXIds(){

        $db = new Fstore(__DIR__.'/test_db');
        $tmp_tbl = uniqid();
        $table = $db->table($tmp_tbl);

        $inserted_ids = [];
        foreach(range('a','z') as $letter){
            $inserted_ids []= $table->insert([
                'letter' => $letter
            ]);
        }

        $first_10_ids = $table->ids(10);

        $this->assertEquals(array_slice($inserted_ids,0,10), $first_10_ids);

    }

    function testGetLastXIds(){

        $db = new Fstore(__DIR__.'/test_db');
        $tmp_tbl = uniqid();
        $table = $db->table($tmp_tbl);

        $inserted_ids = [];
        foreach(range('a','z') as $letter){
            $inserted_ids []= $table->insert([
                'letter' => $letter
            ]);
        }

        $last_10_ids = $table->ids(-10);

        $this->assertEquals(array_slice(array_reverse($inserted_ids),0,10), $last_10_ids);

    }

}

// Query ///////
// fetch rows matching criteria (filter)
// limit result (all, last, first)
// fetch values of a single column
// fetch only the ids matching the criteria
