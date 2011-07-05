<?php
require_once(realpath(dirname(__FILE__)) . '/../../NeoBaze/collections/List.php');

class ListIterator {
	/**
	 * @AssociationType NeoBaze.collections.List
	 */
	private $_unnamed_List_;

	/**
	 * @ParamType comp Component 2
	 * Insere o elemento especificado na lista (opera��o opcional).
	 */
	public function add(Component_2 $comp) {
		// Not yet implemented
	}

	/**
	 * @ReturnType boolean
	 * Retorna "true" caso o elemento atual n�o seja o �ltimo (opera��o opcional).
	 */
	public function hasNext() {
		// Not yet implemented
	}

	/**
	 * @ReturnType boolean
	 * Retorna "true" caso o elemento atual n�o seja o primeiro (opera��o opcional).
	 */
	public function hasPrevious() {
		// Not yet implemented
	}

	/**
	 * @ReturnType Component 2
	 * Retorna o pr�ximo elemento na lista.
	 */
	public function next() {
		// Not yet implemented
	}

	/**
	 * @ReturnType int
	 * Retorna o index do elemento que seria retornado por um "next".
	 */
	public function nextIndex() {
		// Not yet implemented
	}

	/**
	 * @ReturnType Component 2
	 * Retorna o elemento anterior desta lista.
	 */
	public function previous() {
		// Not yet implemented
	}

	/**
	 * @ReturnType int
	 * Retorna o index do elemento que seria retornado por um "previous".
	 */
	public function previousIndex() {
		// Not yet implemented
	}

	/**
	 * Remove da lista o �ltimo elemento que foi retornado por um "next" ou "previous" (opera��o opcional)
	 */
	public function remove() {
		// Not yet implemented
	}

	/**
	 * @ParamType comp Component 2
	 * Substitui o �ltimo elemento retornado pelo "next" ou "previous" com o elemento especificado (opera��o opcional).
	 */
	public function set(Component_2 $comp) {
		// Not yet implemented
	}
}
?>