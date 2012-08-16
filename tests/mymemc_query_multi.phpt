--TEST--
Multi query
--SKIPIF--
<?php
	require('skipif.inc');
	_skipif_check_extensions(array("mysqli"));
	_skipif_connect($host, $user, $passwd, $db, $port, $socket);

	require_once('table.inc');
	$ret = my_memcache_config::init(array('f1', 'f2', 'f3'), true, '|');
	if (true !== $ret) {
		die(sprintf("SKIP %s\n", $ret));
	}
?>
--FILE--
<?php
	require_once('connect.inc');
	function debug_callback() {
		printf("%s()", __FUNCTION__);

		$args = func_get_args();
		foreach ($args as $k => $arg) {
			printf(" %02d: %s / %s\n", $k, gettype($arg), var_export($arg, true));
		}
	}

	$link = my_mysqli_connect($host, $user, $passwd, $db, $port, $socket);
	if ($link->connect_errno) {
		printf("[001] [%d] %s\n", $link->connect_errno, $link->connect_error);
	}

	$memc = my_memcache_connect($memcache_host, $memcache_port);

	if (!mysqlnd_memcache_set($link, $memc, NULL, "debug_callback")) {
		printf("[002] Failed to register connection, [%d] '%s'\n",
			$link->errno, $link->error);
	}

	if (!($key1 = $memc->get("key1"))) {
		printf("[003] Failed to fetch 'key1' using native Memcache API.\n");
	}
	$columns = explode("|", $key1);
	var_dump($columns);

	$resno = 0;
	$query = "SELECT f1, f2, f3 FROM mymem_test WHERE id = 'key1'";
	if ($link->multi_query($query)) {
		do {
			if ($res = $link->store_result()) {
				printf("Result set %d\n", ++$resno);
				while ($row = $res->fetch_row()) {
					var_dump($row);
				}
				$res->free();
			}
		} while ($link->more_results() && $link->next_result());
	} else {
		printf("[004] [%d] %s\n", $link->errno, $link->error);
	}


	print "done!";
?>
--EXPECT--
array(3) {
  [0]=>
  string(1) "a"
  [1]=>
  string(1) "b"
  [2]=>
  string(1) "c"
}
debug_callback() 00: boolean / true
Result set 1
array(3) {
  [0]=>
  string(1) "a"
  [1]=>
  string(1) "b"
  [2]=>
  string(1) "c"
}
done!