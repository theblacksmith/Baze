if(typeof Baze != "undefined") {
	Baze.provide("system.Postback");

	Baze.require("system.viewState.ClientViewState");
	Baze.require("system.Event");
}

/**
 * @class Postbak
 * @alias Postback
 * @classDescription A classe Postback � respons�vel por montar a 
 * mensagem que � enviada ao servidor e interpretar a resposta. 
 * As requisi��es s�o enviadas utilizando a classe Ajax da 
 * biblioteca Prototype. Na montagem da mensagem de sincroniza��o
 * as altera��es da interface s�o obtidas da classe ClientViewState.
 * 
 * @requires system.ClientViewState
 * 
 * @constructor
 * @param {Object} url
 * @param {Object} urlArgs
 * @param {Object} method
 */
Postback = function Postback(url, urlArgs, method) 
{	
	this.url = url;
	
	this.urlArgs = urlArgs;
	
	this.method = method;

	// fired after the message is sent
	this.onSendMessage = new Baze.Event();

	// fired before message validation
	this.onReceiveMessage = new Baze.Event();

	// fired before message validation and before message processing
	this.onBeforeProcessMessage = new Baze.Event();

	// fired after message processing
	this.onAfterProcessMessage = new Baze.Event();
};

Object.extend(Postback.prototype, 
{	
	/**
	 * @type {Enum}
	 */
	commands: {
		BeforeCreateObjects : [],
		BeforeModifyObjects : [],
		BeforeDeleteObjects : [],
		OnMessageEnd : []
	},
	
	/**
	 * Function receiveServerMessage
	 *
	 * 		O xml da volta nao pode ter comentario
	 *
	 * @param XMLHTTPRequest xhr
	 * @param Object xjson
	 * @return boolean
	 */
	receiveServerMessage: function receiveServerMessage(/* XMLHTTPRequest */ xhr, /* Object */ xjson)
	{
		this.onReceiveMessage.raise();
	
		var resp = xhr.responseText;
		
		if(resp === null)
		{
			if(Baze.DEBUG) {
				console.group(__("Response"));
					console.log(__("The server returned an empty response."));
					Baze.raise(__("The message returned by the server is invalid."), new Error(), {error : ErrorCodes.SERVER_ERROR, message : __("The server returned an empty response.")});
				console.groupEnd();
			}
			else
			{
				alert(__("An error occured on the server"));
			}
			
			return false;
		}
		
		try {
			var jsonResp = Baze.evaluate(resp);
		}
		catch(ex)
		{
			if(Baze.DEBUG) {
				console.group(__("Response"));
					console.log(resp);
					Baze.raise(__("The message returned by the server is invalid."), ex, {error : ErrorCodes.SERVER_ERROR, message : resp});
				console.groupEnd();
			}
			else
			{
				alert(__("An error occured on the server"));
			}
			
			return false;
		}
	
		// logging
		console.group("Response");
			console.log(xhr.responseText);
		console.groupEnd();
		console.groupEnd();
			
		// Disparando o evento onBeforeProcessMessage
		this.onBeforeProcessMessage.raise();
	
		// carregando os commandos
		if(typeof jsonResp.CmdMsg != 'undefined')
			this.loadCommands(jsonResp.CmdMsg);
		
		// Atualizando os componentes
		if(typeof jsonResp.SyncMsg != 'undefined')
			this.processMessage(jsonResp.SyncMsg);
	
		ClientViewState.setSynchronized();
	
		this.onAfterProcessMessage.raise();

		this.commands.BeforeCreateObjects = [];
		this.commands.BeforeModifyObjects = [];
		this.commands.BeforeDeleteObjects = [];
		this.commands.OnMessageEnd = [];

		return true;
	},

	/**
	 * 
	 * @param {Document} xmlDoc
	 */
	loadCommands: function loadCommands(xmlDoc) 
	{	
		// Executando comandos na mensagem
		commands = xmlDoc.getElementsByTagName('command');
	
		for (i = 0; i < commands.length; i++)
		{
			var act = commands[i].getAttribute("name");
			var executeOn = commands[i].getAttribute("executeon");
			var parmsNodes = commands[i].getElementsByTagName('param');
			var parms = [];
	
			for (j = 0; j < parmsNodes.length; j++)
			{
				var param = parmsNodes[j].getAttribute('name');
				var value = parmsNodes[j].textContent || parmsNodes[j].text;
				parms.push(value);
			}

			var cmd = Baze.getCommand(act);
		
			var commCall = cmd.prepare(parms);
					
			switch(Number(executeOn))
			{
				case Postback.MessageParsePhase.BeforeCreateObjects :
					this.commands.BeforeCreateObjects.push(commCall);
				break;
				
				case Postback.MessageParsePhase.BeforeModifyObjects : 
					this.commands.BeforeModifyObjects.push(commCall);
				break;
				
				case Postback.MessageParsePhase.BeforeDeleteObjects :
					this.commands.BeforeDeleteObjects.push(commCall);
				break;
				
				case Postback.MessageParsePhase.OnMessageEnd :
					this.commands.OnMessageEnd.push(commCall);
				break;
			}
		}
	},

	/**
	 * 
	 * @param {SyncMsg} msg
	 */
	processMessage: function processMessage(msg)
	{	
		for(var i=0; i < this.commands.BeforeDeleteObjects.length; i++)
			this.commands.BeforeDeleteObjects[i].run();
		
		/*
		 * Remove objects
		 */
	
		if(msg.removedObjects.length > 0) {
			ClientViewState.removeComponents(msg.removedObjects);
		}
	
		// Run commands
		for(var i=0; i < this.commands.BeforeCreateObjects.length; i++)
			this.commands.BeforeCreateObjects[i].run();
	
		/*
		 * Get new objects nodeList
		 */	
		if(msg.newObjects.length > 0) {
			ClientViewState.createComponents(msg.newObjects);
		}

		// Run commands
		for(var i=0; i < this.commands.BeforeModifyObjects.length; i++)
			this.commands.BeforeModifyObjects[i].run();
			
		/*
		 * Get modified objects nodeList
		 */
		if(msg.modifiedObjects.length > 0) {
			ClientViewState.updateComponents(msg.modifiedObjects);
		}

		// Run commands
		for(var i=0; i < this.commands.OnMessageEnd.length; i++)
			this.commands.OnMessageEnd[i].run();
	},
	
	/**
	 * @method send
	 * @memberOf {PostBack}
	 * @alias PostBack.PostbackEventType
	 * @type {Enum}
	 */
	send : function send(message)
	{
		this.myAjax = new Ajax.Request(this.url,
		{
			//method: this.method,
			postBody: "__clientMessage=" + encodeURI(message) + (this.urlArgs ? "&" + this.urlArgs : "") + "&pageId="+PAGE_ID,
			requestHeaders: ["Content-Type", "application/x-www-form-urlencoded"],
			onException : function (xhr, ex) { Baze.raise("Postback exception", ex, {xhr:xhr}); },
			onComplete: this.receiveServerMessage.bind(this)
		});
		
		console.group("NeoBaze postback");
			console.group("Post");
				console.log(message);
			console.groupEnd();
		
	
		this.onSendMessage.raise();
	}
});

/**
 * @memberOf {PostBack}
 * @alias PostBack.PostbackEventType
 * @type {Enum}
 */
Postback.PostbackEventType = {

	OnSendMessage : 500,

	OnReceiveMessage : 501,

	OnBeforeProcessMessage : 502,

	OnAfterProcessMessage : 503
};

/**
 * @memberOf {PostBack}
 * @alias PostBack.MessageParsePhase
 * @type {Enum}
 */
Postback.MessageParsePhase = 
{
	BeforeCreateObjects : 701,
	
	BeforeModifyObjects : 702,

	BeforeDeleteObjects : 703,

	OnMessageEnd : 704
};