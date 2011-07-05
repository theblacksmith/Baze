if(typeof Baze != "undefined")
{
	Baze.provide("xml.XSLT");
	Baze.require("xml.DOM2String");
	Baze.require("external.dojo");
}

/**
 * @class XSLT
 * @alias XSLT
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires xml.DOM2String
 * 
 * @constructor
 * @param {String} xslDocURL URL da stylesheet a ser usada na transforma??o
 */
Baze.XSLT = function XSLT(xslDocURL)
{
	/**
	 * @memberOf Baze.XSLT
	 * @private
	 * @type {Boolean}
	 */
	var _stylesheetImported = false;

	this.xslURL = xslDocURL ? xslDocURL : "";
	this.xsltProcessor = this.getXSLTObj();

	// privilegied methods //
	this.isStylesheetImported = function () { return _stylesheetImported; };

	if(xslDocURL) {
		this.importXSLT(xslDocURL);
	}
};

Object.extend(Baze.XSLT.prototype,
{
	/**
	 * @private
	 * @type {String} URL do arquivo xsl
	 */
	xslURL : "",

	/**
	 * @var DomDocument - A reference do the style sheet document
	 * @private
	 */
	xslRef : null,

	/**
	 * @var XSLTProcessor Motor xslt suportado pelo browser atual
	 * @public
	 */
	xsltProcessor : null,

	/**
	 * Function importXSLT() <br>
	 *
	 * @param string xslURL - URL do arquivo xsl
	 * @return boolean
	 * @public
	 * @author Saulo Vallory
	 * @version 0.9
	 */
	importXSLT: function importXSLT(xslURL)
	{
		if(!xslURL) {
			return false;
		}

		// Load the xsl file using synchronous (third param is set to false) XMLHttpRequest
		if (window.ActiveXObject)
		{
			this.xsltProcessor.load(xslURL);
		}
		else
		{
			var myXHR = new Ajax.Request(xslURL, { onComplete : _loadStylesheetInProcessor.bind(this) });
		}

		return true;
	},

	/**
	 * Function process() <br>
	 *
	 * @param string xslURL - URL do arquivo xsl
	 * @return string
	 * @public
	 * @author Saulo Vallory
	 * @version 0.9
	 */
	process: function process(xmlDoc)
	{
		// create a new XML document in memory
		var d2s = new DOM2String();

		var sourceDoc = d2s.document2String(xmlDoc);

		if(sourceDoc === "") {
			return ""; }

		var fragment = null;
		if (window.ActiveXObject)
		{
			fragment = xmlDoc.transformNode(this.xsltProcessor);

			fragment = dojo.dom.createDocumentFromText(fragment, "text/xml");
		}
		else
		{
			fragment = this.xsltProcessor.transformToDocument(xmlDoc);
		}

		if(fragment.documentElement === null) {
			return ""; }

		return d2s.element2String(fragment.documentElement, true);
	},
	
	/**
	 * Function process() <br>
	 *
	 * @param string xslURL - URL do arquivo xsl
	 * @return string
	 * @public
	 * @author Saulo Vallory - 21/06/2006
	 * @version 0.9
	 */
	getXSLTObj: function getXSLTObj()
	{
		// Internet Explorer
		if (window.ActiveXObject)
		{
			//Carregando o Arquivo XSL
			var objXsl = new ActiveXObject("Microsoft.XMLDOM");
			objXsl.async = false;

			return objXsl;
		}

		if(XSLTProcessor)
		{
			return new XSLTProcessor();
		}

		return null;
	},

	/**
	 * Function loadStylesheetInProcessor() <br>
	 *   Loads the stylesheet in the processor when the xhr state
	 *   is setted to ready.
	 *
	 * @private
	 * @author Saulo Vallory
	 * @version 0.9
	 * 
	 * @param XMLHTTPRequest xhr
	 */
	_loadStylesheetInProcessor: function _loadStylesheetInProcessor(xhr)
	{
		if(xhr.responseXML === null) {
			alert("Error importing xslt."); }

		this.xslRef = xhr.responseXML;

		// Finally import the .xsl
		this.xsltProcessor.importStylesheet(this.xslRef);
	}
});