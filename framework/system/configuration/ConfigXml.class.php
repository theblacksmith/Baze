<?php

/**
 * This class is only a proxy for a Zend_Config_Xml object 
 */
class ConfigXml extends Config
{

    /**
     * Loads the section $section from the config file $filename for
     * access facilitated by nested object properties.
     *
     * Sections are defined in the XML as children of the root element.
     *
     * In order to extend another section, a section defines the "extends"
     * attribute having a value of the section name from which the extending
     * section inherits values.
     *
     * Note that the keys in $section will override any keys of the same
     * name in the sections that have been included via "extends".
     *
     * @param string $filename
     * @param mixed $section
     * @param boolean $allowModifications
     * @throws ConfigException
     */
    public function __construct($filename, $section, $allowModifications = false)
    {
    	try {
			$this->configObject = new Zend_Config_Xml($filename, $section, $allowModifications);
    	}
    	catch(Zend_Config_Exception $ex)
    	{
    		throw new ConfigException($ex->getMessage(),$ex->getCode());
    	}
    }
}