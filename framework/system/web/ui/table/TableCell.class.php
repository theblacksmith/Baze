<?php

/**
 * Arquivo TableCell.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.table
 */

/**
 * Import
 */

import( 'system.web.ui.Style' );

/**
 * Classe TableCell<br />
 * Implements methods of manipulation of a Cell of the one html table.
 * It makes possible to get or set cell value,
 * to get style and the properties of the group of the cell,
 * to change the style and the properties of the group of the cell,
 * to concatenate content to the value of the cell
 * and to clean properties of the group, of a specific type, of the cell.
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.table
 */
class TableCell extends InteractiveContainer
{
	/**
	 * Tag Properties
	 *
	 * @access private
	 */
	protected $abbr;
	protected $align;
	protected $axis;
	protected $bgcolor;
	protected $char;
	protected $charoff;
	protected $class;
	protected $colspan;
	protected $dir;
	protected $headers;
	protected $height;
	//private $id;		[Propriedade Herdada]
	protected $lang;
	protected $nowrap; 	// <Deprecated>
	protected $rowspan;
	protected $scope;
	//private $style;	[Propriedade Herdada]
	protected $title;
	protected $valign;
	protected $width;
	protected $xmlLang;

	/**
	 * Text Cell
	 *
	 * @access private
	 */
	 protected $text;

	/**
	 * Event Attributes
	 *
	 * @access private
	 */
	private $onclick;
	private $ondblclick;
	private $onmousedown;
	private $onmouseup;
	private $onmouseover;
	private $onmousemove;
	private $onmouseout;
	private $onkeypress;
	private $onkeydown;
	private $onkeyup;

	/**#@+
	 * @var string
	 */
	protected $cellValue;
	protected $xhtml;
	/**#@-*/
	/**
	 * It contains the group properties of the cell,
	 * is an array indexed by properties of the group.
	 * <br>Group Properties:<br>
	 * colspan - boolean value that it informs if the cell participates of
	 *  a vertical group.<br>
	 * rowspan - boolean value that it informs if the cell participates of
	 *  a horizontal group.<br>
	 * colIni - initial coordinate of the interval of the vertical group<br>
	 * colFim - final coordinate of the interval of the vertical group<br>
	 * rowIni - initial coordinate of the interval of the horizontal group<br>
	 * rowFim - final coordinate of the interval of the horizontal group
	 * @var array
	 */
	protected $span;
	/**#@-*/

	/**
	 * function __construct()<br>
	 * construtor method of TableCell class.
	 *
	 * @param string $cellValue - value to be atributted to the cell
	 */
	function __construct($cellValue = "")
	{
		parent::__construct();

		$this->cellValue = $cellValue;

		$this->span["rowspan"] = false; // se é rowspan
		$this->span["rowIni"] = -1; // coluna de início do rowspan
		$this->span["rowFim"] = -1; // coluna de fim do rowspan

		$this->span["colspan"] = false; // se é colspan
		$this->span["columnIni"] = -1; // linha de início do colspan
		$this->span["columnFim"] = -1; // linha de fim do colspan
	}

	/**
	 * function getCellValue()<br>
	 * It returns the value of the cell.
	 *
	 * @acces public
	 * @return string - value of the cell
	 */
	function getCellValue()
	{
		return $this->cellValue;
	}

	/**
	 * function setCellValue()<br>
	 * It attributes a value to the cell.
	 *
	 * @acces public
	 * @param string $newValue - value to be attributed to the cell
	 */
	function setCellValue($newValue)
	{
		$this->cellValue = $newValue;
	}

	/**
	 * function getXhtml()<br>
	 * It returns an array with the html code of the object TableCell.
	 *
	 * @acces public
	 * @return $html - array with the html code of the cell
	 */
	function getXhtml()
	{
		$styleProperties = "";
		/*início montagem do xhtml*/
		if(is_object ($this->style))
			$styleProperties.= ' style="' . $this->style->getXHTML() . '"';

		/* abre a montagem da célula */
		$this->xhtml = "\n\t\t<td ";

		if (! empty($this->class))
		{
			$this->xhtml.= ' class="'.$this->class[0].'" ';
		}

		/* adiciona a propriedade de agrupamento horizontal caso exista */
		if($this->span["rowspan"])
		{
			$qtColumns = ((($this->span["rowFim"])-($this->span["rowIni"]))+1);
			$this->xhtml .= " colspan=\"" . $qtColumns . "\"";
		}

		/* adiciona a propriedade de agrupamento vertical caso exista */
		if($this->span["colspan"])
		{
			$qtRows = ((($this->span["columnFim"])-($this->span["columnIni"]))+1);
			$this->xhtml .= " rowspan=\"" . $qtRows . "\"";
		}

		/* adiciona a propriedade estilo caso exista */
		if($this->style!=null)
		{
			$this->xhtml .= $styleProperties;
		}

		/* fecha o html da célula */
		$this->xhtml .=">" ;
		is_object( $this->cellValue ) ? $this->xhtml .= $this->cellValue->getXHTML() : $this->xhtml .= $this->cellValue;

		$this->xhtml .= $this->getChildrenXHTML();
		$this->xhtml .= "</td>";

		/*fim da montagem do xhtml*/

		return $this->xhtml;
	}

	/* função setStylePrpt
	*	Parâmetros:
	*		property - nome da propriedade a ser modificada
	*		value - valor a ser atribuido à propriedade
	*	descrição:
	*		modifica somente uma propriedade do estilo da célula
	*/
	/**
	 * function setStylePrpt()<br>
	 * It attributes a value to a property of the style of the cell.
	 *
	 * @acces public
	 * @param string $property - name of the style property whose value will be modified
	 * @param string $value - value to be attributed to the style property
	 */
	function setStylePrpt($property, $value)
	{
		$this->setStyle($property . ": " . $value . ";");
	}

	/* função: setStyleStr
	*	parâmetros:
	*		strStyle - string com as propriedades do estilo
	*	descrição:
	*		atribui ao estilo da célula as propriedades
	*		contidas na string recebida, depois de fazer
	*		o parser, que retornará um vetor indexado pelas
	*		propriedades referenciando seus respectivos valores.
	*/
	/**
	 * function setStyleStr()<br>
	 * It attributes values to a properties of the style of the cell
	 * based on a style string.
	 *
	 * @acces public
	 * @param string $strStyle - string that contains the properties
	 * and its respective values to be attributed to the style of the cell
	 */
	function setStyleStr($strStyle)
	{
			$this->setStyle($strStyle);
	}

	/* função: setStyleOb
	*	parâmetros:
	*		obStyle - objeto da classe style
	*	descrição:
	*		atribui o estilo recebido à célula
	*/
	/**
	 * function setStyleOb()<br>
	 * It receives an object of the Style Class that will be the new style of the cell.
	 *
	 * @acces public
	 * @param Style $obStyle - object style
	 */
	function setStyleOb($obStyle)
	{
		$this->style = $obStyle;
	}

	/* função getStyle
	*	descrição:
	*		retorna o estilo da célula
	*/
	/**
	 * function getStyle()<br>
	 * It returns the style of the cell.
	 *
	 * @acces public
	 * @return Style - object of the class style of the cell
	 */
	function getStyle()
	{
		return $this->style;
	}

	/* função getSpan
	*	descrição:
	*		retorna as propriedades de span da célula
	*/
	/**
	 * function getSpan()<br>
	 * It returns the array that contains the group properties
	 * of the cell.
	 *
	 * @acces public
	 * @return array - array indexed for the properties of the group of the cell
	 * @see span property of the cell
	 */
	function getSpan()
	{
		return $this->span;
	}

	/* função setSpan
	*	descrição:
	*		atribui a propriedade span  para agregação de células
	*	parâmetros:
	*		span - tipo de span [rowspan | colspan]
	*		ini - coordenada de início do span
	*		fim - coordenada de fim do span
	*/
	/**
	 * function setSpan()<br>
	 * It applies a group in the cell. Function used
	 * only in mother cells of groups.
	 *
	 * @acces public
	 * @param string $span - type of the group
	 * @param int $ini - initial coordinate of the interval of the group
	 * @param int $fim - final coordinate of the interval of the group
	 */
	function setSpan($span, $ini, $fim)
	{
		/* qual o caso de span? rowspan ou colspan? */
		switch($span)
		{
			case 'rowspan': // caso rowspan
				/* determina o intervalo do span */
				$this->span["rowIni"] = $ini;
				$this->span["rowFim"] = $fim;
				break;
			case 'colspan': // caso colspan
				/* determina o intervalo do span */
				$this->span["columnIni"] = $ini;
				$this->span["columnFim"] = $fim;
				break;
			default: // o index span passado é inválido
				/* reportar o erro de span inválido */
				trigger_error("Tipo de agrupamento" . $span . "é inválido. Agrupamento não atribuido à célula!", E_USER_ERROR);
				return;
		}
		/* ativa a propriedade span */
		$this->span[$span] = true;
	}

	/* função valueCat
	*	parâmetros:
	*		value - valor a ser concatenado
	*	descrição:
	*		concatena $value no valor da célula
	*/
	/**
	 * function valueCat()<br>
	 * It concatenates a string to the cell value.
	 *
	 * @acces public
	 * @param string $value - string that will be concatenated to the cell value
	 */
	function valueCat($value)
	{
		$this->cellValue .= $value;
	}

	/* função splitCell
	*	descrição:
	*		desabilita o agrupamento do tipo $type da célula
	*	parâmetros:
	*		type - tipo de agrupamento que será desabilitado
	*/
	/**
	 * function valueCat()<br>
	 * It disactivates a grouping of a specific type of the cell.
	 *
	 * @acces public
	 * @param string $type - group type that will be disactivated
	 */
	function splitCell($type)
	{
		switch ($type)
		{/* para um tipo de agrupamento */

			case 'rowspan': /* desabilita rowspan */
				$this->span["rowspan"]=false;
				$this->span["rowIni"]=-1;
				$this->span["rowFim"]=-1;
				break;

			case 'colspan': /* desabilita colspan */
				$this->span["colspan"]=false;
				$this->span["columnIni"]=-1;
				$this->span["columnFim"]=-1;
				break;

			default: /* reportar erro: tipo inválido de agrupamento */
				trigger_error("Tipo (\"".$type."\") de agrupamento inválido!");
		}
	}

}