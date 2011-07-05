<?php
/**
 * Arquivo DataTable.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.data
 */

/**
 * Import
 */
import( 'system.web.ui.table.*' );

/**
 * Classe DataTable
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.data
 */
class DataTable extends Table
{

	/**
	 * function __construct()<br>
	 * construtor method of class DataTable.
	 *
	 * @param $matrix - matrix of data that will be shown
	 */
	function __construct( $matrix, $style1 = null, $style2 = null  )
	{
		parent::__construct();
		$this->hideCols = array();
		$cols = array();
		$index = 0;

		/*foreach ( $matrix[0] as $value )
		{
			$cols[$index] = $value;
			++$index;
		}*/

		$this->addRow( $matrix[0] );

		/* estilo do cabeçalho do datagrid */
		$style = new Style();
		$style->set( "background-color", "#FFFFFF" );
		$style->set( "font-weight", "bold" );
		$style->set( "font-family", "verdana" );
		$style->set( "font-size", "11px" );
		$style->set( "color", "#000000" );
		$style->set( 'border-bottom','1px solid #DDDDDD');
		/* fim do estilo do cabeçalho do datagrid */

		$this->setRowStyle(0, $style, true);


		if( $style1 == null )
		{
			$style1 = new Style();
			$style1->set("background-color", "#DDDDDD");
			$style1->set("text-align", "center");
			$style1->set("font-family", "Verdana");
			$style1->set("font-size", "11px");
			$style1->set("color", "#000000");
		}

		if( $style2 == null)
		{
			$style2 = new Style();
			$style2->set( "background-color", "#EEEEEE" );
			$style2->set( "text-align", "center" );
			$style2->set( "font-family", "Verdana" );
			$style2->set( "font-size", "11px" );
			$style2->set( "color", "#000000" );
		}

		$estilo = 1;

		for( $lins = 1; $lins < count( $matrix ); $lins++ )
		{
			$linha = array();

			/*$index=0;
			foreach ( $matrix[ $lins ] as $value )
			{
				$linha[ $index ] = $value;
				$index++;
			}*/

			$this->addRow( $matrix[ $lins ] );

			switch($estilo)
			{
				case 1:
					$this->setRowStyle( $lins, $style1, true );
					$estilo++;
					break;
				case 2:
					$this->setRowStyle($lins, $style2, true);
					$estilo--;
					break;
			}

		}

		$this->set("cellpadding", "5");
		$this->set("cellspacing", "0");
		$this->set("border", "1");
	}

	/**
	 * function existTitle()<br>
	 * It informs if it exists a column with one specific title.
	 * It returns a index of the column in this table.
	 *
	 * @acces private
	 * @param $title - title will be search in the DataTable
	 * @return $index - number of the column in the DataTable
	 */
	private function existTitle($title)
	{
		$index = -1;
		for($col = 0; $col < ($this->getColumnNumber()); $col++)
		{
			$columnTitle = $this->tableColumns[$col]->tableCell[0]->getCellValue();
			if($columnTitle == $title)
			{
				$index = $col;
				break;
			}
		}
		return $index;
	}

	/**
	 * function sortByColumn()<br>
	 * It classifies the lines of the table, based on a column,
	 * ascending or descending.
	 *
	 * @acces public
	 * @param $coluna - column title that will serve of base for
	 * the classification
	 * @return $index - index of the column in the DataTable
	 */
	function sortByColumn($coluna, $ordem = 'asc')
	{
		$col = $this->existTitle($coluna);
		if($col==-1)
		{
			trigger_error("Título de coluna inválido! Ordenação não pode ser executada", E_USER_NOTICE);
			return false;
		}
		$troca = false;
		for($ult = (($this->getRowNumber())-1); $ult > 1; $ult--)
	    {
			for($visitLine = 1; $visitLine < $ult; $visitLine++)
			{
				$cell1 = ($this->tableRows[$visitLine]->tableCell[$col]->getCellValue());
				$cell2 = ($this->tableRows[$ult]->tableCell[$col]->getCellValue());
				switch(strtoupper($ordem))
				{
					case 'ASC':
						$compare = strcmp($cell1, $cell2);

						if($compare > 0)
						{

							$aux = ($this->tableRows[$visitLine]);
							$this->tableRows[$visitLine] = ($this->tableRows[$ult]);
							$this->tableRows[$ult] = $aux;

							$auxStyle = ($this->tableRows[$visitLine]->tableCell[0]->getStyle());
							$this->setRowStyle($visitLine, $this->tableRows[$ult]->tableCell[0]->getStyle(), true);
							$this->setRowStyle($ult, $auxStyle, true);

							for($visitCol = 0; $visitCol < ($this->getColumnNumber()); $visitCol++)
							{
								$aux = ($this->tableColumns[$visitCol]->tableCell[$visitLine]);
								$this->tableColumns[$visitCol]->tableCell[$visitLine] = ($this->tableColumns[$visitCol]->tableCell[$ult]);
								$this->tableColumns[$visitCol]->tableCell[$ult] = $aux;
							}

							$trocou = true;
						}
						break;
					case 'DESC':
						$compare = strcmp($cell1, $cell2);

						if($compare<0)
						{
							$aux = ($this->tableRows[$visitLine]);
							$this->tableRows[$visitLine] = ($this->tableRows[$ult]);
							$this->tableRows[$ult] = $aux;

							$auxStyle = ($this->tableRows[$visitLine]->tableCell[0]->getStyle());
							$this->setRowStyle($visitLine, $this->tableRows[$ult]->tableCell[0]->getStyle(), true);
							$this->setRowStyle($ult, $auxStyle, true);

							for($visitCol = 0; $visitCol < ($this->getColumnNumber()); $visitCol++)
							{
								$aux = ($this->tableColumns[$visitCol]->tableCell[$visitLine]);
								$this->tableColumns[$visitCol]->tableCell[$visitLine] = ($this->tableColumns[$visitCol]->tableCell[$ult]);
								$this->tableColumns[$visitCol]->tableCell[$ult] = $aux;
							}

							$trocou = true;
						}
						break;
					default:
						trigger_error("Tipo de ordenação inexistente! As opções possíveis são: [ asc | desc ]", E_USER_NOTICE);
						return false;
				}
			}
		}
		if(!$troca)
			return true;

		return false;
	}

	/**
	 * function showColumn()<br>
	 * It alternate the propertie visible of the column
	 * for true.
	 *
	 * @acces public
	 * @param $ref - column title or column index in the DataTable
	 */
	function showColumn($ref)
	{
		if(!is_int($ref) && !is_string($ref))
		{
			trigger_error("Referência inválida para coluna! Visualização não modificada!", E_USER_NOTICE);
			return false;
		}
		$index = -1;
		if( is_string( $ref ) )
		{
			$index = $this->existTitle($ref);
			if($index==-1)
			{
				trigger_error("Título de coluna inválido! Visualização não modificada!", E_USER_NOTICE);
				return false;
			}
		}

		elseif($ref<0 || $ref>=($this->getColumnNumber()))
		{
			trigger_error("Número da coluna é inválido! Visualização não modificada!", E_USER_NOTICE);
			return false;
		}

		if($index!=-1)
		{
			$this->tableColumns[$index]->setVisible( true );
		}
		else
		{
			$this->tableColumns[$ref]->setVisible( true );
		}
		return true;
	}

	/**
	 * function hideColumn()<br>
	 * It alternate the propertie visible of the column
	 * for false.
	 *
	 * @acces public
	 * @param $ref - column title or column index in the DataTable
	 */
	function hideColumn($ref)
	{
		if(!is_int($ref) && !is_string($ref))
		{
			trigger_error("Referência inválida para coluna! Visualização não modificada!", E_USER_NOTICE);
			return false;
		}
		$index = -1;
		if(is_string($ref))
		{
			$index = $this->existTitle($ref);

			if($index==-1)
			{
				trigger_error("Título de coluna inválido! Visualização não modificada!", E_USER_NOTICE);
				return false;
			}
		}
		elseif($ref<0 || $ref>=($this->getColumnNumber()))
		{
			trigger_error("Número da coluna é inválido! Visualização não modificada!", E_USER_NOTICE);
			return false;
		}

		if($index!=-1)
		{
			$this->tableColumns[$index]->setVisible(false);
		}
		else
		{
			$this->tableColumns[$ref]->setVisible(false);
		}
		return true;
	}

	/**
	 * function getXhtml()<br>
	 * It returns an array with the html code of the object DataTable.
	 *
	 * @acces public
	 * @return $html - array with the html code of the table
	 */
	public function getXhtml()
	{
		$html = "";

		/* inicia a tag de abertura <table> */
		$html .= '<table ';
		/*
		if (! empty($this->class))
		{
			$html.= ' class="'.$this->class.'" ';
		}
		*/
		/* captura o código do estilo */
		$style = $this->style->getXHTML();

		if (! empty($style))
		{
			$html .= ' style="'. $style .'" ';
		}

		/* feha a tag de abertura <table> */
		$html .= ">";			

		/* monta a tabela adicionando suas células */
		for ($i = 0; $i < $this->getRowNumber(); $i++)
		{/* para todas as linhas da tabela */
			/* inicia a linha da tabela */
			$html .= "\n\t<tr>";

			for ($j = 0; $j < $this->getColumnNumber(); $j++)
			{/* para todas as colunas da tabela */
				/* é preciso tratar as células que são agrupadas */

				$cellSpan = $this->tableRows[$i]->tableCell[$j]->getSpan();
				if($cellSpan["rowspan"]||$cellSpan["colspan"])
				{
					trigger_error("Tabela de dados inválida! [Datagrid não pode conter agrupamento de células]", E_USER_ERROR);
					return '';
				}
				if( $this->tableColumns[$j]->isVisible( ) )
				{
					$html .= $this->tableColumns[$j]->tableCell[$i]->getXhtml();
				}
			}

			/* fecha a tag <tr> */
			$html .= "\n\t</tr>";
		}

		/* fecha a tag table */
		$html .= "\n</table>";

		/* retorna o código da tabela */
		return $html;
	}
}