<?php

class VisibilityType
{
	const PUBLIC_ = 'public';
	const PROTECTED_ = 'protected';
	const PRIVATE_ = 'private';
	const PACKAGED = 'packaged';
}

class MetaAttribute
{
	public $visibility;
	public $name;
	public $type;
	public $initialValue;
	public $isFinal;
	public $scope;
	public $doc;
	public $hasSetter;
	public $hasGetter;
	
	public function __construct($name, $visibility = '', $type = '')
	{
		$this->name = $name;
		$this->visibility = VisibilityType::PUBLIC_;
		$this->type = $type;
		$this->initialValue = '';
		$this->isFinal = false;
		$this->scope = 'instance';
		$this->doc = '';
		$this->hasSetter = false;
		$this->hasGetter = false;
	}
	
	public function generateCode()
	{
		$code = '';
		
		if($this->type != '' || $this->doc != '')
		{
			$code .="	/**\n" .
					(trim($this->doc != '') ? "	 * " . str_replace("\n","\n	 * ",$this->doc) . "\n	 *\n" : '') .
					($this->type ? '	 * @var ' . $this->type . "\n" : '') .
					"	 */\n";
		}
		
		$code .= '	' . $this->visibility . ' ' . 
					($this->scope == 'classifier' ? 'static ' : '') .
					($this->isFinal ? 'final ' : '') . 
					'$' . $this->name .
					($this->initialValue ? ' = '.$this->initialValue : '') . ";\n\n";
		
		return $code;
	}
}

class MetaParameter extends MetaAttribute
{
	public $defaultValue;
	public $typeHint;
	
	public function generatePHPDoc()
	{
		return '@param '.$this->type.' '.$this->name . ($this->doc ? ' '.$this->doc : '');
	}
	
	public function generateCode()
	{
		$code = ($this->typeHint ? $this->type.' ' : '') . $this->name;

		if($this->defaultValue)
			$code .= ' = ' . $this->defaultValue;
		
		return $code;
	}
}

class MetaMethod
{
	public $visibility;
	public $name;
	public $returnType;
	public $parameters;
	public $isFinal;
	public $isAbstract;
	public $scope;
	public $doc;
	
	public function __construct($name, $parameters = null, $visibility = '', $returnType = null)
	{
		$this->name = $name;
		$this->visibility = $visibility;
		$this->returnType = $returnType;
		$this->parameters = array();
		$this->isFinal = false;
		$this->isAbstract = false;
		$this->scope = 'instance';
		$this->doc = '';
	}
	
	public function generateCode()
	{
		// PHPDoc
		$doc = "\t/**\n";
		$showDoc = false;
		
		if($this->isAbstract || $this->isFinal || $this->scope == 'classifier' || trim($this->doc) != '')
		{
			$showDoc = true;
			$doc .= (trim($this->doc) != '' ? "\t *" . str_replace("\n", "\n\t *",$this->doc) . "\n\t *\n" : '') .
					($this->isAbstract ? "\t * @abstract\n" : '') .
					($this->isFinal ? "\t * @final\n" : '') .
					($this->scope == 'classifier'? "\t * @static\n" : ''); 
		}
		
		
		// header
		$code = "\t" . ($this->visibility != '' ? $this->visibility.' ' : '') .
				($this->isAbstract ? 'abstract ' : '') .
				($this->scope == 'classifier' ? 'static ' : '') .
				($this->isFinal ? 'final ' : '') .
				'function ' . $this->name . '(';

		if(isset($this->parameters[0]))
		{
			$showDoc = true;

			for($i=0, $len=sizeof($this->parameters); $i < $len-1; $i++) {
				$doc .= "\t * " . $this->parameters[$i]->generatePHPDoc() . "\n";
				$code .= $this->parameters[$i]->generateCode() . ', ';
			}
			
			$doc .= "\t * " . $this->parameters[$i]->generatePHPDoc() . "\n";
			$code .= $this->parameters[$i]->generateCode();
		}
		
		// continuando a doc
		if($this->returnType)
		{
			$showDoc = true;
			$doc .= "\n\t * @return " . $this->returnType;
		}
			
		$doc .= "\t */\n"; 
		
		$code .= ")\n\t{";
		
		// default code goes here
		
		$code .= "}";
		
		return ($showDoc? $doc : '') . $code;
	}
}

class MetaClass
{
	public $attributes;
	public $methods;
	public $name;
	public $extends;
	public $interfaces;
	
	/**
	 * @param string $name
	 * @param string $extends
	 * @param array $interfaces
	 */
	public function __construct($name, $extends = null, array $interfaces = null)
	{
		$this->name = $name;
		$this->extends = $extends;
		
		if($interfaces == null)
			$this->interfaces = array();
		else
			$this->interfaces = $interfaces;
	}
	
	/**
	 * Adiciona um atributo. Se um atibuto com o mesmo nome já existir,
	 * este atributo será sobrescrito
	 *
	 * @param MetaAttribute $att
	 */
	public function addAttribute(MetaAttribute $att)
	{
		$this->attributes[$att->name] = $att;
	}
	
	/**
	 * Cria um novo atributo com os dados passados e adiciona na coleção de atributos
	 *
	 * @param string $name
	 * @param VisibilityType $visibility
	 * @param string $type
	 */
	public function createAttribute($name, $visibility=null, $type=null)
	{
		$this->attributes[$name] = new MetaAttribute($name, $visibility, $type);
	}

	/**
	 * @param string $attName
	 */
	public function hasAttribute($attName)
	{
		return isset($this->attributes[$attName]);
	}
	
	/**
	 * Adiciona um método. Se um método com o mesmo nome já existir,
	 * este método será sobrescrito
	 *
	 * @param MetaMethod $meth
	 */
	public function addMethod(MetaMethod $meth)
	{
		$this->methods[$meth->name] = $meth;
	}
	
	/**
	 * Cria e adiciona um método. Se um método com o mesmo nome já existir,
	 * este método será sobrescrito
	 *
	 * @param string $name
	 * @param array $parameters
	 * @param VisibilityType $visibility
	 * @param returnType $returnType
	 */
	public function createMethod($name, $parameters = null, $visibility = null, $returnType = null)
	{
		$this->methods[$name] = new MetaMethod($name, $parameters, $visibility, $returnType);
	}
	
	/**
	 * @param string $methName
	 */
	public function hasMethod($methName)
	{
		return isset($this->attributes[$methName]);
	}

	public function generateCode()
	{
		$code = "&lt;?\n\nclass " . $this->name;
		
		if($this->extends) $code .= ' extends ' . $this->extends;
		
		if(isset($this->interfaces[0]))
		{
			$code .= ' implements';
			
			for($i=0, $len=sizeof($this->interfaces); $i < $len-1; $i++) {
				$code .= ' ' . $this->interfaces[$i] . ',';
			}
			
			$code .= ' ' . $this->interfaces[$i];
		}
		
		$code .= "\n{\n";
		
		// Atributos
		foreach($this->attributes as $att) {
			$code .= $att->generateCode();
		}
		
		foreach($this->methods as $meth)
		{
			$code .= $meth->generateCode() . "\n\n";
		}
		
		$code .= "\n}";
		
		return $code;
	}
}