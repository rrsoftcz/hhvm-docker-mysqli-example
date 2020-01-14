<?hh
require __DIR__.'/async_mysql_connect.inc.php';

// use \Hack\UserDocumentation\AsyncOps\Extensions\Examples\AsyncMysql\ConnectionInfo as CI;

async function get_connection(): Awaitable<AsyncMysqlConnection> {
  // Get a connection pool with default options
  $pool = new \AsyncMysqlConnectionPool(array());
  // Change credentials to something that works in order to test this code
  return await $pool->connect(
    CI::$host,
    CI::$port,
    CI::$db,
    CI::$user,
    CI::$passwd,
  );
}

async function fetch_user_name(
  AsyncMysqlConnection $conn,
  int $user_id,
): Awaitable<?string> {
  // Your table and column may differ, of course
  $result = await $conn->queryf(
    'SELECT username from cms_users WHERE user_id = %d',
    $user_id,
  );
  // There shouldn't be more than one row returned for one user id
  invariant($result->numRows() === 1, 'The user with ID:%s has not found!', $user_id);
  // A vector of vector objects holding the string values of each column
  // in the query
  $vector = $result->vectorRows();
//   var_dump($vector);
  return $vector[0][0]; // We had one column in our query
}

async function get_user_info(
  AsyncMysqlConnection $conn,
  string $user,
): Awaitable<Vector<Map<string, ?string>>> {
  $result = await $conn->queryf(
    'SELECT * from cms_users WHERE username = %s',
    $conn->escapeString($user),
  );
  // A vector of map objects holding the string values of each column
  // in the query, and the keys being the column names
  $map = $result->mapRows();
  
  return $map;
}

async function async_mysql_tutorial(): Awaitable<void> {
  $conn = await get_connection();
  if ($conn !== null) {
    echo "<pre>";
    $result = await fetch_user_name($conn, 1);
    var_dump($result);
    $info = await get_user_info($conn, 'Bajtlamer');
    var_dump($info is vec<_>);
    var_dump($info);
    var_dump($info[0] is dict<_, _>);
    echo("</pre>");
  }
}

<<__EntryPoint>>
function main(): void {
    try{
        \HH\Asio\join(async_mysql_tutorial());
    }catch(Exception $e){
        var_dump($e->getMessage());
    }
}