<?php

require_once '../../framework/system/web/services/pageService/StringPageParser.class.php';

$i = 10000;
$startMem = memory_get_usage()/1024;

echo "start memory: " . number_format($startMem,2) . "k<br/>";

$sTime = microtime(true);
while($i > 0) {
	$p = new StringParser();
	$p->parseFile('../../examples/LinksList/LinksManager.php');
	$i--;
}
$eTime = microtime(true);

echo "end memory: " . number_format((memory_get_usage()/1024),2). "k<br/>";
echo "peak memory: " . number_format((memory_get_peak_usage(true)/1024)-$startMem,2) . "k<br/>";
echo "peak memory (real): " . number_format((memory_get_peak_usage(true)/1024),2) . "k<br/><br/>";

echo "Total time: ". number_format($eTime - $sTime, 2)."s";

/* RESULTS

start memory: 703.78k
end memory: 710.36k
peak memory: 64.22k
peak memory (real): 768.00k

Total time: 13.84s

*/