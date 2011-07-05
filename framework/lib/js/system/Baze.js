/*INCLUDE>system/Prototype1.5.js</INCLUDE*/
/*INCLUDE>collections/Collection.js</INCLUDE*/

var ErrorCodes = {
	SERVER_ERROR: 1
};

/**
 * @alias Baze
 * @constructor
 */
(function Baze_closure()
{
	if(typeof Baze !== "undefined")
		return;
	
	//////////////////////////////////////////////////////////////////////////////////
	//          Private members                                                     //
	//////////////////////////////////////////////////////////////////////////////////

	/**
	 * URL to make ajax requests
	 * @private
	 */
	var POSTBACK_URL = window.location.href;
	
	/**
	 * Whether the JS API has been initialized or not
	 * @private
	 */
	var initialized = false;

	
	baze = Baze =
	{
		//////////////////////////////////////////////////////////////////////////////////
		//          Properties                                                          //
		//////////////////////////////////////////////////////////////////////////////////

		/**
		 * Wheter we are debugging (using firebug) or not
		 * @public
		 * @type {boolean}
		 */
		DEBUG: true,
		
		/**
		 * Informa se o Baze foi ou n�o inicializado
		 * @private
		 * @type {Collection}
		 */
		_serverObjs : null,
		
		/**
		 * Cole��o de commandos dispon�veis para serem executados pelo servidor via postback 
		 * @private
		 * @type {Collection}
		 */
		_commands : null,
		
		/**
		 * Array de m�dulos requeridos pelo Baze. Cada posi��o armazena um valor (boolean) verdadeiro 
		 * ou falso que indica se o m�dulo foi carrega ou n�o
		 * @private
		 * @type {Array}
		 */
		_requiredModules : [],

		/**
		 * O objeto Baze.dom apenas armazena as constantes do DOM para os tipos de n�
		 * @type {Enum}
		 */
		dom :
		{
			ELEMENT_NODE				   : 1,
			ATTRIBUTE_NODE                 : 2,
			TEXT_NODE                      : 3,
			CDATA_SECTION_NODE             : 4,
			ENTITY_REFERENCE_NODE          : 5,
			ENTITY_NODE                    : 6,
			PROCESSING_INSTRUCTION_NODE    : 7,
			COMMENT_NODE                   : 8,
			DOCUMENT_NODE                  : 9,
			DOCUMENT_TYPE_NODE             : 10,
			DOCUMENT_FRAGMENT_NODE         : 11,
			NOTATION_NODE                  : 12
		},
		
		errorLog : [],
		

		//////////////////////////////////////////////////////////////////////////////////
		//          Private methods                                                      //
		//////////////////////////////////////////////////////////////////////////////////
		
		/**
		 * Function _findServerObjects
		 *
		 * @param {HTMLElement} node
		 * @param {Collection} collection
		 * @private
		 */
		_findServerObjects_old : function _findServerObjects(node, collection)
		{
			if (node.attributes["php:runat"] && node.attributes["php:runat"].nodeValue == "server" && node.attributes["phpclass"])
			{
				var phpClass = node.getAttribute("phpclass");
			
				//var obj = new ServerObject(node);
				var obj = Component.factory(phpClass, node);
	
				if(!obj) {
					Baze.raise("Constructor for component " + phpClass + " not found.(Baze findServerObjects)", new Error());
					return;
				}
	
				obj.onPropertyChange.addListener(ClientViewState.objectChanged.bind(ClientViewState));
				
				if(Baze.isContainer(obj)) {
					obj.onChildAdd.addListener(ClientViewState.objectChanged.bind(ClientViewState));
					obj.onChildRemove.addListener(ClientViewState.objectChanged.bind(ClientViewState));
				}
	
				collection.add(obj.getId(), obj);
			}
	
			// find server objects in children
			if(node.hasChildNodes())
			{
				for (var i = 0; i < node.childNodes.length; i++)
				{
					if(node.childNodes[i].nodeType == Baze.dom.ELEMENT_NODE)
					{
						this._findServerObjects(node.childNodes[i], collection);
					}
				}
			}
		},
		
		_findServerObjects: function Baze_findServerObjects()
		{
			Sizzle('[php\\:runat="server"]').each(function(el, index) {
				var phpClass = el.getAttribute("php:class");
				
				var obj = Component.factory(phpClass, el);
	
				if(!obj) {
					Baze.raise("Constructor for component " + phpClass + " not found.(Baze findServerObjects)", new Error());
					return;
				}
	
				obj.onPropertyChange.addListener(ClientViewState.objectChanged.bind(ClientViewState));
				
				if(Baze.isContainer(obj)) {
					obj.onChildAdd.addListener(ClientViewState.objectChanged.bind(ClientViewState));
					obj.onChildRemove.addListener(ClientViewState.objectChanged.bind(ClientViewState));
				}
	
				Baze._serverObjs.add(obj.getId(), obj);
			});
		},
		
		//////////////////////////////////////////////////////////////////////////////////
		//          Public methods                                                      //
		//////////////////////////////////////////////////////////////////////////////////
	
		initialize: function initialize()
		{
			if(initialized) {
				return;
			}
			
			console.info("Initializing Baze");

			// loading modules
			var lang = (typeof CURRENT_LANG != "undefined"? CURRENT_LANG : "pt-br");
		
			Baze.require("i18n." + lang);
			Baze.require("system.collections.Collection");
			
			if(this._serverObjs === null)
				this._serverObjs = new Collection();
					
			Baze.require("system.viewState.ClientViewState");
			Baze.require("system.Environment");
			Baze.require("system.Postback");
			//Baze.require("web.Component");
			//Baze.require("web.package-src");
			
			this.env = this.environment = new Environment();
	
			Baze.Loading.init();
			
			Ajax.Responders.register(
			{
				onCreate: function(){},
		
				onException : function(xhr, e) {
					Baze.raise(e.message, xhr);
				},
		
				onComplete: function(){}
			});
			
			this.initializing = true;
			this._findServerObjects(document.documentElement, Baze._serverObjs);
			this.initializing = false;
			
			// choosing faster JSON evaluation and stringify methods
			if ( typeof JSON !== 'undefined' ) {
				this.evaluate = JSON.parse;
			}
			else if ( browser.gecko ) {
				this.evaluate = function evaluate(text) {
					return (new Function( "return " + text ))();
				}
			}
			else {
				this.evaluate = function evaluate(text) {
					return eval( "(" + text + ")" );
				}
			}
			
			initialized = true;
		},
		
		onLoadComponents : function onLoadComponents() {
			
		},
		
		/**
		 * Evaluates a JSON string using the fastest method available.
		 * This method is set on initialize
		 * @param {String} text The string to be evaluated
		 */
		evaluate: null,

		/**
		 * Converts an object to JSON using the fastest method available.
		 * @param {Object} obj The object to be stringified
		 */
		stringify: JSON.stringify,
			
		/**
		 * 
		 * @param {String} path
		 * @param {Object} options Options: asynchronous (default: false), forceRetry (default: false), onSuccess (defeaul: null), onFailure (defeaul: null)
		 */
		require: function require(path, options)
		{			
			var opt = {
				asynchronous : false,
				forceRetry : false,
				onSuccess : function (){},
				onFailure : function (){}
			};
			
			for(var o in options)
				opt[o] = options[o];
			
			if(typeof Baze._requiredModules[path] !== "undefined" && 
				(Baze._requiredModules[path] == true || opt.forceRetry) )
			{
				opt.onSuccess();
				return;
			}
			
			Baze._requiredModules[path] = false;
			var url = LIB_ROOT + '/lib/js/' + path.replace(/\./g, '/') + '.js';
			
			function evalAndFeedback(xhr)
			{
				try
				{
					eval(xhr.responseText);

					if(Baze._requiredModules[path])
						opt.onSuccess();
				}
				catch(ex)
				{
					Baze.raise("Erro executando javascript do m�dulo " + path, ex, {xhr: reqAjax});
				}
			}
			
			var reqAjax = new Ajax.Request(url,
			{
				asynchronous : opt.asynchronous,
				onException : function (req, ex) { Baze.raise("Erro requerindo o m�dulo " + path, ex); },
				onSuccess : evalAndFeedback,
				onFailure : opt.onFailure
			});
		},
		
		provide: function provide(path) {
			Baze._requiredModules[path] = true;
		},
	
		/**
		 * @return {Collection}
		 */
		getServerObjects: function getServerObjects()
		{
			return Baze._serverObjs;
		},

		/**
		 *
		 * @param {Component} comp
		 */
		addComponent: function addComponent(comp)
		{	
			comp.onPropertyChange.addListener(ClientViewState.objectChanged.bind(ClientViewState));
	
			if(Baze.isContainer(comp)) {
				comp.onChildAdd.addListener(ClientViewState.objectChanged.bind(ClientViewState));
				comp.onChildRemove.addListener(ClientViewState.objectChanged.bind(ClientViewState));
			}
			
			if(!Baze.initializing)
				ClientViewState.addNewObject(comp);
			
			return Baze._serverObjs.add(comp.getId(), comp);
		},

		/**
		 *
		 * @param {String} id
		 */
		getComponentById: function getComponentById(id)
		{
			return Baze._serverObjs.get(id);
		},

		/**
		 * Uses Sizzle to query components list using a CSS selector
		 * @param {String} query
		 */
		queryComponents: function queryComponents(q)
		{
			var list = [];
			Sizzle.matches(q, Sizzle('[php\\:runat="server"]')).each(function(el, index) {
				list.push($C(el.id));
			});
			return list;
		},

		/**
		 *
		 * @param {String} id
		 * @return {Boolean}
		 */
		removeComponent: function removeServerObjeect(id)
		{
			var comp = Baze._serverObjs.remove(id);
			var node = null;
	
			if(comp === false) {
				return false;
			}
	
			if((node = comp.realElement) != false)
			{
				var parentEl = node.parentNode;
				parentEl.removeChild(comp.realElement);
			}
			
			ClientViewState.addRemovedObject(comp);
	
			return true;
		},

		/**
		 * FUNCTION doPostBack
		 *
		 * @param {String} e Event that fired the postback
		 * @param {Array} args Event arguments
		 */
		doPostBack: function doPostBack(e, args)
		{			
			this.initialize();	// a fun��o initialize n�o executa seu c�digo duas vezes
			
			Baze.Loading.show();
	
			if(!e) e = window.event;
			
			if(e.type == "submit") {
				Baze.require("web.form.Form");
				Baze.submitFiles(args.targetForm);
			}
	
			var pb = new Postback(POSTBACK_URL);
			window.lastPostBack = pb;
	
			var clientMessage =  Baze.stringify({
				EvtMsg: ClientViewState.getEventMessage(e),
				SyncMsg: ClientViewState.getSyncMessage()
			});
	
			pb.onReceiveMessage.addListener(Baze.Loading.hide());
	
			pb.send(clientMessage);
	
			if(typeof args.preventDefault != 'undefined' && args.preventDefault)
			{
				if(typeof e.preventDefault != 'undefined')
					e.preventDefault();
				else
					e.returnValue = false;
					
				return false;
			}
			
			return true;
		},

		synchronize: function synchronize()
		{
			this.initialize();
			
			Baze.Loading.show();
	
			var pb = new Postback(POSTBACK_URL);
			window.lastPostBack = pb;
	
			var clientMessage =  "<?xml version=\"1.0\" encoding=\"UTF-8\"?><messages>";
			clientMessage += ClientViewState.getSyncMessage();
			clientMessage +=  "</messages>";
	
			pb.onReceiveMessage.addListener(Baze.Loading.hide());
	
			pb.send(clientMessage);
		},

		/**
		 *
		 * @param {HTMLElement} object
		 * @param {String} event
		 * @param {Function} func
		 */
		addEventListener: function addEventListener(object, event, func) {
			Event.observe(object, event, func);
		},

		findFileUploads : function findFileUploads(f)
		{
			var fileUploads = [];
			
			var results = f.getElementsByTagName("input");
			
			for(var i = 0; i< results.length; i++)
			{
				if (results[i].type == 'file')
				{
					fileUploads[fileUploads.length] = results[i];
				}
			}
			
			return fileUploads;
		},
	
		/**
		 *
		 * @param {HTMLFormElement} f
		 */
		submitFiles: function submitFiles(f) {
	
			var upFields = Baze.findFileUploads(f); 
			var iframe;
	
			if(upFields.length === 0) {
				return; }
	
			// adding hidden fields to link name and id
			for(var i=0; i < upFields.length; i++)
			{
				if(upFields[i].name != upFields[i].id)
				{
					var hid = document.createElement("input");
					hid.type = "hidden";
					hid.name = upFields[i].name + "ID";
					hid.value = upFields[i].id;
	
					f.appendChild(hid);
				}
	
			}
	
			if(typeof this.submitIframe === "undefined")
			{
				//iframe = document.createElement("iframe");
				iframe = window.open("", "_submitIframe", 'toolbar=0,location=0,statusbar=0,menubar=0,width=500,height=500,left = 262,top = 134');
				iframe.id = "_submitIframe";
				iframe.name = "_submitIframe";
				//iframe.style.display = "none";
	
				//document.body.appendChild(iframe);
			}
			else {
				iframe = this.submitIframe;
			}
	
			var oldTarget = f.target;
			f.target = "_submitIframe";
	
			f.submit();
	
			f.target = oldTarget;
	
			iframe.loaded = false;
			iframe.onload = function() {alert('submit finished. ' + window.location.href); window.loaded = true;};
	
			var maxTime = 3000;
			var elapsedTime = 0;
	
			while(!iframe.loaded && elapsedTime < maxTime) {
				wait(500);
				elapsedTime += 500;
			}
	
			this.submitIframe = iframe;
			return iframe.loaded;
		},

		raise: function raise(message, exception, extraInfo) {
			var canImakeDebuggerStopHere = "yes";
			var msg = message;
			
			if(exception && typeof exception.message != "undefined")
				msg += ': ' + exception.message;

			console.error(msg);
			console.trace();
			
			Baze.errorLog.push({message: message, exception : exception, info : extraInfo});
		},

		// JS Commands mannagement

		/**
		 * @param {string} id
		 * @return {Baze.Command} 
		 */
		getCommand: function getCommand(id) {
			
			var cmd = this._commands.get(id);
			
			if(!cmd)
				Baze.raise('O commando ' + id + ' nao existe ou nao foi registrado');
			
			return cmd;
		},
	
		/**
		 * @memberOf {Baze}
		 * @alias registerCommand
		 * @param {Baze.Command} command
		 */
		registerCommand: function Baze_registerCommand(command)
		{
			if(typeof this._commands == "undefined" || this._commands == null)
				this._commands = new Collection();
				
			this._commands.add(command.getId(), command);
		},

		/**
		 * @param {Baze.Command} command
		 */
		unregisterCommand: function Baze_unregisterCommand(command)
		{
			if(typeof command == "object") {
				if(!(command instanceof Baze.Command))
					Baze.raise("", new Error("Baze.unregisterCommand expects an Baze.Command or string as only parameter, "+(typeof command)+" given."));
				else
					command = command.getId();
			}
	
			this._commands.remove(command);
		},
		
		/**
		 * Diz se o objeto � uma instancia de Component ou de uma classe que extende Component
		 * @param {Object} object
		 */
		isComponent : function isComponet(object) 
		{			
			if(typeof object == "undefined" || object == null)
				return false;
				
			return (typeof object != "undefined" && typeof object.isComponent == "boolean" && object.isComponent == true);
		},
		
		/**
		 * Diz se o objeto � uma instancia de Container ou de uma classe que extende Container
		 * @param {Object} object
		 */
		isContainer : function isContainer(object) 
		{	
			if(typeof object == "undefined" || object == null)
				return false;
				
			return (typeof object.isContainer == "boolean" && object.isContainer == true);
		},
		
		/**
		 * Diz se o objeto � uma instancia de VisualComponent ou de uma classe que extende VisualComponent
		 * @param {Object} object
		 */
		isVisualComponent : function isVisualComponet(object) 
		{	
			if(typeof object == "undefined" || object == null)
				return false;

			return (typeof object != "undefined" && typeof object.isVisualComponent == "boolean" && object.isVisualComponent == true);
		}
	};
	
	$C = Baze.getComponentById;
	$$C = Baze.queryComponents;
	

	Event.observe(document, "dom:loaded", function() {
		Baze.initialize();
	});
})();