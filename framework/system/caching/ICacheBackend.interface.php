<?php

interface ICacheBackend
{
	/**
	 * Retorna o objeto de cache backend da plataforma que está sendo utilizada
	 * e não o objeto manipulado pelo façade.
	 * 
	 * @return mixed
	 */
	public function getBackend();
	
	/**
     * Informar as diretivas de configuração do backend.
     *
     * @param array $directives assoc of directives
     */
    public function setDirectives($directives);

    /**
	 * Testa se um cache está disponível para um determinado id
	 * e, se sim, retorna o valor deste cache, senão, retorna falso.
     *
     * @param string $id cache id
     * @param boolean $doNotTestCacheValidity se true, a validação 
     * 			da existência cache não será feita
     * @return string dados cacheados (ou false)
     */
    public function load($id, $doNotTestCacheValidity = false);

    /**
	 * Testa se um cache está disponível ou não (para um determinado id)
     *
     * @param string $id cache id
     * @return mixed false (cache não está disponível) ou "última modificação" 
     * 					timestamp (int) do cache salvo
     */
    public function test($id);

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
    public function save($data, $id, $tags = array(), $specificLifetime = false);

    /**
     * Remove um cache armazenado
     *
     * @param string $id cache id
     * @return boolean true se não houve problema (false caso contrário)
     */
    public function remove($id);

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
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array());
	
}