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
				
				if(!Object.isUndefined(object.p))
				{
					$H(object.p).entries().each(function(pair) {
						comp.set(pair.key, pair.value);
					});
				}
				
				if(!Object.isUndefined(object.nc))
				{
					object.nc.each(function(id){
						comp.addChild(Baze.getComponentById(id));
					});
				}

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
	
				var comp = Component.factory(object.c);
				
				if(!comp) {
					Baze.raise("", new Error(object.c + " component could not be instantiated"));
					return;
				}

				comp.set("id", object.id);
	
				$H(object.p).each(function(pair){
					comp.set(pair.key, pair.value);
				});
					
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
			objects.each(Baze.removeComponent);
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
							p: {},
							nc: []
						};
						
						var changes = modArr[i].changes.values;
						for(var j=0; j < changes.length; j++) if(ChangeType.PROPERTY_CHANGED == changes[j].type ) {
							obj.p[changes[j].propertyName] = changes[j].newValue;
						}
						
						neoArr.each(function(o){
							if(!Object.isUndefined(o.container))
								if(o.container === modArr[i].realObject)
									obj.nc.push(o.getId());
						});
						
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