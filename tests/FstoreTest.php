<?php

require 'vendor/autoload.php';
use FlSouto\Fstore;

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

}


// Fstore //////////////
// get a table

// Table /////////
// insert
// update
// delete
// get all ids
// get first 10 ids
// get 10 last ids
// get row by id

// Query ///////
// fetch rows matching criteria (filter)
// limit result (all, last, first)
// fetch values of a single column
// fetch only the ids matching the criteria
