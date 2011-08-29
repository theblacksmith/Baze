<?php
require_once(realpath(dirname(__FILE__)) . '/../../NeoBaze/collections/ListIterator.php');
require_once(realpath(dirname(__FILE__)) . '/../../NeoBaze/collections/Collection.php');

class List_ extends Collection {
	/**
	 * @AssociationType NeoBaze.collections.ListIterator
	 */
	private $_unnamed_ListIterator_;

	/**
	 * @ParamType index int
	 * @ParamType comp Component 2
	 * Insere o elemento especificado na posi��o especificada nesta lista (opera��o opcional).
	 */
	public function add($index, Component_2 $comp) {
		// Not yet implemented
	}

	/**
	 * @ReturnType boolean
	 * @ParamType list NeoBaze.collections.List
	 * Adiciona todos os elementos na lista especificada no final desta lista na ordem em que s�o retornados pelo iterador da lista especificada (opera��o opcional).
	 */
	public function addAll(List_ $list) {
		// Not yet implemented
	}

	/**
	 * Remove todos os elementos desta lista (opera��o opcional).
	 */
	public function clear() {
		// Not yet implemented
	}

	/**
	 * @ReturnType boolean
	 * @ParamType comp Component 2
	 * Retorna "true" caso esta lista possua o elemento especificado.
	 */
	public function contains(Component_2 $comp) {
		// Not yet implemented
	}

	/**
	 * @ReturnType boolean
	 * @ParamType list NeoBaze.collections.List
	 * Retorna "true" caso esta lista possua todos os elementos da lista especificada.
	 */
	public function containsAll(List_ $list) {
		// Not yet implemented
	}

	/**
	 * @ReturnType boolean
	 * @ParamType comp Component 2
	 * Retorna "true" caso os objetos especificados sejam iguais.
	 */
	public function equals(Component_2 $comp) {
		// Not yet implemented
	}

	/**
	 * @ReturnType Component 2
	 * @ParamType int index
	 * Retorna o elemento na posi��o especificada nesta lista.
	 */
	public function get(index $int) {
		// Not yet implemented
	}

	/**
	 * @ReturnType int
	 * Retorna o c�digo hash para esta lista.
	 */
	public function hashCode() {
		// Not yet implemented
	}

	/**
	 * @ReturnType int
	 * @ParamType comp Component 2
	 * Retorna o index da primeira ocorr�ncia do elemento especificado desta lista, ou -1 caso a lista n�o possua este elemento.
	 */
	public function indexOf(Component_2 $comp) {
		// Not yet implemented
	}

	/**
	 * @ReturnType boolean
	 * Retorna "true" caso esta lista n�o possua elementos.
	 */
	public function isEmpty() {
		// Not yet implemented
	}

	/**
	 * @ReturnType NeoBaze.collections.ListIterator
	 * Retorna um iterador para os elementos desta lista na seq��ncia apropriada.
	 */
	public function iterator() {
		// Not yet implemented
	}

	/**
	 * @ReturnType int
	 * @ParamType comp Component 2
	 * Retorna o index da �ltima ocorr�ncia do elemento especificado desta lista, ou -1 caso a lista n�o possua este elemento.
	 */
	public function lastIndexOf(Component_2 $comp) {
		// Not yet implemented
	}
}
?>