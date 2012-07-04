--TEST--
mysqlnd_memcache_get_config()
--SKIPIF--
<?php
require('skipif.inc');
_skipif_check_extensions(array("mysql"));
_skipif_no_plugin($host, $user, $passwd, $db, $port, $socket);
?>
--FILE--
<?php
	require 'table.inc';
	init_memcache_config('f1,f2,f3', true, '|');

	if (!$link = my_mysql_connect($host, $user, $passwd, $db, $port, $socket)) {
		printf("[001] [%d] %s\n", mysql_errno(), mysql_error());
	}

	$memc = my_memcache_connect($memcache_host, $memcache_port);
	mysqlnd_memcache_set($link, $memc);

	$config = mysqlnd_memcache_get_config($link);
	if (!isset($config['memcached'])) {
		printf("[002] No memcached entry. Dumping config.\n");
		var_dump($config);
	} else {
		if ($config['memcached'] !== $memc) {
			printf("[003] memcached entry differs from user handle.\n");
		}
		unset($config['memcached']);
	}

	if (!isset($config['pattern'])) {
		printf("[004] No pattern entry. Dumping config.\n");
		var_dump($config);
	} else {
		if (strlen($config['pattern']) < 20) {
			printf("[005] Default pattern suspiciously short. Dumping config\.n");
			var_dump($config);
		}
		unset($config['pattern']);
	}

	$mappings = array(
				"mymem_test" =>
					array(
						"prefix" => "",
						"schema_name" => $db,
						"table_name" => "mymem_test",
						"id_field_name" => "id",
						"separator" => "|",
						"fields" => array("f1", "f2", "f3"),
					)
				);

	if (!isset($config['mappings'])) {
		printf("[006] No mappings entry. Dumping config.\n");
		var_dump($config);
	} else {
		if ($config['mappings'] != $mappings) {
			printf("[007] Mappings differ. Dumping.\n");
			var_dump($mappings);
			var_dump($config['mappings']);
		}
		unset($config['mappings']);
	}

	if (!isset($config['mapping_query'])) {
		printf("[008] No mapping_query entry. Dumping config.\n");
		var_dump($config);
	} else {
		if (strlen($config['mapping_query']) < 20) {
			printf("[009] Mapping query is suspiciously short. Dumping config.\n");
			var_dump($config);
		}
		unset($config['mapping_query']);
	}

	if (!empty($config)) {
		printf("[011] Dumping unexpected config elements\n");
		var_dump($config);
	}

	print "done!";
?>
--EXPECT--
done!