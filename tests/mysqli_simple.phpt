--TEST--
Simple mysqli test
--INI--
extension=/home/johannes/src/php/php-memcached/modules/memcached.so
--SKIPIF--
<?php
require('skipif.inc');
?>
--FILE--
<?php
require 'table.inc';
init_memcache_config('f1,f2,f3', true, '|');

if (!$link = my_mysqli_connect($host, $user, $passwd, $db, $port, $socket)) {
	die("Connection failed");
}

$memc = my_memcache_connect($memcache_host, $memcache_port);
mysqlnd_memcache_set($link, $memc, NULL, function ($success) { echo "Went through memcache: ".($success ? 'Yes' : 'No')."\n";});

echo "Storing 23 via memcache:\n";
var_dump($memc->set("23", "a|b|c"));
echo "Fetching 23 via memcache:\n";
var_dump($memc->get("23"));
echo "Querying SELECT f1, f2, f3 FROM mymem_test WHERE id = 23:\n";
$r = $link->query("SELECT f1, f2, f3 FROM mymem_test WHERE id = 23");
var_dump($r->fetch_all());
?>
--EXPECT--
Storing 23 via memcache:
bool(true)
Fetching 23 via memcache:
string(5) "a|b|c"
Querying SELECT f1, f2, f3 FROM mymem_test WHERE id = 23:
Went through memcache: Yes
array(1) {
  [0]=>
  array(3) {
    [0]=>
    string(1) "a"
    [1]=>
    string(1) "b"
    [2]=>
    string(1) "c"
  }
}
