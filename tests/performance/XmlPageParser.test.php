<?php

require_once '../../framework/system/lang/BazeClassLoader.class.php';
require_once '../../framework/system/web/services/pageService/XmlPageParser.class.php';

$i = 10000;
$startMem = memory_get_usage()/1024;
echo "start memory: " . number_format($startMem,2) . "k<br/>";

$sTime = microtime(true);
while($i > 0) {
	$p = new SimpleXmlPageParser();
	$p->parseFile('../../examples/LinksList/LinksManager.php');
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