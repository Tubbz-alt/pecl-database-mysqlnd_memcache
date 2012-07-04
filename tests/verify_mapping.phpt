--TEST--
Verify mapping
--SKIPIF--
<?php
require('skipif.inc');
_skipif_check_extensions(array("mysqli"));
_skipif_no_plugin($host, $user, $passwd, $db, $port, $socket);
?>
--FILE--
<?php
require_once('connect.inc');
if (!$link = my_mysqli_connect($host, $user, $passwd, $db, $port, $socket)) {
	die("Connection failed");
}

$memc = my_memcache_connect($memcache_host, $memcache_port);
mysqlnd_memcache_set($link, $memc);

var_dump(mysqlnd_memcache_get_config($link)["mappings"]);

--EXPECT--
array(1) {
  ["mymem_test"]=>
  array(6) {
    ["prefix"]=>
    string(0) ""
    ["schema_name"]=>
    string(4) "test"
    ["table_name"]=>
    string(10) "mymem_test"
    ["id_field_name"]=>
    string(2) "id"
    ["separator"]=>
    string(1) "|"
    ["fields"]=>
    array(3) {
      [0]=>
      string(2) "f1"
      [1]=>
      string(2) "f2"
      [2]=>
      string(2) "f3"
    }
  }
}
