if(typeof Baze !== 'undefined') 
{
	Baze.provide("system.collections.Set");
}

/**
 * @class Set
 * @alias Set
 * @author Saulo Vallory
 * @classDescription the Set cannot contain duplicate elements.
 * @namespace system.collections
 * @version 1.0
 *  
 * @constructor
 */
Set = function Set()
{
	this.index = {};
	this.backIndex = [];
	this.values = [];
};

(function Set_closure(){
	
	function compact()
	{
		this.backIndex = this.backIndex.compact();
		this.values = this.values.compact();
		
		for(var i=0; i < this.backIndex.length; i++) {
			this.index[this.backIndex[i]] = i; 
		}
	}
	
	Object.extend(Set.prototype,  {

		/**
		 * @memberOf Set
		 * @method add
		 * @param {string,number} key
		 * @param {Object} value
		 */
		add : function Set_add(key,value)
		{
			if(typeof this.index[key] !== "undefined") {
				return true;
			}
			
			if(typeof key !== "string" && typeof key !== "number") {
				Baze.raise("Error in Set::add", new Error("The key value must be a string or a number."));
				return false;
			}
			
			this.index[key] = this.values.length;
			this.backIndex[this.values.length] = key;
			this.values.push(value);
			
			return true;
		},
			
		count : function Set_count() {
			return this.values.length;
		},
			
		get : function Set_get(key) 
		{
			if(this.index[key] == undefined) {
				return null;
			}
					
			return this.values[this.index[key]];
		},

		/**
		 * @memberOf Set
		 * @alias getKeys
		 */
		getKeys : function getKeys()
		{
			var results = [];
			for(var i=0; i < this.index.length; i++) {
				results.push(this.index[i]);
			}
			
			return results;
		},

		getValues : function Set_getValues() {
			var results = [];
			for(var i=0; i < this.values.length; i++) {
				results.push(this.values[i]);
			}
			
			return results;
		},

		item : function (i)
		{
			return this.values[i];
		},

		remove : function Set_remove(key) 
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
				
				compact.apply(this);
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
				
				compact.apply(this);
			}
			else {
				Baze.raise("", new Error("Set.remove expects a number or string as only parameter, "+(typeof key)+" given."));
				return null;
			}
				
			return bak;
		},
		
		/**
		 * @memberOf Set
		 * @alias removeAll
		 * @method removeAll
		 */
		removeAll : function Set_removeAll()
		{
			this.index = {};
			this.backIndex = [];
			this.values = [];
		}
	});
})();