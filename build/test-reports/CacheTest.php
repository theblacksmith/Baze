<?php

set_include_path(get_include_path() . PATH_SEPARATOR . 'C:\projects\ZendFramework102\library');

require_once('Cache.class.php');
require_once('CacheBackend.class.php');

try{
	$cb = new CacheBackend();
	$cb->setStorageType(StorageType::$File);
	$cb->setDirectives(array('cache_dir' => 'C:\projects\ZendFramework102\demos\cache\tmp'));
	
	$c = new Cache($cb);
	
	if($res = $c->load('testid'))
	{
		echo $res;
		$c->clean(Cache::CLEANING_MODE_ALL);
	}
	else
	{
		echo $c->save('Testando', 'testid');
	}
}
catch(Exception $e)
{
	echo $e->getMessage();
}
