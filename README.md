### Accessing a Database

A database is nothing more than a directory. So all you have to do is specify the path to that
directory when creating a database instance. If the directory does not exist, it will be created
when the first row is inserted.

```php
<?php
require 'vendor/autoload.php';
use FlSouto\Fstore;
use FlSouto\FstoreTable;

$db = new Fstore('/home/fabio/Documents/stuff/fstore/tests'.'/test_db');

```


### Accessing a Table

You can get a table instance from a database by calling the `table` method. If the table does not exist, it will be
created once the first row is inserted.

```php
<?php
require 'vendor/autoload.php';
use FlSouto\Fstore;
use FlSouto\FstoreTable;

$db = new Fstore('/home/fabio/Documents/stuff/fstore/tests'.'/test_db');
// creates users table on the fly
$products = $db->table('products');

```


### Inserting and Retrieving a Row

The table object provides an `insert` method which accepts an associative array and returns the automatically generated
row id:

```php
<?php
require 'vendor/autoload.php';
use FlSouto\Fstore;
use FlSouto\FstoreTable;

$db = new Fstore('/home/fabio/Documents/stuff/fstore/tests'.'/test_db');
$products = $db->table('products');
$id = $products->insert([
    'name' => 'Pencil',
    'description' => 'Can be used to write things down.',
    'price' => 1.99
]);

$new_row = $products->get($id);

print_r($new_row);
```

Outputs:

```
Array
(
    [name] => Pencil
    [description] => Can be used to write things down.
    [price] => 1.99
)

```

### Getting the Insert Date Based on the Row's ID

The `insert` method of a table object generates an id which contains the timestamp itself so there is no need to create a "date_added" column.
But, in order to avoid conflicts when inserting multiple rows on the same table at the same time the generated id has the miliseconds as well as a counter
which guarantees that the ids will always be different, even if inserted on the same nanosecond! So, in order to take the corrrect date/time from this hash you have to remove those extra numbers.
The good news is that there is a `$table->date` method which does just that for you:

```php
<?php
require 'vendor/autoload.php';
use FlSouto\Fstore;
use FlSouto\FstoreTable;

$db = new Fstore('/home/fabio/Documents/stuff/fstore/tests'.'/test_db');
$products = $db->table('products');
$id = $products->insert([
    'name' => 'Pencil',
    'description' => 'Can be used to write things down.',
    'price' => 1.99
]);

$date = $products->date('d/m/Y H:i', $id);

echo $date;
```

Outputs:

```
08/09/2017 11:45
```

### Updating a Record
Updating a table record is pretty straightforward: just call `update` and pass it an associative array followed by
the row id:

```php
<?php
require 'vendor/autoload.php';
use FlSouto\Fstore;
use FlSouto\FstoreTable;

$db = new Fstore('/home/fabio/Documents/stuff/fstore/tests'.'/test_db');
$products = $db->table('products');

$id = $products->insert([
    'name' => 'Pencil',
    'description' => 'Can be used to write things down.',
    'price' => 1.99
]);

$products->update(['price'=>1.5], $id);

$row = $products->get($id);

print_r($row);
```

Outputs:

```
Array
(
    [name] => Pencil
    [description] => Can be used to write things down.
    [price] => 1.5
)

```

### Deleting a Record
To delete a record simply call `$table->delete($id)`, see example:

```php
<?php
require 'vendor/autoload.php';
use FlSouto\Fstore;
use FlSouto\FstoreTable;

$db = new Fstore('/home/fabio/Documents/stuff/fstore/tests'.'/test_db');
$products = $db->table('products');

$id = $products->insert([
    'name' => 'Pencil',
    'description' => 'Can be used to write things down.',
    'price' => 1.99
]);

$products->delete($id);

```


### Retrieving all IDs from a Table
Use `$table->ids()` to get all ids:

```php
<?php
require 'vendor/autoload.php';
use FlSouto\Fstore;
use FlSouto\FstoreTable;

$db = new Fstore('/home/fabio/Documents/stuff/fstore/tests'.'/test_db');
$table = $db->table('alphabet');

foreach(['a','b','c','d'] as $letter){
    $table->insert([
        'letter' => $letter
    ]);
}

$ids = $table->ids();

print_r($ids);
```

Outputs:

```
Array
(
    [0] => 15048819217229
    [1] => 15048819217231
    [2] => 15048819217233
    [3] => 15048819217234
)

```

### Retrieving only first 10 IDs from a Table
Use `$table->ids(X)` to get the first X ids:

```php
<?php
require 'vendor/autoload.php';
use FlSouto\Fstore;
use FlSouto\FstoreTable;

$db = new Fstore('/home/fabio/Documents/stuff/fstore/tests'.'/test_db');
$table = $db->table('alphabet');

foreach(range('a','z') as $letter){
    $table->insert([
        'letter' => $letter
    ]);
}

$first_10_ids = $table->ids(10);

print_r($first_10_ids);
```

Outputs:

```
Array
(
    [0] => 15048819217229
    [1] => 15048819217231
    [2] => 15048819217233
    [3] => 15048819217234
    [4] => 15048819217238
    [5] => 15048819217240
    [6] => 15048819217242
    [7] => 15048819217243
    [8] => 15048819217245
    [9] => 15048819217246
)

```

### Retrieving only the LAST 10 IDs from a Table
Use `$table->ids(-X)` to get the last X ids:


```php
<?php
require 'vendor/autoload.php';
use FlSouto\Fstore;
use FlSouto\FstoreTable;

$db = new Fstore('/home/fabio/Documents/stuff/fstore/tests'.'/test_db');
$table = $db->table('alphabet');

foreach(range('a','z') as $letter){
    $table->insert([
        'letter' => $letter
    ]);
}

$last_10_ids = $table->ids(-10);

print_r($last_10_ids);
```

Outputs:

```
Array
(
    [0] => 15048819217336
    [1] => 15048819217334
    [2] => 15048819217332
    [3] => 15048819217331
    [4] => 15048819217329
    [5] => 15048819217328
    [6] => 15048819217326
    [7] => 15048819217324
    [8] => 15048819217323
    [9] => 15048819217321
)

```

### Querying a Table using Filters
The table object provides a query builder which can be created via `$table->query()`. 
This query builder allows us to filter results by means of a `$query->filter()` function
which accepts a callback. The following example illustrates this better:

```php
<?php
require 'vendor/autoload.php';
use FlSouto\Fstore;
use FlSouto\FstoreTable;

$db = new Fstore('/home/fabio/Documents/stuff/fstore/tests'.'/test_db');
$table = $db->table('alphabet');

foreach(range('a','z') as $letter){
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

print_r($rows);
```

Outputs:

```
Array
(
    [15048819217229] => Array
        (
            [letter] => a
        )

    [15048819217231] => Array
        (
            [letter] => b
        )

    [15048819217238] => Array
        (
            [letter] => a
        )

    [15048819217240] => Array
        (
            [letter] => b
        )

    [15048819217246] => Array
        (
            [letter] => f
        )

    [15048819217251] => Array
        (
            [letter] => i
        )

    [15048819217279] => Array
        (
            [letter] => o
        )

    [15048819217298] => Array
        (
            [letter] => a
        )

    [15048819217299] => Array
        (
            [letter] => b
        )

    [15048819217305] => Array
        (
            [letter] => f
        )

    [15048819217309] => Array
        (
            [letter] => i
        )

    [15048819217318] => Array
        (
            [letter] => o
        )

    [15048819217340] => Array
        (
            [letter] => a
        )

    [15048819217342] => Array
        (
            [letter] => b
        )

    [15048819217348] => Array
        (
            [letter] => f
        )

    [15048819217353] => Array
        (
            [letter] => i
        )

    [15048819217361] => Array
        (
            [letter] => o
        )

)

```

### Querying The Last X Rows...
Call the `limit` method on a query object and pass it a negative number. See example:

```php
<?php
require 'vendor/autoload.php';
use FlSouto\Fstore;
use FlSouto\FstoreTable;

$db = new Fstore('/home/fabio/Documents/stuff/fstore/tests'.'/test_db');
$table = $db->table('alphabet');

foreach(range('a','z') as $letter){
    $table->insert([
        'letter' => $letter
    ]);
}

$q = $table->query();

// the last ten
$q->limit(-10);

// fetch them
$rows = $q->rows();

print_r($rows);
```

Outputs:

```
Array
(
    [15048819217438] => Array
        (
            [letter] => z
        )

    [15048819217437] => Array
        (
            [letter] => y
        )

    [15048819217435] => Array
        (
            [letter] => x
        )

    [15048819217434] => Array
        (
            [letter] => w
        )

    [15048819217432] => Array
        (
            [letter] => v
        )

    [15048819217431] => Array
        (
            [letter] => u
        )

    [15048819217430] => Array
        (
            [letter] => t
        )

    [15048819217428] => Array
        (
            [letter] => s
        )

    [15048819217427] => Array
        (
            [letter] => r
        )

    [15048819217425] => Array
        (
            [letter] => q
        )

)

```


### Fetching only one column from the result
Call the `values` method on a query object passing it the name of the desired column.
This will return an array of all values of that column. See example:

```php
<?php
require 'vendor/autoload.php';
use FlSouto\Fstore;
use FlSouto\FstoreTable;

$db = new Fstore('/home/fabio/Documents/stuff/fstore/tests'.'/test_db');
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

print_r($values);
```

Outputs:

```
Array
(
    [15048819217229] => a
    [15048819217231] => b
    [15048819217233] => c
    [15048819217234] => d
    [15048819217238] => a
    [15048819217240] => b
    [15048819217242] => c
    [15048819217243] => d
    [15048819217245] => e
    [15048819217246] => f
)

```

### Fetching only the ids from a result
Call the `ids` method on a query object:

```php
<?php
require 'vendor/autoload.php';
use FlSouto\Fstore;
use FlSouto\FstoreTable;

$db = new Fstore('/home/fabio/Documents/stuff/fstore/tests'.'/test_db');
$table = $db->table('alphabet');

foreach(range('a','z') as $letter){
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

print_r($ids);
```

Outputs:

```
Array
(
    [0] => 15048819217229
    [1] => 15048819217231
    [2] => 15048819217238
    [3] => 15048819217240
    [4] => 15048819217246
    [5] => 15048819217251
    [6] => 15048819217279
    [7] => 15048819217298
    [8] => 15048819217299
    [9] => 15048819217305
    [10] => 15048819217309
    [11] => 15048819217318
    [12] => 15048819217340
    [13] => 15048819217342
    [14] => 15048819217348
    [15] => 15048819217353
    [16] => 15048819217361
    [17] => 15048819217402
    [18] => 15048819217404
    [19] => 15048819217410
    [20] => 15048819217414
    [21] => 15048819217422
    [22] => 15048819217444
    [23] => 15048819217446
    [24] => 15048819217452
    [25] => 15048819217456
    [26] => 15048819217465
    [27] => 15048819217485
    [28] => 15048819217487
    [29] => 15048819217493
    [30] => 15048819217497
    [31] => 15048819217506
)

```