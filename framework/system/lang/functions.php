<?php

/**
 * This function converts a given string to camel case format with the first letter lower cased.
 * 
 * Any non alphanumeric char will be removed and the next letter
 * will be capitalized. For example, "Bob's Burger" will became 
 * "bobSBurger".
 *
 * @param string $str
 * @param boolean $upperFirst Wheter the first letter should be upper cased
 * @return string
 */

function strToCamelCase($str)
{
	$str = str_replace(' ', '', ucwords(preg_replace('/[^A-Z^a-z^0-9]+/',' ', $str)));
	$str[0] = strtolower($str[0]);
	
    return $str;
}