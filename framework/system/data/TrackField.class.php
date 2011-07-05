<?php
/**
 * Arquivo TrackField.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */

/*Obs: para entender o funcionamento desta classe, descomente
os testes que existem no final deste arquivo.
*/

/**
 * Classe TrackField
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */
class TrackField
{
	private $SQL_PREDEF	= array( 'distinct', 'limit','order');
	private $SQL_FUNCS	= array( 'avg', 'count', 'first', 'last', 'max','min','stdev','stdevp','sum','var','varp','ucase','lcase','mid','len','instr','left','right','round','mod','now','format','datediff');
	private $SQL_XCHAR	= array( ',', '(', ')', '+', '-', '*', '/', '%', '^', '!', '=', '>', '<', ' ', '[', ']');

	/**
	 * Properties
	 */
	 /**#@+
	 * @access private
	 * @var string
	 **/
	private $realName;			//string: real DbName of field
	private $alias;				//string: alias if field have a one
	private $tableName;			//string: tableName if field is not a calculate column
	private $tableAlias;
	//private $functionsName;		//will possess the name of all the functions in future versions
	private $argsFunction; 		//string: arguments of all function field
	private $text;				//string: text field not formated
	/**#@- */

	/**#@+
	 * @access private
	 * @var boolean
	 */
	private $distinct = false;	//boolean: true if field is set distinct consult
	private $all = false; 		//boolean: true if field is set all consult
	private $calculate = false;	//boolean: true if field is a calculate column
	private $constant = false;	//boolean: true if field is a constant column
	/**#@- */

	/**
	 * @access private
	 * @var array
	 */
	private $pieces;	 		//array: definitions of pieces column

	/**
	 * Function __construct<br>
	 * This is the constructor method of this class.
	 *
	 * @param string $textField
	 */
	function __construct($textField = null)
	{
		if ($textField != null)
		{
			$this->setTextField($textField);
		}
	}

	/**
	 * Function definePieces()<br>
	 * This function generates a array of command text.
	 * All the text of the field is broken and each word is inserted in this array.
	 *
	 * @access private
	 * @return void
	 */
	private function definePieces()
	{
		$lowerField = trim($this->text);
		$constant = false;
		$j = strlen($lowerField);
		$defField = array();
		$index = 0;

		if ($lowerField{$j-1} === ",")
		{ $lowerField = substr($lowerField,0,$j-1); }

		if ($lowerField{0}=== "'" || $lowerField{0}==='"')
		{
			$this->constant = true;

			$constText = $lowerField{0};
			$error = true;

			for ($i=1; $i<$j; $i++)
			{
				$c = $lowerField{$i};

				if ($c==="'" || $c==='"')
				{
					$constText.=$c;
					$constant = true;
					$error = false;
					break 1;
				}
				$constText.=$c;
			}

			if ($error)
			{ trigger_error ("Warning: Field may have a wrong text name!"); }
			else
			{
				$defField[$index] = $constText;
				$index++;
			}
			$lowerField= substr($lowerField,$i+1,$j);
		}

		$j = strlen($lowerField);
		$def = "";

		for ($i=0; $i<$j; $i++)
		{
			$c = $lowerField{$i};

			if (in_array($c,$this->SQL_XCHAR))
			{
				if ($def)
				{
					$defField[$index] = $def;
					$index++;
				}

				if ($c!=" ")
				{
					$defField[$index] = $c;
					$index++;
				}

				$def = "";
				$c="";
			}
			else { $def.=$c; }
		}

		if ( !empty($def))
		{ $defField[$index] = $def; }

		$this->pieces = array();
		$this->pieces = $defField;
		//print_r($this->pieces);
	}

	/**
	 * Function defineIsCalculate()<br>
	 * This function defines if a field of the consultation was generated of a calculation or not.
	 *
	 * @access private
	 * @return void
	 */
	private function defineIsCalculate()
	{
		$j = count($this->pieces);

		for ($i=0; $i<$j; $i++)
		{
			if (in_array(strtolower($this->pieces[$i]),$this->SQL_FUNCS))
			{
				$this->argsFunction = $this->pieces[$i];
				$this->defineArguments($i);
				$this->calculate = true;
				break 1;
			}
		}
	}

	/**
	 * Function defineAlias()<br>
	 * This function defines the alias that the field possesss.
	 *
	 * @access private
	 * @return boolean - true if have an alias or false if no found an alias
	 */
	private function defineAlias()
	{
		$j = count ($this->pieces);

		//procura a ocorrência imediatamente após à palavra reservada AS, pois esta será o 'Alias' do 'field'
		for ($i=0; $i<$j; $i++)
		{
			if (strtolower($this->pieces[$i]) == "as")
			{
				$this->alias = $this->pieces[$i+1];
				return true;
			}
		}

		//Se field possuir mais de uma 'peça', verifica se o campo imediatamente anterior ao
		//último é uma palavra reservada
		if ($j>1)
		{
			$isPredef 	= in_array(strtolower($this->pieces[$j-2]),$this->SQL_PREDEF);
			$isFunc 	= in_array(strtolower($this->pieces[$j-2]),$this->SQL_FUNCS);
			$isXchar 	= in_array(strtolower($this->pieces[$j-2]),$this->SQL_XCHAR);
		}

		//Caso existir mais de uma palavra no campo e a penúltima não for uma palavra reservada
		if ($j>1 && (!$isPredef) && (!$isFunc) && (!$isXchar))
		{
			//Se a última palavra do campo não for um Caracter Especial
			//significa que esta possui o 'Alias' do field
			if (!in_array($this->pieces[$j-1],$this->SQL_XCHAR))
			{
				$this->alias = $this->pieces[$j-1];
				return true;
			}
		}

		//há apenas uma palavra no campo ou a coluna é calculada sem um 'Alias'
		return false;
	}

	/**
	 * Function defineArguments<br>
	 * This function defines the used expression for the calculated fields.
	 *
	 * @access private
	 * @param int $index
	 * @return void
	 */
	private function defineArguments($index)
	{
		$begin = $index+1;
		$count = count($this->pieces) - $begin;

		$args = "";

		for ($i=$begin; $i<=$count && strtolower($this->pieces[$i])!="as";$i++)
		{ $args.=$this->pieces[$i]; }

		$this->argsFunction.=$args;
	}

	/**
	 * Function defineIsAll()<br>
	 * This function defines if the search of the values of the field is of the 'all' type or not.
	 *
	 * @access private
	 * @return void
	 **/
	private function defineIsAll()
	{
		if (strtolower($this->pieces[0]) == "all")
		{ $this->all = true; }
	}

	/**
	 * Function defineIsDistinct<br>
	 * This function defines if the search of the values of the field is of the 'distinct' type or not.
	 *
	 * @access private
	 * @return void
	 **/
	private function defineIsDisinct()
	{
		if (strtolower($this->pieces[0]) == "distinct")
		{
			$this->distinct = true;
		}
	}

	/**
	 * <b>Function defineNames</b><br>
	 * This function defines the real name of the field.
	 * And at the same time, defines the table alias of the field.
	 *
	 * @return void
	 **/
	private function defineNames()
	{

		$name = $this->pieces[0];
		$tableAlias = null;

		//Verifica se a primeira ocorrência do array de peças é um 'distinct'
		//pois sendo verdadeiro, significa que a segunda ocorrência provalmente é o nome do campo
		if (strtolower($name)=="distinct")
		{ $name = $this->pieces[1]; }

		//Verifica se a primeira ocorrência do array de peças é um 'all'
		//pois sendo verdadeiro, significa que a segunda ocorrência provalmente é o nome do campo
		if (strtolower($name)=="all")
		{ $name = $this->pieces[1]; }

		//Verifica se o campo é uma função interna do comando SQL
		if (   array_search(strtolower($name),$this->SQL_FUNCS) === false   )
		{
			$j=strlen($name);
			$c = "";
			$auxName = "";

			for ($i=0; $i<$j; $i++)
			{
				$c = substr($name,$i,1);

				//se o nome possuir um caracter 'ponto', significa que o texto que possui
				//antes desse ponto é o alias da tabela referenciada.
				if ($c === ".")
				{
					$tableAlias = $auxName;
					$auxName= "";
				}
				else { $auxName.= $c; }
			}
			$name = $auxName;
		}
		else { $name = null; }

		$this->realName = $name;
		$this->tableAlias = $tableAlias;
	}

	/**
	 * <b>Function getAlias()</b><br>
	 * This function returns the alias of the field, contained in the property 'alias'.
	 *
	 * @return string
	 */
	public function getAlias()
	{ return $this->alias; }

	/**
	 * Function getRealName()<br>
	 * This function returns the real name of the field, contained in the property 'realName'.
	 *
	 * Note: 'real name' means that the field belongs to a column of a data base and the property 'realName'
	 * contains the real name of this field.
	 *
	 * @return string
	 */
	public function getRealName()
	{ return $this->realName; }

	/**
	 * Function getTableAlias()<br>
	 * This function returns the table alias of the field, contained in the property 'tableAlias'.
	 *
	 * @return string
	 */
	public function getTableAlias()
	{ return $this->tableAlias; }

	/**
	 * <b>Function getTableName</b><br>
	 * This function returns the table name of the field, contained in the property 'tableName'.
	 *
	 * @return string
	 */
	public function getTableName()
	{ return $this->tableName; }

	/**
	 * Function getArguments()<br>
	 * This function returns the expression from the calculated field.
	 *
	 * @return string
	 */
	public function getExpression()
	{ return $this->argsFunction; }

	/**
	 * Function isAll()<br>
	 * This function return the boolean value in the property 'all'.
	 *
	 * @return boolean true or false
	 */
	public function isAll()
	{ return $this->all; }

	/**
	 * Function isDistinct()<br>
	 * This function return the boolean value in the property 'distinct'.
	 *
	 * @return boolean true or false
	 */
	public function isDistinct()
	{ return $this->distinct; }

	/**
	 * <b>Function isCalculate()</b><br>
	 * This function return the boolean value in the property 'calculate'.
	 *
	 * @return boolean true or false
	 */
	public function isCalculate()
	{ return $this->calculate; }

	/**
	 * Function isConstant()<br>
	 * This function return the boolean value in the property 'constant'.
	 *
	 * @return boolean true or false
	 */
	public function isConstant()
	{ return $this->constant; }

	/**
	 * <b>Function setTableName()</b><br>
	 * This function set a name text for the tableName property.
	 *
	 * @param string $tableName - name of table
	 * @return boolean true or false
	 */
	public function setTableName($tableName)
	{
		if (is_string($tableName))
		{
			$this->tableName = $tableName;
			return true;
		}
		return false;
	}

	/**
	 * Function setIsCalculate()<br>
	 * This function set a boolean value for the calculate property
	 *
	 * @param boolean $isCalculate - boolean value to set in calclulate property
	 * @return boolean true or false
	 */
	public function setIsCalculate($isCalculate)
	{
		if ($isCalculate === true || $isCalculate === false)
		{
			$this->calculate = $isCalculate;
			return true;
		}
		return false;
	}

	/**
	 * <b>Function setTextField()</b><br>
	 * This function set a commant text for the text property,
	 * and call all defines functions in the correct order.
	 *
	 * @param string $textField - SQL text of field
	 * @return boolean true or false
	 **/
	public function setTextField ($textField)
	{
		if (is_string($textField))
		{
			$this->text = $textField;

			$this->definePieces();
			$this->defineIsCalculate();
			$this->defineIsAll();
			$this->defineIsDisinct();
			$this->defineAlias();
			$this->defineNames();

			return true;
		}
		return false;
	}

}
/*
//$text = "f.UF as UF";
//$text = "'sdfa ewf,efe' as TESTE";
//$text = "distinct G.name as Nome_Funcionário";
//$text = "COUNT(*) as Total";
//$text = "now()";
//$text = "Distinct SUM(Sal)*COUNT((HE)/AVG(Vendas))+Premios as Salario";
//$text = "'Muito Bom' as EU_ACHO";
$o = new TrackField($text);

echo "Alias: ".$o->getAlias()."\n";
echo "Arguments: ".$o->getExpression()."\n";
echo "Real Name: ".$o->getRealName()."\n";
echo "Table Name: ".$o->getTableName()."\n";
echo "Table Alias: ".$o->getTableAlias()."\n";

echo "Calculate: ";
if ($o->isCalculate())
{
	echo "yes\n";
}
else { echo "no\n"; }

echo "All: ";
if ($o->isAll())
{
	echo "yes\n";
}
else { echo "no\n"; }

echo "Distinct: ";
if ($o->isDistinct())
{
	echo "yes\n";
}
else { echo "no\n"; }

echo "Constant: ";
if ($o->isConstant())
{
	echo "yes\n";
}
else { echo "no\n"; }
*/