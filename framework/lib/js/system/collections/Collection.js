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