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