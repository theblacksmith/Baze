<?php

define('NB_ROOT', '/projects/baze/framework');
set_include_path(get_include_path() . PATH_SEPARATOR . NB_ROOT);

require_once 'system/application/services/pageService/SimpleXmlPageParser.class.php';
require_once 'system/web/ui/Page.class.php';
require_once NB_ROOT . '/../examples/HelloWorld/HelloWorld.code.php';

define('_IS_POSTBACK', false);

$i = 10000;
$startMem = memory_get_usage()/1024;
echo "start memory: " . number_format($startMem,2) . "k<br/>";

$sTime = microtime(true);
while($i > 0) {
	$p = new SimpleXmlPageParser();
	$p->parsePageFile(NB_ROOT . '/../examples/HelloWorld/HelloWorld.php', new HelloWorld());
	$i--;
}
$eTime = microtime(true);

echo "end memory: " . number_format((memory_get_usage()/1024),2) . "k<br/>";
echo "peak memory: " . number_format((memory_get_peak_usage(true)/1024)-$startMem,2) . "k<br/>";
echo "peak memory (real): " . number_format((memory_get_peak_usage(true)/1024),2) . "k<br/><br/>";

echo "Total time: " . number_format($eTime - $sTime, 2)."s";

/* RESULTS

start memory: 80.75k
end memory: 85.62k
peak memory: 175.25k
peak memory (real): 256.00k

Total time: 15.71s

*/