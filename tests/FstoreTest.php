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



}

// Table /////////
// get date based on id
// update
// delete
// get all ids
// get first 10 ids
// get 10 last ids

// Query ///////
// fetch rows matching criteria (filter)
// limit result (all, last, first)
// fetch values of a single column
// fetch only the ids matching the criteria
