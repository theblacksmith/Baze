if (window.ActiveXObject)
{
	function findMenus()
	{
		elems = document.getElementsByTagName('ul');
		for (var i = 0; i < elems.length; i++)
		{
			elem = elems[i];
			if (elem.getAttribute('phpclass') != 'php:menu')
			{
				continue;
			}

			nodes = elem.getElementsByTagName('li');
			for (var j = 0; j < nodes.length; j++)
			{
				node = nodes[j];
				Event.observe(node, 'mouseover', (function(event)
				{
					var a = this.getElementsByTagName('a');
					var ul = this.getElementsByTagName('ul');
//					if (a.length) {dojo.html.addClass(a[0], 'over');}
//					if (ul.length) {dojo.html.addClass(ul[0], 'over');}
					if (a.length && a[0].className.indexOf('over') == -1) {
						a[0].className += ' over';
						a[0].className = a[0].className.trim();
					}
					if (ul.length && ul[0].className.indexOf('over') == -1) {
						ul[0].className += ' over';
						ul[0].className = ul[0].className.trim();
					}
				}).bind(node));

				Event.observe(node, 'mouseout', (function(event)
				{
					var a = this.getElementsByTagName('a');
					var ul = this.getElementsByTagName('ul');
//					if (a.length) {dojo.html.removeClass(a[0], 'over');}
//					if (ul.length) {dojo.html.removeClass(ul[0], 'over');}
					if (a.length) {a[0].className = a[0].className.replace('over', '');}
					if (ul.length) {ul[0].className = ul[0].className.replace('over', '');}
				}).bind(node));
			}
		}
	}

	Baze.addBehaviour(findMenus);
}
