if(typeof Baze != "undefined")
{
	Baze.provide("system.commands.Command");
	
	// Assumir que j� est� inclu�do
	// Baze.require("system.jext");
}

/**
 * @class PreparedCommand
 * @alias PreparedCommand
 * @classDescription Esta classe implementa uma interface para 
 * criar e manipular commandos
 * 
 * @constructor
 * @param {Command} cmd
 * @param {Array} args
 */
var PreparedCommand = function PreparedCommand(cmd, args)
{
	this.command = cmd;
	
	if(typeof args !== 'undefined')
		this.arguments = args;
};

Object.extend(PreparedCommand.prototype, {
	
	/**
	 * @type {Command}
	 */
	command: null,
	
	/**
	 * @type {Array}
	 */
	arguments: [],
	
	/**
	 * @method run
	 * Executa o comando que gerou esse PreparedCommand passando os 
	 * argumentos utilizados na constru��o deste objeto
	 */
	run: function run() {
		if(this.arguments) {
			this.command.run(this.arguments);
		}
		else {
			this.command.run();
		}
	}
});


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

/**
 * @class Command
 * @alias Command
 * @namespace Baze
 * 
 * @constructor
 * @param {Object} options Objeto com as propriedades que devem ser definidas pelo construtor
 */
Baze.Command = function Command(options){
	
	this.checkArgumentTypes = true;
	
	for(var v in options)
		this[v] = options[v];
};

Object.extend(Baze.Command.prototype, {

	/**
	 * @type {Function} The function that acttualy executes the command action
	 */
	action : null,

	/**
	 * @type {Array}
	 */
	argumentTypes : [],

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
	 * @private
	 */
	id : null,

	/**
	 * @type {string}
	 */
	name : null,
	
	getId : function() { return this.id; },
	
	run: function Command_run(args)
	{	
		if(this.checkArgumentTypes) {
			var len = this.argumentTypes.length;
			for(var i=0; i < len; i++)
			{
				if(!checkType(args[i],this.argumentTypes[i]))
				{
					Baze.raise("Erro executando commando " + this.id + '. ', new Error("Argument "+i+" should be "+(typeof this.argumentTypes[i])+", "+(typeof arguments[i])+'('+arguments[i]+') given.' ));
					return;
				}
			}
		}
		
		return this.action.apply(this,args);
	},
	
	/**
	 * Cria um PreparedCommand para este commando com os argumentos
	 * passados
	 * 
	 * @method prepare
	 * @param {Array} args
	 */
	prepare: function prepare(args)
	{
		if(this.checkArgumentTypes) {
			var len = this.argumentTypes.length;
			for(var i=0; i < len; i++)
			{
				if(!checkType(args[i],this.argumentTypes[i]))
				{
					Baze.raise("Erro executando commando " + this.id + '. ', new Error("Argument "+i+" should be "+(typeof this.argumentTypes[i])+", "+(typeof arguments[i])+'('+arguments[i]+') given.' ));
					return;
				}
			}
		}
		
		return new PreparedCommand(this, args);
	}
});