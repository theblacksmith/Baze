<?php

/**
 * This class is only a proxy for Zend_Config_Ini object
 */
class ConfigIni extends Config
{
    /**
     * Loads the section $section from the config file $filename for
     * access facilitated by nested object properties.
     *
     * If any keys with $section are called "extends", then the section
     * pointed to by the "extends" is then included into the properties.
     * Note that the keys in $section will override any keys of the same
     * name in the sections that have been included via "extends".
     *
     * If any key includes a ".", then this will act as a separator to
     * create a sub-property.
     *
     * example ini file:
     *      [all]
     *      db.connection = database
     *      hostname = live
     *
     *      [staging]
     *      extends = all
     *      hostname = staging
     *
     * after calling $data = new Zend_Config_Ini($file, 'staging'); then
     *      $data->hostname === "staging"
     *      $data->db->connection === "database"
     *
     * The $config parameter may be provided as either a boolean or an array. If provided as a boolean, this sets the
     * $allowModifications option of Zend_Config. If provided as an array, there are two configuration directives that
     * may be set. For example:
     *
     * $config = array(
     *     'allowModifications' => false,
     *     'nestSeparator'      => '.'
     *      );
     *
     * @param  string        $filename
     * @param  mixed         $section
     * @param  boolean|array $config
     * @throws ConfigException
     */
    public function __construct($filename, $section, $config = false)
    {
    	try {
    		$this->configObject = new Zend_Config_Ini($filename, $section, $config);
    	}
    	catch(Zend_Config_Exception $ex)
    	{
    		throw new ConfigException($ex->getMessage(),$ex->getCode());
    	}
    }
}