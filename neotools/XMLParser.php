<?php

class XMLParser {
	
	/**
	 * Documento XML a ser parseado
	 *
	 * @var DOMDocument
	 */
	protected $document;
	
	/**
	 * Método construtor padrão. Opcionalmente recebe o caminho do arquivo a ser parseado
	 *
	 * @param string $filePath Caminho do arquivo a ser parseado
	 * @param boolean $preserveWhiteSpace Diz se os espaços devem ser preservados. O padrão é true (preservar os espaços). 
	 */
	public function __constructor($filePath=null, $preserveWhiteSpace = true)
	{
		$doc = new DOMDocument();
		$doc->preserveWhiteSpace = $preserveWhiteSpace;
		
		if($filePath != null)
		{
			if(!file_exists($filePath))
				throw new FileNotFoundException("");
				
			$doc->load($filePath);
			$xPath = new DOMXPath($doc);
		}
	}
	
	/**
	 * Carrega no parser o XML a partir de um arquivo 
	 *
	 * @param string $path Caminho do arquivo a ser parseado
	 * @throws FileNotFoundException
	 * @throws XMLException
	 */
	public function load($path)
	{
		
	}
	
	/**
	 * Carrega no parser o XML a partir de uma string
	 *
	 * @param string $xml String contendo o XML a ser parseado
	 * @throws XMLException
	 */
	public function loadXML($xml)
	{
		
	}
}