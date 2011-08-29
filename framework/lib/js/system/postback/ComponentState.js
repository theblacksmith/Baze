/**
 * @alias ComponentState
 * @classDescription Hold the current state modifications made to a component since the last postback
 */
var ComponentState = Class.create(BazeObject, {
	
	/**
	 * @alias properties
	 * @type {Array}
	 */
	
	/**
	 * @alias newChildren
	 * @type {Set}
	 */
	
	/**
	 * @alias removedChildren
	 * @type {Set}
	 */
	
	/**
	 * @alias manager
	 * @type {ViewStateManager}
	 */
	
	/**
	 * @alias component
	 * @type {Component}
	 */
	
	/**
	 * @alias countChange
	 * Keep tracking of current number of changes so we don't need to count all the arrays
	 * @type {number}
	 */
	
	/**
	 * @param {ViewStateManager}
	 * @param {Component}
	 */
	initialize: function(vsm, comp)
	{
		this.manager = vsm;
		this.component = comp;
		this.changesCount = 0;
		this.properties = [];
		this.removedChildren [];
		this.newChildren = [];
	},
	
	setSynchronized: function()
	{
		this.properties = [];
		
		if(this.newChildren instanceof Set)
			this.newChildren.removeAll();
			
		if(this.removedChildren instanceof Set)
			this.removedChildren.removeAll();
	},
	
	hasProperty: function(name)
	{
		return !Object.isUndefined(this.properties[name]);	
	},
	
	getProperty: function(name)
	{
		return (!Object.isUndefined(this.properties[name]) ? this.properties[name] : null);
	},
	
	addProperty: function(name, value, defaultValue)
	{
		defaultValue = defaultValue || null;
		if(value === defaultValue) {
			delete(this.properties[name]);
			this.updateCount(-1);
		}
		else {
			this.properties[name] = value;
			this.updateCount(+1);
		}
	},
	
	removeProperty: function(name)
	{
			delete(this.properties[name]);
			this.updateCount(-1);
	},
	
	addNewChild: function(component)
	{
		if(!(this.removedChildren instanceof Set))
			this.removedChildren = new Set(gettype(new Component()));
			
		if(!(this.newChildren instanceof Set))
			this.newChildren = new Set(gettype(new Component()));
			
		if(this.removedChildren.contains(component)) {
			this.removedChildren.remove(component);
			this.updateCount(-1);
		}
		else {
			this.newChildren.add(component);
			this.updateCount(+1);
		}
	},
	
	getNewChildren: function()
	{
		if(!this.newChildren)
			return array();
			
		return this.newChildren.toArray();
	},
	
	addRemovedChild: function(component)
	{
		if(!(this.removedChildren instanceof Set))
			this.removedChildren = new Set(gettype(new Component()));
			
		if(!(this.newChildren instanceof Set))
			this.newChildren = new Set(gettype(new Component()));
			
		if(this.newChildren.contains(component)) {
			this.newChildren.remove(component);
			this.updateCount(-1);
		}
		else {
			this.removedChildren.add(component);
			this.updateCount(+1);
		}
	},
	
	getRemovedChildren: function()
	{
		if(!this.removedChildren)
			return [];
			
		return this.removedChildren.toArray();
	},
	
	/**
	 * Updates changesCount adding it to operation and adds or removes the component to ServerViewState
	 * modified objects list accordingly.
	 * 
	 * @param int operation +1 or -1
	 */
	updateCount: function(operation)
	{
		this.changesCount += operation;
		
		if(this.changesCount > 0)
			this.manager.addModifiedObject(this.component);
		else
			this.manager.removeModifiedObject(this.component);
	},
	
	getProperties: function()
	{
		return this.properties;
	}
});