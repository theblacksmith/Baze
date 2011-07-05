<?php
/**
 * Arquivo TableRow.class.php
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
 * Classe TableRow<br />
 * Implements methods of manipulation of a row of the one html table.
 * It makes possible to get a cell object or set cell object value,
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
class TableRow extends InteractiveContainer
{
	/** Tag Properties
	 *
	 * @access private
	 */
	protected $align;
	protected $bgcolor;
	protected $char;
	protected $charoff;
	protected $class;
	protected $dir;
	protected $id;
	protected $lang;
	//protected $style;
	protected $title;
	protected $valign;
	protected $xmlLang;

	/** Event Attributes
	 *
	 * @access private
	 */
	//protected $onclick;
	//protected $ondblclick;
	//protected $onmousedown;
	//protected $onmouseup;
	//protected $onmouseover;
	//protected $onmousemove;
	//protected $onmouseout;
	//protected $onkeypress;
	//protected $onkeydown;
	//protected $onkeyup;

	/**
	 * array of objects TableCells
	 * @access public
	 * @var array
	 */
	public $tableCell = array();

	/**
	 * It informs if the row is visible or not in the table
	 * @acces private
	 * @var boolean
	 */
	protected $visible;

	/**
	 * function __construct()<br>
	 * construtor method of TableRow class.
	 *
	 * @param array $row - array that contains the values of the new row
	 */
	function __construct(array $row)
	{
		parent::__construct();
		
		$this->visible = true;

		$count = count($row);
		
		for($i = 0; $i < $count; $i++)
		{
			if($row[$i] instanceof TableCell)
				$this->tableCell[$i] = $row[$i];
			else
				$this->tableCell[$i] = new TableCell( $row[$i] );
		}
	}

	/*	função: setCell
	*	Parâmetros:
	*		$cell - número da célula na linha
	*		<espera-se receber o índice correto do vetor>
	*		$valor - novo valor da célula
	*/
	/**
	 * function setCell()<br>
	 * It attributes a value to a cell of the row.
	 *
	 * @acces public
	 * @param int $cell - number of the cell that will be changed your value
	 * @param int $valor - value to be atribubutted to the cell of the row
	 */
	public function setCell($cell, $valor)
	{
		$this->tableCell[$cell]->setCellValue($valor);
	}

	/*	função: getCell
	*	Parâmetros:
	*		$cell - número da célula na linha
	*		<!-- espera-se receber o índice correto do vetor -->
	*	retorna a célula $cell
	*/
	/**
	 * function getCell()<br>
	 * It returns a specific TableCell object of the row.
	 *
	 * @acces public
	 * @param int $cell - number of the cell that will be returned
	 * @return TableCell -  requested cell of the row
	 */
	public function getCell($cell)
	{
		return $this->tableCell[$cell];
	}

	/*	função: addCell
	*	parâmetros:
	*		$cell - objeto da Classe TableCell
	*	descrição:
	*		adiciona a célula $cell no fim da linha ("à direita na tabela")
	*		procedimento - se $cell for uma variável instanciada adiciona no
	*		final da linha, caso contrário, cria-se uma nova célula com conteúdo
	*		vazio e adiciona no final da linha
	*/
	/**
	 * function addCell()<br>
	 * It inserts one cell in the end (to the right) of the row.
	 *
	 * @acces public
	 * @param TableCell $cell
	 */
	public function addCell($cell)
	{
		array_push($this->tableCell, $cell);
	}

	/*	função: deleteCell
	*	descrição:
	*		deleta uma célula da linha
	*		procedimento - caso a coordenada da célula seja válida,
	*		cria um vetor com as células antes e um com
	*		as células depois da célula selecionada e merge esses dois vetores
	*		no vetor de células da linha,
	*		caso contrário, reporta o erro
	*	parâmetros:
	*		cell - índice no vetor da célula a ser excluída
	*/
	/**
	 * function deleteCell()<br>
	 * It removes a specific cell of the row.
	 *
	 * @acces public
	 * @param int $cell - number of the cell to be removed
	 */
	public function deleteCell($cell)
	{
		/* verificar existência da célula */
		if($cell<0 || $cell>=count($this->tableCell))
		{
			//reportar erro
			trigger_error("célula da linha" . $cell . "não foi excluída. Coordenada inválida", E_USER_ERROR);
		}// se coordenada for inválida

		/*deleta a célula da linha*/
		//vetor com as células à esquerda
		$leftCells = array_slice($this->tableCell, 0, $cell);
		//vetor com as células à direita
		$rightCells = array_slice($this->tableCell, $cell+1);
		//merge as células à esquerda e à direita da célula a ser excluída
		$this->tableCell = array_merge($leftCells, $rightCells);
	}

	/**
	 * function isVisible()<br>
	 * It informs if the row is visible or not.
	 *
	 * @acces public
	 * @return boolean
	 */
	function isVisible()
	{
		return $this->visible;
	}

	/**
	 * Function getTableCells()<br><br>
	 *
	 * @author Luciano (27/07/06)
	 * @return array - array contain all cell in the column
	 */
	public function getTableCells()
	{
		return $this->tableCell;
	}

	/**
	 * function setVisible()<br>
	 * It becomes the row visible or not.
	 *
	 * @acces public
	 * @param boolean $visibility
	 */
	function setVisible($visibility)
	{
		if(is_bool($visibility))
			$this->visible = $visibility;
	}
}