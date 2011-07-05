
if (!("console" in window) || !("firebug" in console))
{
    var names = ["log", "debug", "info", "warn", "error", "assert", "dir", "dirxml",
    "group", "groupEnd", "time", "timeEnd", "count", "trace", "profile", "profileEnd"];

    window.console = {};
    for (var i = 0; i < names.length; ++i)
        window.console[names[i]] = function() {}
}
if(typeof Baze !== 'undefined')
{
	Baze.provide("system.lang");
}

/**
 * @author Saulo Vallory
 * 
 * Esses m�todos foram emprestados da Yahoo UI Library e ent�o
 * adaptados para o estilo php.
 */

/**
 * Determines whether or not the provided object is an array
 * @alias is_array
 * @param {any} obj The object being testing
 * @return Boolean
 */
is_array = function(obj) {
    if (obj instanceof Array) {
        return true;
    } else {
        return is_object(obj) && obj.constructor == Array;
    }
};

/**
 * Determines whether or not the provided object is a boolean
 * @alias is_boolean
 * @param {any} obj The object being testing
 * @return Boolean
 */
is_boolean = function(obj) {
    return typeof obj == 'boolean';
};

/**
 * Determines whether or not the provided object is a function
 * @alias is_function
 * @param {any} obj The object being testing
 * @return Boolean
 */
is_function = function(obj) {
    return typeof obj == 'function';
};
    
/**
 * Determines whether or not the provided object is null
 * @alias is_null
 * @param {any} obj The object being testing
 * @return Boolean
 */
is_null = function(obj) {
    return obj === null;
};
    
/**
 * Determines whether or not the provided object is a legal number
 * @alias is_number
 * @param {any} obj The object being testing
 * @return Boolean
 */
is_number = function(obj) {
    return typeof obj == 'number' && isFinite(obj);
};
  
/**
 * Determines whether or not the provided object is of type object
 * or function
 * @alias is_object
 * @param {any} obj The object being testing
 * @return Boolean
 */  
is_object = function(obj) {
    return obj && (typeof obj == 'object' || is_function(obj));
};
    
/**
 * Determines whether or not the provided object is a string
 * @alias is_string
 * @param {any} obj The object being testing
 * @return Boolean
 */
is_string = function(obj) {
    return typeof obj == 'string';
},
    
/**
 * Determines whether or not the provided object is undefined
 * @alias is_undefined
 * @param {any} obj The object being testing
 * @return Boolean
 */
is_undefined = function(obj) {
    return typeof obj == 'undefined';
};

/**
 * Determines whether or not the provided object is set
 * If the evaluate parameter is true, the 'obj' parameter 
 * must be the name of object to test
 * @alias isset
 * @param {any} obj The object being testing
 * @param {boolean} evaluate Evaluates obj parameter to get the real object 
 * @return Boolean
 */
isset = function(obj, evaluate) {
	if(typeof evaluate !== 'undefined')
	{
		if(!is_string(obj))
			return false;	// TODO: throw an exception
			
		eval('var set = (typeof '+obj.toString()+' !== "undefined");');
		return set;
	}
	else
		return typeof obj !== 'undefined';
};

/**
 * De forma semelhante a fun��o is_a do PHP, essa fun��o diz se o objeto obj 
 * � uma inst�ncia da classe "class_name"
 * @param {Object} obj
 * @param {String} class_name
 */
is_a : function is_a(obj, class_name)
{
	if(typeof obj == "object" && typeof constructor == "string")
	{
		return (typeof obj.constructor == 'function' && obj.constructor.name == class_name);
	}
	
	//TODO: throw an exception
	return false;
}


Object.extend(String.prototype, 
	{
		isUpperCased : function() {
			return (this.match(/[a-z]/) == null);
		},

		isLowerCased : function() {
			return (this.match(/[A-Z]/) == null);
		}
	}
);
			

/*
http://www.JSON.org/json2.js
2011-02-23

Public Domain.

NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.

See http://www.JSON.org/js.html


This code should be minified before deployment.
See http://javascript.crockford.com/jsmin.html

USE YOUR OWN COPY. IT IS EXTREMELY UNWISE TO LOAD CODE FROM SERVERS YOU DO
NOT CONTROL.


This file creates a global JSON object containing two methods: stringify
and parse.

    JSON.stringify(value, replacer, space)
        value       any JavaScript value, usually an object or array.

        replacer    an optional parameter that determines how object
                    values are stringified for objects. It can be a
                    function or an array of strings.

        space       an optional parameter that specifies the indentation
                    of nested structures. If it is omitted, the text will
                    be packed without extra whitespace. If it is a number,
                    it will specify the number of spaces to indent at each
                    level. If it is a string (such as '\t' or '&nbsp;'),
                    it contains the characters used to indent at each level.

        This method produces a JSON text from a JavaScript value.

        When an object value is found, if the object contains a toJSON
        method, its toJSON method will be called and the result will be
        stringified. A toJSON method does not serialize: it returns the
        value represented by the name/value pair that should be serialized,
        or undefined if nothing should be serialized. The toJSON method
        will be passed the key associated with the value, and this will be
        bound to the value

        For example, this would serialize Dates as ISO strings.

            Date.prototype.toJSON = function (key) {
                function f(n) {
                    // Format integers to have at least two digits.
                    return n < 10 ? '0' + n : n;
                }

                return this.getUTCFullYear()   + '-' +
                     f(this.getUTCMonth() + 1) + '-' +
                     f(this.getUTCDate())      + 'T' +
                     f(this.getUTCHours())     + ':' +
                     f(this.getUTCMinutes())   + ':' +
                     f(this.getUTCSeconds())   + 'Z';
            };

        You can provide an optional replacer method. It will be passed the
        key and value of each member, with this bound to the containing
        object. The value that is returned from your method will be
        serialized. If your method returns undefined, then the member will
        be excluded from the serialization.

        If the replacer parameter is an array of strings, then it will be
        used to select the members to be serialized. It filters the results
        such that only members with keys listed in the replacer array are
        stringified.

        Values that do not have JSON representations, such as undefined or
        functions, will not be serialized. Such values in objects will be
        dropped; in arrays they will be replaced with null. You can use
        a replacer function to replace those with JSON values.
        JSON.stringify(undefined) returns undefined.

        The optional space parameter produces a stringification of the
        value that is filled with line breaks and indentation to make it
        easier to read.

        If the space parameter is a non-empty string, then that string will
        be used for indentation. If the space parameter is a number, then
        the indentation will be that many spaces.

        Example:

        text = JSON.stringify(['e', {pluribus: 'unum'}]);
        // text is '["e",{"pluribus":"unum"}]'


        text = JSON.stringify(['e', {pluribus: 'unum'}], null, '\t');
        // text is '[\n\t"e",\n\t{\n\t\t"pluribus": "unum"\n\t}\n]'

        text = JSON.stringify([new Date()], function (key, value) {
            return this[key] instanceof Date ?
                'Date(' + this[key] + ')' : value;
        });
        // text is '["Date(---current time---)"]'


    JSON.parse(text, reviver)
        This method parses a JSON text to produce an object or array.
        It can throw a SyntaxError exception.

        The optional reviver parameter is a function that can filter and
        transform the results. It receives each of the keys and values,
        and its return value is used instead of the original value.
        If it returns what it received, then the structure is not modified.
        If it returns undefined then the member is deleted.

        Example:

        // Parse the text. Values that look like ISO date strings will
        // be converted to Date objects.

        myData = JSON.parse(text, function (key, value) {
            var a;
            if (typeof value === 'string') {
                a =
/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2}(?:\.\d*)?)Z$/.exec(value);
                if (a) {
                    return new Date(Date.UTC(+a[1], +a[2] - 1, +a[3], +a[4],
                        +a[5], +a[6]));
                }
            }
            return value;
        });

        myData = JSON.parse('["Date(09/09/2001)"]', function (key, value) {
            var d;
            if (typeof value === 'string' &&
                    value.slice(0, 5) === 'Date(' &&
                    value.slice(-1) === ')') {
                d = new Date(value.slice(5, -1));
                if (d) {
                    return d;
                }
            }
            return value;
        });


This is a reference implementation. You are free to copy, modify, or
redistribute.
*/

/*jslint evil: true, strict: false, regexp: false */

/*members "", "\b", "\t", "\n", "\f", "\r", "\"", JSON, "\\", apply,
call, charCodeAt, getUTCDate, getUTCFullYear, getUTCHours,
getUTCMinutes, getUTCMonth, getUTCSeconds, hasOwnProperty, join,
lastIndex, length, parse, prototype, push, replace, slice, stringify,
test, toJSON, toString, valueOf
*/


//Create a JSON object only if one does not already exist. We create the
//methods in a closure to avoid creating global variables.

var JSON;
if (!JSON) {
JSON = {};
}

(function () {
"use strict";

function f(n) {
    // Format integers to have at least two digits.
    return n < 10 ? '0' + n : n;
}

if (typeof Date.prototype.toJSON !== 'function') {

    Date.prototype.toJSON = function (key) {

        return isFinite(this.valueOf()) ?
            this.getUTCFullYear()     + '-' +
            f(this.getUTCMonth() + 1) + '-' +
            f(this.getUTCDate())      + 'T' +
            f(this.getUTCHours())     + ':' +
            f(this.getUTCMinutes())   + ':' +
            f(this.getUTCSeconds())   + 'Z' : null;
    };

    String.prototype.toJSON      =
        Number.prototype.toJSON  =
        Boolean.prototype.toJSON = function (key) {
            return this.valueOf();
        };
}

var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
    escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
    gap,
    indent,
    meta = {    // table of character substitutions
        '\b': '\\b',
        '\t': '\\t',
        '\n': '\\n',
        '\f': '\\f',
        '\r': '\\r',
        '"' : '\\"',
        '\\': '\\\\'
    },
    rep;


function quote(string) {

//If the string contains no control characters, no quote characters, and no
//backslash characters, then we can safely slap some quotes around it.
//Otherwise we must also replace the offending characters with safe escape
//sequences.

    escapable.lastIndex = 0;
    return escapable.test(string) ? '"' + string.replace(escapable, function (a) {
        var c = meta[a];
        return typeof c === 'string' ? c :
            '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
    }) + '"' : '"' + string + '"';
}


function str(key, holder) {

//Produce a string from holder[key].

    var i,          // The loop counter.
        k,          // The member key.
        v,          // The member value.
        length,
        mind = gap,
        partial,
        value = holder[key];

//If the value has a toJSON method, call it to obtain a replacement value.

    if (value && typeof value === 'object' &&
            typeof value.toJSON === 'function') {
        value = value.toJSON(key);
    }

//If we were called with a replacer function, then call the replacer to
//obtain a replacement value.

    if (typeof rep === 'function') {
        value = rep.call(holder, key, value);
    }

//What happens next depends on the value's type.

    switch (typeof value) {
    case 'string':
        return quote(value);

    case 'number':

//JSON numbers must be finite. Encode non-finite numbers as null.

        return isFinite(value) ? String(value) : 'null';

    case 'boolean':
    case 'null':

//If the value is a boolean or null, convert it to a string. Note:
//typeof null does not produce 'null'. The case is included here in
//the remote chance that this gets fixed someday.

        return String(value);

//If the type is 'object', we might be dealing with an object or an array or
//null.

    case 'object':

//Due to a specification blunder in ECMAScript, typeof null is 'object',
//so watch out for that case.

        if (!value) {
            return 'null';
        }

//Make an array to hold the partial results of stringifying this object value.

        gap += indent;
        partial = [];

//Is the value an array?

        if (Object.prototype.toString.apply(value) === '[object Array]') {

//The value is an array. Stringify every element. Use null as a placeholder
//for non-JSON values.

            length = value.length;
            for (i = 0; i < length; i += 1) {
                partial[i] = str(i, value) || 'null';
            }

//Join all of the elements together, separated with commas, and wrap them in
//brackets.

            v = partial.length === 0 ? '[]' : gap ?
                '[\n' + gap + partial.join(',\n' + gap) + '\n' + mind + ']' :
                '[' + partial.join(',') + ']';
            gap = mind;
            return v;
        }

//If the replacer is an array, use it to select the members to be stringified.

        if (rep && typeof rep === 'object') {
            length = rep.length;
            for (i = 0; i < length; i += 1) {
                if (typeof rep[i] === 'string') {
                    k = rep[i];
                    v = str(k, value);
                    if (v) {
                        partial.push(quote(k) + (gap ? ': ' : ':') + v);
                    }
                }
            }
        } else {

//Otherwise, iterate through all of the keys in the object.

            for (k in value) {
                if (Object.prototype.hasOwnProperty.call(value, k)) {
                    v = str(k, value);
                    if (v) {
                        partial.push(quote(k) + (gap ? ': ' : ':') + v);
                    }
                }
            }
        }

//Join all of the member texts together, separated with commas,
//and wrap them in braces.

        v = partial.length === 0 ? '{}' : gap ?
            '{\n' + gap + partial.join(',\n' + gap) + '\n' + mind + '}' :
            '{' + partial.join(',') + '}';
        gap = mind;
        return v;
    }
}

//If the JSON object does not yet have a stringify method, give it one.

if (typeof JSON.stringify !== 'function') {
    JSON.stringify = function (value, replacer, space) {

//The stringify method takes a value and an optional replacer, and an optional
//space parameter, and returns a JSON text. The replacer can be a function
//that can replace values, or an array of strings that will select the keys.
//A default replacer method can be provided. Use of the space parameter can
//produce text that is more easily readable.

        var i;
        gap = '';
        indent = '';

//If the space parameter is a number, make an indent string containing that
//many spaces.

        if (typeof space === 'number') {
            for (i = 0; i < space; i += 1) {
                indent += ' ';
            }

//If the space parameter is a string, it will be used as the indent string.

        } else if (typeof space === 'string') {
            indent = space;
        }

//If there is a replacer, it must be a function or an array.
//Otherwise, throw an error.

        rep = replacer;
        if (replacer && typeof replacer !== 'function' &&
                (typeof replacer !== 'object' ||
                typeof replacer.length !== 'number')) {
            throw new Error('JSON.stringify');
        }

//Make a fake root object containing our value under the key of ''.
//Return the result of stringifying the value.

        return str('', {'': value});
    };
}


//If the JSON object does not yet have a parse method, give it one.

if (typeof JSON.parse !== 'function') {
    JSON.parse = function (text, reviver) {

//The parse method takes a text and an optional reviver function, and returns
//a JavaScript value if the text is a valid JSON text.

        var j;

        function walk(holder, key) {

//The walk method is used to recursively walk the resulting structure so
//that modifications can be made.

            var k, v, value = holder[key];
            if (value && typeof value === 'object') {
                for (k in value) {
                    if (Object.prototype.hasOwnProperty.call(value, k)) {
                        v = walk(value, k);
                        if (v !== undefined) {
                            value[k] = v;
                        } else {
                            delete value[k];
                        }
                    }
                }
            }
            return reviver.call(holder, key, value);
        }


//Parsing happens in four stages. In the first stage, we replace certain
//Unicode characters with escape sequences. JavaScript handles many characters
//incorrectly, either silently deleting them, or treating them as line endings.

        text = String(text);
        cx.lastIndex = 0;
        if (cx.test(text)) {
            text = text.replace(cx, function (a) {
                return '\\u' +
                    ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
            });
        }

//In the second stage, we run the text against regular expressions that look
//for non-JSON patterns. We are especially concerned with '()' and 'new'
//because they can cause invocation, and '=' because it can cause mutation.
//But just to be safe, we want to reject all unexpected forms.

//We split the second stage into 4 regexp operations in order to work around
//crippling inefficiencies in IE's and Safari's regexp engines. First we
//replace the JSON backslash pairs with '@' (a non-JSON character). Second, we
//replace all simple value tokens with ']' characters. Third, we delete all
//open brackets that follow a colon or comma or that begin the text. Finally,
//we look to see that the remaining characters are only whitespace or ']' or
//',' or ':' or '{' or '}'. If that is so, then the text is safe for eval.

        if (/^[\],:{}\s]*$/
                .test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@')
                    .replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']')
                    .replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {

//In the third stage we use the eval function to compile the text into a
//JavaScript structure. The '{' operator is subject to a syntactic ambiguity
//in JavaScript: it can begin a block or an object literal. We wrap the text
//in parens to eliminate the ambiguity.

            j = eval('(' + text + ')');

//In the optional fourth stage, we recursively walk the new structure, passing
//each name/value pair to a reviver function for possible transformation.

            return typeof reviver === 'function' ?
                walk({'': j}, '') : j;
        }

//If the text is not JSON parseable, then a SyntaxError is thrown.

        throw new SyntaxError('JSON.parse');
    };
}
}());
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
Baze.Loading = {
	
	el : null,
	
	init : function()
	{
		if(this.el !== null)
			return;
		
		var el = document.createElement("div");
			
		// Loading panel style
		el.id = 'baze-loading';
		
		el.innerHTML = '<img src="'+LIB_ROOT + '/img/loading.gif'+'" /><span class="text">' + __('loading') + '</span>';
/*
		var img = document.createElement("img");
		img.src = LIB_ROOT + '/img/loading.gif';
		el.appendChild(img);
	*/	
		this.el = document.body.appendChild(el);
	},

	/**
	 * Shows the loading panel
	 * @public
	 */
	show : function baze_loading_show()
	{
		if(!this.el) {
			this.init();
		}
					
		this.el.style.display = 'block';
	},

	/**
	 * Hides the loading panel
	 * @public
	 */
	hide : function baze_loading_hide() {
		this.el.style.display = 'none';
	}
};
if(typeof Baze !== 'undefined')
{
	Baze.provide("i18n.pt-br");
}

i18n = {
	defaultLang: 'en_US',

	messages: {
		en_US: {
			loading: 'Loading...',
			server_error: 'An error occured on the server.'
		}
	}
};

__ = function baze_i18n_(msgid, lang)
{
	if(!lang)
		lang = i18n.defaultLang
	
	if(typeof i18n.messages[lang][msgid] != 'undefined')
		return i18n.messages[lang][msgid];
	
	return msgid;
}
if(typeof Baze !== 'undefined')
{
	Baze.provide("system.collections.Collection");
}

/**
 * @class Collection
 * @alias Collection
 * @author Saulo Vallory
 * @classDescription the collection cannot contain duplicate elements
 * @namespace system.collections
 * @version 0.9
 * 
 * @constructor
 */
Collection = function Collection()
{
	this.index = {};
	this.backIndex = [];
	this.values = [];
};

(function Collection_closure()
{		
	Object.extend(Collection.prototype, {
	
		/**
		 * @method add
		 * @param {string,number} key
		 * @param {Object} value
		 */
		add : function Collection_add(key,value)
		{ 
			if(this.index[key] !== undefined) {
				return false;
			}
			
			if(typeof key !== "string" && typeof key !== "number") {
				Baze.raise("Error in Collection::add", new Error("The key value must be a string or a number"));
				return false;
			}
			
			this.index[key] = this.values.length;
			this.backIndex[this.values.length] = key;
			this.values.push(value);
			
			return true;
		},
			
		count : function Collection_count() {
			return this.values.length;
		},

		/**
		 * @alias Collection
		 * @private
		 * @constructor
		 */
		_compact : function _compact()
		{
			this.backIndex = this.backIndex.compact();
			this.values = this.values.compact();
			
			for(var i=0; i < this.backIndex.length; i++) {
				this.index[this.backIndex[i]] = i;
			}
		},
		
		get : function Collection_get(key) 
		{
			if(typeof key == "number")
			{				
				return this.values[key];
			}
			else
			{
				if(this.index[key] == undefined) {
					return null;
				}
					
				return this.values[this.index[key]];
			}
		},
		
		item : this.get,
	
		getValues : function Collection_getValues() {
			var results = [];
			for(var i=0; i < this.values.length; i++) {
				results.push(this.values[i]);
			}
			
			return results;
		},
	
		remove : function Collection_remove(key) 
		{	
			var bak;
			
			if(typeof key == "number")
			{
				if(this.values[key] === undefined) {
					return false;
				}
				
				bak = this.values[key];
				
				delete this.values[key];
				delete this.index[this.backIndex[key]];
				delete this.backIndex[key];
				
				this._compact();
			}
			else if(typeof key == "string")
			{
				if(this.index[key] === undefined) {
					return false;
				}
					
				bak = this.values[this.index[key]];
				
				delete this.values[this.index[key]];
				delete this.backIndex[this.index[key]];
				delete this.index[key];
				
				this._compact();
			}
			else {
				Baze.raise("", new Error("Collection.remove expects a number or string as only parameter, "+(typeof key)+" given."));
				return null;
			}
				
			return bak;
		},
		
		removeAll : function Collection_removeAll()
		{
			this.index = {};
			this.backIndex = [];
			this.values = [];
		}
	});
})();
if(typeof Baze != "undefined")
{
	Baze.provide("system.Event");
}
	
Baze.Event = function Event() {
	this.listeners = [];
};
	
Object.extend(Baze.Event.prototype,  {
	listeners : null,

	addListener : function (fn) {
		if(typeof fn !== "function") {
			return null;
		}

		var ind = this.listeners.indexOf(fn);

		if(ind == -1)
			this.listeners.push(fn);
	},	
	
	removeListener : function(fn) {
		var ind = this.listeners.indexOf(fn);
		
		if(ind == -1)
			delete this.listeners[ind];
	},
	
	raise : function (obj, args) {
		for(var i=0; i < this.listeners.length; i++) {
			this.listeners[i](obj, args);
		}
	}
});
if(typeof Baze !== 'undefined')
{
	Baze.provide("system.viewState.Change");
}

ChangeType = {
	PROPERTY_CHANGED : 1,
	CHILD_ADDED : 2,
	CHILD_REMOVED : 3
};

/**
 * @class Change
 * @alias Change
 * @classDescription A classe Change representa uma altera��o na interface
 * @package system.viewState 
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires system.viewState.ClientViewState
 * 
 * @constructor
 * @param {Object} type
 * @param {Object} data
 */
Change = function Change(type, data) {

	this.type = type;
	
	// TODO: checar se os dados requeridos pelo tipo da Change existem em data
	for(var v in data) {
		this[v] = data[v];
	}
};

Object.extend(Change.prototype,  {

	type : null,

	/**
	 * @method getXML
	 * @return {String} xml que representa a altera��o
	 */
	toJSON : function toJSON()
	{
		var o = {};

		switch(this.type)
		{
			case ChangeType.CHILD_ADDED :
				o = '<change type="childAdded" childId="' + this.child.get("id") + '" />';
				break;

			case ChangeType.CHILD_REMOVED :
				o = '<change type="childRemoved" childId="' + this.child.get("id") + '" />';
				break;

			case ChangeType.PROPERTY_CHANGED :
				var propType = "";

				if(this.newValue == null)
					this.newValue = "";
				if(is_array(this.newValue)) {
					propType = "array"; 
				}
				else {
					propType = typeof this.newValue; 
				}

				o = {};
				o[this.propertyName] = this.newValue;
		}

		return o;
	},

	/**
	 * O id foi criado dessa forma para que se possa perceber quando uma altera��o se chocar com outra
	 */
	getId : function ()
	{
		switch(this.type)
		{
			case ChangeType.PROPERTY_CHANGED :
				return "PropChange_" + this.propertyName;

			case ChangeType.CHILD_ADDED :
				return "ChildAddOrRemove_" + this.child.getId();

			case ChangeType.CHILD_REMOVED :
				return "ChildAddOrRemove_" + this.child.getId();
		}
	},

	/**
 	 * Checa se a altera��o desta inst�ncia � anulada pela altera��o do objeto passado como par�metro.
	 * 
	 * @memberOf Change
	 * @param {Change} chg
	 */
	isMirror : function Change_isMirror(chg)
	{
		switch(this.type)
		{
			case ChangeType.PROPERTY_CHANGED :
				if(chg.type == ChangeType.PROPERTY_CHANGED && this.newValue == chg.oldValue && this.oldValue == chg.newValue) {
					return true; }
				break;

			case ChangeType.CHILD_ADDED :
				if(chg.type == ChangeType.CHILD_REMOVED && chg.child.getId() == this.child.getId()) {
					return true; }
				break;

			case ChangeType.CHILD_REMOVED :
				if(chg.type == ChangeType.CHILD_ADDED && chg.child.getId() == this.child.getId()) {
					return true; }
				break;
		}

		return false;
	},

	/**
	 * Soma duas alter��es transformando-as em uma s�.
	 *
	 * @param {Change} chg
	 */
	mergeWith : function Change_mergeWith(chg)
	{
		if(this.type !== ChangeType.PROPERTY_CHANGED) {
			//Baze.raise("Only property changes can be merged", new Error());
			return null;
		}

		if(this.type !== chg.type) {
			Baze.raise("Different types of changes can not be merged", new Error());
			return null;
		}

		this.newValue = chg.newValue;
	}
});
if(typeof Baze != "undefined")
{
	Baze.provide("system.viewState.ClientViewState");
	
	Baze.require("system.collections.Collection");
	Baze.require("system.viewState.Change");
}

/**
 * @class ClientViewState
 * @alias ClientViewState
 * @classDescription ClientViewState � um singleton que prov�
 * m�todos para armazenar as altera��es na interface at� que 
 * elas sejam enviadas ao servidor 
 * @author Saulo Vallory
 * @namespace Baze
 * @version 0.8
 * 
 * @requires Baze
 * @requires Collection
 */
(function ClientViewState_closure()
{
	if(typeof ClientViewState !== 'undefined')
		return;
	
	/**
	 * @property cache
	 * @private
	 */
	var _cache = {
		neo : new Collection(),
		deleted : new Collection(),
		modified : new Collection()
	};

	ClientViewState = 
	{
		/**
		 * @method getCache
		 * @return {Array}
		 */
		getCache: function ClientViewState_getCache() {
			return _cache;
		},
		
		/**
		 * 
		 */
		addNewObject: function ClientViewState_addNewObject(obj) {
			_cache.neo.add(obj.getId(), obj);
		},
		
		addRemovedObject: function ClientViewState_addRemovedObject(obj) {
			_cache.deleted.add(obj.getId(), obj);
		},
	
		/**
		 *
		 * @param {NodeList} objects
		 */
		updateComponents: function updateComponents(objects)
		{
			var object, objectChanges;
			var comp, change, cType;
	
			for(var i=0; i < objects.length; i++)
			{
				object = objects[i];
	
				// find the object
				comp = Baze.getComponentById(object.id);
	
				if(!comp)
				{
					Baze.raise(__("Error updating components"), new Error(__("Component with id {id} could not be found.").replace('{id}', object.id)));
					continue;
				}
				
				for(var p=null, j=0; j < object.properties.length; j++)
				{
					p = object.properties[j];
					if(p.eval)
						comp.set(p.name, Baze.evaluate(p.value));
					else
						comp.set(p.name, p.value);
						
				}
				// applying the changes
				/*
				for(var j=0; j < objectChanges.length; j++)
				{
					change = objectChanges[j];
					cType = change.type;
	
					switch(Number(cType))
					{
						case ChangeType.PROPERTY_CHANGED :
							var propertyName = change.getAttribute('propertyName');
							var newValue = change.textContent || change.text;
	
							if(newValue)
								comp.set(propertyName, newValue);
							else
								comp.set(propertyName, "");
								
							break;
	
						case ChangeType.CHILD_ADDED :
							
							var childObj = Baze.getComponentById(change.getAttribute('childId'));
							
							if(!childObj) {
								Baze.raise("", new Error("Child addition error. Object with id (" + change.getAttribute('childId') + ") not found"));
								return;
							}
	
							comp.addChild(childObj);
							break;
	
						case ChangeType.CHILD_REMOVED :
							comp.removeChild(change.getAttribute('childId'));
							break;
					}
				}
				*/
				if(_cache.modified.get(object.id))
					_cache.modified.get(object.id).changes.removeAll()
	
			}
		},
	
		/**
		 *
		 * @param {NodeList} objects
		 */
		createComponents: function createComponents(objects)
		{
			for(var i=0; i < objects.length; i++)
			{
				var object = objects[i];
				var props = object.properties;
	
				var comp = Component.factory(object.klass);
				
				if(!comp) {
					Baze.raise("", new Error(object.klass + " component could not be instantiated"));
					return;
				}

				comp.set("id", object.id);
	
				for(var j=0; j < props.length; j++)
				{
					if(props[j].eval) {
						comp.set(props[j].name, Baze.evaluate(props[j].value));
					}
					else
						comp.set(props[j].name, props[j].value);
				}
					
				// inserting the object on the page
				Baze.addComponent(comp);
			}
		},
	
		/**
		 *
		 * @param {NodeList} objects
		 */
		removeComponents: function removeComponents(objects)
		{
			for(var i=0; i < objects.length; i++) {
				Baze.removeComponent(objects[i]);
			}
		},
	
	
		/**
		 * @param {event} evt
		 */
		getEventMessage: function ClientViewState_getEventMessage(evt)
		{
			if(!evt) {
				Baze.raise("", new Error("Event object is required to build event messages."));
				return null;
			}
	
			var evtMsg = {
				type: evt.type,
				target: null,
				args: {}
			};
	
			for(var v in evt) {
				// bypass type, UPPER CASED properties (are constants), functions e objects
				if(v != "type" && !v.isUpperCased() && typeof evt[v] != 'function' && typeof evt[v] != 'object' )
				{
					evtMsg.args[v] = evt[v];
				}
			}
			
			var target = evt.currentTarget;
			
			if(evt.srcElement)
				target = evt.srcElement;
			
			// find the form when the event is a submit
			if (this.eventName == 'submit')
			{
				var targetElem = target;
	
				while (targetElem !== null && targetElem.tagName != 'form') {
					targetElem = targetElem.parentNode; }
	
				if (targetElem !==  null) {
					target = targetElem; }
			}
			else
			{
				// finds the component which parents this element (when the element isn't a component)
				while(target.parentNode && target.parentNode.id && target.attributes.phpclass === null) {
					target = evt.target.parentNode;
				}
			}
	
			evtMsg.target = target.id;
			
			return evtMsg;
		},
	
		getSyncMessage: function ClientViewState_getSyncMessage()
		{
			var neoArr = _cache.neo.values;
			var modArr = _cache.modified.values;
			var delArr = _cache.deleted.values;
			
			if(neoArr.length == 0 && modArr.length == 0 && delArr.length == 0)
				return {};
			
			var msg = {};
	
			if(neoArr.length > 0)
			{
				msg.n = [];
				for(var i=0; i < neoArr.length; i++)
				{
					msg.n.push(neoArr[i].getSyncObj());
				}
			}
	
			// MODIFIED objects
	
			if(modArr.length > 0)
			{
				msg.m = [];
				var obj;
				for(i=0; i < modArr.length; i++)
				{
					if(modArr[i].changes.values.length > 0)
					{
						obj = {
							id: modArr[i].realObject.getId(),
							properties: {}
						};
						
						var changes = modArr[i].changes.values;
						for(var j=0; j < changes.length; j++) if(ChangeType.PROPERTY_CHANGED == changes[j].type ) {
							obj.properties[changes[j].propertyName] = changes[j].newValue;
						}
						
						msg.m.push(obj);
					}
				}
			}
			
			
			// DELETED objects
	
			if(delArr.length > 0)
			{
				msg.r = delArr.pluck('id');
				for(i=0; i < delArr.length; i++)
				{
					msg += '<object id="' + delArr[i].getId() + '" />';
				}
			}
			
			return msg;
		},
		
		objectChanged: function ClientViewState_objectChanged(obj, args) 
		{
			var chg = null;
	
			if(!obj) {
				Baze.raise("", new Error("Object argument missing."));
				return;
			}
	
			if(!args) {
				Baze.raise("", new Error("ObjectChanged function expects to receive 2 arguments. One given."));
				return null;
			}
			
			switch(args.changeType)
			{
				case ChangeType.PROPERTY_CHANGED :
					chg = new Change(ChangeType.PROPERTY_CHANGED, {propertyName : args.propertyName,
															oldValue : args.oldValue,
															newValue : obj.get(args.propertyName)});
					break;
	
				case ChangeType.CHILD_ADDED :
					chg = new Change(ChangeType.CHILD_ADDED, {child : args.child});
					break;
	
				case ChangeType.CHILD_REMOVED :
					chg = new Change(ChangeType.CHILD_REMOVED, {child : args.child});
					break;
	
				default :
					Baze.raise("", new Error("Change type '" + args.changeType + "' is not supported"));
					return null;
			}
	
			var chgObj = null;
	
			if(!_cache.modified.get(obj.getId())) {
				_cache.modified.add(obj.getId(), { realObject : obj, changes : new Collection() });
				chgObj = _cache.modified.get(obj.getId());
			}
			else {
				chgObj = _cache.modified.get(obj.getId());
			}

			// verifica se existe um conflito de altera��es
			var change_ = chgObj.changes.get(chg.getId());
	
			if(change_ != null) {
				// Existe um conflito!
				// verifica se altera��o atual anula uma altera��o anterior
				if(change_.isMirror(chg)) {
					// neste caso deleta a altera��o anterior
					chgObj.changes.remove(chg.getId());
				}
				else {
					// sen�o faz um merge das altera��es
					change_.mergeWith(chg);
				}
			}
			else {
				// adiciona a altera��o
				chgObj.changes.add(chg.getId(), chg);
			}
		},
	
		setSynchronized: function ClientViewState_setSynchronized()
		{
			_cache.neo.removeAll();
			_cache.modified.removeAll();
			_cache.deleted.removeAll();
		}
	}
})();
if(typeof Baze != "undefined")
{
	Baze.provide("system.Environment");
}

/**
 * @alias BrowserInfo
 * @author Saulo Vallory
 * @classDescription A fun��o Browser info prov� informa��es 
 * sobre o browser do cliente
 * @version 0.9
 * @return {Object}
 */
var getBrowserInfo = function getBrowserInfo()
{
	function trim(str)
	{
		return str.replace(/^\s+|\s+$/gi, "").replace(/\s{2,}/gi, " ");
	}
	
	function detectRenderEngine()
	{
		var presto  = window.opera;
		var gecko   = navigator.product == 'Gecko';
		var mariner = document.layers && navigator.mimeTypes['*'];
		var khtml   = navigator.vendor == 'KDE' || ( document.childNodes && !document.all && !navigator.taintEnabled );
		var icab    = window.ScriptEngine && ScriptEngine().indexOf('InScript') + 1;
		var trident = window.ActiveXObject;

		if      (presto)  {this.renderEngine = "Presto";}  // Opera, Dreamweaver MX, Nintendo DS, Nintendo Wii
		else if (gecko)   {this.renderEngine = "Gecko";}   // Netscape 6+, Firefox, Galeon/Epiphany
		else if (mariner) {this.renderEngine = "Mariner";} // Netscape Communicator
		else if (khtml)   {this.renderEngine = "KHTML";}   // Konqueror, Safari, OmniWeb
		else if (icab)    {this.renderEngine = "iCab";}    // iCab
		else if (trident) {this.renderEngine = "Trident";} // Internet Explorer
	}

	function detectNameAndVersion()
	{
		var ua = navigator.userAgent;

		var uaLen = ua.length;
		var i, j, ind;

		// ##### Split into stuff before parens and in/after parens
		var preparens = "";
		var parenthesized = "";
		var postparens = "";

		ind = ua.indexOf("(");

		if (ind >= 0)
		{
			preparens = trim(ua.substring(0,ind));
			parenthesized = ua.substring(ind+1, uaLen);
			j = parenthesized.indexOf(")");

			if (j >= 0)
			{
				postparens = trim((parenthesized.substring(j+1, uaLen)).replace(' ', ';'));
				parenthesized = parenthesized.substring(0, j)+'; '+postparens;
			}
		}
		else
		{
			preparens = ua;
		}

		// ##### First assume browser and version are in preparens
		// ##### override later if we find them in the parenthesized stuff
		var browVer = preparens;

		var tokens = parenthesized.split(";");
		var token = "";

		// # Now go through parenthesized tokens
		for (i=0; i < tokens.length; i++)
		{
			token = trim(tokens[i]);

			//## compatible - might want to reset from Netscape
			if (token == "compatible")
			{
				//## One might want to reset browVer to a null string
				//## here, but instead, we'll assume that if we don't
				//## find out otherwise, then it really is Mozilla
				//## (or whatever showed up before the parens).
				//## browser - try for Opera or IE
	    	}
			else if (token.indexOf("MSIE") >= 0
			      || token.indexOf("Gecko") >= 0
			      || token.indexOf("Opera") >= 0
			      || token.indexOf("AppleWebKit") >= 0
			      || token.indexOf("Konqueror") >= 0
			      || token.indexOf("Safari") >= 0
			      || token.indexOf("OmniWeb") >= 0
			      || token.indexOf("Netscape") >= 0
			      || token.indexOf("SeaMonkey") >= 0
			      || token.indexOf("Firefox") >= 0)
		    {
				browVer = token;
		    }

		    //'## platform - try for X11, SunOS, Win, Mac, PPC
		    if ((token.indexOf("X11") >= 0) || (token.indexOf("SunOS") >= 0) || (token.indexOf("Linux") >= 0))
		    {
				this.platform = "Unix";
			}
		    else if (token.indexOf("Win") >= 0)
		    {
				this.platform = token;
			}
		    else if ((token.indexOf("Mac") >= 0) || (token.indexOf("PPC") >= 0))
		    {
				this.platform = token;
			}
		}

		var msieIndex = browVer.indexOf("MSIE");

		if (msieIndex >= 0)
		{
			browVer = browVer.substring(msieIndex, browVer.length);
		}

		var leftover = "";

		if (browVer.substring(0, "Mozilla".length) == "Mozilla")
		{
			this.name = "Netscape";
	        leftover = browVer.substring("Mozilla".length+1, browVer.length);
		}
		else if (browVer.substring(0, "Lynx".length) == "Lynx")
		{
			this.name = "Lynx";
	        leftover = browVer.substring("Lynx".length+1, browVer.length);
		}
		else if (browVer.substring(0, "MSIE".length) == "MSIE")
		{
			this.name = "IE";
			leftover = browVer.substring("MSIE".length+1, browVer.length);
		}
		else if (browVer.substring(0, "Microsoft Internet Explorer".length) == "Microsoft Internet Explorer")
		{
	    	this.name = "IE";
	        leftover = browVer.substring("Microsoft Internet Explorer".length+1, browVer.length);
		}
		else if (browVer.substring(0, "Opera".length) == "Opera")
		{
			this.name = "Opera";
			leftover = browVer.substring("Opera".length+1, browVer.length);
		}
		else if (navigator.userAgent.indexOf("Firefox") >= 0)
		{
			this.name = "Firefox";
			leftover = browVer.substring("Firefox".length+1, browVer.length);
		}
		else if (browVer.substring(0, "SeaMonkey".length) == "SeaMonkey")
		{
			this.name = "SeaMonkey";
			leftover = browVer.substring("SeaMonkey".length+1, browVer.length);
		}
		else if (browVer.substring(0, "Netscape".length) == "Netscape")
		{
			this.name = "Netscape6+";
			leftover = browVer.substring("Netscape".length+1, browVer.length);
		}
		else if (browVer.substring(0, "AppleWebKit".length) == "AppleWebKit")
		{
			this.name = "(Safari-based)";
			leftover = browVer.substring("AppleWebKit".length+1, browVer.length);
		}
		else if (browVer.substring(0, "Safari".length) == "Safari")
		{
			this.name = "Safari";
			leftover = browVer.substring("Safari".length+1, browVer.length);
		}
		else if (browVer.substring(0, "Konqueror".length) == "Konqueror")
		{
			this.name = "Konqueror";
			leftover = browVer.substring("Konqueror".length+1, browVer.length);
		}
		else if (browVer.substring(0, "OmniWeb".length) == "OmniWeb")
		{
			this.name = "OmniWeb";
			leftover = browVer.substring("OmniWeb".length+1, browVer.length);
		}
		else if (browVer.substring(0, "Gecko".length) == "Gecko")
		{
			this.name = "(Gecko-based)";
			leftover = browVer.substring("Gecko".length+1, browVer.length);
		}

		leftover = trim(leftover);

	  	// # Try to get version info out of leftover stuff
		ind = leftover.indexOf(" ");

		if (i >= 0)
		{
			this.version = trim(leftover.substring(0, i));
		}
		else
		{
			this.version = trim(leftover);
		}

		j = this.version.indexOf(".");

		if (j >= 0)
		{
			this.majorver = this.version.substring(0,j);
			this.minorver = this.version.substring(j+1, this.version.length);
		}
		else
		{
			this.majorver = this.version;
		}
	}

	var info = 
	{
		name: null,
		platform: null,
		renderEngine: null,
		version: null,
		majorver: null,
		minorver: null
	};

	detectNameAndVersion.apply(info);
	detectRenderEngine.apply(info);

	return info;
};

Environment = function Environment()
{
		/*
		 * Getting screen.innerHeight/Width
		 */
		var myWidth = 0, myHeight = 0;

		if( typeof( window.innerWidth ) == 'number' ) {
			//Non-IE
			this.screen.getInnerWidth = function() { return window.innerWidth; };
			this.screen.getInnerHeight = function() { return window.innerHeight; };
		}
		else if( typeof document.documentElement.clientWidth != 'undefined' ) 
		{
			//IE7 and IE 6+ in 'standards compliant mode'
			this.screen.getInnerWidth = function() { return document.documentElement.clientWidth; };
			this.screen.getInnerHeight = function() { return document.documentElement.clientHeight; };
		} 
		else if( typeof document.body.clientWidth != 'undefined' ) 
		{
			//IE 4 compatible
			this.screen.getInnerWidth = function() { return document.body.clientWidth; };
			this.screen.getInnerHeight = function() { return document.body.clientHeight; };
		}
		
		/* ------- Getting screen.innerHeight/Width End ------- */ 


		/*
		 * Getting body.scrollTop/Left
		 */ 
		var scrOfX = 0, scrOfY = 0;
		
		if( typeof( window.pageYOffset ) == 'number' ) 
		{
			//Netscape compliant
			this.body.getScrollTop = function () { return window.pageYOffset; };
			this.body.getScrollLeft = function () { return window.pageXOffset; };
		}
		else if( typeof document.body.scrollLeft != 'undefined' )
		{
			//DOM compliant
			this.body.getScrollTop = function () { return document.body.scrollTop; };
			this.body.getScrollLeft = function () { return document.body.scrollLeft; };
		} 
		else if( typeof document.documentElement.scrollLeft  != 'undefined' ) 
		{
			//IE6 standards compliant mode
			this.body.getScrollTop = function () { return document.documentElement.scrollTop; };
			this.body.getScrollLeft = function () { return document.documentElement.scrollLeft; };
		}

		/* ------- Getting body.scrollTop/Left End ------- */ 
};

Object.extend(Environment.prototype,  
{
	browser : getBrowserInfo(),

	screen : {/*
		innerWidth : null,
		innerHeight : null
	*/},
	
	body : { /*
		scrollTop : null,
		scrollLeft : null
	*/}
});
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
if(typeof Baze != "undefined") {
	Baze.provide("system.util");
}

wait = function wait(millis)
{
	date = new Date();

	do {
		var curDate = new Date();
	}
	while(curDate - date < millis);
};

uid = function uid(prefix)
{
	var id;
	
	if(!prefix) prefix = "";
	
	do {
		id = prefix + Math.round(Math.random()*10000000000).toString();
	}
	while($(id) != null)
	
	return id;
};
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
