<?php

require_once(dirname(__FILE__).'/System.class.php');

$system = System::getInstance();

$system->init();

$system->run();

exit;