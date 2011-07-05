<?php
/**
 * Arquivo Table.class.php
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
import( 'system.web.ui.table.TableCell' );
import( 'system.web.ui.table.TableRow' );
import( 'system.web.ui.table.TableColumn' );

/**
 * Classe Table<br />
 * Implements methods of manipulation of a html table.
 * It makes possible to insert rows or columns,
 * to group and to split cells, change the style of the row or column,
 * to show and to hide rows or columns.
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.table
 */
class Table extends InteractiveContainer
{
	/**#@+
	 * It contains the number of rows and columns of the table
	 * @access private
	 * @var string
	 */
	private $rowNumber; //deve condizer com a qt de células de todas as colunas
	private $columnNumber; // deve condizer com a qt de células de todas as linhas
	/**#@-*/

	/**#@+
	 * Pointer
	 *
	 * @access public
	 */

	/**
	 * Array that contains the pointers for the
	 * TableRow objects of the table.
	 * @var TableRow
	 */
	public $tableRows;// = array();

	/**
	 * Array that contains the pointers for the
	 * TableColumn objects of the table.
	 * @var TableColumn
	 */
	public $tableColumns;// = array();

	/**
	 * @var ColumnSet
	 */
	//public $columnSet;

	/**
	 * @var RowSet
	 */
	//public $rowSet;
	/**#@-**/

	/**#@+
	 * Tag attributes
	 *
	 * @access protected
	 * @var string
	 */
	protected $align;
	protected $bgcolor;
	protected $border;
	protected $cellpadding;
	protected $cellspacing;
	protected $class;
	protected $dir;
	protected $frame;
	//protected $id;	[Propriedade Herdada]
	protected $lang;
	protected $rules;
	//protected $style;	[Propriedade Herdada]
	protected $summary;
	protected $title;
	protected $width;
	protected $xmlLang;
	/**#@-**/

	/**
	 * function __construct()<br>
	 * construtor method of class Table.
	 * 
	 * @param  $cols int number of columns (default: 0)
	 * @param  $rows int number of rows (default: 0)
	 */
	function __construct($cols = 0, $rows = 0)
	{
		$this->rowNumber    = $rows;
		$this->columnNumber = 4;
		$this->style = new Style ();
	}

	/**
	 * function set()<br>
	 * It attributes a value to a property of the table.
	 *
	 * @acces public
	 * @param string $prop - name of the property whose value will be modified
	 * @param string $value - value to be attributed to the property
	 */
	function set ($prop, $value)
	{
		$this->$prop = $value;
	}

	/**
	 * function setCellValue()<br>
	 * It attributes a value to a cell of the table.
	 *
	 * @acces public
	 * @param int $lin - number of the row of the cell that will
	 * have its changed value
	 * @param int $col - number of the column of the cell that will
	 * have its changed value
	 * @param string $value - value to be attributed to the cell
	 */
	function setCellValue($lin, $col, $value)
	{
		$this->tableColumns[$col]->tableCell[$lin]->setCellValue($value);
	}

	/**
	 * function getRowNumber()<br>
	 * It returns the number of rows of the table.
	 *
	 * @acces public
	 * @return int - number of rows of the table
	 */
	function getRowNumber()
	{
		return $this->rowNumber;
	}

	/**
	 * function getColumnNumber()<br>
	 * It returns the number of columns of the table.
	 *
	 * @acces public
	 * @return int - number of columns of the table
	 */
	function getColumnNumber()
	{
		return $this->columnNumber;
	}

	/*	função: addRow
	*	descrição:
	*		adiciona uma linha à tabela
	*		procedimento - preenche ou decrementa o vetor de conteúdo recebido
	*		de acordo com a quantidade de colunas da tabela, instancia a linha
	*		no final da tabela (abaixo), reajusta as colunas para que
	*		enxerguem as novas células da tabela e incrementa a quantidade
	*		de linhas
	*	parâmetros:
	*		row - array com os conteúdos iniciais da linha
	*/
	/**
	 * function addRow()<br>
	 * It inserts one row in the end (under) of the table.
	 *
	 * @acces public
	 * @param array $row - array that contains the values of the new row
	 */
	function addRow($row)
	{
		/* instancia a linha no final da tabela */
		if(!($row instanceof TableRow))
			$row = new TableRow($row);
			
		/* 	se qt elementos do vetor linha for menor que o número de colunas
	    *	já criadas
	    */
		$nCells = count($row->getTableCells());
		if( $nCells < $this->columnNumber )
		{
			for( $j = count( $row ); $j < $this->columnNumber; $j++ )
			{
				/*	preenche o vetor com espaços vazios até o número de
		    	*	colunas da tabela
		    	*/
				$row->addCell(new TableCell("&nbsp;"));
			}
		}
		else
		{
			/*	enquanto o número de elementos do vetor linha for maior
			*	que o número de colunas já criadas
			*/
			while($nCells > $this->columnNumber) {
				$this->newColumn();
			}
		}

		$this->tableRows[ $this->rowNumber ] = $row;

		/*  apontar as colunas da tabela para as novas células */
		for ( $i = 0; $i < $this->columnNumber; $i++ ) {
			$this->tableColumns[$i]->tableCell[$this->rowNumber] = $this->tableRows[$this->rowNumber]->tableCell[$i];
		}

		$this->rowNumber++;
	}

	/* função newColumn
	*	descrição:
	*		adiciona uma coluna em branco na tabela
	*/
	/**
	 * function newColumn()<br>
	 * It inserts one empty column in the end (to the left) of the table.
	 *
	 * @acces private
	 */
	private function newColumn()
	{
		$this->tableColumns[$this->columnNumber] = new TableColumn( array() );

		/* em todas as linhas da tabela */
		for($x = 0; $x < $this->rowNumber; $x++)
		{
			/* adicionar mais uma célula ao final da linha */
			$this->tableRows[$x]->tableCell[$this->columnNumber] = new TableCell ("&nbsp;");
			$this->tableColumns[$this->columnNumber]->tableCell[$x] = $this->tableRows[$x]->tableCell[$this->columnNumber];
		}

		/* incrementar número de colunas */
		$this->columnNumber++;
	}

	/* função newRow
	*	descrição:
	*		adiciona uma linha em branco na tabela
	*/
	/**
	 * function newRow()<br>
	 * It inserts one empty row in the end (under) of the table.
	 *
	 * @acces private
	 */
	private function newRow()
	{
		$this->tableRows[$this->rowNumber] = new TableRow( array() );

		/* em todas as colunas da tabela */
		for($x = 0; $x < $this->columnNumber; $x++)
		{
			/* adicionar mais uma célula ao final da coluna */
			$this->tableColumns[$x]->tableCell[$this->rowNumber] = new TableCell ("&nbsp;");
			$this->tableRows[$this->rowNumber]->tableCell[$x] = $this->tableColumns[$x]->tableCell[$this->rowNumber];
		}

		/* incrementar número de linhas */
		$this->rowNumber++;
	}

	/*	função: removeRow
	*	descrição:
	*		remove uma linha da tabela
	*		procedimento - avalia se o número da linha solicitada
	*		para exclusão é válida, deleta a linha na tabela
	*		reajusta as colunas para não enxergar mais as células
	*		removidas, decrementa a quantidade de linhas da tabela
	*	parâmetros:
	*		row - número da linha a ser removida
	*		<índice do vetor>
	*/
	/**
	 * function removeRow()<br>
	 * It removes one especific row of the table.
	 *
	 * @acces public
	 * @param int $row - index of the row that will be removed
	 */
	function removeRow($row)
	{
		if($row >= $this->rowNumber || $row < 0)
		{
			// reportar erro: número da linha inválido
			trigger_error("Número da linha é inválido!", E_USER_NOTICE);
		}//if número da linha inválido
		else
		{
			/*deleta a linha do array de linhas*/
			//vetor com as linhas acima da selecionada para exclusão
			$upRows = array_slice($this->tableRows, 0, $row);
			//vetor com as linhas abaixo da selecionada para exclusão
			$downRows = array_slice($this->tableRows, $row+1);
			//merge as linhas à esquerda e à direita da linha excluída
			$this->tableRows = array_merge($upRows, $downRows);

			/* deletar as células da linha excluída em todas as colunas */
			foreach($this->tableColumns as $column)
			{
				//deleta a célula da linha excluída na coluna
				$column->deleteCell($row);
			}//para toda coluna

			/*decrementa a quantidade de linhas da tabela*/
			$this->rowNumber--;
		}
	}

	/*	função: addColumn
	*	descrição:
	*		adiciona uma coluna à tabela
	*		procedimento - preenche ou decrementa o vetor de conteúdo recebido
	*		de acordo com a quantidade de linhas da tabela, instancia a coluna
	*		no final da tabela (à direita), reajusta as linhas para que
	*		enxerguem as novas células e incrementa a quantidade
	*		de colunas
	*	parâmetros:
	*		column - array com os conteúdos iniciais da coluna
	*/
	/**
	 * function addColumn()<br>
	 * It inserts one column in the end (to the left) of the table.
	 *
	 * @acces public
	 * @param array $column - array that contains the values of the new column
	 */
	function addColumn($column)
	{
		/* 	se qt elementos do vetor coluna for menor que o número de linhas
	    *	já criadas
	    */
		if(count($column) < $this->rowNumber)
		{
			for($j = count($column); $j < $this->rowNumber; $j++)
			{
				/*	preenche o vetor com espaços vazios até o número de
		    	*	linhas da tabela
		    	*/
				array_push($column, "&nbsp;");
			}
		}
		else
		{
			/*	enquanto o número de elementos do vetor coluna for maior
			*	que o número de linhas já criadas
			*/
			$rows = $this->rowNumber;
			for($j = count($column); $j > $rows; $j--)
			{
				/* inserir uma linha em branco
				*/
				$this->newRow();
			}
		}
		/* instancia a coluna no final da tabela */
		$this->tableColumns[$this->columnNumber] = new TableColumn($column);

		/*  apontar as linhas da tabela para as novas células */
		for ($i = 0; $i < $this->rowNumber; $i++)
		{
			$this->tableRows[$i]->tableCell[$this->columnNumber] = $this->tableColumns[$this->columnNumber]->tableCell[$i];
		}

		$this->columnNumber++;
	}

	/* função: getColumn
	*
	*	descrição:
	*		deve receber um número de coluna e retorná-la a seu cliente
	*		<trata o número da coluna recebido para avaliar sua validade>
	*	parâmetros:
	*		column - número da coluna que se quer obter
	*	retorno:
	*		ERROR - caso o número da coluna não seja válido
	*		TableColumn - coluna desejada
	*/
	/**
	 * function getColumn()<br>
	 * It returns a pointer of the requested object TableColumn of the table.
	 *
	 * @access public
	 * @param int $column - index of the column in the table
	 * @return TableColumn - pointer to the column requested
	 */
	public function getColumn($column)
	{
		if($column < 0 || $column >= $this->columnNumber)
		{
			// reportar erro: número de coluna inválido
			trigger_error('Número da coluna inválido!', E_USER_NOTICE);
			return null;
		}//if número da coluna é inválida inválida

		// retorna coluna (TableColumn)
		return ($this->tableColumns[$column]);
	}

	/*	função: removeColumn
	*	descrição:
	*		remove uma coluna da tabela
	*		procedimento - avalia se o número da coluna solicitada
	*		para exclusão é válida, deleta a coluna na tabela
	*		reajusta as linhas para não enxergar mais as células
	*		removidas, decrementa a quantidade de colunas da tabela
	*	parâmetros:
	*		column - número da coluna a ser removida
	*		<índice do vetor>
	*/
	/**
	 * function removeColumn()<br>
	 * It removes a specific column of the table
	 *
	 * @acces public
	 * @param int $column - index of the column in the table
	 */
	function removeColumn($column)
	{
		if($column >= $this->columnNumber || $column < 0)
		{
			// reportar erro: número da coluna inválido
			trigger_error("Número da coluna é inválido!", E_USER_NOTICE);
		}//if número da coluna inválido
		else
		{
			/*deleta a coluna do array de colunas*/
			//vetor com as colunas à esquerda
			$leftColumns = array_slice($this->tableColumns, 0, $column);
			//vetor com as colunas à direita
			$rightColumns = array_slice($this->tableColumns, $column+1);
			//merge as colunas à esquerda e à direita da coluna excluída
			$this->tableColumns = array_merge($leftColumns, $rightColumns);

			/* deletar as células da coluna excluída em todas as linhas */
			foreach($this->tableRows as $row)
			{
				//deleta a célula da coluna excluída na linha
				$row->deleteCell($column);
			}//para toda linha

			/*decrementa a quantidade de colunas da tabela*/
			$this->columnNumber--;
		}
	}

	/* função: getRow
	*
	*	descrição:
	*		deve receber um número de linha e retorná-la a seu cliente
	*		<trata o número da linha recebido para avaliar sua validade>
	*	parâmetros:
	*		row - número da linha que se quer obter
	*	retorno:
	*		-1 - caso o número da linha não seja válido
	*		TableRow - linha desejada
	*/
	/**
	 * function getRow()<br>
	 * It returns a pointer of the requested object TableRow of the table.
	 *
	 * @acces public
	 * @param int $row - index of the row in the table
	 * @return TableRow - pointer to the row requested
	 */
	public function getRow($row)
	{
		if($row < 0 || $row >= $this->rowNumber)
		{
			// reportar erro: número de linha inválido
			trigger_error('Número da linha inválido!', E_USER_NOTICE);
			return null;
		}//if número da linha é inválida
		// retorna linha (TableRow)
		return ($this->tableRows[$row]);
	}

	/* função getXhtml
	*	descrição:
	*		monta o html do objeto table e o retorna
	*	retorno:
	*		html - código html da tabela
	*/
	/**
	 * function getXhtml()<br>
	 * It returns an array with the html code of the object Table.
	 *
	 * @acces public
	 * @return $html - array with the html code of the table
	 */
	public function getEntireElement()
	{
		$html = "";

		/* inicia a tag de abertura <table> */
		$html .= '<table width="'. $this->width .'"
				 border="' . $this->border .'"
				 cellspacing="' . $this->cellspacing .'"
				 cellpadding="' . $this->cellpadding .'"
					 class="'. $this->class.'"';

		/* captura valores de id e class do estilo */
		$id=$this->style->get("id");
		$class=$this->style->get("class");

		/*se existir o id do estilo */
		if($id)
		{
			/* adiciona somente o id da tabela */
			$html .= " id=\"" . $id . "\"";
		}
		/* senão, caso exista a class do estilo */
		elseif($class)
		{
			/* adiciona somente o class da tabela */
			$html .= " class=\"" . $class . "\"";
		}
		/*senão, adiciona as propriedades do estilo
		*	diretamente na tag table */
		else
		{
			/* captura o código do estilo */
			$style = $this->style->getXHTML();
			/* adiciona os atributos do estilo na
			*	propriedade style da tag table */
			$html .= " style=\"" . $style . "\"";
		}

		/* feha a tag de abertura <table> */
		$html .= ">";

		/* monta a tabela adicionando suas células */

		for ($i = 0; $i < $this->rowNumber; $i++)
		{/* para todas as linhas da tabela */
			/* inicia a linha da tabela */

			if($this->tableRows[$i]->isVisible())
			{
				$html .= "\n\t<tr>" . $this->columnNumber;

				for ($j = 0; $j < $this->columnNumber; $j++)
				{/* para todas as colunas da tabela */
					/* é preciso tratar as células que são agrupadas */

					$cellSpan = $this->tableRows[$i]->tableCell[$j]->getSpan();
					if($cellSpan["rowspan"])
					{/* caso a célula participe de um agrupamento horizontal */
						if(!$cellSpan["colspan"])
						{ /* caso a célula só participe de um agrupamento horizontal */
							if($cellSpan["rowIni"]==$j)
							{ /* caso ela seja a célula mãe deste agrupamento */
								/* verifica a quantidade de colunas escondidas no intervalo do agrupamento */
								$invCols=0;
								for($auxCol = $j; $auxCol <= $cellSpan["rowFim"]; $auxCol++)
								{
									if(!$this->tableColumns[$auxCol]->isVisible())
										++$invCols;
								}

								/* calcula o colspan final */
								$colSpan = ($cellSpan["rowFim"] - $cellSpan["rowIni"]) + 1;
								$finalSpan = $colSpan - $invCols;

								/* pega o html code original da célula */
								$cellHtml = $this->tableRows[$i]->tableCell[$j]->getXhtml();

								/* monta o html code final da célula com o colspan corrigido para exibição */
								if($finalSpan>1)
									$finalHtmlCell = str_replace("colspan=\"" . $colSpan . "\"", "colspan=\"" . $finalSpan . "\"", $cellHtml);
								else
									$finalHtmlCell = str_replace("colspan=\"" . $colSpan . "\"", "", $cellHtml);
								/* insere o código da célula mãe na linha */
								$html .= $finalHtmlCell;
							}
						}
						else
						{ /* caso a célula participe de um agrupamento de agrupamentos */
							if($cellSpan["rowIni"]==$j && $cellSpan["columnIni"]==$i)
							{/* caso ela seja a célula mãe do agrupamento */

								/* verifica a quantidade de colunas escondidas no intervalo do agrupamento */
								$invCols=0;
								for($auxCol = $j; $auxCol <= $cellSpan["rowFim"]; $auxCol++)
								{
									if(!$this->tableColumns[$auxCol]->isVisible())
										++$invCols;
								}

								/* calcula o colspan final */
								$colSpan = ($cellSpan["rowFim"] - $cellSpan["rowIni"]) + 1;
								$finalColSpan = $colSpan - $invCols;

								/* verifica a quantidade de linhas escondidas no intervalo do agrupamento */
								$invRows=0;
								for($auxRow = $i; $auxRow <= $cellSpan["columnFim"]; $auxRow++)
								{
									if(!$this->tableRows[$auxRow]->isVisible())
										++$invRows;
								}

								/* calcula o colspan final */
								$rowSpan = ($cellSpan["columnFim"] - $cellSpan["columnIni"]) + 1;
								$finalRowSpan = $rowSpan - $invRows;

								/* pega o html code original da célula */
								$cellHtml = $this->tableRows[$i]->tableCell[$j]->getXhtml();

								/* monta o html code final da célula com o colspan corrigido para exibição */
								if($finalColSpan>1)
									$finalHtmlCell1 = str_replace("colspan=\"" . $colSpan . "\"", "colspan=\"" . $finalColSpan . "\"", $cellHtml);
								else
									$finalHtmlCell1 = str_replace("colspan=\"" . $colSpan . "\"", "", $cellHtml);
								if($finalRowSpan>1)
									$finalHtmlCell2 = str_replace("rowspan=\"" . $rowSpan . "\"", "rowspan=\"" . $finalRowSpan . "\"", $finalHtmlCell1);
								else
									$finalHtmlCell2 = str_replace("rowspan=\"" . $rowSpan . "\"", "", $finalHtmlCell1);

								/* insere o código da célula mãe na linha */
								$html .= $finalHtmlCell2;
							}
							else
							{
								if(!$this->tableRows[$cellSpan["columnIni"]]->isVisible())
								{
									$newMotherCell = ($cellSpan["columnIni"]+1);
									while((!$this->tableRows[$newMotherCell]->isVisible()) && ($newMotherCell<=$cellSpan["columnFim"]))
									{
										$newMotherCell++;
									}
									if($newMotherCell<=$cellSpan["columnFim"] && $i==($newMotherCell) && $j == $cellSpan["rowIni"])
									{
										/* verifica a quantidade de colunas escondidas no intervalo do agrupamento */
										$invCols=0;
										for($auxCol = $cellSpan["rowIni"]; $auxCol <= $cellSpan["rowFim"]; $auxCol++)
										{
											if(!$this->tableColumns[$auxCol]->isVisible())
												++$invCols;
										}

										/* calcula o colspan final */
										$colSpan = ($cellSpan["rowFim"] - $cellSpan["rowIni"]) + 1;
										$finalColSpan = $colSpan - $invCols;

										/* verifica a quantidade de linhas escondidas no intervalo do agrupamento */
										$invRows=0;
										for($auxRow = $cellSpan["columnIni"]; $auxRow <= $cellSpan["columnFim"]; $auxRow++)
										{
											if(!$this->tableRows[$auxRow]->isVisible())
												++$invRows;
										}

										/* calcula o colspan final */
										$rowSpan = ($cellSpan["columnFim"] - $cellSpan["columnIni"]) + 1;
										$finalRowSpan = $rowSpan - $invRows;

										/* pega o html code original da célula */
										$cellHtml = $this->tableRows[$i]->tableCell[$j]->getXhtml();

										/* monta o html code final da célula com o colspan corrigido para exibição */
										if($finalColSpan>1)
											$finalHtmlCell1 = str_replace("colspan=\"" . $colSpan . "\"", "colspan=\"" . $finalColSpan . "\"", $cellHtml);
										else
											$finalHtmlCell1 = str_replace("colspan=\"" . $colSpan . "\"", "", $cellHtml);

										if($finalRowSpan>1)
											$finalHtmlCell2 = str_replace("rowspan=\"" . $rowSpan . "\"", "rowspan=\"" . $finalRowSpan . "\"", $finalHtmlCell1);
										else
											$finalHtmlCell2 = str_replace("rowspan=\"" . $rowSpan . "\"", "", $finalHtmlCell1);
										/* insere o código da célula mãe na linha */
										$html .= $finalHtmlCell2;
									}
								}
							}
						}
					}
					else
					{/* caso a célula não participe de um agrupamento horizontal */
						if($cellSpan["colspan"])
						{ /* porém a célula participa de um agrupamento vertical */
							if($cellSpan["columnIni"]==$i)
							{/* se ela for a célula mãe deste agrupamento */

								/* verifica a quantidade de linhas escondidas no intervalo do agrupamento */
								$invRows=0;
								for($auxRow = $cellSpan["columnIni"]; $auxRow <= $cellSpan["columnFim"]; $auxRow++)
								{
									if(!$this->tableRows[$auxRow]->isVisible())
										++$invRows;
								}

								/* calcula o rowspan final */
								$rowSpan = ($cellSpan["columnFim"] - $cellSpan["columnIni"]) + 1;
								$finalRowSpan = $rowSpan - $invRows;

								/* pega o html code original da célula */
								$cellHtml = $this->tableRows[$i]->tableCell[$j]->getXhtml();

								/* monta o html code final da célula com o rowspan corrigido para exibição */
								if($finalRowSpan>1)
									$finalHtmlCell = str_replace("rowspan=\"" . $rowSpan . "\"", "rowspan=\"" . $finalRowSpan . "\"", $cellHtml);
								else
									$finalHtmlCell = str_replace("rowspan=\"" . $rowSpan . "\"", "", $cellHtml);

								/* insere o código da célula mãe na linha */
								$html .= $finalHtmlCell;
							}
							else
							{
								if(!$this->tableRows[$cellSpan["columnIni"]]->isVisible())
								{
									$newMotherCell = ($cellSpan["columnIni"]+1);
									while((!$this->tableRows[$newMotherCell]->isVisible()) && ($newMotherCell<=$cellSpan["columnFim"]))
									{
										$newMotherCell++;
									}
									if($newMotherCell<=$cellSpan["columnFim"] && $i==($newMotherCell))
									{
										/* verifica a quantidade de linhas escondidas no intervalo do agrupamento */
										$invRows=0;
										for($auxRow = $cellSpan["columnIni"]; $auxRow <= $cellSpan["columnFim"]; $auxRow++)
										{
											if(!$this->tableRows[$auxRow]->isVisible())
												++$invRows;
										}

										/* calcula o rowspan final */
										$rowSpan = ($cellSpan["columnFim"] - $cellSpan["columnIni"]) + 1;
										$finalRowSpan = $rowSpan - $invRows;

										/* pega o html code original da célula */
										$cellHtml = $this->tableRows[$i]->tableCell[$j]->getXhtml();

										/* monta o html code final da célula com o rowspan corrigido para exibição */
										if($finalRowSpan>1)
											$finalHtmlCell = str_replace("rowspan=\"" . $rowSpan . "\"", "rowspan=\"" . $finalRowSpan . "\"", $cellHtml);
										else
											$finalHtmlCell = str_replace("rowspan=\"" . $rowSpan . "\"", "", $cellHtml);

										/* insere o código da célula mãe na linha */
										$html .= $finalHtmlCell;
									}
								}
							}
						}
						else
						{ /* caso a célula não participe de nenhum agrupamento */
							/* insere o código da célula mãe na linha */
							if($this->tableColumns[$j]->isVisible())
								$html .= $this->tableColumns[$j]->tableCell[$i]->getXhtml();
						}
					}
				}

				/* fecha a tag <tr> */
				$html .= "\n\t</tr>";
			}
		}

		/* fecha a tag table */
		$html .= "\n</table>";

		/* retorna o código da tabela */
		return $html;
	}

	/* função setRowStyle
	*	parâmetros:
	* 		row - número da linha a ser modificado o estilo
	*		style - objeto da classe style que contém o estilo
	*			a ser aplicado na linha
	*		overwriteStyle - atributo opcional, booleano
	*			que será usado como opção de sobrepor o estilo
	*			das células da linha (caso: true) ou não sobre por
	*			o estilo das células (caso: false <default>)
	*			quando da existência do mesmo.
	*		chessStyle - objeto da classe Style. Só será utilizado
	*			no caso de overwriteStyle for true.
	*			este estilo será aplicado nas células que
	*			já possuem estilo naquela linha. Caso
	*			este estilo não seja passado para a função
	*			e overwriteStyle seja true, todas as células
	*			da linha terão o estilo passado no parâmetro
	*			style.
	*	descrição:
	*		modifica o estilo das células de uma determinada linha.
	*/
	/**
	 * function setRowStyle()<br>
	 * It modifies the style of a row of the table.
	 *
	 * @acces public
	 * @param int $row - number of the row of the table that will have your style modified
	 * @param Style $style - object Style to attributed to the row
	 * @param bool $overwriteStyle - optional parameter that informs to the function
	 * if the style will be applied over another style previously attributed
	 * @param Style $chessStyle - optional parameter, object Style, that if exists
	 * will be the style applied in the cells that have another style previously attributed
	 * in the row
	 */
	public function setRowStyle($row, $style, $overwriteStyle = false, $chessStyle = null)
	{
		/* testa a validade do número da linha a ser modificada */
		if(($row < 0) || ($row >= ($this->rowNumber)))
		{
			/* reportar erro: linha inválida */
			trigger_error("Linha Inválida. Estilo não atribuído!", E_USER_NOTICE);
			return;
		}/* se linha for inválida */

		/* se overwriteStyle estiver ativado */
		if($overwriteStyle)
		{
			/* se chessStyle não existir */
			if($chessStyle==null)
			{
				/* para todas as células da linha solicitada */
				for ($j = 0; $j < $this->columnNumber; $j++)
				{
					/* atribui $style ao estilo de todas as células
					*	da linha $row */
					$this->tableRows[$row]->tableCell[$j]->setStyleOb($style);
				}
			}
			/* se chessStyle existir */
			else
			{
				/* para todas as células da linha selecionada */
				for ($j = 0; $j < $this->columnNumber; $j++)
				{
					/* captura o estilo da celula */
					$cellStyle = $this->tableRows[$row]->tableCell[$j]->getStyle();

					/* verifica a existência de um estilo na célula
					*	se existir */
					if($cellStyle!=null)
					{
						/* aplica o estilo $chessStyle na célula */
						$this->tableRows[$row]->tableCell[$j]->setStyleOb($chessStyle);
					}
					/* caso não exista um estilo na célula */
					else
					{
						/* aplicar o estilo $style na célula */
						$this->tableRows[$row]->tableCell[$j]->setStyleOb($style);
					}
				}
			}
		}
		/* se overwriteStyle estiver desativado */
		else
		{
			/* para todas as células da linha */
			for ($j = 0; $j < $this->columnNumber; $j++)
			{
				/* captura o estilo atribuido à célula */
				$cellStyle = $this->tableRows[$row]->tableCell[$j]->getStyle();
				/* se não existir um estilo atribuido à célula */
				if($cellStyle==null)
				{
					/* aplicar o $style no estilo da célula */
					$this->tableRows[$row]->tableCell[$j]->setStyleOb($style);
				}
			}
		}
	}

	/* função setRowStyle
	*	parâmetros:
	* 		column - número da coluna a ser modificado o estilo
	*		style - objeto da classe style que contém o estilo
	*			a ser aplicado na coluna
	*		overwriteStyle - atributo opcional, booleano
	*			que será usado como opção de sobrepor o estilo
	*			das células da coluna (caso: true) ou não sobrepor
	*			o estilo das células (caso: false <default>)
	*			quando da existência do mesmo.
	*		chessStyle - objeto da classe Style. Só será utilizado
	*			no caso de overwriteStyle for true.
	*			este estilo será aplicado nas células que
	*			já possuem estilo naquela coluna. Caso
	*			este estilo não seja passado para a função
	*			e overwriteStyle seja true, todas as células
	*			da coluna terão o estilo passado no parâmetro
	*			style.
	*	descrição:
	*		modifica o estilo das células de uma determinada coluna.
	*/
	/**
	 * function setColumnStyle()<br>
	 * It modifies the style of a column of the table.
	 *
	 * @acces public
	 * @param int $column - number of the column of the table that will have your style modified
	 * @param Style $style - object Style to attributed to the column
	 * @param bool $overwriteStyle - optional parameter that informs to the function
	 * if the style will be applied over another style previously attributed
	 * @param Style $chessStyle - optional parameter, object Style, that if exists
	 * will be the style applied in the cells that have another style previously attributed
	 * in the column
	 */
	public function setColumnStyle($column, $style, $overwriteStyle = false, $chessStyle = null)
	{
		/* testa a validade do número da coluna a ser modificada */
		if($column < 0 || $column >= $this->columnNumber)
		{
			/* reportar erro: coluna inválida */
			trigger_error("Coluna Inválida. Estilo não atribuído!", E_USER_NOTICE);
			return;
		}/* se coluna for inválida */

		/* se overwriteStyle estiver ativado */
		if($overwriteStyle)
		{
			/* se chessStyle não existir */
			if($chessStyle==null)
			{
				/* para todas as células da coluna solicitada */
				for ($j = 0; $j < $this->rowNumber; $j++)
				{
					/* atribui $style ao estilo de todas as células
					*	da coluna $column */
					$this->tableColumns[$column]->tableCell[$j]->setStyleOb($style);
				}
			}
			/* se chessStyle existir */
			else
			{
				/* para todas as células da coluna selecionada */
				for ($j = 0; $j < $this->rowNumber; $j++)
				{
					/* captura o estilo da célula */
					$cellStyle = $this->tableColumns[$column]->tableCell[$j]->getStyle();

					/* verifica a existência de um estilo na célula
					*	se existir */
					if($cellStyle!=null)
					{
						/* aplica o estilo $chessStyle na célula */
						$this->tableColumns[$column]->tableCell[$j]->setStyleOb($chessStyle);
					}
					/* caso não exista um estilo na célula */
					else
					{
						/* aplicar o estilo $style na célula */
						$this->tableColumns[$column]->tableCell[$j]->setStyleOb($style);
					}
				}
			}
			}
			/* se overwriteStyle estiver desativado */
			else
			{
				/* para todas as células da coluna */
				for ($j = 0; $j < $this->rowNumber; $j++)
				{
					/* captura o estilo atribuido á célula */
					$cellStyle = $this->tableColumns[$column]->tableCell[$j]->getStyle();
					/* se não existir um estilo atribuido á célula */
					if($cellStyle==null)
					{
						/* aplicar o $style no estilo da célula */
						$this->tableColumns[$column]->tableCell[$j]->setStyleOb($style);
					}
				}
			}
		}

		/* função setRowSpan
		*	parâmetros:
		*		row - linha na qual será feito o agrupamento
		*		ini - coluna inicial do agrupamento
		*		fim - coluna final do agrupamento
		*		overwrite - flag booleana que informa se o agrupamento irá sobrepor outros agrupamentos
		*	descrição:
		*		aplica um agrupamento horizontal de células em um intervalo de colunas da tabela
		*/
		/**
		 * function setRowSpan()<br>
		 * It groups a horizontal interval of cells in a row of the table.
		 * It undoes other groups that conflict in the way of the grouping
		 * if the grouping will be with priority.
		 *
		 * @acces public
		 * @param int $row - number of the row of the table where will be made the group
		 * @param int $ini - coordinate initial of the interval of cells that they will be grouped
		 * @param int $fim - coordinate final of the interval of cells that they will be grouped
		 * @param bool $overwrite - it informs if the group has priority on others
		 */
		function setRowSpan($row, $ini, $fim, $overwrite = false)
		{
			/* testa a validade do número da linha a sofrer o span */
			if($row < 0 || $row >= $this->rowNumber)
			{
				/* reportar erro: linha inválida */
				trigger_error("Linha Inválida. Merge não executado!", E_USER_NOTICE);
				return;
			}/* se linha for inválida */

			/* testa validade do intervalo */
			if(($ini >= $fim)||($ini<0)||($fim>=$this->columnNumber))
			{
				/* reportar erro de intervalo */
				trigger_error("Intervalo de células inválidas. Merge não executado!", E_USER_NOTICE);
				return;
			}

			/* verificar se há conflito antes de fazer o merge */

			$conflict = false; // flag de verificação de existência de conflito
			$x = 0;
			$j = $ini;
			while($j <= $fim)
			{
				/* verifica se está em algum agrupamento */
				$spanned = $this->tableRows[$row]->tableCell[$j]->getSpan();
				if($spanned["rowspan"] || $spanned["colspan"])
				{
					/* aciona a flag de conflito */
					if(!$conflict)
						$conflict = true;

					$confs[$x]["rowNum"]=-1;
					$confs[$x]["colNum"]=-1;

					/* guarda o agrupamento encontrado */
					// é um rowspan?
					if($spanned["rowspan"])
					{
						// em qual linha?
						$confs[$x]["rowNum"]=$row;
						$j += (($spanned["rowFim"]) - ($spanned["rowIni"]));
					}
					// é um colspan?
					if($spanned["colspan"])
					{
						// em qual coluna?
						$confs[$x]["colNum"]=$j;
					}
					/* guardar as propriedades */
					foreach($spanned as $p => $v)
					{
						$confs[$x][$p]=$v;
					}
					$x++;
				}
				$j++;
			}

			if(!$conflict)
			{
				/* executar o merge nas células da linha */

				/* prepara a célula mãe do merge */
				$motherCell = $this->tableRows[$row]->tableCell[$ini];
				$motherCell->setSpan("rowspan", $ini, $fim);
				/* para todas as células do intervalo na linha */
				for($j=($ini+1); $j<=$fim; $j++)
				{
					/* obtém o valor da célula a ser agrupada */
					$value = $this->tableRows[$row]->tableCell[$j]->getCellValue();
					/* concatena o valor obtido com o valor da célula mãe */
					if($value!= '&nbsp;')
						$motherCell->valueCat($value);
					/* posição $j da linha aponta para a célula mãe também */
					$this->tableRows[$row]->tableCell[$j]=$motherCell;
					/* célula pertencente a linha $row da coluna $j aponta para a célula mãe também */
					$this->tableColumns[$j]->tableCell[$row]=$motherCell;
				}
			}
			else
			{
				/* verifica se os conflitos existentes são de agrupamentos contrários, vizinhos e de mesmas propriedades */

				/* contador auxiliar que contém o número de spans de conflito possíveis para agrupamento
				*	caso ele não seja igual ao número de conflitos o merge deverá fazer o split dos agrupamentos
				*	antes de fazê-lo
				*/
				$countColspan = 0;

				$iterCount = 0; /* variável auxiliar. contador de iterações. Será útil para saber se algum
				*	conflito não foi contado e sair da iteração */

				/* para todo conflito existente */
				foreach($confs as $index => $properties)
				{
					/* verifica se ele não é do tipo colspan */
					if(!$properties["colspan"])
						break; /* pára a testagem */

					if($iterCount == 0)
					{
						/* pega o intervalo que deve ser padrão nos agrupamentos para que sejam
						*	válidos para agrupamentos de agrupamentos */
						$interval["ini"]=$properties["columnIni"];
						$interval["fim"]=$properties["columnFim"];
						/* incrementa o contador de iterações e o contador de agrupamentos aprovados, pois
						*	o primeiro agrupamento já é pré-aprovado e serve como padrão de validade dos
						*	outros */
						$iterCount++;

					}/* se for a primeira iteração */
					else
					{
						if($iterCount == 1)
							$countColspan++;
						/* se contador de iterações for diferente do número de agrupamentos aprovados, sair da iteração
						*	pois algum agrupamento foi reprovado */
						if($iterCount != $countColspan)
							break;

						/* incrementa o contador de agrupamentos aprovados para um novo agrupamento se ele for vizinho e
						*	estiver dentro do mesmo intervalo do primeiro agrupamento de conflito */
						$countColspan += ($properties["columnIni"]==$interval["ini"] && $properties["columnFim"]==$interval["fim"]) ? 1 : 0;
						/* incrementa o contador de iterações */
						$iterCount++;
					}/* se não for a primeira iteração */
				}

				if($overwrite)
				{
					/* se for um agrupamento de agrupamentos */
					if($countColspan == (count($confs)))
					{
						/* prepara a célula mãe do agrupamento */
						$auxLin1 = $confs[0]["columnIni"];
						$auxLin2 = $confs[0]["columnFim"];
						$motherCell = $this->tableRows[$auxLin1]->tableCell[$ini];
						$motherCell->setSpan("rowspan", $ini, $fim);

						for($auxrow = $auxLin1+1; $auxrow <= $auxLin2; $auxrow++)
						{
							$this->tableColumns[$ini]->tableCell[$auxrow] = $motherCell;
							$this->tableRows[$auxrow]->tableCell[$ini] = $motherCell;
						}

						/*para toda coluna com colspan a ser agrupado */
						for($coluna = $ini+1; $coluna <= $fim; $coluna++)
						{
							/* concatena o valor do colspan ao novo agrupamento */
							$valueToCat = $this->tableColumns[$coluna]->tableCell[$row]->getCellValue();
							if($valueToCat!= '&nbsp;')
								$motherCell->valueCat($valueToCat);

							/* para todas as células do agrupamento */
							for($linha=($auxLin1); $linha<=($auxLin2); $linha++)
							{
								/* aponta para a nova célula mãe do novo agrupamento */
								$this->tableColumns[$coluna]->tableCell[$linha] = $motherCell;
								$this->tableRows[$linha]->tableCell[$coluna] = $motherCell;
							}
						}
					}
					else
					{ /* se não for um agrupamento de agrupamentos */
						/* desfaz os agrupamentos em conflito */

						/* para todos os conflitos existentes */
						foreach($confs as $index => $props)
						{
							$doubleGroup = false;
							if($props["colspan"])
							{ /* se o conflito for na vertical */
								/* desagrupar células do agrupamento vertical */
								$this->splitCells($props["columnIni"], $props["colNum"], "colspan");
								$doubleGroup = true;
							}
							if($props["rowspan"])
							{ /* se for um agrupamento horizontal */
								if($doubleGroup)
								{ /* se é um agrupamento de agrupamentos */
									/* para todas as linhas que restaram do desagrupamento anterior */
									for($row = $props["columnIni"]; $row<=$props["columnFim"]; $row++)
									{
										/* fazer um desagrupamento horizontal na linha */
										$this->splitCells($row, $props["rowIni"], "rowspan");
									}
								}
								else
								{ /* se não é um agrupamento de agrupamentos */
									/* desfazer o agrupamento horizontal */
									$this->splitCells($props["rowNum"], $props["rowIni"], "rowspan");
								}
							}

						}

						/* executar o merge nas células da linha */

						/* prepara a célula mãe do merge */
						$motherCell = $this->tableRows[$row]->tableCell[$ini];
						$motherCell->setSpan("rowspan", $ini, $fim);

						/* para todas as células do intervalo na linha */
						for($j=($ini+1); $j<=$fim; $j++)
						{
							/* obtém o valor da célula a ser agrupada */
							$value = $this->tableRows[$row]->tableCell[$j]->getCellValue();
							/* concatena o valor obtido com o valor da célula mãe */
							if($value!= '&nbsp;')
								$motherCell->valueCat($value);
							/* posição $j da linha aponta para a célula mãe também */
							$this->tableRows[$row]->tableCell[$j]=$motherCell;
							/* célula pertencente a linha $row da coluna $j aponta para a célula mãe também */
							$this->tableColumns[$j]->tableCell[$row]=$motherCell;
						}
					}
				}/* se for overwritable */
				else
				{
					/* reportar erro: conflito de agrupamentos */
					trigger_error("Conflito de agrupamentos. Agrupamento não efetuado!", E_USER_NOTICE);
					return;
				}/* se não for overwritable */
			}
		}

		/* função setColSpan
		*	parâmetros:
		*		col - coluna na qual será feito o agrupamento
		*		ini - linha inicial do agrupamento
		*		fim - linha final do agrupamento
		*		overwrite - flag booleana que informa se o agrupamento irá sobrepor outros agrupamentos
		*	descrição:
		*		aplica um agrupamento vertical de células em um intervalo de linhas da tabela
		*/
		/**
		 * function setColSpan()<br>
		 * It groups a vertical interval of cells in a column of the table.
		 * It undoes other groups that conflict in the way of the grouping
		 * if the grouping will be with priority.
		 *
		 * @acces public
		 * @param int $col - number of the column of the table where will be made the group
		 * @param int $ini - coordinate initial of the interval of cells that they will be grouped
		 * @param int $fim - coordinate final of the interval of cells that they will be grouped
		 * @param bool $overwrite - it informs if the group has priority on others
		 */
		function setColSpan($col, $ini, $fim, $overwrite = false)
		{
			/* testa a validade do número da coluna a sofrer o span */
			if($col < 0 || $col >= $this->columnNumber)
			{
				/* reportar erro: coluna inválida */
				trigger_error("Coluna Inválida. Merge não executado!", E_USER_NOTICE);
				return;
			}/* se coluna for inválida */

			/* testa validade do intervalo */
			if(($ini >= $fim)||($ini < 0)||($fim >= $this->rowNumber))
			{
				/* reportar erro de intervalo */
				trigger_error("Intervalo de células inválidas. Merge não executado!", E_USER_NOTICE);
				return;
			}

			/* verificar se há conflito antes de fazer o merge */

			$conflict = false; // flag de verificação de existência de conflito
			$x = 0;
			$j = $ini;
			while($j <= $fim)
			{
				/* verifica se está em algum agrupamento */
				$spanned = $this->tableColumns[$col]->tableCell[$j]->getSpan();
				if($spanned["rowspan"] || $spanned["colspan"])
				{
					/* aciona a flag de conflito */
					if(!$conflict)
						$conflict = true;

					$confs[$x]["rowNum"]=-1;
					$confs[$x]["colNum"]=-1;

					/* guarda o agrupamento encontrado */
					// é um rowspan?
					if($spanned["rowspan"])
					{
						// em qual linha?
						$confs[$x]["rowNum"]=$j;

					}
					// é um colspan?
					if($spanned["colspan"])
					{
						// em qual coluna?
						$confs[$x]["colNum"]=$col;
						$j += (($spanned["columnFim"]) - ($spanned["columnIni"]));
					}
					/* guardar as propriedades */
					foreach($spanned as $p => $v)
					{
						$confs[$x][$p]=$v;
					}
					$x++;
				}
				$j++;
			}

			if(!$conflict)
			{
				/* executar o merge nas células da coluna */

				/* prepara a célula mãe do merge */
				$motherCell = $this->tableColumns[$col]->tableCell[$ini];
				$motherCell->setSpan("colspan", $ini, $fim);
				/* para todas as células do intervalo na coluna */
				for($j=($ini+1); $j<=$fim; $j++)
				{
					/* obtém o valor da célula a ser agrupada */
					$value = $this->tableColumns[$col]->tableCell[$j]->getCellValue();
					/* concatena o valor obtido com o valor da célula mãe */
					if($value!= '&nbsp;')
						$motherCell->valueCat($value);
					/* posição $j da coluna aponta para a célula mãe também */
					$this->tableColumns[$col]->tableCell[$j]=$motherCell;
					/* célula pertencente a coluna $col da linha $j aponta para a célula mãe também */
					$this->tableRows[$j]->tableCell[$col]=$motherCell;
				}
			}
			else
			{
				/* verifica se os conflitos existentes são de agrupamentos contrários, vizinhos e de mesmas propriedades */

				/* contador auxiliar que contém o número de spans de conflito possíveis para agrupamento
				*	caso ele não seja igual ao número de conflitos o merge deverá fazer o split dos agrupamentos
				*	antes de fazê-lo
				*/
				$countRowspan = 0;

				$iterCount = 0; /* variável auxiliar. contador de iterações. Será útil para saber se algum
				*	conflito não foi contado e sair da iteração */

				/* para todo conflito existente */
				foreach($confs as $index => $properties)
				{
					/* verifica se ele não é do tipo rowspan */
					if(!$properties["rowspan"])
						break; /* pára a testagem */

					if($iterCount == 0)
					{
						/* pega o intervalo que deve ser padrão nos agrupamentos para que sejam
						*	válidos para agrupamentos de agrupamentos */
						$interval["ini"]=$properties["rowIni"];
						$interval["fim"]=$properties["rowFim"];

						/* incrementa o contador de iterações e o contador de agrupamentos aprovados, pois
						*	o primeiro agrupamento já é pré-aprovado e serve como padrão de validade dos
						*	outros */
						$iterCount++;

					}/* se for a primeira iteração */
					else
					{
						if($iterCount == 1)
							$countRowspan++;
						/* se contador de iterações for diferente do número de agrupamentos aprovados, sair da iteração
						*	pois algum agrupamento foi reprovado */
						if($iterCount != $countRowspan)
							break;

						/* incrementa o contador de agrupamentos aprovados para um novo agrupamento se ele for vizinho e
						*	estiver dentro do mesmo intervalo do primeiro agrupamento de conflito */
						$countRowspan += ($properties["rowIni"]==$interval["ini"] && $properties["rowFim"]==$interval["fim"]) ? 1 : 0;
						/* incrementa o contador de iterações */
						$iterCount++;
					}/* se não for a primeira iteração */
				}

				if($overwrite)
				{

					/* se for um agrupamento de agrupamentos */
					if($countRowspan == (count($confs)))
					{
						/* prepara a célula mãe do agrupamento */
						$auxCol1 = $confs[0]["rowIni"];
						$auxCol2 = $confs[0]["rowFim"];
						$motherCell = $this->tableColumns[$auxCol1]->tableCell[$ini];
						$motherCell->setSpan("colspan", $ini, $fim);

						for($auxColumn = $auxCol1+1; $auxColumn <= $auxCol2; $auxColumn++)
						{
							$this->tableRows[$ini]->tableCell[$auxColumn] = $motherCell;
							$this->tableColumns[$auxColumn]->tableCell[$ini] = $motherCell;
						}

						/*para toda linha com rowspan a ser agrupado */
						for($row = $ini+1; $row <= $fim; $row++)
						{
							/* concatena o valor do colspan ao novo agrupamento */
							$valueToCat = $this->tableRows[$row]->tableCell[$col]->getCellValue();
							if($valueToCat!= '&nbsp;')
								$motherCell->valueCat($valueToCat);

							/* para todas as células do agrupamento */
							for($coluna=($auxCol1); $coluna<=($auxCol2); $coluna++)
							{
								/* aponta para a nova célula mãe do novo agrupamento */
								$this->tableRows[$row]->tableCell[$coluna] = $motherCell;
								$this->tableColumns[$coluna]->tableCell[$row] = $motherCell;
							}
						}
					}
					else
					{ /* se não for um agrupamento de agrupamentos */
						/* desfaz os agrupamentos em conflito */

						/* para todos os conflitos existentes */
						foreach($confs as $index => $props)
						{
							$doubleGroup = false;
							if($props["colspan"])
							{ /* se o conflito for na vertical */
								/* desagrupar células do agrupamento vertical */
								$this->splitCells($props["columnIni"], $props["colNum"], "colspan");
								$doubleGroup = true;
							}
							if($props["rowspan"])
							{ /* se for um agrupamento horizontal */
								if($doubleGroup)
								{ /* se é um agrupamento de agrupamentos */
									/* para todas as linhas que restaram do desagrupamento anterior */
									for($row = $props["columnIni"]; $row<=$props["columnFim"]; $row++)
									{
										/* fazer um desagrupamento horizontal na linha */
										$this->splitCells($row, $props["rowIni"], "rowspan");
									}
								}
								else
								{ /* se não é um agrupamento de agrupamentos */
									/* desfazer o agrupamento horizontal */
									$this->splitCells($props["rowNum"], $props["rowIni"], "rowspan");
								}
							}

						}

						/* executar o merge nas células da linha */

						/* prepara a célula mãe do merge */
						$motherCell = $this->tableColumns[$col]->tableCell[$ini];
						$motherCell->setSpan("colspan", $ini, $fim);

						/* para todas as células do intervalo na linha */
						for($j=($ini+1); $j<=$fim; $j++)
						{
							/* obtém o valor da célula a ser agrupada */
							$value = $this->tableColumns[$col]->tableCell[$j]->getCellValue();
							/* concatena o valor obtido com o valor da célula mãe */
							if($value!= '&nbsp;')
								$motherCell->valueCat($value);
							/* posição $j da coluna aponta para a célula mãe também */
							$this->tableColumns[$col]->tableCell[$j]=$motherCell;
							/* célula pertencente a coluna $col da linha $j aponta para a célula mãe também */
							$this->tableRows[$j]->tableCell[$col]=$motherCell;
						}
					}
				}/* se for overwritable */
				else
				{
					/* reportar erro: conflito de agrupamentos */
					trigger_error("Conflito de agrupamentos. Agrupamento não efetuado!", E_USER_NOTICE);
					return;
				}/* se não for overwritable */
			}
		}

		/* função splitCells
		*	parâmetros:
		*		row - linha da coordenada da célula que participa do agrupamento de será desfeito
		*		col - coluna da coordenada da célula que participa do agrupamento de será desfeito
		*		type - tipo de agrupamento que será desfeito
		*	descrição:
		*		recebe as coordenadas de uma célula da tabela e desfaz o agrupamento ao qual ela
		*		pertence, caso pertença a algum agrupamento e que seja do tipo para o qual foi solicitado
		*		o desagrupamento
		*/
		/**
		 * function splitCells()<br>
		 * It undoes a group of cells.
		 *
		 * @acces public
		 * @param int $row - number of the row whose cell participates
		 * of the grouping that will be undoes
		 * @param int $col - number of the column whose cell participates
		 * of the grouping that will be undoes
		 * @param string $type - type of group that will be undoes
		 */
		function splitCells($row, $col, $type)
		{
			/* verifica validade da célula */
			if($row<0 || $row>=$this->rowNumber || $col<0 || $col>=$this->columnNumber)
			{
				/* reportar erro: célula inválida */
				trigger_error("Coordenada (" . $row . ", " . $col . ") Inválida. célula não existe!", E_USER_NOTICE);
				return;
			}/* se coordenadas não estiver contida dentro dos limites da tabela */

			/* captura as propriedades de span da célula */
			$span = $this->tableRows[$row]->tableCell[$col]->getSpan();

			if($type!='rowspan' && $type!='colspan')
			{
				/* reportar erro: tipo de agrupamento inválido */
				trigger_error("Tipo (\"" . $type . "\") de agrupamento inválido. $type = [ rowspan | colspan ]", E_USER_NOTICE);
				return;
			}

			/* verifica se a célula participa de um agrupamento do tipo $type */
			if($span[$type]!=true)
			{
				/* reportar erro: tipo de span não aplicado na célula */
				trigger_error("Conflito de informações: A célula não participa de um agrupamento do tipo " . $type . "!", E_USER_NOTICE);
				return;
			}/* se a propriedade rowspan não estiver ativada */

			/* guarda o intervalo do span em variáveis */
			switch($type)
			{
				case 'rowspan':
					$ini = $span["rowIni"];
					$fim = $span["rowFim"];
					break;
				case 'colspan':
					$ini = $span["columnIni"];
					$fim = $span["columnFim"];
					break;
			}

			switch($type)
			{
				case 'rowspan': /* caso split rowspan */
					if($span["colspan"])
					{/* se a célula participar de um agrupamento de agrupamentos */

						/* captura a primeira coordenada do agrupamento */
						$coordIni["col"] = $span["rowIni"];
						$coordIni["lin"] = $span["columnIni"];

						/* captura o estilo e o valor do antigo agrupamento para mantê-lo após os ajustes */
						$styleCell = $this->tableColumns[($coordIni["col"])]->tableCell[($coordIni["lin"])]->getStyle();
						$valueCell = $this->tableColumns[($coordIni["col"])]->tableCell[($coordIni["lin"])]->getCellValue();

						/* fazer o split na primeira célula do agrupamento */
						$this->tableColumns[($coordIni["col"])]->tableCell[($coordIni["lin"])]->splitCell("rowspan");

						for($inrow = ($coordIni["lin"]+1); $inrow <= ($span["columnFim"]); $inrow++)
						{
							$this->tableRows[$inrow]->tableCell[($coordIni["col"])] = $this->tableColumns[($coordIni["col"])]->tableCell[($coordIni["lin"])];
							$this->tableColumns[($coordIni["col"])]->tableCell[$inrow] = $this->tableColumns[($coordIni["col"])]->tableCell[($coordIni["lin"])];
						}

						/* ajustar as colunas para que tenham somente seu agrupamento colspan e apontem para suas respectivas
						*	células-mãe correspondentes
						*/
						for($y=($coordIni["col"]+1); $y<=$span["rowFim"]; $y++)
						{
							/* cria a nova célula mãe do agrupamento */

							$this->tableColumns[$y]->tableCell[($coordIni["lin"])] = new TableCell("&nbsp;");
							$motherCell = $this->tableColumns[$y]->tableCell[($coordIni["lin"])];
							$this->tableRows[($coordIni["lin"])]->tableCell[$y] = $motherCell;
							/* atribui o agrupamento correto à célula mãe */
							$motherCell->setSpan("colspan", $span["columnIni"], $span["columnFim"]);
							/* atribui o estilo correto à célula mãe */
							$motherCell->setStyleOb($styleCell);

							/* para as outras células do agrupamento */
							for($j=(($coordIni["lin"])+1); $j<=($span["columnFim"]); $j++)
							{
								/* direciona o ponteiro da célula agrupada para a célula mãe */
								$this->tableColumns[$y]->tableCell[$j] = $motherCell;
								$this->tableRows[$j]->tableCell[$y] = $motherCell;
							}
						}
					}
					else
					{/* caso para split sem agrupamento de agrupamentos */
						/* captura o estilo da célula agrupada */
						$styleMerge = $this->tableRows[$row]->tableCell[$ini]->getStyle();

						/* trata a primeira célula do intervalo separadamente, pois será a célula que conterá o antigo conteúdo da célula agrupada */
						$this->tableRows[$row]->tableCell[$ini]->splitCell($type);

						/* para todas as outras células do agrupamento */
						for($y=($ini+1); $y<=$fim; $y++)
						{
							$this->tableRows[$row]->tableCell[$y] = new TableCell ("&nbsp;") ;
							$this->tableRows[$row]->tableCell[$y]->setStyleOb($styleMerge);
							$this->tableColumns[$y]->tableCell[$row] = $this->tableRows[$row]->tableCell[$y];
						}
					}
					break;
				case 'colspan':
					if($span["rowspan"])
					{/* se a célula participar de um agrupamento de agrupamentos */

						/* captura a primeira coordenada do agrupamento */
						$coordIni["lin"] = $span["columnIni"];
						$coordIni["col"] = $span["rowIni"];

						/* captura o estilo do antigo agrupamento para mantê-lo após os ajustes */
						$styleCell = $this->tableRows[($coordIni["lin"])]->tableCell[($coordIni["col"])]->getStyle();

						/* fazer o split na primeira célula do agrupamento */
						$this->tableRows[($coordIni["lin"])]->tableCell[($coordIni["col"])]->splitCell("colspan");

						/* ajustar as linhas para que tenham somente seu agrupamento rowspan e apontem para suas respectivas
						*	células-mãe correspondentes
						*/
						for($y=($coordIni["lin"]+1); $y<=$span["columnFim"]; $y++)
						{
							/* cria a nova célula mãe do agrupamento */

							$this->tableRows[$y]->tableCell[($coordIni["col"])] = new TableCell("&nbsp;");
							$motherCell = $this->tableRows[$y]->tableCell[($coordIni["col"])];
							$this->tableColumns[($coordIni["col"])]->tableCell[$y] = $motherCell;
							/* atribui o agrupamento correto à célula mãe */
							$motherCell->setSpan("rowspan", $span["rowIni"], $span["rowFim"]);
							/* atribui o estilo correto à célula mãe */
							$motherCell->setStyleOb($styleCell);

							/* para as outras células do agrupamento */
							for($j=(($coordIni["col"])+1); $j<=($span["rowFim"]); $j++)
							{
								/* direciona o ponteiro da célula agrupada para a célula mãe */
								$this->tableRows[$y]->tableCell[$j] = $motherCell;
								$this->tableColumns[$j]->tableCell[$y] = $motherCell;
							}
						}
					}
					else
					{/* caso para split sem agrupamento de agrupamentos */
						/* captura o estilo da célula agrupada */
						$styleMerge = $this->tableColumns[$col]->tableCell[$ini]->getStyle();

						/* trata a primeira célula do intervalo separadamente, pois será a célula que conterá o antigo conteúdo da célula agrupada */
						$this->tableColumns[$col]->tableCell[$ini]->splitCell($type);

						/* para todas as outras células do agrupamento */
						for($y=($ini+1); $y<=$fim; $y++)
						{
							$this->tableColumns[$col]->tableCell[$y] = new TableCell ("&nbsp;") ;
							$this->tableColumns[$col]->tableCell[$y]->setStyleOb($styleMerge);
							$this->tableRows[$y]->tableCell[$col] = $this->tableColumns[$col]->tableCell[$y];
						}
					}
					break;
			}
		}

		/**
		 * function hideColumn()<br>
		 * It hidden a specific column of the table.
		 *
		 * @acces public
		 * @param int $index - number of the column that will be hidden
		 */
		function hideColumn($index)
		{
			if($index < 0 || $index >= $this->columnNumber)
			{
				// reportar erro: número de coluna inválido
				trigger_error("Número da coluna inválido!", E_USER_NOTICE);
			}
			else
			{
				$this->tableColumns[$index]->setVisible(false);
			}
		}

		/**
		 * function hideRow()<br>
		 * It hidden a specific row of the table.
		 *
		 * @acces public
		 * @param int $index - number of the row that will be hidden
		 */
		function hideRow($index)
		{
			if($index < 0 || $index >= $this->rowNumber)
			{
				// reportar erro: número de linha inválido
				trigger_error("Número da linha inválido!", E_USER_NOTICE);
			}
			else
			{
				$this->tableRows[$index]->setVisible(false);
			}
		}

		/**
		 * function showColumn()<br>
		 * It shown a specific column of the table.
		 *
		 * @acces public
		 * @param int $index - number of the column that will be shown
		 */
		function showColumn($index)
		{
			if($index < 0 || $index >= $this->columnNumber)
			{
				// reportar erro: número de coluna inválido
				trigger_error("Número da coluna inválido!", E_USER_NOTICE);
			}
			else
			{
				$this->tableColumns[$index]->setVisible(true);
			}
		}

		/**
		 * function showRow()<br>
		 * It shown a specific row of the table.
		 *
		 * @acces public
		 * @param int $index - number of the row that will be shown
		 */
		function showRow($index)
		{
			if($index < 0 || $index >= $this->rowNumber)
			{
				// reportar erro: número de linha inválido
				trigger_error("Número da linha inválido!", E_USER_NOTICE);
			}
			else
			{
				$this->tableRows[$index]->setVisible(true);
			}
		}
	}