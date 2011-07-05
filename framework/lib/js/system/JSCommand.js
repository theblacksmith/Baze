/**
 * @classDescription This class implements an interface to create and manipulate functions with the command semantics 
 */

if(typeof dojo != "undefined")
{
	dojo.provide("system.JSCommand");
	
	dojo.require("system.prototype");
}

/**
 * @constructor
 */
function CommandCall(options)
{
	this.arguments = [];
	this.id = "";
	this.name = "empty";
	
	for(var v in options)
		this[v] = options[v];
}

CommandCall.prototype.call = function call()
{
	var cmd = base.getCommand(this.name);
	
	if(!cmd)
		base.raise('O commando ' + this.name + ' n�o existe ou n�o foi registrado');
		
	if(this.arguments)
		cmd.run(this.arguments);
	else
		cmd.run();
};

/**
 * @return {JSCommand}
 * @type {Object}
 * @alias JSCommand
 * @constructor
 */
JSCommand = function (options){
	
	this.checkArgumentTypes = true;
	
	for(var v in options)
		this[v] = options[v];
};

/**
 * Checks if the object type matchs the type passed in type argument. 
 * If the type argument is a string, the test is made using the typeof argument.
 * If the type argument is an object, the test is made using the instanceof argument.
 * If the type argument is an array, the test is made using the above rules for each member in the
 * array. The function will return true if the object type matches at least one of the types in the array.
 *  
 * @param {Object} obj
 * @param {Object, array, string} type
 */
function checkType(obj, type)
{
	// se type � um array, a fun��o deve retornar true caso pelo menos uma das checagens seja v�lida 
	if(typeof type == "object" && is_array(type))
	{
		for(var i=0; i < type.length; i++)	{
			if(checkType(obj, type[i])) {
				return true;
			}
		}
		
		return false;
	}
	
	if(typeof type == "string")
		return (typeof obj == type);
	
	if(typeof type == "object")
		return (obj instanceof type);
}

JSCommand.prototype = {

	/**
	 * @type {Function} The function that acttualy executes the command action
	 */
	action : null,

	/**
	 * @type {Array}
	 */
	argumentsList : [],

	/**
	 * @type {boolean}
	 */
	checkArgumentTypes : true,

	/**
	 * @type {Postback.MessageParsePhase}
	 */
	executeOn : null,

	/**
	 * @type {string}
	 */
	id : null,

	/**
	 * @type {string}
	 */
	name : null,
	
	getId : function() { return this.id; }

};

JSCommand.prototype.run = function JSCommand_run(args)
{	
	if(this.checkArgumentTypes) {
		for(var i=0; i < args.length; i++)
		{
			if(!checkType(args[i],this.argumentsList[i]))
			{
				base.raise("Erro executando commando " + this.id + '. ', new Error("Argument "+i+" should be "+(typeof this.argumentsList[i])+", "+(typeof arguments[i])+'('+arguments[i]+') given.' ));
				return;
			}
		}
	}
	
	return this.action.apply(this,args);
};

var CMD_Empty = new JSCommand(
{
	id : "Baze_EmptyCommand",
		
	name : "empty",

	action : function () {},

	argumentsList : [],
	
	checkArgumentTypes : false
});

//base.registerCommand(CMD_Empty);