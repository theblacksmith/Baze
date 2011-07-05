<?php

$args = isset($argv) ? $argv : $_SERVER['argv'];

// skipping the first parameter is (the script name)
array_shift($args);

$output = array_shift($args);

$contents = '';

foreach($args as $inFile)
{
	if(!is_file($inFile))
		echo 'The file ', $inFile, " does not exists\n";
	else
		$contents .=  file_get_contents($inFile) . "\n";
}

file_put_contents($output, $contents);