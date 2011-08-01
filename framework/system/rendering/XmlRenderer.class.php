<?php
/**
 * Arquivo da classe XmlRender
 *
 * Esse arquivo ainda não foi documentado
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze.system.rendering
 */

import('system.rendering.IRenderer');

/**
 * Classe XmlRender
 *
 * Essa classe ainda não foi documentada
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze.system.rendering
 */
class XmlRenderer implements IRenderer {

	/**
 	 * @access public
	 * @param IRenderable $object
	 * @param IOutputWriter $writer
	 */
	public function render(IRenderable $object, IWriter $writer)
	{
		if($object->hasCustomRenderer())
		{
			$renderFunc = $object->getCustomRenderer();
			call_user_func($renderFunc, $object, $this, $writer);
		}
		else
		{
			$name = $object->getObjectName();
			$atts = $object->getAttributesToRender();
			$joinedAtts = array();

			if(count($atts) > 0)
			{
				foreach ($atts as $key => $val) {
					if(is_callable($val))
						$joinedAtts[] = call_user_func($val);
					else
						$joinedAtts[] = $key.'="'.$val.'"';
				}

				$openTag = "<$name ".join(' ', $joinedAtts).'>';
			}
			else
				$openTag = "<$name>";

			$writer->write($openTag);

			$object->renderChildren($this, $writer);

			$writer->write("</$name>");
		}
	}
}