if(typeof Baze != "undefined") {
	Baze.provide("xml.XMLDebug");
}

if(typeof Baze !== "undefined") {
	Baze.require("xml.domlib"); }

function XMLDebugNode(node)
{
	this.node = node;

	this.attributes  = node.attributes;
	this.firstChild  = node.firstChild;
	this.localName   = node.localName;
	this.prefix      = node.prefix;
	this.textContent = node.textContent;

	for (var i=0; i < node.childNodes.length; i++)
	{
		this.childNodes[i] = new XMLDebugNode(node.childNodes[i]);
	}
}

function XMLDebugDoc(doc)
{
	this.doc = doc;

	this.baseURI = doc.baseURI;

	this.documentElement = new XMLDebugNode(doc.documentElement);

	for (var i=0; i < doc.childNodes.length; i++)
	{
		this.childNodes[i] = new XMLDebugNode(doc.childNodes[i]);
	}
}