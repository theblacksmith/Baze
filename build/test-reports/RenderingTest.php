<?php
$curdir = dirname(__FILE__);
require_once($curdir.'\..\system\lang\PhpType.php');
require_once($curdir.'/TextBox.class.php');
require_once($curdir.'/../system/rendering/XhtmlRender.php');
require_once($curdir.'/../system/io/HttpResponseWriter.php');

define('_IS_POSTBACK', false);

$r = new XhtmlRender();
$w = new HttpResponseWriter();
$t = new TextBox();

$t->setValue("testeeeeeeeeeee");

$r->render($t, $w);