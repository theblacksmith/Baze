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