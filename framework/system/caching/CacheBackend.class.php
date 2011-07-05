<?php

import('system.caching.ICacheBackend');
import('system.lang.Enumeration');

final class StorageType extends Enumeration
{
    public static $File = "external.Zend.Cache.Core.File";
    public static $Apc = "external.Zend.Cache.Core.Apc";
    public static $Sqlite = "external.Zend.Cache.Core.Sqlite";
    public static $ZendPlatform = "external.Zend.Cache.Core.ZendPlatform";
    public static $Memcached = "external.Zend.Cache.Core.Memcached";
}

Enumeration::init("StorageType");

class CacheBackend implements ICacheBackend
{	
	/**
	 * 
	 *
	 * @var StorageType
	 */
	private $storageType = null;
	
	/**
	 * @var Zend_Cache_Backend_Interface
	 */
	private $backend = null;
	
	/**
	 * @param StorageType $st A value from StorageType enum
	 * @param array $directives {@see CacheBackend::setDirectives()}
	 */
	public function __construct(StorageType $st = null, array $directives = null)
	{
		if($st !== null)
			$this->setStorageType($st);
		
		if($directives != null)
			$this->setDirectives($directives);
	}
	
	/**
	 * Informar tipo de armazenamento do cache, dentro das possibilidades
	 * disponibilizadas pelo "enum" StorageType.
	 * Cria o objeto backend original que será utilizado pelo escopo original
	 * por trás da classe de fachada.
	 * 
	 * Nota: Ao mudar o tipo de armazenamento, todas as diretivas
	 * armazenadas anteriormente não persistem, sendo o objeto backend
	 * real substituido por um novo do novo tipo informado.
	 * 
	 * @param StorageType $st
	 */
	public function setStorageType(StorageType $st)
	{
		if(!is_null($this->storageType) && $this->storageType->toString() == $st->toString())
			return;
			
		$this->storageType = $st;
		
		$backendName = $this->storageType->toString();
		
		import($backendName);
		
		$class = 'Zend_Cache_Backend_'.$backendName;
		$stObj = new $class();
		$this->backend = $stObj;
	}
	
	/**
	 * Retorna o objeto de cache backend da plataforma que está sendo utilizada
	 * e não o objeto manipulado pelo façade.
	 * 
	 * @return mixed
	 */
	public function getBackend()
	{		
		return $this->backend;
	}
	 
    /**
     * Available options
     *
     * =====> (string) cache_dir :
     * - Directory where to put the cache files
     *
     * =====> (boolean) file_locking :
     * - Enable / disable file_locking
     * - Can avoid cache corruption under bad circumstances but it doesn't work on multithread
     * webservers and on NFS filesystems for example
     *
     * =====> (boolean) read_control :
     * - Enable / disable read control
     * - If enabled, a control key is embeded in cache file and this key is compared with the one
     * calculated after the reading.
     *
     * =====> (string) read_control_type :
     * - Type of read control (only if read control is enabled). Available values are :
     *   'md5' for a md5 hash control (best but slowest)
     *   'crc32' for a crc32 hash control (lightly less safe but faster, better choice)
     *   'adler32' for an adler32 hash control (excellent choice too, faster than crc32)
     *   'strlen' for a length only test (fastest)
     *
     * =====> (int) hashed_directory_level :
     * - Hashed directory level
     * - Set the hashed directory structure level. 0 means "no hashed directory
     * structure", 1 means "one level of directory", 2 means "two levels"...
     * This option can speed up the cache only when you have many thousands of
     * cache file. Only specific benchs can help you to choose the perfect value
     * for you. Maybe, 1 or 2 is a good start.
     *
     * =====> (int) hashed_directory_umask :
     * - Umask for hashed directory structure
     *
     * =====> (string) file_name_prefix :
     * - prefix for cache files
     * - be really carefull with this option because a too generic value in a system cache dir
     *   (like /tmp) can cause disasters when cleaning the cache
     *
     * =====> (int) cache_file_umask :
     * - Umask for cache files
     * 
     * =====> (int) metatadatas_array_max_size :
     * - max size for the metadatas array (don't change this value unless you
     *   know what you are doing)
     * 
     * @param array $directives assoc of directives
     */
	public function setDirectives($directives)
	{
		if(!$this->_test())
			throw new Exception('Not Set Backend Type.');
			
		if(!is_array($directives))
			throw new Exception('Invalid Param Type. (Array Expected)');
			
		return $this->backend->setDirectives($directives);
	}
	
	/**
	 * Testa se um cache está disponível para um determinado id
	 * e, se sim, retorna o valor deste cache, senão, retorna falso.
     *
     * @param string $id cache id
     * @param boolean $doNotTestCacheValidity se true, a validação 
     * 			da existência cache não será feita
     * @return string dados cacheados (ou false)
     */
	public function load($id, $doNotTestCacheValidity = false)
	{
		if(!$this->_test())
		{
			throw new Exception('Not Set Backend Type.');
			return false;
		}
		
		return $this->backend->load($id, $doNotTestCacheValidity);
	}
	
	/**
	 * Testa se um cache está disponível ou não (para um determinado id)
     *
     * @param string $id cache id
     * @return mixed false (cache não está disponível) ou "última modificação" 
     * 					timestamp (int) do cache salvo
     */
	public function test($id)
	{
		if(!$this->_test())
		{
			throw new Exception('Not Set Backend Type.');
			return false;
		}
		
		return $this->backend->test($id);
	}
	
	/**
     * Salva um determinado texto em um cache
     *
     * @param string $data dado a ser armazenado
     * @param string $id cache id
     * @param array $tags array de strings, que servirão como tags para o 
     * 					dado a ser armazenado
     * @param int $specificLifetime se != false, seta um tempo de vida 
     * 				específico para o cache (null => tempo de vida infinito)
     * @return boolean true se não houve problema no cacheamento
     */
	public function save($data, $id, $tags = array(), $specificLifetime = false)
	{
		if(!$this->_test())
		{
			throw new Exception('Not Set Backend Type.');
			return false;
		}
		
		return $this->backend->save($data, $id, $tags, $specificLifetime);
	}
	
	/**
     * Remove um cache armazenado
     *
     * @param string $id cache id
     * @return boolean true se não houve problema (false caso contrário)
     */
	public function remove($id)
	{
		if(!$this->_test())
		{
			throw new Exception('Not Set Backend Type.');
			return false;
		}
		
		return $this->backend->remove($id);
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
	public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
	{
		if(!$this->_test())
		{
			throw new Exception('Not Set Backend Type.');
			return false;
		}
		
		return $this->backend->clean($mode, $tags);
	}
	
	/**
	 * Testa se o tipo de backend (consequentemente, uma instância de backend) já está disponível
	 * @return boolean true se backend está disponível
	 */
	private function _test()
	{
		if(is_null($this->backend))
		{
			throw new Exception('Not Set Backend Type.');
			return false;
		}
		return true;
	}
}