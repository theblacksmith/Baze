<?php

import('external.Zend.Cache');
import('external.Zend.Cache.Core');
import('system.caching.ICacheBackend');

/**
 * 
 */
class Cache
{	
	const CLEANING_MODE_ALL              = 'all';
    const CLEANING_MODE_OLD              = 'old';
    const CLEANING_MODE_MATCHING_TAG     = 'matchingTag';
    const CLEANING_MODE_NOT_MATCHING_TAG = 'notMatchingTag';
    
	/**
	 * @var Zend_Cache_Core
	 */
	private $cacheObject;
	
	/**
	 * @var ICacheBackend
	 */
	private $cacheBackend = null;
	
	public function __construct(ICacheBackend $cb = null)
	{
		$cacheCore = new Zend_Cache_Core();
		
		if(!is_null($cb))
		{
			$this->cacheBackend = $cb;
			$cacheCore->setBackend($cb->getBackend());
		}
		
		$this->cacheObject = $cacheCore;
	}
	
	/**
	 * Testa se um cache está disponível para um determinado id
	 * e, se sim, retorna o valor deste cache, senão, retorna falso.
     *
     * @param string $id cache id
     * @return string dados cacheados (ou false)
     */
	public function load($id)
	{
		return $this->cacheObject->load($id);
	}
	
	/**
     * Salva um determinado texto em um cache
     *
     * @param string $data dado a ser armazenado
     * @param string $id cache id
     * @return boolean true se não houve problema no cacheamento
     */
	function save($data, $id, $tags = array(), $specificLifetime = false)
	{
		return $this->cacheObject->save($data, $id, $tags, $specificLifetime);
	}
	
	/**
     * Remove um cache armazenado
     *
     * @param string $id cache id
     * @return boolean true se não houve problema (false caso contrário)
     */
	function remove($id)
	{
		return $this->cacheObject->remove($id);
	}
	
	/**
     * Limpa caches armazenados
     *
     * Os modos de limpeza permitidos são :
     * Cache::CLEANING_MODE_ALL (default)    => remove todos os caches
     * Cache::CLEANING_MODE_OLD              => remove somente os caches antigos expirados
     * Cache::CLEANING_MODE_MATCHING_TAG     => remove caches que obtenham determinada(s) tag(s)
     * Cache::CLEANING_MODE_NOT_MATCHING_TAG => remove caches que não contenham determinada(s) tag(s)
     *
     * @param string $mode modo de limpeza
     * @param tags array $tags array de tags
     * @return boolean true se não encontrou problema
     */
	function clean($mode = 'all', $tags = array())
	{
		return $this->cacheObject->clean($mode, $tags);
	}
	
	/**
	 * Informa o tempo de vida do cache. (null => tempo de vida infinita).
	 * Essa função não afeta o tempo de vida de itens já salvos no cache
	 * 
	 * @param int $lifetime (em segundos)
	 */
	function setLifetime($lifetime)
	{
		$this->cacheObject->setLifetime($lifetime);
	}
	
	/**
     * Setar um valor para uma diretiva do cache.
     *
     * @param string $name nome da diretiva
     * @param mixed $value valor da diretiva
     */
	function setOption($optionName, $optionValue)
	{
		$this->cacheObject->setOption($optionName, $optionValue);
	}
	
	/**
	 * Informar o backend a ser utilizado para o cacheamento.
	 * Chama o método necessário para atualizar o backend 
	 * do objeto cache real (não fachada).
	 * 
	 * @param ICacheBackend $backend Objeto que implementa 
	 * 									a interface ICacheBackend
	 */
	function setBackend(ICacheBackend $backend)
	{
		$this->cacheBackend = $backend;
		$this->cacheObject->setBackend($backend->getBackend());
	}
}