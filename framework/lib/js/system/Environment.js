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