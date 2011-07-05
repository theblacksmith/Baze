<?php
/**
 * Collection class file
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze.system.collections
 */

require_once NB_ROOT . '/system/Component.class.php';

/**
 * Classe Collection
 *
 * Essa classe ainda não foi documentada
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze.system.collections
 */
class Collection extends Component implements Countable, ArrayAccess, Iterator {

	/**
	 * @var int items count
	 * @access protected
	 */
	protected $count = 0;

	/**
	 * @var array collection item
	 * @access protected
	 */
	protected $items;

	/**
	 * @var string collection type
	 * @access protected
	 */
	protected $type;

	/**
	 * @var boolean whether the collection is read only or not
	 * @access protected
	 */
	protected $readOnly;

	/**
	 * @var int
	 * @desc Internal pointer used to implement iterator interface
	 */
	protected $i;

	public function __construct($type = false, ArrayAccess $data = null, $readOnly = false)
	{
		if($data!==null)
			$this->addAll($data);

		$this->type = $type;
		$this->setReadOnly($readOnly);
		$this->items = array();
	}

	/**
	 * @return boolean se a coleção é ou não somente leitura
	 */
	public function getReadOnly()
	{
		return $this->readOnly;
	}

	/**
	 * Define a coleção como somente leitura ou não
	 *
	 * @param mixed $readOnly boolean ou string("true" ou "false")
	 */
	protected function setReadOnly($readOnly)
	{
		$this->readOnly = PhpType::toBoolean($readOnly);
	}

	/**
	 * Assegura que esta coleção contenha o elemento especificado (operação opcional).
	 *
 	 * @access public
	 * @param mixed $item item a ser adicionado na coleção
	 * @throws InvalidOperationException Quando a função é chamada em uma coleção
	 * que só permite sua leitura
	 * @return int índice onde o item foi adicionado
	 */
	public function add($item)
	{
		$this->insertAt($item, $this->count);

		return $this->count - 1;
	}

	/**
	 * Adiciona todos os elementos da coleção especificada nesta coleção.
	 *
 	 * @access public
 	 * @throws InvalidOperationException Quando a função é chamada em uma coleção
	 * que só permite sua leitura
	 * @param mixed $coll uma coleção ou um array de onde os itens devem ser copiados
	 */
	public function addAll(ArrayAccess $coll)
	{
		foreach($coll as $item)
		{
			$this->insertAt($item, $this->count);
		}
	}

	/**
	 * Remove todos os elementos desta coleção (operação opcional).
	 *
 	 * @access public
	 */
	public function clear()
	{
		if(!$this->readOnly)
		{
			$this->items = array();
			$this->count = 0;
		}
		else
			throw new InvalidOperationException(ErrorMessages::ModifingReadOnlyCollection, get_class($this), __METHOD__);
	}

	/**
	 * Retorna "true" caso esta coleção contenha o elemento especificado.
	 *
 	 * @access public
	 * @param mixed $item
	 * @return boolean
	 */
	public function contains($item)
	{
		foreach($this->items as $it)
		{
			if($item === $it)
				return true;
		}

		return false;
	}

	/**
	 * Retorna "true" se esta coleção contiver todos os elementos da coleção especificada.
	 *
 	 * @access public
 	 * @param ArrayAccess $coll um array ou um objeto que implemente a interface ArrayAccess
	 * @return boolean
	 */
	public function containsAll($coll)
	{
		foreach($coll as $item)
		{
			if(!$this->contains($item))
				return false;
		}

		return true;
	}

	/**
	 * Retorna o número de elementos desta coleção.
	 *
 	 * @access public
	 * @return int
	 */
	public function count() {
		return $this->count;
	}
	
	/**
	 * Alias for count()
	 * @see Collection::count()
	 */
	public function size() {
		return $this->count();
	}

	/**
	 * Retorna "true" caso as coleções sejam iguais.
	 *
 	 * @access public
	 * @param Collection $comp
	 * @return boolean
	 */
	public function equals(Collection $comp)
	{
		if($this->count == $comp->count() && $this->containsAll($comp))
		{
			return true;
		}

		return false;
	}

	/**
	 * Retorna um iterador para os elementos desta coleção.
	 *
 	 * @access public
	 * @return CollectionIterator
	 */
	public function getIterator()
	{
		return new CollectionIterator($this);
	}

	/**
	 * Retorna um string com o tipo ou nome da classe dos
	 *  objetos aceitos pela coleção. Caso o tipo não tenha
	 * sido definido o pesudo-tipo mixed será retornado
	 *
 	 * @access public
	 * @return string
	 */
	public function getType() {
		return $this->type ? $this->type : 'mixed';
	}

	/**
 	 * @access public
	 * @param mixed $item
	 * @return mixed um inteiro quando o objeto é encontrado e false quando não
	 */
	public function indexOf($item)
	{
		foreach($this->items as $index => $obj)
		{
			if($item === $obj)
				return $index;
		}

		return false;
	}

	/**
 	 * @access public
	 * @param mixed $item
	 * @param int $offset
 	 * @throws InvalidArgumentValueException Quando o índice recebido é menor que 0
 	 * ou maior que a última posição da coleção
 	 * @throws InvalidOperationException Quando a função é chamada em uma coleção
	 * que só permite sua leitura
	 */
	public function insertAt($item, $offset)
	{
		if(!$this->readOnly)
		{
			if($this->type && strcasecmp(gettype($item), $this->type) !== 0 && strcasecmp(get_class($item), $this->type) !== 0)
				throw new InvalidArgumentValueException();

			if($offset === $this->count)
			{
				$this->items[] = $item;
				$this->count++;
			}
			else if($offset >= 0 && $offset < $this->count)
			{
				array_splice($this->items,$offset,0,array($item));
				$this->count++;
			}
			else
				throw new InvalidArgumentValueException(ErrorMessages::Collection_InvalidIndex, get_class($this), $offset);
		}
		else
			throw new InvalidOperationException(ErrorMessages::Collection_ModifingReadOnly, get_class($this));
	}

	/**
	 * Retorna "true" caso esta coleção não possua elementos.
	 *
 	 * @access public
	 * @return boolean
	 */
	public function isEmpty() {
		return ($this->count == 0);
	}

	/**
 	 * @access public
	 * @param int $offset
	 * @throws IndexOutOfBoundsException Quando o índice passado excede o índice
	 * do último elemento
	 * @throws InvalidArgumentValueException Quando o índice passado é negativo ou
	 * não é um número inteiro
	 * @return mixed
	 */
	public function item($offset)
	{
		if(!is_int($offset) || $offset < 0) {
			throw new InvalidArgumentValueException(Collection_InvalidIndex, get_class($this), $offset);
		}
		else if($offset < $this->count) {
			return $this->items[$offset];
		}
		else {
			throw new IndexOutOfBoundsException(Msg::Collection_IndexOutOfBounds, array($offset, $this->count));
		}
	}

	/**
	 * Remove uma única instância do elemento especificado desta coleção, caso exista.
	 *
 	 * @access public
	 * @param mixed $item
	 * @return boolean
	 */
	public function remove($item)
	{
		if(!$this->readOnly)
		{
			$index = $this->indexOf($item);

			if(!$index)
				return false;

			array_splice($this->items, $index, 1);
		}
		else
			throw new InvalidOperationException(Msg::Collection_ModifingReadOnly, get_class($this));
	}

	/**
	 * Remove todos os elementos desta coleção que também existam na coleção especificada.
	 *
 	 * @access public
	 * @param ArrayAccess $coll
	 * @return boolean
	 */
	public function removeThese($coll)
	{
		foreach($coll as $item)
		{
			$this->remove($item, $this->count);
		}
	}

	/**
 	 * @access public
	 * @param int $offset
 	 * @throws InvalidArgumentValueException Quando o índice recebido é menor que 0
 	 * ou maior que a última posição da coleção
 	 * @throws InvalidOperationException Quando a função é chamada em uma coleção
	 * que só permite sua leitura
	 * @return mixed
	 */
	public function removeAt($offset)
	{
		if(!$this->readOnly)
		{
			if($index === $this->count-1)
			{
				$this->count--;
				unset($this->items[$this->count-1]);
			}
			else if($offset >= 0 && $offset < $this->count)
			{
				$remArr = array_splice($this->items,$offset,1,array($item));
				$this->count--;
				return $remArr[0];
			}
			else
				throw new InvalidArgumentValueException(ErrorMessages::Collection_InvalidIndex, get_class($this), 'offset', $offset);
		}
		else
			throw new InvalidOperationException(ErrorMessages::Collection_ModifingReadOnly, get_class($this));
	}

	/**
	 * Remove da coleção <i>length</i> itens a partir do índice <i>fromIndex</i> inclusive.
	 * Caso length ultrapasse o número de itens a partir do índice indicado até o fim da coleção
	 * todos os itens serao removidos.
	 *
 	 * @access public
	 * @param int $fromIndex Primeiro elemento a ser removido
	 * @param int $length Quantidade de elementos a ser removida
	 * @return Collection Uma coleção contendo os itens removidos
	 */
	public function removeRange($fromIndex, $length)
	{
		if(!$this->readOnly)
		{
			if($lenght < 0)
				throw new InvalidArgumentValueException(ErrorMessages::Generic_InvalidNegativeLength, get_class($this), 'length', $length);

			if($fromIndex < 0)
				throw new InvalidArgumentValueException(ErrorMessages::Collection_InvalidIndex, get_class($this), 'fromIndex', $fromIndex);

			if($fromIndex > $this->count)
				throw new IndexOutOfBoundsException(ErrorMessages::Collection_IndexOutOfBounds, get_class($this), $fromIndex, $this->count);

			return new Collection(array_splice($this->items,$fromIndex,$length));
		}
		else
			throw new InvalidOperationException(ErrorMessages::Collection_ModifingReadOnly, get_class($this));
	}

	/**
	 * Substitui o item na posição indicada pelo item passado como parâmetro
	 *
	 * @access public
	 * @param int $offset Posição do item a ser substituído
	 * @param mixed $item Item a ser inserido
	 * @throws InvalidOperationException Se a coleção for somente leitura
	 * @throws InvalidArgumentValueException Se o índice offset for menor que 0
	 * @throws IndexOutOfBoundsException Se o índice passado for maior que o último índice da colação
	 * @return mixed O elemento que ocupava a posição <i>offset</i>
	 */
	public function replace($offset, $item)
	{
		if(!$this->readOnly)
		{
			if($offset > 0)
			{
				if($offset < $this->count)
				{
					$remArr = array_splice($this->items, $offset, 1, array($item));
					return $remArr[0];
				}
				else
					throw new IndexOutOfBoundsException(ErrorMessages::Collection_IndexOutOfBounds, get_class($this), $offset, $this->count);
			}
			else
				throw new InvalidArgumentValueException(ErrorMessages::Collection_InvalidIndex, get_class($this), 'offset', $offset);
		}
		else
			throw new InvalidOperationException(ErrorMessages::Collection_ModifingReadOnly, get_class($this));
	}

	/**
	 * Mantém somente os elementos desta coleção que estejam contidos na coleção especificada (operação opcional).
	 *
 	 * @access public
	 * @param ArrayAccess $coll
	 * @return boolean
	 */
	public function retainAll($coll)
	{
		if(!$this->readOnly)
		{
			foreach($this->items as $index => $item)
			{
				$retain = false;

				foreach($coll as $retIndex => $retainItem)
				{
					if($retainItem === $item)
					{
						$retain = true;
						break;
					}
				}

				if($retain === false)
					$this->removeAt($index);
			}
		}
		else
			throw new InvalidOperationException(ErrorMessages::Collection_ModifingReadOnly, get_class($this));
	}

	/**
	 * Retorna um array contendo todos os elementos desta coleção.
	 *
 	 * @access public
	 * @return array
	 */
	public function toArray() {
		return $this->items;
	}

	/**
	 * Define o tipo de objeto que a coleção pode receber. Este tipo não pode ser alterado em uma coleção não vazia
	 * a não ser que, não tendo um tipo definido e contendo objetos de um único tipo, queira-se definir como tipo
	 * atual o tipo destes objetos.
	 *
	 * @throws InvalidOperationException Se a função for chamada para definir o tipo de uma coleção que já contém objetos de outro tipo
	 * ou se a coleção for somente leitura
	 * @access public
	 * @param string $type
	 */
	public function setType($type)
	{
		if(!$this->readOnly)
		{
			if(strcasecmp($type,'mixed'))
				$this->type = 'mixed';
			else
			{
				foreach($this->items as $item)
					if(!PhpType::checkType($item, $type))
						throw new InvalidOperationException(ErrorMessages::Collection_SettingTypeInMixedCollection, get_class($this));

				$this->type = strtolower($type);
			}
		}
		else
			throw new InvalidOperationException(ErrorMessages::Collection_ModifingReadOnly, get_class($this));
	}

	/**
	 * Retorna uma coleção com <i>quantity</i> itens a partir do índice <i>fromIndex</i>. Estes itens não são removidos.
	 *
 	 * @access public
	 * @param int $fromIndex
	 * @param int $quantity Quantidade de elementos na sub-coleção ou null para todos os elementos até o fim da coleção
	 * @return Collection
	 */
	public function subCollection($fromIndex, $quantity=null)
	{
		if($quantity === null)
			$quantity = $this->count;

		return new Collection(array_slice($arr, $fromIndex, $quantity));
	}

	/**
	 * Retorna se há ou não um item na posição especificada. Esse método é requerido pela interface ArrayAccess.
	 *
 	 * @access public
	 * @param int $offset
	 */
	public function offsetExists($offset) {
		return ($offset >= 0 && $offset < $this->count);
	}

	/**
	 * Retorna o item na posição especificada. Esse método é requerido pela interface ArrayAccess.
	 *
 	 * @access public
	 * @param int $offset
	 */
	public function offsetGet($offset) {
		return $this->item($offset);
	}

	/**
	 * Define o item na posição especificada. Esse método é requerido pela interface ArrayAccess.
	 *
 	 * @access public
	 * @param int $offset
	 * @param mixed $item
	 */
	public function offsetSet($offset, $item)
	{
		if($offset === null || $offset === $this->count) {
			$this->insertAt($this->count, $item);
		}
		else {
			$this->replace($offset, $item);
		}
	}

	/**
	 * Remove o item na posição especificada. Esse método é requerido pela interface ArrayAccess.
	 *
 	 * @access public
	 * @param int $offset
	 */
	public function offsetUnset($offset) {
		return $this->removeAt($offset);
	}

	/**
	 * Retorna o elemento atual. Esse método é exigido pela interface Iterator.
	 *
 	 * @access public
	 * @return mixed
	 */
	public function current() {
		return current($this->items);
	}

	/**
	 * Retorna "true" caso o elemento atual não seja o último.
	 *
 	 * @access public
	 * @return boolean
	 */
	public function hasNext()
	{
		return key($this->items) < $this->count-1;
	}

	/**
	 * Retorna "true" caso o elemento atual não seja o primeiro
	 *
 	 * @access public
	 * @return boolean
	 */
	public function hasPrevious() {
		return (key($this->i) > 0);
	}

	/**
	 * Retorna a chave do elemento atual. Esse método é exigido pela interface Iterator.
	 *
 	 * @access public
	 * @return int
	 */
	public function key() {
		return key($this->items);
	}

	/**
	 * Move o ponteiro interno para o próximo elemento da coleção. Esse método é exigido pela interface Iterator
	 *
 	 * @access public
	 */
	public function next() {
		return next($this->items);
	}

	/**
	 * Retorna o elemento anterior desta lista.
	 *
 	 * @access public
	 */
	public function previous() {
		return prev($this->items);
	}

	/**
	 * Retorna o ponteiro interno para o primeiro elemento. Esse método é exigido pela interface Iterator.
	 *
 	 * @access public
	 */
	public function rewind() {
		return reset($this->items);
	}

	/**
	 * Checa se há um elemento atual válido. Geralmente é chamado depois de uma chamada à {@see CollectionIterator::next()}
	 * ou {@see CollectionIterator::rewind()}. Esse método é exigido pela interface Iterator.
	 *
 	 * @access public
	 * @return boolean
	 */
	public function valid() {
		return current($this->items) !== false;
	}
}

/**
 * Implementa o padrão Iterator para a classe Collection
 *
 * @author Saulo Vallory
 * @version 1.0
 * @package System.collections
 * @since 1.0
 */
class CollectionIterator implements Iterator {

	/**
	 * @var int
	 * @desc current position
	 */
	protected $i;

	/**
	 * @var Collection
	 * @desc The collection to iterate
	 */
	protected $items;

	/**
	 * @var int
	 * @desc number of items
	 */
	protected $count;

	/**
 	 * @access public
	 * @param ArrayAccess $coll
	 */
	public function __construct(&$coll) {
		$this->i = 0;
		$this->items = $coll;
		$this->count = count($coll);
	}

	/**
	 * Retorna o elemento atual. Esse método é exigido pela interface Iterator.
	 *
 	 * @access public
	 * @return mixed
	 */
	public function current() {
		return current($this->items);
	}

	/**
	 * Retorna "true" caso o elemento atual não seja o último.
	 *
 	 * @access public
	 * @return boolean
	 */
	public function hasNext()
	{
		return key($this->items) < $this->count-1;
	}

	/**
	 * Retorna "true" caso o elemento atual não seja o primeiro
	 *
 	 * @access public
	 * @return boolean
	 */
	public function hasPrevious() {
		return (key($this->i) > 0);
	}

	/**
	 * Retorna a chave do elemento atual. Esse método é exigido pela interface Iterator.
	 *
 	 * @access public
	 * @return int
	 */
	public function key() {
		return key($this->items);
	}

	/**
	 * Move o ponteiro interno para o próximo elemento da coleção. Esse método é exigido pela interface Iterator
	 *
 	 * @access public
	 */
	public function next() {
		return next($this->items);
	}

	/**
	 * Retorna o elemento anterior desta lista.
	 *
 	 * @access public
	 */
	public function previous() {
		return prev($this->items);
	}

	/**
	 * Retorna o ponteiro interno para o primeiro elemento. Esse método é exigido pela interface Iterator.
	 *
 	 * @access public
	 */
	public function rewind() {
		return reset($this->items);
	}

	/**
	 * Checa se há um elemento atual válido. Geralmente é chamado depois de uma chamada à {@see CollectionIterator::next()}
	 * ou {@see CollectionIterator::rewind()}. Esse método é exigido pela interface Iterator.
	 *
 	 * @access public
	 * @return boolean
	 */
	public function valid() {
		return current($this->items) !== false;
	}
}