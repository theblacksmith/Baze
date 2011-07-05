if(typeof Baze != "undefined") {
	Baze.provide("xml.DOM2String");
}

(function DOM2String_closure()
{
	// Node Types
	var	ELEMENT_NODE                   = 1,
		ATTRIBUTE_NODE                 = 2,
		TEXT_NODE                      = 3,
		CDATA_SECTION_NODE             = 4,
		ENTITY_REFERENCE_NODE          = 5,
		ENTITY_NODE                    = 6,
		PROCESSING_INSTRUCTION_NODE    = 7,
		COMMENT_NODE                   = 8,
		DOCUMENT_NODE                  = 9,
		DOCUMENT_TYPE_NODE             = 10,
		DOCUMENT_FRAGMENT_NODE         = 11,
		NOTATION_NODE                  = 12;

	DOM2String = 
	{
		document2String:  function document2String(doc)
		{
			var docElem = doc.documentElement;
			var str = "";
			var i = 0;
	
			str = '<?xml version="' + doc.xmlVersion + '"' + (doc.xmlEncoding !== null? 'encoding="UTF-16" ?>' : '?>' );
			str += this.node2String(docElem);
	
			return str;
		},
	
		node2String:  function node2String(node)
		{
			switch(node.nodeType)
			{
				case ELEMENT_NODE :
					return this.element2String(node);
	
				case ATTRIBUTE_NODE :
					return this.attribute2String(node);
	
				case TEXT_NODE :
					return this.textNode2String(node);
	
				case CDATA_SECTION_NODE :
					return "not implemented";
	
				case ENTITY_REFERENCE_NODE :
					return "not implemented";
	
				case ENTITY_NODE :
					return "not implemented";
	
				case PROCESSING_INSTRUCTION_NODE :
					return "not implemented";
	
				case COMMENT_NODE :
					return "not implemented";
	
				case DOCUMENT_NODE :
					return "not implemented";
	
				case DOCUMENT_TYPE_NODE :
					return "not implemented";
	
				case DOCUMENT_FRAGMENT_NODE :
					return "not implemented";
	
				case NOTATION_NODE :
					return "not implemented";
			}
		},
	
		/**
		 *	private function element2String
		 */
		element2String:  function element2String(node, includeDocElem, printEmptyAttributes)
		{
			var str = "";
			var i = 0;
	
			if(includeDocElem === false)
			{
				if(node.hasChildNodes())
				{
					for(i=0; i < node.childNodes.length; i++)
					{
						str += this.node2String(node.childNodes.item(i));
					}
				}
				return str;
			}
	
			str = "<" + node.nodeName;
	
			if(node.attributes && node.attributes.length > 0) {
				for(i=0; i < node.attributes.length; i++)
				{
					if(node.attributes.item(i).nodeValue !== "" || printEmptyAttributes === true) {
						str += " " + node.attributes.item(i).nodeName + '="' + node.attributes.item(i).nodeValue + '"'; }
				}
			}
	
			if(node.hasChildNodes()) {
				str += ">";
	
				for(i=0; i < node.childNodes.length; i++)
				{
					str += this.node2String(node.childNodes.item(i));
				}
	
				str += "</" + node.nodeName + ">";
			}
			else {
				str += "/>";
			}
	
			return str;
		},
	
		/**
		 *	private function attribute2String
		 */
		attribute2String:  function attribute2String(node)
		{
			return node.nodeName + '="' + node.nodeValue + '"';
		},
	
		/**
		 *	private function textNode2String
		 */
		textNode2String:  function textNode2String(node)
		{
			return node.nodeValue;
		}
	};

	return DOM2String;
})();