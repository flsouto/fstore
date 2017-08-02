<?php

require 'vendor/autoload.php';
use FlSouto\Fstore;
use FlSouto\FstoreTable;

class FstoreTest extends PHPUnit\Framework\TestCase{

/* 
### Accessing a Database

A database is nothing more than a directory. So all you have to do is specify the path to that
directory when creating a database instance. If the directory does not exist, it will be created
when the first row is inserted.

#mdx:CreateDatabase

*/
    function testCreateDatabase(){
        #mdx:CreateDatabase
        $db = new Fstore(__DIR__.'/test_db');
        #/mdx
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

You can get a table instance from a database by calling the `table` method. If the table does not exist, it will be
created once the first row is inserted.

#mdx:GetTable

*/
    function testGetTable(){
        #mdx:GetTable
        $db = new Fstore(__DIR__.'/test_db');
        // creates users table on the fly
        $products = $db->table('products');
        #/mdx
        $this->assertInstanceOf(FstoreTable::class, $products);
    }

/* 
### Inserting and Retrieving a Row

The table object provides an `insert` method which accepts an associative array and returns the automatically generated
row id:

#mdx:InsertAndGet

Outputs:

#mdx:InsertAndGet -o
*/
    function testInsertAndGet(){
        #mdx:InsertAndGet
        $db = new Fstore(__DIR__.'/test_db');
        $products = $db->table('products');
        $id = $products->insert([
            'name' => 'Pencil',
            'description' => 'Can be used to write things down.',
            'price' => 1.99
        ]);

        $new_row = $products->get($id);
        #/mdx print_r($new_row)
        $this->assertEquals([
            'name' => 'Pencil',
            'description' => 'Can be used to write things down.',
            'price' => 1.99
        ],$new_row);
    }

/* 
### Getting the Insert Date Based on the Row's ID

The `insert` method of a table object generates an id which contains the timestamp itself so there is no need to create a "date_added" column.
But, in order to avoid conflicts when inserting multiple rows on the same table at the same time the generated id has the miliseconds as well as a counter
which guarantees that the ids will always be different, even if inserted on the same nanosecond! So, in order to take the corrrect date/time from this hash you have to remove those extra numbers.
The good news is that there is a `$table->date` method which does just that for you:

#mdx:GetDateBasedOnId

Outputs:

#mdx:GetDateBasedOnId -o
*/
    function testGetDateBasedOnId(){
        #mdx:GetDateBasedOnId
        $db = new Fstore(__DIR__.'/test_db');
        $products = $db->table('products');
        $id = $products->insert([
            'name' => 'Pencil',
            'description' => 'Can be used to write things down.',
            'price' => 1.99
        ]);
        
        $date = $products->date('d/m/Y H:i', $id);
        #/mdx echo $date
        $this->assertEquals($date, date('d/m/Y H:i'));
    }

/* 
### Updating a Record
Updating a table record is pretty straightforward: just call `update` and pass it an associative array followed by
the row id:

#mdx:Update

Outputs:

#mdx:Update -o
*/
    function testUpdate(){

        #mdx:Update
        $db = new Fstore(__DIR__.'/test_db');
        $products = $db->table('products');

        $id = $products->insert([
            'name' => 'Pencil',
            'description' => 'Can be used to write things down.',
            'price' => 1.99
        ]);

        $products->update(['price'=>1.5], $id);

        $row = $products->get($id);
        #/mdx print_r($row)
        $this->assertEquals(1.5, $row['price']);

    }

/*
### Deleting a Record
To delete a record simply call `$table->delete($id)`, see example:

#mdx:Delete

*/
    function testDelete(){

        #mdx:Delete
        $db = new Fstore(__DIR__.'/test_db');
        $products = $db->table('products');

        $id = $products->insert([
            'name' => 'Pencil',
            'description' => 'Can be used to write things down.',
            'price' => 1.99
        ]);

        $products->delete($id);
        #/mdx
        $this->expectException(\InvalidArgumentException::class);

        $products->get($id);

    }
/* 
### Retrieving all IDs from a Table
Use `$table->ids()` to get all ids:

#mdx:GetAllIds

Outputs:

#mdx:GetAllIds -o
*/
    function testGetAllIds(){

        #mdx:GetAllIds
        $db = new Fstore(__DIR__.'/test_db');
        $table = $db->table('alphabet');

        $inserted_ids = []; #mdx:skip
        foreach(['a','b','c','d'] as $letter){
            $inserted_ids []= #mdx:skip
            $table->insert([
                'letter' => $letter
            ]);
        }

        $ids = $table->ids();
        #/mdx print_r($ids)
        $this->assertEquals($ids, $inserted_ids);

    }
/* 
### Retrieving only first 10 IDs from a Table
Use `$table->ids(X)` to get the first X ids:

#mdx:GetFirstXIds

Outputs:

#mdx:GetFirstXIds -o
*/
    function testGetFirstXIds(){

        #mdx:GetFirstXIds
        $db = new Fstore(__DIR__.'/test_db');
        $table = $db->table('alphabet');

        $inserted_ids = []; #mdx:skip
        foreach(range('a','z') as $letter){
            $inserted_ids []= #mdx:skip
            $table->insert([
                'letter' => $letter
            ]);
        }

        $first_10_ids = $table->ids(10);
        #/mdx print_r($first_10_ids)
        $this->assertEquals(array_slice($inserted_ids,0,10), $first_10_ids);

    }

/* 
### Retrieving only the LAST 10 IDs from a Table
Use `$table->ids(-X)` to get the last X ids:

*/
/*
#mdx:GetLastXIds

Outputs:

#mdx:GetLastXIds -o
*/
    function testGetLastXIds(){

        #mdx:GetLastXIds
        $db = new Fstore(__DIR__.'/test_db');
        $table = $db->table('alphabet');

        $inserted_ids = []; #mdx:skip
        foreach(range('a','z') as $letter){
            $inserted_ids []= #mdx:skip
            $table->insert([
                'letter' => $letter
            ]);
        }

        $last_10_ids = $table->ids(-10);
        #/mdx print_r($last_10_ids)
        $this->assertEquals(array_slice(array_reverse($inserted_ids),0,10), $last_10_ids);

    }

/* 
### Querying a Table using Filters
The table object provides a query builder which can be created via `$table->query()`. 
This query builder allows us to filter results by means of a `$query->filter()` function
which accepts a callback. The following example illustrates this better:

#mdx:FetchRowsWithFilter

Outputs:

#mdx:FetchRowsWithFilter -o
*/
    function testFetchRowsWithFilter(){

        #mdx:FetchRowsWithFilter
        $db = new Fstore(__DIR__.'/test_db');
        $table = $db->table('alphabet');

        $inserted_ids = [];#mdx:skip
        foreach(range('a','z') as $letter){
            $inserted_ids []= #mdx:skip
            $table->insert([
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
        
        #/mdx print_r($rows)
        $this->assertEquals(array_column($rows,'letter'),['a','b','f','i','o']);
        
    }

/* 
### Querying The Last X Rows...
Call the `limit` method on a query object and pass it a negative number. See example:

#mdx:LastXRows

Outputs:

#mdx:LastXRows -o

*/
    function testLastXRows(){

        #mdx:LastXRows
        $db = new Fstore(__DIR__.'/test_db');
        $table = $db->table('alphabet');

        $inserted_ids = [];#mdx:skip
        foreach(range('a','z') as $letter){
            $inserted_ids []= #mdx:skip
            $table->insert([
                'letter' => $letter
            ]);
        }

        $q = $table->query();

        // the last ten
        $q->limit(-10);

        // fetch them
        $rows = $q->rows();
        #/mdx print_r($rows)

        $this->assertEquals(
            array_reverse(['q','r','s','t','u','v','w', 'x', 'y', 'z']),
            array_slice(array_column($rows,'letter'),-10)
        );

    }

/*
### Fetching only one column from the result
Call the `values` method on a query object passing it the name of the desired column.
This will return an array of all values of that column. See example:

#mdx:FetchValues

Outputs:

#mdx:FetchValues -o
*/
    function testFetchValues(){

        #mdx:FetchValues
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
        #/mdx print_r($values)
        $this->assertEquals(
            range('a','j'),
            array_values($values)
        );
    }

/*
### Fetching only the ids from a result
Call the `ids` method on a query object:

#mdx:FetchIds

Outputs:

#mdx:FetchIds -o
*/
    function testFetchIds(){

        #mdx:FetchIds
        $db = new Fstore(__DIR__.'/test_db');
        $table = $db->table('alphabet');

        $insert_ids = []; #mdx:skip
        foreach(range('a','z') as $letter){
            $insert_ids[$letter] = #mdx:skip
            $table->insert([
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
        #/mdx print_r($ids)

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