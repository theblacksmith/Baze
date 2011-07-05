if(typeof dojo != "undefined")
{
	dojo.require("system.Prototype");
	if (window.ActiveXObject){
		dojo.require("web.components.PanelsIE");}
}

if (!window.ActiveXObject)
{
	function TabGroup(id)
	{
		this.elem = document.getElementById(id);
		if (!this.elem) {return;}

		var uls = this.elem.getElementsByTagName('ul');
		var ul = uls[0];
		if (ul.parentNode != this.elem) {ul = uls[uls.length-1];}
		ul.style.display = 'block';
		this.tabs = ul.getElementsByTagName('li');
		var divs = this.elem.getElementsByTagName('div');
		this.pans = [];
		var skipped = 0;
		for (var i = 0; i < this.tabs.length; i++) {
			var d = divs[i+skipped];
			if (!dojo.html.hasClass(d, 'panel') || !d.parentNode.hasAttribute('phpclass') || d.parentNode.getAttribute('phpclass') != 'php:panelcontainer') {skipped++;i--;continue;}
			this.pans[i] = d;
			var o = this.tabs[i].firstChild;
			Event.observe(o, 'click', this.setVisible.bind(this, [i]));
		}
		this.setVisible(-1);
	}

	TabGroup.prototype.setVisible = function(index, ev)
	{
		for (var i = 0; i < this.tabs.length; i++) {
			o = this.pans[i];

			o.style.display = 'none';
			if (index != -1)
			{
				dojo.html.removeClass(this.tabs[i], 'selected');
			}
			else if (dojo.html.hasClass(this.tabs[i], 'selected'))
			{
				o.style.display = 'block';
			}
		}

		if (index >= 0)
		{
			dojo.html.addClass(this.tabs[index], 'selected');
			this.pans[index].style.display = 'block';
		}
		if (ev)
		{
			target = ev.srcElement || ev.currentTarget;
			target.blur();
			Event.stop(ev);
		}
	};

	var tgs = [];
	var num = -1;
	function findTabGroupsOnNode(node)
	{
		var divs = node.getElementsByTagName('div');
		for (var i = 0; i < divs.length; i++)
		{
			var o = divs[i];
			if (o.getAttribute('phpclass') == 'php:panelcontainer')
			{
				num++;
				if (tgs[num] !== null)
				{
					delete tgs[num];
				}
				tgs[num] = new TabGroup(o.id);
			}
			findTabGroupsOnNode(o);
		}
	}

	function findTabGroups()
	{
		findTabGroupsOnNode(document);
	}

	base.addBehaviour(findTabGroups);
}