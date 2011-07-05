<?php
set_include_path(get_include_path() . PATH_SEPARATOR . 'C:\projects\ZendFramework102\library');

require_once 'SessionNamespace.class.php';

/*$namespace = new SessionNamespace(); // default namespace

$namespace->foo = 100;

echo "\$namespace->foo = $namespace->foo\n";

if (!isset($namespace->bar)) {
    echo "\$namespace->bar not set\n";
}

unset($namespace->foo);*/

$defaultNamespace = new SessionNamespace('Default', true);

if (isset($defaultNamespace->numberOfPageRequests)) {
    $defaultNamespace->numberOfPageRequests++; // this will increment for each page load
} else {
    $defaultNamespace->numberOfPageRequests = 1; // first time
}

if(($defaultNamespace->numberOfPageRequests % 3)==0)
{
	echo "Page requests this session: P&iacute;";
}
else
	echo "Page requests this session: ", $defaultNamespace->numberOfPageRequests;

if($defaultNamespace->numberOfPageRequests >= 15)
{
	unset($defaultNamespace->numberOfPageRequests);
}

/*$s = new SessionNamespace('expireAll');
$s->a = 'apple';
$s->p = 'pear';
$s->o = 'orange';

$s->setExpirationSeconds(5, 'a'); // expire only the key "a" in 5 seconds

// expire entire namespace in 5 "hops"
$s->setExpirationHops(10);

$s->setExpirationSeconds(60);
// The "expireAll" namespace will be marked "expired" on
// the first request received after 60 seconds have elapsed,
// or in 5 hops, whichever happens first.

echo $s->a . '-' . $s->p . '-' . $s->o;*/

/*
require_once 'Zend/Session/Namespace.php';

$namespace = new Zend_Session_Namespace(); // default namespace

$namespace->foo = 100;

echo "\$namespace->foo = $namespace->foo\n";

if (!isset($namespace->bar)) {
    echo "\$namespace->bar not set\n";
}

unset($namespace->foo);*/