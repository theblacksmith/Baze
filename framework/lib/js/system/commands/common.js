if(typeof Baze !== 'undefined')
{
	Baze.provide("system.commands.common");
	
	Baze.require("system.commands.Command");
}

/**
 * @author Saulo Vallory
 * @package system.commands
 * @version 0.9
 */
Baze.CommandType = 
{
	Redirect : "Redirect",
	CallJSFunction : "CallFunction",
	EmptyCommand: "EmptyCommand"
};

// Criando e registrando o commando Redirect
Baze.registerCommand(new Baze.Command(
{
	id : Baze.CommandType.Redirect,

	name : Baze.CommandType.Redirect,

	action : function Redirect(url) {
		window.location.href = url;
	},

	argumentTypes : ["string"]
}));

// Criando e registrando o commando CallJSFunction
Baze.registerCommand(new Baze.Command(
{
	id : Baze.CommandType.CallJSFunction,

	name : Baze.CommandType.CallJSFunction,

	action : function (name, args) {
		eval(name+'('+args+')');
	},

	argumentTypes : ["string", "string"]
}));

// Criando e registrando um commando vazio que n�o recebe nada e n�o faz nada
Baze.registerCommand(new Baze.Command(
{
	id : Baze.CommandType.EmptyCommand,
		
	name : Baze.CommandType.EmptyCommand,

	action : function () {},

	argumentTypes : [],
	
	checkArgumentTypes : false
}));