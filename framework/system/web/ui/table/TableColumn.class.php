<?php
/**
 * Arquivo TableColumn.class.php
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
 * Classe TableColumn<br />
 * Implements methods of manipulation of a Column of the one html table.
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
class TableColumn extends InteractiveContainer
{
	/**
	 * array of objects TableCells
	 * @access public
	 * @var array
	 */
	public $tableCell = array();

	/**
	 * It informs if the column is visible or not in the table
	 * @acces private
	 * @var boolean
	 */
	private $visible;

	/**
	 * function __construct()<br>
	 * construtor method of TableColumn class.
	 *
	 * @param array $column - array that contains the values of the new column
	 */
	function __construct($cellArray)
	{
		$this->visible = true;
		for ($i = 0; $i < count($cellArray); $i++)
		{
			$this->tableCell[$i] = new TableCell($cellArray[$i]);
		}
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

	/*	função: setCell
	*	Parâmetros:
	*		$cell - número da célula na coluna
	*		<espera-se receber o índice correto do vetor>
	*		$valor - novo valor da célula
	*/
	/**
	 * function setCell()<br>
	 * It attributes a value to a cell of the column.
	 *
	 * @acces public
	 * @param int $cell - number of the cell that will be changed your value
	 * @param int $valor - value to be atribubutted to the cell of the column
	 */
	public function setCell($cell, $valor)
	{
		$this->tableCell[$cell]->setCellValue($valor);
	}

	/*	função: getCell
	*	Parâmetros:
	*		$cell - número da célula na coluna
	*		<!-- espera-se receber o índice correto do vetor -->
	*	retorna a célula $cell
	*/
	/**
	 * function getCell()<br>
	 * It returns a specific TableCell object of the column.
	 *
	 * @acces public
	 * @param int $cell - number of the cell that will be returned
	 * @return TableCell -  requested cell of the column
	 */
	public function getCell($cell)
	{
		return $this->tableCell[$cell];
	}

	/*	função: addCell
	*	parâmetros:
	*		$cell - objeto da Classe TableCell
	*	descrição:
	*		adiciona a célula $cell no fim da coluna ("abaixo na tabela")
	*		procedimento - se $cell for uma variável instanciada adiciona no
	*		final da coluna, caso contrário, cria-se uma nova célula com conteúdo
	*		vazio e adiciona no final da coluna
	*/
	/**
	 * function addCell()<br>
	 * It inserts one cell in the end (under) of the column.
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
	*		deleta uma célula da coluna
	*		procedimento - caso a coordenada da célula seja válida,
	*		cria um vetor com as células antes e um com
	*		as células depois da célula selecionada e merge esses dois vetores
	*		no vetor de células da coluna,
	*		caso contrário, reporta o erro
	*	parâmetros:
	*		cell - índice no vetor da célula a ser excluída
	*/
	/**
	 * function deleteCell()<br>
	 * It removes a specific cell of the column.
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
			trigger_error("célula da coluna" . $cell . "não foi excluída. Coordenada inválida!", E_USER_ERROR);
		}// se coordenada for inválida

		/*deleta a célula da coluna*/
		//vetor com as células acima
		$upCells = array_slice($this->tableCell, 0, $cell);
		//vetor com as células abaixo
		$downCells = array_slice($this->tableCell, $cell+1);
		//merge as células à esquerda e à direita da célula a ser excluída
		$this->tableCell = array_merge($upCells, $downCells);
	}

	/**
	 * function isVisible()<br>
	 * It informs if the column is visible or not.
	 *
	 * @acces public
	 * @return boolean
	 */
	function isVisible()
	{
		return $this->visible;
	}

	/**
	 * function setVisible()<br>
	 * It becomes the column visible or not.
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