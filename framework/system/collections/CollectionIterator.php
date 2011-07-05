<?php
class CollectionIterator implements Iterator {

	/**
	 * @var int índice atual
	 * @access protected
	 */
	protected $i;

	/**
	 * @var Collection A coleção a ser iterada
	 * @access protected
	 */
	protected $coll;

	/**
 	 * @access public
	 * @param Collection $coll 
	 */
	public function __construct(Collection $coll) {
		throw new NotImplementedException(__method__);
	}

	/**
	 * Retorna o elemento atual. Esse método é exigido pela interface Iterator.
	 * 
 	 * @access public
	 * @return mixed 
	 */
	public function current() {
		throw new NotImplementedException(__method__);
	}

	/**
	 * Retorna "true" caso o elemento atual não seja o último.
	 * 
 	 * @access public
	 * @return boolean 
	 */
	public function hasNext() {
		throw new NotImplementedException(__method__);
	}

	/**
	 * Retorna "true" caso o elemento atual não seja o primeiro
	 * 
 	 * @access public
	 * @return boolean 
	 */
	public function hasPrevious() {
		throw new NotImplementedException(__method__);
	}

	/**
	 * Retorna a chave do elemento atual. Esse método é exigido pela interface Iterator.
	 * 
 	 * @access public
	 * @return int 
	 */
	public function key() {
		throw new NotImplementedException(__method__);
	}

	/**
	 * Move o ponteiro interno para o próximo elemento da coleção. Esse método é exigido pela interface Iterator
	 * 
 	 * @access public
	 */
	public function next() {
		throw new NotImplementedException(__method__);
	}

	/**
	 * Retorna o elemento anterior desta lista.
	 * 
 	 * @access public
	 */
	public function previous() {
		throw new NotImplementedException(__method__);
	}

	/**
	 * Retorna o ponteiro interno para o primeiro elemento. Esse método é exigido pela interface Iterator.
	 * 
 	 * @access public
	 */
	public function rewind() {
		throw new NotImplementedException(__method__);
	}

	/**
	 * Checa se há um elemento atual válido. Geralmente é chamado depois de uma chamada à {@see CollectionIterator::next()} ou {@see CollectionIterator::rewind()}. Esse método é exigido pela interface Iterator.
	 * 
 	 * @access public
	 * @return boolean 
	 */
	public function valid() {
		throw new NotImplementedException(__method__);
	}
}