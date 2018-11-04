## How to Use (Silvestris Database and Pagination Server Side)

### Create your own config.json in your root project directory like this.
```
{
  "mysql" : {
    "dbh"       : "mysql:hostname = localhost; dbname=ProjectDB;",
    "user"      : "root",
    "password"  : ""
  }
}
```

### Get the Connection
```
<?php

require_once __DIR__ .'/vendor/autoload.php';

use Felis\Silvestris\Database as DB;

$db = DB::connect('mysql');

```

#### `Select Example 1`
```
$data = $db->select('users')->fetchAll()->get(); // select all fields from 'users' table
print_r($data);
```

#### `Select Example 2`
```
$data = $db->select('users', 'name')->fetchAll()->get(); // select 'name' field from 'users' table
print_r($data);
```

#### `Select Example 3`
```
$data = $db->select('users')->fetchAll()->toJson()->get(); // return JSON data
print_r($data);
```

#### `Select with where clauses Example`
```
$select = $db->select('users')->where([
  'name' => ['LIKE' => '%John%'],
  'job' => ['=' => 'Developer']
]);
$data = $select->fetchAll()->get();
print_r($data);
```

#### `Insert Example`
```
$insert = $db->insert('users', [
  'name' => 'Johny',
  'job' => 'Developer'
]);
var_dump($insert); //return true or false
```

#### `Update Example`
```
$update = $db->update('users', 'userid', 2, [
  'name' => 'Pretty',
  'job' => 'Sales'
]);
var_dump($update); //return true or false
```

#### `Delete Example`
```
$delete = $db->delete('users', 'userid', 2);
var_dump($delete); //return true or false
```

#### `Query builder fetch() data Example`
```
$query = $db->query("SELECT * FROM users WHERE id = 1");
$data = $query->fetch()->get(); //use fetch() to fetch data to object and get() to get data
print_r($data); //return data
```

#### `Query builder execute() Example`
```
$query = $db->query("DELETE FROM users WHERE id = 1");
$exec = $query->execute(); //use execute() to execute a query
var_dump($exec); return true or false
```


## How to Use (Silvestris Paging Client ES6 imports)


Source code:
 [Github](https://github.com/itpolsri/SilvestrisPagingClient)


Install it via [npm](https://www.npmjs.com/package/@itpolsri/felis-silvestris-paging) `npm i @itpolsri/felis-silvestris-paging`

import modules   
`import { requestPage } from "@itpolsri/felis-silvestris-paging";`

define your html elements for paging
```
const htmlEltOpts = {
    pageElContainerId: '#page-el-container',
    tBodyId: '#tbody',
    pageElChildClassName: 'page-el-child'
}
```

call our method `requestPage(...)`
```
requestPage('YOUR_SILVESTRIS_PAGING_ENDPOINT','SILVESTRIS_QUERY_STRING_NAME',
    SILVESTRIS_QUERY_STRING_VALUE,
    htmlEltOpts,
    err=>{
        console.error(err)
    }
)
```

example:
```
requestPage('http://localhost:8080/Paging/api.php','page',
    1,
    htmlEltOpts,
    err=>{
        console.error(err)
    }
)
```
