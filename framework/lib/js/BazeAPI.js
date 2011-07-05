/*!
 * Sizzle CSS Selector Engine
 *  Copyright 2011, The Dojo Foundation
 *  Released under the MIT, BSD, and GPL Licenses.
 *  More information: http://sizzlejs.com/
 */
(function(){

var chunker = /((?:\((?:\([^()]+\)|[^()]+)+\)|\[(?:\[[^\[\]]*\]|['"][^'"]*['"]|[^\[\]'"]+)+\]|\\.|[^ >+~,(\[\\]+)+|[>+~])(\s*,\s*)?((?:.|\r|\n)*)/g,
	done = 0,
	toString = Object.prototype.toString,
	hasDuplicate = false,
	baseHasDuplicate = true,
	rBackslash = /\\/g,
	rNonWord = /\W/;

// Here we check if the JavaScript engine is using some sort of
// optimization where it does not always call our comparision
// function. If that is the case, discard the hasDuplicate value.
//   Thus far that includes Google Chrome.
[0, 0].sort(function() {
	baseHasDuplicate = false;
	return 0;
});

var Sizzle = function( selector, context, results, seed ) {
	results = results || [];
	context = context || document;

	var origContext = context;

	if ( context.nodeType !== 1 && context.nodeType !== 9 ) {
		return [];
	}
	
	if ( !selector || typeof selector !== "string" ) {
		return results;
	}

	var m, set, checkSet, extra, ret, cur, pop, i,
		prune = true,
		contextXML = Sizzle.isXML( context ),
		parts = [],
		soFar = selector;
	
	// Reset the position of the chunker regexp (start from head)
	do {
		chunker.exec( "" );
		m = chunker.exec( soFar );

		if ( m ) {
			soFar = m[3];
		
			parts.push( m[1] );
		
			if ( m[2] ) {
				extra = m[3];
				break;
			}
		}
	} while ( m );

	if ( parts.length > 1 && origPOS.exec( selector ) ) {

		if ( parts.length === 2 && Expr.relative[ parts[0] ] ) {
			set = posProcess( parts[0] + parts[1], context );

		} else {
			set = Expr.relative[ parts[0] ] ?
				[ context ] :
				Sizzle( parts.shift(), context );

			while ( parts.length ) {
				selector = parts.shift();

				if ( Expr.relative[ selector ] ) {
					selector += parts.shift();
				}
				
				set = posProcess( selector, set );
			}
		}

	} else {
		// Take a shortcut and set the context if the root selector is an ID
		// (but not if it'll be faster if the inner selector is an ID)
		if ( !seed && parts.length > 1 && context.nodeType === 9 && !contextXML &&
				Expr.match.ID.test(parts[0]) && !Expr.match.ID.test(parts[parts.length - 1]) ) {

			ret = Sizzle.find( parts.shift(), context, contextXML );
			context = ret.expr ?
				Sizzle.filter( ret.expr, ret.set )[0] :
				ret.set[0];
		}

		if ( context ) {
			ret = seed ?
				{ expr: parts.pop(), set: makeArray(seed) } :
				Sizzle.find( parts.pop(), parts.length === 1 && (parts[0] === "~" || parts[0] === "+") && context.parentNode ? context.parentNode : context, contextXML );

			set = ret.expr ?
				Sizzle.filter( ret.expr, ret.set ) :
				ret.set;

			if ( parts.length > 0 ) {
				checkSet = makeArray( set );

			} else {
				prune = false;
			}

			while ( parts.length ) {
				cur = parts.pop();
				pop = cur;

				if ( !Expr.relative[ cur ] ) {
					cur = "";
				} else {
					pop = parts.pop();
				}

				if ( pop == null ) {
					pop = context;
				}

				Expr.relative[ cur ]( checkSet, pop, contextXML );
			}

		} else {
			checkSet = parts = [];
		}
	}

	if ( !checkSet ) {
		checkSet = set;
	}

	if ( !checkSet ) {
		Sizzle.error( cur || selector );
	}

	if ( toString.call(checkSet) === "[object Array]" ) {
		if ( !prune ) {
			results.push.apply( results, checkSet );

		} else if ( context && context.nodeType === 1 ) {
			for ( i = 0; checkSet[i] != null; i++ ) {
				if ( checkSet[i] && (checkSet[i] === true || checkSet[i].nodeType === 1 && Sizzle.contains(context, checkSet[i])) ) {
					results.push( set[i] );
				}
			}

		} else {
			for ( i = 0; checkSet[i] != null; i++ ) {
				if ( checkSet[i] && checkSet[i].nodeType === 1 ) {
					results.push( set[i] );
				}
			}
		}

	} else {
		makeArray( checkSet, results );
	}

	if ( extra ) {
		Sizzle( extra, origContext, results, seed );
		Sizzle.uniqueSort( results );
	}

	return results;
};

Sizzle.uniqueSort = function( results ) {
	if ( sortOrder ) {
		hasDuplicate = baseHasDuplicate;
		results.sort( sortOrder );

		if ( hasDuplicate ) {
			for ( var i = 1; i < results.length; i++ ) {
				if ( results[i] === results[ i - 1 ] ) {
					results.splice( i--, 1 );
				}
			}
		}
	}

	return results;
};

Sizzle.matches = function( expr, set ) {
	return Sizzle( expr, null, null, set );
};

Sizzle.matchesSelector = function( node, expr ) {
	return Sizzle( expr, null, null, [node] ).length > 0;
};

Sizzle.find = function( expr, context, isXML ) {
	var set;

	if ( !expr ) {
		return [];
	}

	for ( var i = 0, l = Expr.order.length; i < l; i++ ) {
		var match,
			type = Expr.order[i];
		
		if ( (match = Expr.leftMatch[ type ].exec( expr )) ) {
			var left = match[1];
			match.splice( 1, 1 );

			if ( left.substr( left.length - 1 ) !== "\\" ) {
				match[1] = (match[1] || "").replace( rBackslash, "" );
				set = Expr.find[ type ]( match, context, isXML );

				if ( set != null ) {
					expr = expr.replace( Expr.match[ type ], "" );
					break;
				}
			}
		}
	}

	if ( !set ) {
		set = typeof context.getElementsByTagName !== "undefined" ?
			context.getElementsByTagName( "*" ) :
			[];
	}

	return { set: set, expr: expr };
};

Sizzle.filter = function( expr, set, inplace, not ) {
	var match, anyFound,
		old = expr,
		result = [],
		curLoop = set,
		isXMLFilter = set && set[0] && Sizzle.isXML( set[0] );

	while ( expr && set.length ) {
		for ( var type in Expr.filter ) {
			if ( (match = Expr.leftMatch[ type ].exec( expr )) != null && match[2] ) {
				var found, item,
					filter = Expr.filter[ type ],
					left = match[1];

				anyFound = false;

				match.splice(1,1);

				if ( left.substr( left.length - 1 ) === "\\" ) {
					continue;
				}

				if ( curLoop === result ) {
					result = [];
				}

				if ( Expr.preFilter[ type ] ) {
					match = Expr.preFilter[ type ]( match, curLoop, inplace, result, not, isXMLFilter );

					if ( !match ) {
						anyFound = found = true;

					} else if ( match === true ) {
						continue;
					}
				}

				if ( match ) {
					for ( var i = 0; (item = curLoop[i]) != null; i++ ) {
						if ( item ) {
							found = filter( item, match, i, curLoop );
							var pass = not ^ !!found;

							if ( inplace && found != null ) {
								if ( pass ) {
									anyFound = true;

								} else {
									curLoop[i] = false;
								}

							} else if ( pass ) {
								result.push( item );
								anyFound = true;
							}
						}
					}
				}

				if ( found !== undefined ) {
					if ( !inplace ) {
						curLoop = result;
					}

					expr = expr.replace( Expr.match[ type ], "" );

					if ( !anyFound ) {
						return [];
					}

					break;
				}
			}
		}

		// Improper expression
		if ( expr === old ) {
			if ( anyFound == null ) {
				Sizzle.error( expr );

			} else {
				break;
			}
		}

		old = expr;
	}

	return curLoop;
};

Sizzle.error = function( msg ) {
	throw "Syntax error, unrecognized expression: " + msg;
};

var Expr = Sizzle.selectors = {
	order: [ "ID", "NAME", "TAG" ],

	match: {
		ID: /#((?:[\w\u00c0-\uFFFF\-]|\\.)+)/,
		CLASS: /\.((?:[\w\u00c0-\uFFFF\-]|\\.)+)/,
		NAME: /\[name=['"]*((?:[\w\u00c0-\uFFFF\-]|\\.)+)['"]*\]/,
		ATTR: /\[\s*((?:[\w\u00c0-\uFFFF\-]|\\.)+)\s*(?:(\S?=)\s*(?:(['"])(.*?)\3|(#?(?:[\w\u00c0-\uFFFF\-]|\\.)*)|)|)\s*\]/,
		TAG: /^((?:[\w\u00c0-\uFFFF\*\-]|\\.)+)/,
		CHILD: /:(only|nth|last|first)-child(?:\(\s*(even|odd|(?:[+\-]?\d+|(?:[+\-]?\d*)?n\s*(?:[+\-]\s*\d+)?))\s*\))?/,
		POS: /:(nth|eq|gt|lt|first|last|even|odd)(?:\((\d*)\))?(?=[^\-]|$)/,
		PSEUDO: /:((?:[\w\u00c0-\uFFFF\-]|\\.)+)(?:\((['"]?)((?:\([^\)]+\)|[^\(\)]*)+)\2\))?/
	},

	leftMatch: {},

	attrMap: {
		"class": "className",
		"for": "htmlFor"
	},

	attrHandle: {
		href: function( elem ) {
			return elem.getAttribute( "href" );
		},
		type: function( elem ) {
			return elem.getAttribute( "type" );
		}
	},

	relative: {
		"+": function(checkSet, part){
			var isPartStr = typeof part === "string",
				isTag = isPartStr && !rNonWord.test( part ),
				isPartStrNotTag = isPartStr && !isTag;

			if ( isTag ) {
				part = part.toLowerCase();
			}

			for ( var i = 0, l = checkSet.length, elem; i < l; i++ ) {
				if ( (elem = checkSet[i]) ) {
					while ( (elem = elem.previousSibling) && elem.nodeType !== 1 ) {}

					checkSet[i] = isPartStrNotTag || elem && elem.nodeName.toLowerCase() === part ?
						elem || false :
						elem === part;
				}
			}

			if ( isPartStrNotTag ) {
				Sizzle.filter( part, checkSet, true );
			}
		},

		">": function( checkSet, part ) {
			var elem,
				isPartStr = typeof part === "string",
				i = 0,
				l = checkSet.length;

			if ( isPartStr && !rNonWord.test( part ) ) {
				part = part.toLowerCase();

				for ( ; i < l; i++ ) {
					elem = checkSet[i];

					if ( elem ) {
						var parent = elem.parentNode;
						checkSet[i] = parent.nodeName.toLowerCase() === part ? parent : false;
					}
				}

			} else {
				for ( ; i < l; i++ ) {
					elem = checkSet[i];

					if ( elem ) {
						checkSet[i] = isPartStr ?
							elem.parentNode :
							elem.parentNode === part;
					}
				}

				if ( isPartStr ) {
					Sizzle.filter( part, checkSet, true );
				}
			}
		},

		"": function(checkSet, part, isXML){
			var nodeCheck,
				doneName = done++,
				checkFn = dirCheck;

			if ( typeof part === "string" && !rNonWord.test( part ) ) {
				part = part.toLowerCase();
				nodeCheck = part;
				checkFn = dirNodeCheck;
			}

			checkFn( "parentNode", part, doneName, checkSet, nodeCheck, isXML );
		},

		"~": function( checkSet, part, isXML ) {
			var nodeCheck,
				doneName = done++,
				checkFn = dirCheck;

			if ( typeof part === "string" && !rNonWord.test( part ) ) {
				part = part.toLowerCase();
				nodeCheck = part;
				checkFn = dirNodeCheck;
			}

			checkFn( "previousSibling", part, doneName, checkSet, nodeCheck, isXML );
		}
	},

	find: {
		ID: function( match, context, isXML ) {
			if ( typeof context.getElementById !== "undefined" && !isXML ) {
				var m = context.getElementById(match[1]);
				// Check parentNode to catch when Blackberry 4.6 returns
				// nodes that are no longer in the document #6963
				return m && m.parentNode ? [m] : [];
			}
		},

		NAME: function( match, context ) {
			if ( typeof context.getElementsByName !== "undefined" ) {
				var ret = [],
					results = context.getElementsByName( match[1] );

				for ( var i = 0, l = results.length; i < l; i++ ) {
					if ( results[i].getAttribute("name") === match[1] ) {
						ret.push( results[i] );
					}
				}

				return ret.length === 0 ? null : ret;
			}
		},

		TAG: function( match, context ) {
			if ( typeof context.getElementsByTagName !== "undefined" ) {
				return context.getElementsByTagName( match[1] );
			}
		}
	},
	preFilter: {
		CLASS: function( match, curLoop, inplace, result, not, isXML ) {
			match = " " + match[1].replace( rBackslash, "" ) + " ";

			if ( isXML ) {
				return match;
			}

			for ( var i = 0, elem; (elem = curLoop[i]) != null; i++ ) {
				if ( elem ) {
					if ( not ^ (elem.className && (" " + elem.className + " ").replace(/[\t\n\r]/g, " ").indexOf(match) >= 0) ) {
						if ( !inplace ) {
							result.push( elem );
						}

					} else if ( inplace ) {
						curLoop[i] = false;
					}
				}
			}

			return false;
		},

		ID: function( match ) {
			return match[1].replace( rBackslash, "" );
		},

		TAG: function( match, curLoop ) {
			return match[1].replace( rBackslash, "" ).toLowerCase();
		},

		CHILD: function( match ) {
			if ( match[1] === "nth" ) {
				if ( !match[2] ) {
					Sizzle.error( match[0] );
				}

				match[2] = match[2].replace(/^\+|\s*/g, '');

				// parse equations like 'even', 'odd', '5', '2n', '3n+2', '4n-1', '-n+6'
				var test = /(-?)(\d*)(?:n([+\-]?\d*))?/.exec(
					match[2] === "even" && "2n" || match[2] === "odd" && "2n+1" ||
					!/\D/.test( match[2] ) && "0n+" + match[2] || match[2]);

				// calculate the numbers (first)n+(last) including if they are negative
				match[2] = (test[1] + (test[2] || 1)) - 0;
				match[3] = test[3] - 0;
			}
			else if ( match[2] ) {
				Sizzle.error( match[0] );
			}

			// TODO: Move to normal caching system
			match[0] = done++;

			return match;
		},

		ATTR: function( match, curLoop, inplace, result, not, isXML ) {
			var name = match[1] = match[1].replace( rBackslash, "" );
			
			if ( !isXML && Expr.attrMap[name] ) {
				match[1] = Expr.attrMap[name];
			}

			// Handle if an un-quoted value was used
			match[4] = ( match[4] || match[5] || "" ).replace( rBackslash, "" );

			if ( match[2] === "~=" ) {
				match[4] = " " + match[4] + " ";
			}

			return match;
		},

		PSEUDO: function( match, curLoop, inplace, result, not ) {
			if ( match[1] === "not" ) {
				// If we're dealing with a complex expression, or a simple one
				if ( ( chunker.exec(match[3]) || "" ).length > 1 || /^\w/.test(match[3]) ) {
					match[3] = Sizzle(match[3], null, null, curLoop);

				} else {
					var ret = Sizzle.filter(match[3], curLoop, inplace, true ^ not);

					if ( !inplace ) {
						result.push.apply( result, ret );
					}

					return false;
				}

			} else if ( Expr.match.POS.test( match[0] ) || Expr.match.CHILD.test( match[0] ) ) {
				return true;
			}
			
			return match;
		},

		POS: function( match ) {
			match.unshift( true );

			return match;
		}
	},
	
	filters: {
		enabled: function( elem ) {
			return elem.disabled === false && elem.type !== "hidden";
		},

		disabled: function( elem ) {
			return elem.disabled === true;
		},

		checked: function( elem ) {
			return elem.checked === true;
		},
		
		selected: function( elem ) {
			// Accessing this property makes selected-by-default
			// options in Safari work properly
			if ( elem.parentNode ) {
				elem.parentNode.selectedIndex;
			}
			
			return elem.selected === true;
		},

		parent: function( elem ) {
			return !!elem.firstChild;
		},

		empty: function( elem ) {
			return !elem.firstChild;
		},

		has: function( elem, i, match ) {
			return !!Sizzle( match[3], elem ).length;
		},

		header: function( elem ) {
			return (/h\d/i).test( elem.nodeName );
		},

		text: function( elem ) {
			var attr = elem.getAttribute( "type" ), type = elem.type;
			// IE6 and 7 will map elem.type to 'text' for new HTML5 types (search, etc) 
			// use getAttribute instead to test this case
			return elem.nodeName.toLowerCase() === "input" && "text" === type && ( attr === type || attr === null );
		},

		radio: function( elem ) {
			return elem.nodeName.toLowerCase() === "input" && "radio" === elem.type;
		},

		checkbox: function( elem ) {
			return elem.nodeName.toLowerCase() === "input" && "checkbox" === elem.type;
		},

		file: function( elem ) {
			return elem.nodeName.toLowerCase() === "input" && "file" === elem.type;
		},

		password: function( elem ) {
			return elem.nodeName.toLowerCase() === "input" && "password" === elem.type;
		},

		submit: function( elem ) {
			var name = elem.nodeName.toLowerCase();
			return (name === "input" || name === "button") && "submit" === elem.type;
		},

		image: function( elem ) {
			return elem.nodeName.toLowerCase() === "input" && "image" === elem.type;
		},

		reset: function( elem ) {
			var name = elem.nodeName.toLowerCase();
			return (name === "input" || name === "button") && "reset" === elem.type;
		},

		button: function( elem ) {
			var name = elem.nodeName.toLowerCase();
			return name === "input" && "button" === elem.type || name === "button";
		},

		input: function( elem ) {
			return (/input|select|textarea|button/i).test( elem.nodeName );
		},

		focus: function( elem ) {
			return elem === elem.ownerDocument.activeElement;
		}
	},
	setFilters: {
		first: function( elem, i ) {
			return i === 0;
		},

		last: function( elem, i, match, array ) {
			return i === array.length - 1;
		},

		even: function( elem, i ) {
			return i % 2 === 0;
		},

		odd: function( elem, i ) {
			return i % 2 === 1;
		},

		lt: function( elem, i, match ) {
			return i < match[3] - 0;
		},

		gt: function( elem, i, match ) {
			return i > match[3] - 0;
		},

		nth: function( elem, i, match ) {
			return match[3] - 0 === i;
		},

		eq: function( elem, i, match ) {
			return match[3] - 0 === i;
		}
	},
	filter: {
		PSEUDO: function( elem, match, i, array ) {
			var name = match[1],
				filter = Expr.filters[ name ];

			if ( filter ) {
				return filter( elem, i, match, array );

			} else if ( name === "contains" ) {
				return (elem.textContent || elem.innerText || Sizzle.getText([ elem ]) || "").indexOf(match[3]) >= 0;

			} else if ( name === "not" ) {
				var not = match[3];

				for ( var j = 0, l = not.length; j < l; j++ ) {
					if ( not[j] === elem ) {
						return false;
					}
				}

				return true;

			} else {
				Sizzle.error( name );
			}
		},

		CHILD: function( elem, match ) {
			var type = match[1],
				node = elem;

			switch ( type ) {
				case "only":
				case "first":
					while ( (node = node.previousSibling) )	 {
						if ( node.nodeType === 1 ) { 
							return false; 
						}
					}

					if ( type === "first" ) { 
						return true; 
					}

					node = elem;

				case "last":
					while ( (node = node.nextSibling) )	 {
						if ( node.nodeType === 1 ) { 
							return false; 
						}
					}

					return true;

				case "nth":
					var first = match[2],
						last = match[3];

					if ( first === 1 && last === 0 ) {
						return true;
					}
					
					var doneName = match[0],
						parent = elem.parentNode;
	
					if ( parent && (parent.sizcache !== doneName || !elem.nodeIndex) ) {
						var count = 0;
						
						for ( node = parent.firstChild; node; node = node.nextSibling ) {
							if ( node.nodeType === 1 ) {
								node.nodeIndex = ++count;
							}
						} 

						parent.sizcache = doneName;
					}
					
					var diff = elem.nodeIndex - last;

					if ( first === 0 ) {
						return diff === 0;

					} else {
						return ( diff % first === 0 && diff / first >= 0 );
					}
			}
		},

		ID: function( elem, match ) {
			return elem.nodeType === 1 && elem.getAttribute("id") === match;
		},

		TAG: function( elem, match ) {
			return (match === "*" && elem.nodeType === 1) || elem.nodeName.toLowerCase() === match;
		},
		
		CLASS: function( elem, match ) {
			return (" " + (elem.className || elem.getAttribute("class")) + " ")
				.indexOf( match ) > -1;
		},

		ATTR: function( elem, match ) {
			var name = match[1],
				result = Expr.attrHandle[ name ] ?
					Expr.attrHandle[ name ]( elem ) :
					elem[ name ] != null ?
						elem[ name ] :
						elem.getAttribute( name ),
				value = result + "",
				type = match[2],
				check = match[4];

			return result == null ?
				type === "!=" :
				type === "=" ?
				value === check :
				type === "*=" ?
				value.indexOf(check) >= 0 :
				type === "~=" ?
				(" " + value + " ").indexOf(check) >= 0 :
				!check ?
				value && result !== false :
				type === "!=" ?
				value !== check :
				type === "^=" ?
				value.indexOf(check) === 0 :
				type === "$=" ?
				value.substr(value.length - check.length) === check :
				type === "|=" ?
				value === check || value.substr(0, check.length + 1) === check + "-" :
				false;
		},

		POS: function( elem, match, i, array ) {
			var name = match[2],
				filter = Expr.setFilters[ name ];

			if ( filter ) {
				return filter( elem, i, match, array );
			}
		}
	}
};

var origPOS = Expr.match.POS,
	fescape = function(all, num){
		return "\\" + (num - 0 + 1);
	};

for ( var type in Expr.match ) {
	Expr.match[ type ] = new RegExp( Expr.match[ type ].source + (/(?![^\[]*\])(?![^\(]*\))/.source) );
	Expr.leftMatch[ type ] = new RegExp( /(^(?:.|\r|\n)*?)/.source + Expr.match[ type ].source.replace(/\\(\d+)/g, fescape) );
}

var makeArray = function( array, results ) {
	array = Array.prototype.slice.call( array, 0 );

	if ( results ) {
		results.push.apply( results, array );
		return results;
	}
	
	return array;
};

// Perform a simple check to determine if the browser is capable of
// converting a NodeList to an array using builtin methods.
// Also verifies that the returned array holds DOM nodes
// (which is not the case in the Blackberry browser)
try {
	Array.prototype.slice.call( document.documentElement.childNodes, 0 )[0].nodeType;

// Provide a fallback method if it does not work
} catch( e ) {
	makeArray = function( array, results ) {
		var i = 0,
			ret = results || [];

		if ( toString.call(array) === "[object Array]" ) {
			Array.prototype.push.apply( ret, array );

		} else {
			if ( typeof array.length === "number" ) {
				for ( var l = array.length; i < l; i++ ) {
					ret.push( array[i] );
				}

			} else {
				for ( ; array[i]; i++ ) {
					ret.push( array[i] );
				}
			}
		}

		return ret;
	};
}

var sortOrder, siblingCheck;

if ( document.documentElement.compareDocumentPosition ) {
	sortOrder = function( a, b ) {
		if ( a === b ) {
			hasDuplicate = true;
			return 0;
		}

		if ( !a.compareDocumentPosition || !b.compareDocumentPosition ) {
			return a.compareDocumentPosition ? -1 : 1;
		}

		return a.compareDocumentPosition(b) & 4 ? -1 : 1;
	};

} else {
	sortOrder = function( a, b ) {
		// The nodes are identical, we can exit early
		if ( a === b ) {
			hasDuplicate = true;
			return 0;

		// Fallback to using sourceIndex (in IE) if it's available on both nodes
		} else if ( a.sourceIndex && b.sourceIndex ) {
			return a.sourceIndex - b.sourceIndex;
		}

		var al, bl,
			ap = [],
			bp = [],
			aup = a.parentNode,
			bup = b.parentNode,
			cur = aup;

		// If the nodes are siblings (or identical) we can do a quick check
		if ( aup === bup ) {
			return siblingCheck( a, b );

		// If no parents were found then the nodes are disconnected
		} else if ( !aup ) {
			return -1;

		} else if ( !bup ) {
			return 1;
		}

		// Otherwise they're somewhere else in the tree so we need
		// to build up a full list of the parentNodes for comparison
		while ( cur ) {
			ap.unshift( cur );
			cur = cur.parentNode;
		}

		cur = bup;

		while ( cur ) {
			bp.unshift( cur );
			cur = cur.parentNode;
		}

		al = ap.length;
		bl = bp.length;

		// Start walking down the tree looking for a discrepancy
		for ( var i = 0; i < al && i < bl; i++ ) {
			if ( ap[i] !== bp[i] ) {
				return siblingCheck( ap[i], bp[i] );
			}
		}

		// We ended someplace up the tree so do a sibling check
		return i === al ?
			siblingCheck( a, bp[i], -1 ) :
			siblingCheck( ap[i], b, 1 );
	};

	siblingCheck = function( a, b, ret ) {
		if ( a === b ) {
			return ret;
		}

		var cur = a.nextSibling;

		while ( cur ) {
			if ( cur === b ) {
				return -1;
			}

			cur = cur.nextSibling;
		}

		return 1;
	};
}

// Utility function for retreiving the text value of an array of DOM nodes
Sizzle.getText = function( elems ) {
	var ret = "", elem;

	for ( var i = 0; elems[i]; i++ ) {
		elem = elems[i];

		// Get the text from text nodes and CDATA nodes
		if ( elem.nodeType === 3 || elem.nodeType === 4 ) {
			ret += elem.nodeValue;

		// Traverse everything else, except comment nodes
		} else if ( elem.nodeType !== 8 ) {
			ret += Sizzle.getText( elem.childNodes );
		}
	}

	return ret;
};

// Check to see if the browser returns elements by name when
// querying by getElementById (and provide a workaround)
(function(){
	// We're going to inject a fake input element with a specified name
	var form = document.createElement("div"),
		id = "script" + (new Date()).getTime(),
		root = document.documentElement;

	form.innerHTML = "<a name='" + id + "'/>";

	// Inject it into the root element, check its status, and remove it quickly
	root.insertBefore( form, root.firstChild );

	// The workaround has to do additional checks after a getElementById
	// Which slows things down for other browsers (hence the branching)
	if ( document.getElementById( id ) ) {
		Expr.find.ID = function( match, context, isXML ) {
			if ( typeof context.getElementById !== "undefined" && !isXML ) {
				var m = context.getElementById(match[1]);

				return m ?
					m.id === match[1] || typeof m.getAttributeNode !== "undefined" && m.getAttributeNode("id").nodeValue === match[1] ?
						[m] :
						undefined :
					[];
			}
		};

		Expr.filter.ID = function( elem, match ) {
			var node = typeof elem.getAttributeNode !== "undefined" && elem.getAttributeNode("id");

			return elem.nodeType === 1 && node && node.nodeValue === match;
		};
	}

	root.removeChild( form );

	// release memory in IE
	root = form = null;
})();

(function(){
	// Check to see if the browser returns only elements
	// when doing getElementsByTagName("*")

	// Create a fake element
	var div = document.createElement("div");
	div.appendChild( document.createComment("") );

	// Make sure no comments are found
	if ( div.getElementsByTagName("*").length > 0 ) {
		Expr.find.TAG = function( match, context ) {
			var results = context.getElementsByTagName( match[1] );

			// Filter out possible comments
			if ( match[1] === "*" ) {
				var tmp = [];

				for ( var i = 0; results[i]; i++ ) {
					if ( results[i].nodeType === 1 ) {
						tmp.push( results[i] );
					}
				}

				results = tmp;
			}

			return results;
		};
	}

	// Check to see if an attribute returns normalized href attributes
	div.innerHTML = "<a href='#'></a>";

	if ( div.firstChild && typeof div.firstChild.getAttribute !== "undefined" &&
			div.firstChild.getAttribute("href") !== "#" ) {

		Expr.attrHandle.href = function( elem ) {
			return elem.getAttribute( "href", 2 );
		};
	}

	// release memory in IE
	div = null;
})();

if ( document.querySelectorAll ) {
	(function(){
		var oldSizzle = Sizzle,
			div = document.createElement("div"),
			id = "__sizzle__";

		div.innerHTML = "<p class='TEST'></p>";

		// Safari can't handle uppercase or unicode characters when
		// in quirks mode.
		if ( div.querySelectorAll && div.querySelectorAll(".TEST").length === 0 ) {
			return;
		}
	
		Sizzle = function( query, context, extra, seed ) {
			context = context || document;

			// Only use querySelectorAll on non-XML documents
			// (ID selectors don't work in non-HTML documents)
			if ( !seed && !Sizzle.isXML(context) ) {
				// See if we find a selector to speed up
				var match = /^(\w+$)|^\.([\w\-]+$)|^#([\w\-]+$)/.exec( query );
				
				if ( match && (context.nodeType === 1 || context.nodeType === 9) ) {
					// Speed-up: Sizzle("TAG")
					if ( match[1] ) {
						return makeArray( context.getElementsByTagName( query ), extra );
					
					// Speed-up: Sizzle(".CLASS")
					} else if ( match[2] && Expr.find.CLASS && context.getElementsByClassName ) {
						return makeArray( context.getElementsByClassName( match[2] ), extra );
					}
				}
				
				if ( context.nodeType === 9 ) {
					// Speed-up: Sizzle("body")
					// The body element only exists once, optimize finding it
					if ( query === "body" && context.body ) {
						return makeArray( [ context.body ], extra );
						
					// Speed-up: Sizzle("#ID")
					} else if ( match && match[3] ) {
						var elem = context.getElementById( match[3] );

						// Check parentNode to catch when Blackberry 4.6 returns
						// nodes that are no longer in the document #6963
						if ( elem && elem.parentNode ) {
							// Handle the case where IE and Opera return items
							// by name instead of ID
							if ( elem.id === match[3] ) {
								return makeArray( [ elem ], extra );
							}
							
						} else {
							return makeArray( [], extra );
						}
					}
					
					try {
						return makeArray( context.querySelectorAll(query), extra );
					} catch(qsaError) {}

				// qSA works strangely on Element-rooted queries
				// We can work around this by specifying an extra ID on the root
				// and working up from there (Thanks to Andrew Dupont for the technique)
				// IE 8 doesn't work on object elements
				} else if ( context.nodeType === 1 && context.nodeName.toLowerCase() !== "object" ) {
					var oldContext = context,
						old = context.getAttribute( "id" ),
						nid = old || id,
						hasParent = context.parentNode,
						relativeHierarchySelector = /^\s*[+~]/.test( query );

					if ( !old ) {
						context.setAttribute( "id", nid );
					} else {
						nid = nid.replace( /'/g, "\\$&" );
					}
					if ( relativeHierarchySelector && hasParent ) {
						context = context.parentNode;
					}

					try {
						if ( !relativeHierarchySelector || hasParent ) {
							return makeArray( context.querySelectorAll( "[id='" + nid + "'] " + query ), extra );
						}

					} catch(pseudoError) {
					} finally {
						if ( !old ) {
							oldContext.removeAttribute( "id" );
						}
					}
				}
			}
		
			return oldSizzle(query, context, extra, seed);
		};

		for ( var prop in oldSizzle ) {
			Sizzle[ prop ] = oldSizzle[ prop ];
		}

		// release memory in IE
		div = null;
	})();
}

(function(){
	var html = document.documentElement,
		matches = html.matchesSelector || html.mozMatchesSelector || html.webkitMatchesSelector || html.msMatchesSelector;

	if ( matches ) {
		// Check to see if it's possible to do matchesSelector
		// on a disconnected node (IE 9 fails this)
		var disconnectedMatch = !matches.call( document.createElement( "div" ), "div" ),
			pseudoWorks = false;

		try {
			// This should fail with an exception
			// Gecko does not error, returns false instead
			matches.call( document.documentElement, "[test!='']:sizzle" );
	
		} catch( pseudoError ) {
			pseudoWorks = true;
		}

		Sizzle.matchesSelector = function( node, expr ) {
			// Make sure that attribute selectors are quoted
			expr = expr.replace(/\=\s*([^'"\]]*)\s*\]/g, "='$1']");

			if ( !Sizzle.isXML( node ) ) {
				try { 
					if ( pseudoWorks || !Expr.match.PSEUDO.test( expr ) && !/!=/.test( expr ) ) {
						var ret = matches.call( node, expr );

						// IE 9's matchesSelector returns false on disconnected nodes
						if ( ret || !disconnectedMatch ||
								// As well, disconnected nodes are said to be in a document
								// fragment in IE 9, so check for that
								node.document && node.document.nodeType !== 11 ) {
							return ret;
						}
					}
				} catch(e) {}
			}

			return Sizzle(expr, null, null, [node]).length > 0;
		};
	}
})();

(function(){
	var div = document.createElement("div");

	div.innerHTML = "<div class='test e'></div><div class='test'></div>";

	// Opera can't find a second classname (in 9.6)
	// Also, make sure that getElementsByClassName actually exists
	if ( !div.getElementsByClassName || div.getElementsByClassName("e").length === 0 ) {
		return;
	}

	// Safari caches class attributes, doesn't catch changes (in 3.2)
	div.lastChild.className = "e";

	if ( div.getElementsByClassName("e").length === 1 ) {
		return;
	}
	
	Expr.order.splice(1, 0, "CLASS");
	Expr.find.CLASS = function( match, context, isXML ) {
		if ( typeof context.getElementsByClassName !== "undefined" && !isXML ) {
			return context.getElementsByClassName(match[1]);
		}
	};

	// release memory in IE
	div = null;
})();

function dirNodeCheck( dir, cur, doneName, checkSet, nodeCheck, isXML ) {
	for ( var i = 0, l = checkSet.length; i < l; i++ ) {
		var elem = checkSet[i];

		if ( elem ) {
			var match = false;

			elem = elem[dir];

			while ( elem ) {
				if ( elem.sizcache === doneName ) {
					match = checkSet[elem.sizset];
					break;
				}

				if ( elem.nodeType === 1 && !isXML ){
					elem.sizcache = doneName;
					elem.sizset = i;
				}

				if ( elem.nodeName.toLowerCase() === cur ) {
					match = elem;
					break;
				}

				elem = elem[dir];
			}

			checkSet[i] = match;
		}
	}
}

function dirCheck( dir, cur, doneName, checkSet, nodeCheck, isXML ) {
	for ( var i = 0, l = checkSet.length; i < l; i++ ) {
		var elem = checkSet[i];

		if ( elem ) {
			var match = false;
			
			elem = elem[dir];

			while ( elem ) {
				if ( elem.sizcache === doneName ) {
					match = checkSet[elem.sizset];
					break;
				}

				if ( elem.nodeType === 1 ) {
					if ( !isXML ) {
						elem.sizcache = doneName;
						elem.sizset = i;
					}

					if ( typeof cur !== "string" ) {
						if ( elem === cur ) {
							match = true;
							break;
						}

					} else if ( Sizzle.filter( cur, [elem] ).length > 0 ) {
						match = elem;
						break;
					}
				}

				elem = elem[dir];
			}

			checkSet[i] = match;
		}
	}
}

if ( document.documentElement.contains ) {
	Sizzle.contains = function( a, b ) {
		return a !== b && (a.contains ? a.contains(b) : true);
	};

} else if ( document.documentElement.compareDocumentPosition ) {
	Sizzle.contains = function( a, b ) {
		return !!(a.compareDocumentPosition(b) & 16);
	};

} else {
	Sizzle.contains = function() {
		return false;
	};
}

Sizzle.isXML = function( elem ) {
	// documentElement is verified for cases where it doesn't yet exist
	// (such as loading iframes in IE - #4833) 
	var documentElement = (elem ? elem.ownerDocument || elem : 0).documentElement;

	return documentElement ? documentElement.nodeName !== "HTML" : false;
};

var posProcess = function( selector, context ) {
	var match,
		tmpSet = [],
		later = "",
		root = context.nodeType ? [context] : context;

	// Position selectors must be done after the filter
	// And so must :not(positional) so we move all PSEUDOs to the end
	while ( (match = Expr.match.PSEUDO.exec( selector )) ) {
		later += match[0];
		selector = selector.replace( Expr.match.PSEUDO, "" );
	}

	selector = Expr.relative[selector] ? selector + "*" : selector;

	for ( var i = 0, l = root.length; i < l; i++ ) {
		Sizzle( selector, root[i], tmpSet );
	}

	return Sizzle.filter( later, tmpSet );
};

// EXPOSE

window.Sizzle = Sizzle;

})();

/*  Prototype JavaScript framework, version 1.7
 *  (c) 2005-2010 Sam Stephenson
 *
 *  Prototype is freely distributable under the terms of an MIT-style license.
 *  For details, see the Prototype web site: http://www.prototypejs.org/
 *
 *--------------------------------------------------------------------------*/

var Prototype = {

  Version: '1.7',

  Browser: (function(){
    var ua = navigator.userAgent;
    var isOpera = Object.prototype.toString.call(window.opera) == '[object Opera]';
    return {
      IE:             !!window.attachEvent && !isOpera,
      Opera:          isOpera,
      WebKit:         ua.indexOf('AppleWebKit/') > -1,
      Gecko:          ua.indexOf('Gecko') > -1 && ua.indexOf('KHTML') === -1,
      MobileSafari:   /Apple.*Mobile/.test(ua)
    }
  })(),

  BrowserFeatures: {
    XPath: !!document.evaluate,

    SelectorsAPI: !!document.querySelector,

    ElementExtensions: (function() {
      var constructor = window.Element || window.HTMLElement;
      return !!(constructor && constructor.prototype);
    })(),
    SpecificElementExtensions: (function() {
      if (typeof window.HTMLDivElement !== 'undefined')
        return true;

      var div = document.createElement('div'),
          form = document.createElement('form'),
          isSupported = false;

      if (div['__proto__'] && (div['__proto__'] !== form['__proto__'])) {
        isSupported = true;
      }

      div = form = null;

      return isSupported;
    })()
  },

  ScriptFragment: '<script[^>]*>([\\S\\s]*?)<\/script>',
  JSONFilter: /^\/\*-secure-([\s\S]*)\*\/\s*$/,

  emptyFunction: function() { },

  K: function(x) { return x }
};

if (Prototype.Browser.MobileSafari)
  Prototype.BrowserFeatures.SpecificElementExtensions = false;


var Abstract = { };


var Try = {
  these: function() {
    var returnValue;

    for (var i = 0, length = arguments.length; i < length; i++) {
      var lambda = arguments[i];
      try {
        returnValue = lambda();
        break;
      } catch (e) { }
    }

    return returnValue;
  }
};

/* Based on Alex Arnell's inheritance implementation. */

var Class = (function() {

  var IS_DONTENUM_BUGGY = (function(){
    for (var p in { toString: 1 }) {
      if (p === 'toString') return false;
    }
    return true;
  })();

  function subclass() {};
  function create() {
    var parent = null, properties = $A(arguments);
    if (Object.isFunction(properties[0]))
      parent = properties.shift();

    function klass() {
      this.initialize.apply(this, arguments);
    }

    Object.extend(klass, Class.Methods);
    klass.superclass = parent;
    klass.subclasses = [];

    if (parent) {
      subclass.prototype = parent.prototype;
      klass.prototype = new subclass;
      parent.subclasses.push(klass);
    }

    for (var i = 0, length = properties.length; i < length; i++)
      klass.addMethods(properties[i]);

    if (!klass.prototype.initialize)
      klass.prototype.initialize = Prototype.emptyFunction;

    klass.prototype.constructor = klass;
    return klass;
  }

  function addMethods(source) {
    var ancestor   = this.superclass && this.superclass.prototype,
        properties = Object.keys(source);

    if (IS_DONTENUM_BUGGY) {
      if (source.toString != Object.prototype.toString)
        properties.push("toString");
      if (source.valueOf != Object.prototype.valueOf)
        properties.push("valueOf");
    }

    for (var i = 0, length = properties.length; i < length; i++) {
      var property = properties[i], value = source[property];
      if (ancestor && Object.isFunction(value) &&
          value.argumentNames()[0] == "$super") {
        var method = value;
        value = (function(m) {
          return function() { return ancestor[m].apply(this, arguments); };
        })(property).wrap(method);

        value.valueOf = method.valueOf.bind(method);
        value.toString = method.toString.bind(method);
      }
      this.prototype[property] = value;
    }

    return this;
  }

  return {
    create: create,
    Methods: {
      addMethods: addMethods
    }
  };
})();
(function() {

  var _toString = Object.prototype.toString,
      NULL_TYPE = 'Null',
      UNDEFINED_TYPE = 'Undefined',
      BOOLEAN_TYPE = 'Boolean',
      NUMBER_TYPE = 'Number',
      STRING_TYPE = 'String',
      OBJECT_TYPE = 'Object',
      FUNCTION_CLASS = '[object Function]',
      BOOLEAN_CLASS = '[object Boolean]',
      NUMBER_CLASS = '[object Number]',
      STRING_CLASS = '[object String]',
      ARRAY_CLASS = '[object Array]',
      DATE_CLASS = '[object Date]',
      NATIVE_JSON_STRINGIFY_SUPPORT = window.JSON &&
        typeof JSON.stringify === 'function' &&
        JSON.stringify(0) === '0' &&
        typeof JSON.stringify(Prototype.K) === 'undefined';

  function Type(o) {
    switch(o) {
      case null: return NULL_TYPE;
      case (void 0): return UNDEFINED_TYPE;
    }
    var type = typeof o;
    switch(type) {
      case 'boolean': return BOOLEAN_TYPE;
      case 'number':  return NUMBER_TYPE;
      case 'string':  return STRING_TYPE;
    }
    return OBJECT_TYPE;
  }

  function extend(destination, source) {
    for (var property in source)
      destination[property] = source[property];
    return destination;
  }

  function inspect(object) {
    try {
      if (isUndefined(object)) return 'undefined';
      if (object === null) return 'null';
      return object.inspect ? object.inspect() : String(object);
    } catch (e) {
      if (e instanceof RangeError) return '...';
      throw e;
    }
  }

  function toJSON(value) {
    return Str('', { '': value }, []);
  }

  function Str(key, holder, stack) {
    var value = holder[key],
        type = typeof value;

    if (Type(value) === OBJECT_TYPE && typeof value.toJSON === 'function') {
      value = value.toJSON(key);
    }

    var _class = _toString.call(value);

    switch (_class) {
      case NUMBER_CLASS:
      case BOOLEAN_CLASS:
      case STRING_CLASS:
        value = value.valueOf();
    }

    switch (value) {
      case null: return 'null';
      case true: return 'true';
      case false: return 'false';
    }

    type = typeof value;
    switch (type) {
      case 'string':
        return value.inspect(true);
      case 'number':
        return isFinite(value) ? String(value) : 'null';
      case 'object':

        for (var i = 0, length = stack.length; i < length; i++) {
          if (stack[i] === value) { throw new TypeError(); }
        }
        stack.push(value);

        var partial = [];
        if (_class === ARRAY_CLASS) {
          for (var i = 0, length = value.length; i < length; i++) {
            var str = Str(i, value, stack);
            partial.push(typeof str === 'undefined' ? 'null' : str);
          }
          partial = '[' + partial.join(',') + ']';
        } else {
          var keys = Object.keys(value);
          for (var i = 0, length = keys.length; i < length; i++) {
            var key = keys[i], str = Str(key, value, stack);
            if (typeof str !== "undefined") {
               partial.push(key.inspect(true)+ ':' + str);
             }
          }
          partial = '{' + partial.join(',') + '}';
        }
        stack.pop();
        return partial;
    }
  }

  function stringify(object) {
    return JSON.stringify(object);
  }

  function toQueryString(object) {
    return $H(object).toQueryString();
  }

  function toHTML(object) {
    return object && object.toHTML ? object.toHTML() : String.interpret(object);
  }

  function keys(object) {
    if (Type(object) !== OBJECT_TYPE) { throw new TypeError(); }
    var results = [];
    for (var property in object) {
      if (object.hasOwnProperty(property)) {
        results.push(property);
      }
    }
    return results;
  }

  function values(object) {
    var results = [];
    for (var property in object)
      results.push(object[property]);
    return results;
  }

  function clone(object) {
    return extend({ }, object);
  }

  function isElement(object) {
    return !!(object && object.nodeType == 1);
  }

  function isArray(object) {
    return _toString.call(object) === ARRAY_CLASS;
  }

  var hasNativeIsArray = (typeof Array.isArray == 'function')
    && Array.isArray([]) && !Array.isArray({});

  if (hasNativeIsArray) {
    isArray = Array.isArray;
  }

  function isHash(object) {
    return object instanceof Hash;
  }

  function isFunction(object) {
    return _toString.call(object) === FUNCTION_CLASS;
  }

  function isString(object) {
    return _toString.call(object) === STRING_CLASS;
  }

  function isNumber(object) {
    return _toString.call(object) === NUMBER_CLASS;
  }

  function isDate(object) {
    return _toString.call(object) === DATE_CLASS;
  }

  function isUndefined(object) {
    return typeof object === "undefined";
  }

  extend(Object, {
    extend:        extend,
    inspect:       inspect,
    toJSON:        NATIVE_JSON_STRINGIFY_SUPPORT ? stringify : toJSON,
    toQueryString: toQueryString,
    toHTML:        toHTML,
    keys:          Object.keys || keys,
    values:        values,
    clone:         clone,
    isElement:     isElement,
    isArray:       isArray,
    isHash:        isHash,
    isFunction:    isFunction,
    isString:      isString,
    isNumber:      isNumber,
    isDate:        isDate,
    isUndefined:   isUndefined
  });
})();
Object.extend(Function.prototype, (function() {
  var slice = Array.prototype.slice;

  function update(array, args) {
    var arrayLength = array.length, length = args.length;
    while (length--) array[arrayLength + length] = args[length];
    return array;
  }

  function merge(array, args) {
    array = slice.call(array, 0);
    return update(array, args);
  }

  function argumentNames() {
    var names = this.toString().match(/^[\s\(]*function[^(]*\(([^)]*)\)/)[1]
      .replace(/\/\/.*?[\r\n]|\/\*(?:.|[\r\n])*?\*\//g, '')
      .replace(/\s+/g, '').split(',');
    return names.length == 1 && !names[0] ? [] : names;
  }

  function bind(context) {
    if (arguments.length < 2 && Object.isUndefined(arguments[0])) return this;
    var __method = this, args = slice.call(arguments, 1);
    return function() {
      var a = merge(args, arguments);
      return __method.apply(context, a);
    }
  }

  function bindAsEventListener(context) {
    var __method = this, args = slice.call(arguments, 1);
    return function(event) {
      var a = update([event || window.event], args);
      return __method.apply(context, a);
    }
  }

  function curry() {
    if (!arguments.length) return this;
    var __method = this, args = slice.call(arguments, 0);
    return function() {
      var a = merge(args, arguments);
      return __method.apply(this, a);
    }
  }

  function delay(timeout) {
    var __method = this, args = slice.call(arguments, 1);
    timeout = timeout * 1000;
    return window.setTimeout(function() {
      return __method.apply(__method, args);
    }, timeout);
  }

  function defer() {
    var args = update([0.01], arguments);
    return this.delay.apply(this, args);
  }

  function wrap(wrapper) {
    var __method = this;
    return function() {
      var a = update([__method.bind(this)], arguments);
      return wrapper.apply(this, a);
    }
  }

  function methodize() {
    if (this._methodized) return this._methodized;
    var __method = this;
    return this._methodized = function() {
      var a = update([this], arguments);
      return __method.apply(null, a);
    };
  }

  return {
    argumentNames:       argumentNames,
    bind:                bind,
    bindAsEventListener: bindAsEventListener,
    curry:               curry,
    delay:               delay,
    defer:               defer,
    wrap:                wrap,
    methodize:           methodize
  }
})());



(function(proto) {


  function toISOString() {
    return this.getUTCFullYear() + '-' +
      (this.getUTCMonth() + 1).toPaddedString(2) + '-' +
      this.getUTCDate().toPaddedString(2) + 'T' +
      this.getUTCHours().toPaddedString(2) + ':' +
      this.getUTCMinutes().toPaddedString(2) + ':' +
      this.getUTCSeconds().toPaddedString(2) + 'Z';
  }


  function toJSON() {
    return this.toISOString();
  }

  if (!proto.toISOString) proto.toISOString = toISOString;
  if (!proto.toJSON) proto.toJSON = toJSON;

})(Date.prototype);


RegExp.prototype.match = RegExp.prototype.test;

RegExp.escape = function(str) {
  return String(str).replace(/([.*+?^=!:${}()|[\]\/\\])/g, '\\$1');
};
var PeriodicalExecuter = Class.create({
  initialize: function(callback, frequency) {
    this.callback = callback;
    this.frequency = frequency;
    this.currentlyExecuting = false;

    this.registerCallback();
  },

  registerCallback: function() {
    this.timer = setInterval(this.onTimerEvent.bind(this), this.frequency * 1000);
  },

  execute: function() {
    this.callback(this);
  },

  stop: function() {
    if (!this.timer) return;
    clearInterval(this.timer);
    this.timer = null;
  },

  onTimerEvent: function() {
    if (!this.currentlyExecuting) {
      try {
        this.currentlyExecuting = true;
        this.execute();
        this.currentlyExecuting = false;
      } catch(e) {
        this.currentlyExecuting = false;
        throw e;
      }
    }
  }
});
Object.extend(String, {
  interpret: function(value) {
    return value == null ? '' : String(value);
  },
  specialChar: {
    '\b': '\\b',
    '\t': '\\t',
    '\n': '\\n',
    '\f': '\\f',
    '\r': '\\r',
    '\\': '\\\\'
  }
});

Object.extend(String.prototype, (function() {
  var NATIVE_JSON_PARSE_SUPPORT = window.JSON &&
    typeof JSON.parse === 'function' &&
    JSON.parse('{"test": true}').test;

  function prepareReplacement(replacement) {
    if (Object.isFunction(replacement)) return replacement;
    var template = new Template(replacement);
    return function(match) { return template.evaluate(match) };
  }

  function gsub(pattern, replacement) {
    var result = '', source = this, match;
    replacement = prepareReplacement(replacement);

    if (Object.isString(pattern))
      pattern = RegExp.escape(pattern);

    if (!(pattern.length || pattern.source)) {
      replacement = replacement('');
      return replacement + source.split('').join(replacement) + replacement;
    }

    while (source.length > 0) {
      if (match = source.match(pattern)) {
        result += source.slice(0, match.index);
        result += String.interpret(replacement(match));
        source  = source.slice(match.index + match[0].length);
      } else {
        result += source, source = '';
      }
    }
    return result;
  }

  function sub(pattern, replacement, count) {
    replacement = prepareReplacement(replacement);
    count = Object.isUndefined(count) ? 1 : count;

    return this.gsub(pattern, function(match) {
      if (--count < 0) return match[0];
      return replacement(match);
    });
  }

  function scan(pattern, iterator) {
    this.gsub(pattern, iterator);
    return String(this);
  }

  function truncate(length, truncation) {
    length = length || 30;
    truncation = Object.isUndefined(truncation) ? '...' : truncation;
    return this.length > length ?
      this.slice(0, length - truncation.length) + truncation : String(this);
  }

  function strip() {
    return this.replace(/^\s+/, '').replace(/\s+$/, '');
  }

  function stripTags() {
    return this.replace(/<\w+(\s+("[^"]*"|'[^']*'|[^>])+)?>|<\/\w+>/gi, '');
  }

  function stripScripts() {
    return this.replace(new RegExp(Prototype.ScriptFragment, 'img'), '');
  }

  function extractScripts() {
    var matchAll = new RegExp(Prototype.ScriptFragment, 'img'),
        matchOne = new RegExp(Prototype.ScriptFragment, 'im');
    return (this.match(matchAll) || []).map(function(scriptTag) {
      return (scriptTag.match(matchOne) || ['', ''])[1];
    });
  }

  function evalScripts() {
    return this.extractScripts().map(function(script) { return eval(script) });
  }

  function escapeHTML() {
    return this.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }

  function unescapeHTML() {
    return this.stripTags().replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&');
  }


  function toQueryParams(separator) {
    var match = this.strip().match(/([^?#]*)(#.*)?$/);
    if (!match) return { };

    return match[1].split(separator || '&').inject({ }, function(hash, pair) {
      if ((pair = pair.split('='))[0]) {
        var key = decodeURIComponent(pair.shift()),
            value = pair.length > 1 ? pair.join('=') : pair[0];

        if (value != undefined) value = decodeURIComponent(value);

        if (key in hash) {
          if (!Object.isArray(hash[key])) hash[key] = [hash[key]];
          hash[key].push(value);
        }
        else hash[key] = value;
      }
      return hash;
    });
  }

  function toArray() {
    return this.split('');
  }

  function succ() {
    return this.slice(0, this.length - 1) +
      String.fromCharCode(this.charCodeAt(this.length - 1) + 1);
  }

  function times(count) {
    return count < 1 ? '' : new Array(count + 1).join(this);
  }

  function camelize() {
    return this.replace(/-+(.)?/g, function(match, chr) {
      return chr ? chr.toUpperCase() : '';
    });
  }

  function capitalize() {
    return this.charAt(0).toUpperCase() + this.substring(1).toLowerCase();
  }

  function underscore() {
    return this.replace(/::/g, '/')
               .replace(/([A-Z]+)([A-Z][a-z])/g, '$1_$2')
               .replace(/([a-z\d])([A-Z])/g, '$1_$2')
               .replace(/-/g, '_')
               .toLowerCase();
  }

  function dasherize() {
    return this.replace(/_/g, '-');
  }

  function inspect(useDoubleQuotes) {
    var escapedString = this.replace(/[\x00-\x1f\\]/g, function(character) {
      if (character in String.specialChar) {
        return String.specialChar[character];
      }
      return '\\u00' + character.charCodeAt().toPaddedString(2, 16);
    });
    if (useDoubleQuotes) return '"' + escapedString.replace(/"/g, '\\"') + '"';
    return "'" + escapedString.replace(/'/g, '\\\'') + "'";
  }

  function unfilterJSON(filter) {
    return this.replace(filter || Prototype.JSONFilter, '$1');
  }

  function isJSON() {
    var str = this;
    if (str.blank()) return false;
    str = str.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@');
    str = str.replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']');
    str = str.replace(/(?:^|:|,)(?:\s*\[)+/g, '');
    return (/^[\],:{}\s]*$/).test(str);
  }

  function evalJSON(sanitize) {
    var json = this.unfilterJSON(),
        cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;
    if (cx.test(json)) {
      json = json.replace(cx, function (a) {
        return '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
      });
    }
    try {
      if (!sanitize || json.isJSON()) return eval('(' + json + ')');
    } catch (e) { }
    throw new SyntaxError('Badly formed JSON string: ' + this.inspect());
  }

  function parseJSON() {
    var json = this.unfilterJSON();
    return JSON.parse(json);
  }

  function include(pattern) {
    return this.indexOf(pattern) > -1;
  }

  function startsWith(pattern) {
    return this.lastIndexOf(pattern, 0) === 0;
  }

  function endsWith(pattern) {
    var d = this.length - pattern.length;
    return d >= 0 && this.indexOf(pattern, d) === d;
  }

  function empty() {
    return this == '';
  }

  function blank() {
    return /^\s*$/.test(this);
  }

  function interpolate(object, pattern) {
    return new Template(this, pattern).evaluate(object);
  }

  return {
    gsub:           gsub,
    sub:            sub,
    scan:           scan,
    truncate:       truncate,
    strip:          String.prototype.trim || strip,
    stripTags:      stripTags,
    stripScripts:   stripScripts,
    extractScripts: extractScripts,
    evalScripts:    evalScripts,
    escapeHTML:     escapeHTML,
    unescapeHTML:   unescapeHTML,
    toQueryParams:  toQueryParams,
    parseQuery:     toQueryParams,
    toArray:        toArray,
    succ:           succ,
    times:          times,
    camelize:       camelize,
    capitalize:     capitalize,
    underscore:     underscore,
    dasherize:      dasherize,
    inspect:        inspect,
    unfilterJSON:   unfilterJSON,
    isJSON:         isJSON,
    evalJSON:       NATIVE_JSON_PARSE_SUPPORT ? parseJSON : evalJSON,
    include:        include,
    startsWith:     startsWith,
    endsWith:       endsWith,
    empty:          empty,
    blank:          blank,
    interpolate:    interpolate
  };
})());

var Template = Class.create({
  initialize: function(template, pattern) {
    this.template = template.toString();
    this.pattern = pattern || Template.Pattern;
  },

  evaluate: function(object) {
    if (object && Object.isFunction(object.toTemplateReplacements))
      object = object.toTemplateReplacements();

    return this.template.gsub(this.pattern, function(match) {
      if (object == null) return (match[1] + '');

      var before = match[1] || '';
      if (before == '\\') return match[2];

      var ctx = object, expr = match[3],
          pattern = /^([^.[]+|\[((?:.*?[^\\])?)\])(\.|\[|$)/;

      match = pattern.exec(expr);
      if (match == null) return before;

      while (match != null) {
        var comp = match[1].startsWith('[') ? match[2].replace(/\\\\]/g, ']') : match[1];
        ctx = ctx[comp];
        if (null == ctx || '' == match[3]) break;
        expr = expr.substring('[' == match[3] ? match[1].length : match[0].length);
        match = pattern.exec(expr);
      }

      return before + String.interpret(ctx);
    });
  }
});
Template.Pattern = /(^|.|\r|\n)(#\{(.*?)\})/;

var $break = { };

var Enumerable = (function() {
  function each(iterator, context) {
    var index = 0;
    try {
      this._each(function(value) {
        iterator.call(context, value, index++);
      });
    } catch (e) {
      if (e != $break) throw e;
    }
    return this;
  }

  function eachSlice(number, iterator, context) {
    var index = -number, slices = [], array = this.toArray();
    if (number < 1) return array;
    while ((index += number) < array.length)
      slices.push(array.slice(index, index+number));
    return slices.collect(iterator, context);
  }

  function all(iterator, context) {
    iterator = iterator || Prototype.K;
    var result = true;
    this.each(function(value, index) {
      result = result && !!iterator.call(context, value, index);
      if (!result) throw $break;
    });
    return result;
  }

  function any(iterator, context) {
    iterator = iterator || Prototype.K;
    var result = false;
    this.each(function(value, index) {
      if (result = !!iterator.call(context, value, index))
        throw $break;
    });
    return result;
  }

  function collect(iterator, context) {
    iterator = iterator || Prototype.K;
    var results = [];
    this.each(function(value, index) {
      results.push(iterator.call(context, value, index));
    });
    return results;
  }

  function detect(iterator, context) {
    var result;
    this.each(function(value, index) {
      if (iterator.call(context, value, index)) {
        result = value;
        throw $break;
      }
    });
    return result;
  }

  function findAll(iterator, context) {
    var results = [];
    this.each(function(value, index) {
      if (iterator.call(context, value, index))
        results.push(value);
    });
    return results;
  }

  function grep(filter, iterator, context) {
    iterator = iterator || Prototype.K;
    var results = [];

    if (Object.isString(filter))
      filter = new RegExp(RegExp.escape(filter));

    this.each(function(value, index) {
      if (filter.match(value))
        results.push(iterator.call(context, value, index));
    });
    return results;
  }

  function include(object) {
    if (Object.isFunction(this.indexOf))
      if (this.indexOf(object) != -1) return true;

    var found = false;
    this.each(function(value) {
      if (value == object) {
        found = true;
        throw $break;
      }
    });
    return found;
  }

  function inGroupsOf(number, fillWith) {
    fillWith = Object.isUndefined(fillWith) ? null : fillWith;
    return this.eachSlice(number, function(slice) {
      while(slice.length < number) slice.push(fillWith);
      return slice;
    });
  }

  function inject(memo, iterator, context) {
    this.each(function(value, index) {
      memo = iterator.call(context, memo, value, index);
    });
    return memo;
  }

  function invoke(method) {
    var args = $A(arguments).slice(1);
    return this.map(function(value) {
      return value[method].apply(value, args);
    });
  }

  function max(iterator, context) {
    iterator = iterator || Prototype.K;
    var result;
    this.each(function(value, index) {
      value = iterator.call(context, value, index);
      if (result == null || value >= result)
        result = value;
    });
    return result;
  }

  function min(iterator, context) {
    iterator = iterator || Prototype.K;
    var result;
    this.each(function(value, index) {
      value = iterator.call(context, value, index);
      if (result == null || value < result)
        result = value;
    });
    return result;
  }

  function partition(iterator, context) {
    iterator = iterator || Prototype.K;
    var trues = [], falses = [];
    this.each(function(value, index) {
      (iterator.call(context, value, index) ?
        trues : falses).push(value);
    });
    return [trues, falses];
  }

  function pluck(property) {
    var results = [];
    this.each(function(value) {
      results.push(value[property]);
    });
    return results;
  }

  function reject(iterator, context) {
    var results = [];
    this.each(function(value, index) {
      if (!iterator.call(context, value, index))
        results.push(value);
    });
    return results;
  }

  function sortBy(iterator, context) {
    return this.map(function(value, index) {
      return {
        value: value,
        criteria: iterator.call(context, value, index)
      };
    }).sort(function(left, right) {
      var a = left.criteria, b = right.criteria;
      return a < b ? -1 : a > b ? 1 : 0;
    }).pluck('value');
  }

  function toArray() {
    return this.map();
  }

  function zip() {
    var iterator = Prototype.K, args = $A(arguments);
    if (Object.isFunction(args.last()))
      iterator = args.pop();

    var collections = [this].concat(args).map($A);
    return this.map(function(value, index) {
      return iterator(collections.pluck(index));
    });
  }

  function size() {
    return this.toArray().length;
  }

  function inspect() {
    return '#<Enumerable:' + this.toArray().inspect() + '>';
  }









  return {
    each:       each,
    eachSlice:  eachSlice,
    all:        all,
    every:      all,
    any:        any,
    some:       any,
    collect:    collect,
    map:        collect,
    detect:     detect,
    findAll:    findAll,
    select:     findAll,
    filter:     findAll,
    grep:       grep,
    include:    include,
    member:     include,
    inGroupsOf: inGroupsOf,
    inject:     inject,
    invoke:     invoke,
    max:        max,
    min:        min,
    partition:  partition,
    pluck:      pluck,
    reject:     reject,
    sortBy:     sortBy,
    toArray:    toArray,
    entries:    toArray,
    zip:        zip,
    size:       size,
    inspect:    inspect,
    find:       detect
  };
})();

function $A(iterable) {
  if (!iterable) return [];
  if ('toArray' in Object(iterable)) return iterable.toArray();
  var length = iterable.length || 0, results = new Array(length);
  while (length--) results[length] = iterable[length];
  return results;
}


function $w(string) {
  if (!Object.isString(string)) return [];
  string = string.strip();
  return string ? string.split(/\s+/) : [];
}

Array.from = $A;


(function() {
  var arrayProto = Array.prototype,
      slice = arrayProto.slice,
      _each = arrayProto.forEach; // use native browser JS 1.6 implementation if available

  function each(iterator, context) {
    for (var i = 0, length = this.length >>> 0; i < length; i++) {
      if (i in this) iterator.call(context, this[i], i, this);
    }
  }
  if (!_each) _each = each;

  function clear() {
    this.length = 0;
    return this;
  }

  function first() {
    return this[0];
  }

  function last() {
    return this[this.length - 1];
  }

  function compact() {
    return this.select(function(value) {
      return value != null;
    });
  }

  function flatten() {
    return this.inject([], function(array, value) {
      if (Object.isArray(value))
        return array.concat(value.flatten());
      array.push(value);
      return array;
    });
  }

  function without() {
    var values = slice.call(arguments, 0);
    return this.select(function(value) {
      return !values.include(value);
    });
  }

  function reverse(inline) {
    return (inline === false ? this.toArray() : this)._reverse();
  }

  function uniq(sorted) {
    return this.inject([], function(array, value, index) {
      if (0 == index || (sorted ? array.last() != value : !array.include(value)))
        array.push(value);
      return array;
    });
  }

  function intersect(array) {
    return this.uniq().findAll(function(item) {
      return array.detect(function(value) { return item === value });
    });
  }


  function clone() {
    return slice.call(this, 0);
  }

  function size() {
    return this.length;
  }

  function inspect() {
    return '[' + this.map(Object.inspect).join(', ') + ']';
  }

  function indexOf(item, i) {
    i || (i = 0);
    var length = this.length;
    if (i < 0) i = length + i;
    for (; i < length; i++)
      if (this[i] === item) return i;
    return -1;
  }

  function lastIndexOf(item, i) {
    i = isNaN(i) ? this.length : (i < 0 ? this.length + i : i) + 1;
    var n = this.slice(0, i).reverse().indexOf(item);
    return (n < 0) ? n : i - n - 1;
  }

  function concat() {
    var array = slice.call(this, 0), item;
    for (var i = 0, length = arguments.length; i < length; i++) {
      item = arguments[i];
      if (Object.isArray(item) && !('callee' in item)) {
        for (var j = 0, arrayLength = item.length; j < arrayLength; j++)
          array.push(item[j]);
      } else {
        array.push(item);
      }
    }
    return array;
  }

  Object.extend(arrayProto, Enumerable);

  if (!arrayProto._reverse)
    arrayProto._reverse = arrayProto.reverse;

  Object.extend(arrayProto, {
    _each:     _each,
    clear:     clear,
    first:     first,
    last:      last,
    compact:   compact,
    flatten:   flatten,
    without:   without,
    reverse:   reverse,
    uniq:      uniq,
    intersect: intersect,
    clone:     clone,
    toArray:   clone,
    size:      size,
    inspect:   inspect
  });

  var CONCAT_ARGUMENTS_BUGGY = (function() {
    return [].concat(arguments)[0][0] !== 1;
  })(1,2)

  if (CONCAT_ARGUMENTS_BUGGY) arrayProto.concat = concat;

  if (!arrayProto.indexOf) arrayProto.indexOf = indexOf;
  if (!arrayProto.lastIndexOf) arrayProto.lastIndexOf = lastIndexOf;
})();
function $H(object) {
  return new Hash(object);
};

var Hash = Class.create(Enumerable, (function() {
  function initialize(object) {
    this._object = Object.isHash(object) ? object.toObject() : Object.clone(object);
  }


  function _each(iterator) {
    for (var key in this._object) {
      var value = this._object[key], pair = [key, value];
      pair.key = key;
      pair.value = value;
      iterator(pair);
    }
  }

  function set(key, value) {
    return this._object[key] = value;
  }

  function get(key) {
    if (this._object[key] !== Object.prototype[key])
      return this._object[key];
  }

  function unset(key) {
    var value = this._object[key];
    delete this._object[key];
    return value;
  }

  function toObject() {
    return Object.clone(this._object);
  }



  function keys() {
    return this.pluck('key');
  }

  function values() {
    return this.pluck('value');
  }

  function index(value) {
    var match = this.detect(function(pair) {
      return pair.value === value;
    });
    return match && match.key;
  }

  function merge(object) {
    return this.clone().update(object);
  }

  function update(object) {
    return new Hash(object).inject(this, function(result, pair) {
      result.set(pair.key, pair.value);
      return result;
    });
  }

  function toQueryPair(key, value) {
    if (Object.isUndefined(value)) return key;
    return key + '=' + encodeURIComponent(String.interpret(value));
  }

  function toQueryString() {
    return this.inject([], function(results, pair) {
      var key = encodeURIComponent(pair.key), values = pair.value;

      if (values && typeof values == 'object') {
        if (Object.isArray(values)) {
          var queryValues = [];
          for (var i = 0, len = values.length, value; i < len; i++) {
            value = values[i];
            queryValues.push(toQueryPair(key, value));
          }
          return results.concat(queryValues);
        }
      } else results.push(toQueryPair(key, values));
      return results;
    }).join('&');
  }

  function inspect() {
    return '#<Hash:{' + this.map(function(pair) {
      return pair.map(Object.inspect).join(': ');
    }).join(', ') + '}>';
  }

  function clone() {
    return new Hash(this);
  }

  return {
    initialize:             initialize,
    _each:                  _each,
    set:                    set,
    get:                    get,
    unset:                  unset,
    toObject:               toObject,
    toTemplateReplacements: toObject,
    keys:                   keys,
    values:                 values,
    index:                  index,
    merge:                  merge,
    update:                 update,
    toQueryString:          toQueryString,
    inspect:                inspect,
    toJSON:                 toObject,
    clone:                  clone
  };
})());

Hash.from = $H;
Object.extend(Number.prototype, (function() {
  function toColorPart() {
    return this.toPaddedString(2, 16);
  }

  function succ() {
    return this + 1;
  }

  function times(iterator, context) {
    $R(0, this, true).each(iterator, context);
    return this;
  }

  function toPaddedString(length, radix) {
    var string = this.toString(radix || 10);
    return '0'.times(length - string.length) + string;
  }

  function abs() {
    return Math.abs(this);
  }

  function round() {
    return Math.round(this);
  }

  function ceil() {
    return Math.ceil(this);
  }

  function floor() {
    return Math.floor(this);
  }

  return {
    toColorPart:    toColorPart,
    succ:           succ,
    times:          times,
    toPaddedString: toPaddedString,
    abs:            abs,
    round:          round,
    ceil:           ceil,
    floor:          floor
  };
})());

function $R(start, end, exclusive) {
  return new ObjectRange(start, end, exclusive);
}

var ObjectRange = Class.create(Enumerable, (function() {
  function initialize(start, end, exclusive) {
    this.start = start;
    this.end = end;
    this.exclusive = exclusive;
  }

  function _each(iterator) {
    var value = this.start;
    while (this.include(value)) {
      iterator(value);
      value = value.succ();
    }
  }

  function include(value) {
    if (value < this.start)
      return false;
    if (this.exclusive)
      return value < this.end;
    return value <= this.end;
  }

  return {
    initialize: initialize,
    _each:      _each,
    include:    include
  };
})());



var Ajax = {
  getTransport: function() {
    return Try.these(
      function() {return new XMLHttpRequest()},
      function() {return new ActiveXObject('Msxml2.XMLHTTP')},
      function() {return new ActiveXObject('Microsoft.XMLHTTP')}
    ) || false;
  },

  activeRequestCount: 0
};

Ajax.Responders = {
  responders: [],

  _each: function(iterator) {
    this.responders._each(iterator);
  },

  register: function(responder) {
    if (!this.include(responder))
      this.responders.push(responder);
  },

  unregister: function(responder) {
    this.responders = this.responders.without(responder);
  },

  dispatch: function(callback, request, transport, json) {
    this.each(function(responder) {
      if (Object.isFunction(responder[callback])) {
        try {
          responder[callback].apply(responder, [request, transport, json]);
        } catch (e) { }
      }
    });
  }
};

Object.extend(Ajax.Responders, Enumerable);

Ajax.Responders.register({
  onCreate:   function() { Ajax.activeRequestCount++ },
  onComplete: function() { Ajax.activeRequestCount-- }
});
Ajax.Base = Class.create({
  initialize: function(options) {
    this.options = {
      method:       'post',
      asynchronous: true,
      contentType:  'application/x-www-form-urlencoded',
      encoding:     'UTF-8',
      parameters:   '',
      evalJSON:     true,
      evalJS:       true
    };
    Object.extend(this.options, options || { });

    this.options.method = this.options.method.toLowerCase();

    if (Object.isHash(this.options.parameters))
      this.options.parameters = this.options.parameters.toObject();
  }
});
Ajax.Request = Class.create(Ajax.Base, {
  _complete: false,

  initialize: function($super, url, options) {
    $super(options);
    this.transport = Ajax.getTransport();
    this.request(url);
  },

  request: function(url) {
    this.url = url;
    this.method = this.options.method;
    var params = Object.isString(this.options.parameters) ?
          this.options.parameters :
          Object.toQueryString(this.options.parameters);

    if (!['get', 'post'].include(this.method)) {
      params += (params ? '&' : '') + "_method=" + this.method;
      this.method = 'post';
    }

    if (params && this.method === 'get') {
      this.url += (this.url.include('?') ? '&' : '?') + params;
    }

    this.parameters = params.toQueryParams();

    try {
      var response = new Ajax.Response(this);
      if (this.options.onCreate) this.options.onCreate(response);
      Ajax.Responders.dispatch('onCreate', this, response);

      this.transport.open(this.method.toUpperCase(), this.url,
        this.options.asynchronous);

      if (this.options.asynchronous) this.respondToReadyState.bind(this).defer(1);

      this.transport.onreadystatechange = this.onStateChange.bind(this);
      this.setRequestHeaders();

      this.body = this.method == 'post' ? (this.options.postBody || params) : null;
      this.transport.send(this.body);

      /* Force Firefox to handle ready state 4 for synchronous requests */
      if (!this.options.asynchronous && this.transport.overrideMimeType)
        this.onStateChange();

    }
    catch (e) {
      this.dispatchException(e);
    }
  },

  onStateChange: function() {
    var readyState = this.transport.readyState;
    if (readyState > 1 && !((readyState == 4) && this._complete))
      this.respondToReadyState(this.transport.readyState);
  },

  setRequestHeaders: function() {
    var headers = {
      'X-Requested-With': 'XMLHttpRequest',
      'X-Prototype-Version': Prototype.Version,
      'Accept': 'text/javascript, text/html, application/xml, text/xml, */*'
    };

    if (this.method == 'post') {
      headers['Content-type'] = this.options.contentType +
        (this.options.encoding ? '; charset=' + this.options.encoding : '');

      /* Force "Connection: close" for older Mozilla browsers to work
       * around a bug where XMLHttpRequest sends an incorrect
       * Content-length header. See Mozilla Bugzilla #246651.
       */
      if (this.transport.overrideMimeType &&
          (navigator.userAgent.match(/Gecko\/(\d{4})/) || [0,2005])[1] < 2005)
            headers['Connection'] = 'close';
    }

    if (typeof this.options.requestHeaders == 'object') {
      var extras = this.options.requestHeaders;

      if (Object.isFunction(extras.push))
        for (var i = 0, length = extras.length; i < length; i += 2)
          headers[extras[i]] = extras[i+1];
      else
        $H(extras).each(function(pair) { headers[pair.key] = pair.value });
    }

    for (var name in headers)
      this.transport.setRequestHeader(name, headers[name]);
  },

  success: function() {
    var status = this.getStatus();
    return !status || (status >= 200 && status < 300) || status == 304;
  },

  getStatus: function() {
    try {
      if (this.transport.status === 1223) return 204;
      return this.transport.status || 0;
    } catch (e) { return 0 }
  },

  respondToReadyState: function(readyState) {
    var state = Ajax.Request.Events[readyState], response = new Ajax.Response(this);

    if (state == 'Complete') {
      try {
        this._complete = true;
        (this.options['on' + response.status]
         || this.options['on' + (this.success() ? 'Success' : 'Failure')]
         || Prototype.emptyFunction)(response, response.headerJSON);
      } catch (e) {
        this.dispatchException(e);
      }

      var contentType = response.getHeader('Content-type');
      if (this.options.evalJS == 'force'
          || (this.options.evalJS && this.isSameOrigin() && contentType
          && contentType.match(/^\s*(text|application)\/(x-)?(java|ecma)script(;.*)?\s*$/i)))
        this.evalResponse();
    }

    try {
      (this.options['on' + state] || Prototype.emptyFunction)(response, response.headerJSON);
      Ajax.Responders.dispatch('on' + state, this, response, response.headerJSON);
    } catch (e) {
      this.dispatchException(e);
    }

    if (state == 'Complete') {
      this.transport.onreadystatechange = Prototype.emptyFunction;
    }
  },

  isSameOrigin: function() {
    var m = this.url.match(/^\s*https?:\/\/[^\/]*/);
    return !m || (m[0] == '#{protocol}//#{domain}#{port}'.interpolate({
      protocol: location.protocol,
      domain: document.domain,
      port: location.port ? ':' + location.port : ''
    }));
  },

  getHeader: function(name) {
    try {
      return this.transport.getResponseHeader(name) || null;
    } catch (e) { return null; }
  },

  evalResponse: function() {
    try {
      return eval((this.transport.responseText || '').unfilterJSON());
    } catch (e) {
      this.dispatchException(e);
    }
  },

  dispatchException: function(exception) {
    (this.options.onException || Prototype.emptyFunction)(this, exception);
    Ajax.Responders.dispatch('onException', this, exception);
  }
});

Ajax.Request.Events =
  ['Uninitialized', 'Loading', 'Loaded', 'Interactive', 'Complete'];








Ajax.Response = Class.create({
  initialize: function(request){
    this.request = request;
    var transport  = this.transport  = request.transport,
        readyState = this.readyState = transport.readyState;

    if ((readyState > 2 && !Prototype.Browser.IE) || readyState == 4) {
      this.status       = this.getStatus();
      this.statusText   = this.getStatusText();
      this.responseText = String.interpret(transport.responseText);
      this.headerJSON   = this._getHeaderJSON();
    }

    if (readyState == 4) {
      var xml = transport.responseXML;
      this.responseXML  = Object.isUndefined(xml) ? null : xml;
      this.responseJSON = this._getResponseJSON();
    }
  },

  status:      0,

  statusText: '',

  getStatus: Ajax.Request.prototype.getStatus,

  getStatusText: function() {
    try {
      return this.transport.statusText || '';
    } catch (e) { return '' }
  },

  getHeader: Ajax.Request.prototype.getHeader,

  getAllHeaders: function() {
    try {
      return this.getAllResponseHeaders();
    } catch (e) { return null }
  },

  getResponseHeader: function(name) {
    return this.transport.getResponseHeader(name);
  },

  getAllResponseHeaders: function() {
    return this.transport.getAllResponseHeaders();
  },

  _getHeaderJSON: function() {
    var json = this.getHeader('X-JSON');
    if (!json) return null;
    json = decodeURIComponent(escape(json));
    try {
      return json.evalJSON(this.request.options.sanitizeJSON ||
        !this.request.isSameOrigin());
    } catch (e) {
      this.request.dispatchException(e);
    }
  },

  _getResponseJSON: function() {
    var options = this.request.options;
    if (!options.evalJSON || (options.evalJSON != 'force' &&
      !(this.getHeader('Content-type') || '').include('application/json')) ||
        this.responseText.blank())
          return null;
    try {
      return this.responseText.evalJSON(options.sanitizeJSON ||
        !this.request.isSameOrigin());
    } catch (e) {
      this.request.dispatchException(e);
    }
  }
});

Ajax.Updater = Class.create(Ajax.Request, {
  initialize: function($super, container, url, options) {
    this.container = {
      success: (container.success || container),
      failure: (container.failure || (container.success ? null : container))
    };

    options = Object.clone(options);
    var onComplete = options.onComplete;
    options.onComplete = (function(response, json) {
      this.updateContent(response.responseText);
      if (Object.isFunction(onComplete)) onComplete(response, json);
    }).bind(this);

    $super(url, options);
  },

  updateContent: function(responseText) {
    var receiver = this.container[this.success() ? 'success' : 'failure'],
        options = this.options;

    if (!options.evalScripts) responseText = responseText.stripScripts();

    if (receiver = $(receiver)) {
      if (options.insertion) {
        if (Object.isString(options.insertion)) {
          var insertion = { }; insertion[options.insertion] = responseText;
          receiver.insert(insertion);
        }
        else options.insertion(receiver, responseText);
      }
      else receiver.update(responseText);
    }
  }
});

Ajax.PeriodicalUpdater = Class.create(Ajax.Base, {
  initialize: function($super, container, url, options) {
    $super(options);
    this.onComplete = this.options.onComplete;

    this.frequency = (this.options.frequency || 2);
    this.decay = (this.options.decay || 1);

    this.updater = { };
    this.container = container;
    this.url = url;

    this.start();
  },

  start: function() {
    this.options.onComplete = this.updateComplete.bind(this);
    this.onTimerEvent();
  },

  stop: function() {
    this.updater.options.onComplete = undefined;
    clearTimeout(this.timer);
    (this.onComplete || Prototype.emptyFunction).apply(this, arguments);
  },

  updateComplete: function(response) {
    if (this.options.decay) {
      this.decay = (response.responseText == this.lastText ?
        this.decay * this.options.decay : 1);

      this.lastText = response.responseText;
    }
    this.timer = this.onTimerEvent.bind(this).delay(this.decay * this.frequency);
  },

  onTimerEvent: function() {
    this.updater = new Ajax.Updater(this.container, this.url, this.options);
  }
});


function $(element) {
  if (arguments.length > 1) {
    for (var i = 0, elements = [], length = arguments.length; i < length; i++)
      elements.push($(arguments[i]));
    return elements;
  }
  if (Object.isString(element))
    element = document.getElementById(element);
  return Element.extend(element);
}

if (Prototype.BrowserFeatures.XPath) {
  document._getElementsByXPath = function(expression, parentElement) {
    var results = [];
    var query = document.evaluate(expression, $(parentElement) || document,
      null, XPathResult.ORDERED_NODE_SNAPSHOT_TYPE, null);
    for (var i = 0, length = query.snapshotLength; i < length; i++)
      results.push(Element.extend(query.snapshotItem(i)));
    return results;
  };
}

/*--------------------------------------------------------------------------*/

if (!Node) var Node = { };

if (!Node.ELEMENT_NODE) {
  Object.extend(Node, {
    ELEMENT_NODE: 1,
    ATTRIBUTE_NODE: 2,
    TEXT_NODE: 3,
    CDATA_SECTION_NODE: 4,
    ENTITY_REFERENCE_NODE: 5,
    ENTITY_NODE: 6,
    PROCESSING_INSTRUCTION_NODE: 7,
    COMMENT_NODE: 8,
    DOCUMENT_NODE: 9,
    DOCUMENT_TYPE_NODE: 10,
    DOCUMENT_FRAGMENT_NODE: 11,
    NOTATION_NODE: 12
  });
}



(function(global) {
  function shouldUseCache(tagName, attributes) {
    if (tagName === 'select') return false;
    if ('type' in attributes) return false;
    return true;
  }

  var HAS_EXTENDED_CREATE_ELEMENT_SYNTAX = (function(){
    try {
      var el = document.createElement('<input name="x">');
      return el.tagName.toLowerCase() === 'input' && el.name === 'x';
    }
    catch(err) {
      return false;
    }
  })();

  var element = global.Element;

  global.Element = function(tagName, attributes) {
    attributes = attributes || { };
    tagName = tagName.toLowerCase();
    var cache = Element.cache;

    if (HAS_EXTENDED_CREATE_ELEMENT_SYNTAX && attributes.name) {
      tagName = '<' + tagName + ' name="' + attributes.name + '">';
      delete attributes.name;
      return Element.writeAttribute(document.createElement(tagName), attributes);
    }

    if (!cache[tagName]) cache[tagName] = Element.extend(document.createElement(tagName));

    var node = shouldUseCache(tagName, attributes) ?
     cache[tagName].cloneNode(false) : document.createElement(tagName);

    return Element.writeAttribute(node, attributes);
  };

  Object.extend(global.Element, element || { });
  if (element) global.Element.prototype = element.prototype;

})(this);

Element.idCounter = 1;
Element.cache = { };

Element._purgeElement = function(element) {
  var uid = element._prototypeUID;
  if (uid) {
    Element.stopObserving(element);
    element._prototypeUID = void 0;
    delete Element.Storage[uid];
  }
}

Element.Methods = {
  visible: function(element) {
    return $(element).style.display != 'none';
  },

  toggle: function(element) {
    element = $(element);
    Element[Element.visible(element) ? 'hide' : 'show'](element);
    return element;
  },

  hide: function(element) {
    element = $(element);
    element.style.display = 'none';
    return element;
  },

  show: function(element) {
    element = $(element);
    element.style.display = '';
    return element;
  },

  remove: function(element) {
    element = $(element);
    element.parentNode.removeChild(element);
    return element;
  },

  update: (function(){

    var SELECT_ELEMENT_INNERHTML_BUGGY = (function(){
      var el = document.createElement("select"),
          isBuggy = true;
      el.innerHTML = "<option value=\"test\">test</option>";
      if (el.options && el.options[0]) {
        isBuggy = el.options[0].nodeName.toUpperCase() !== "OPTION";
      }
      el = null;
      return isBuggy;
    })();

    var TABLE_ELEMENT_INNERHTML_BUGGY = (function(){
      try {
        var el = document.createElement("table");
        if (el && el.tBodies) {
          el.innerHTML = "<tbody><tr><td>test</td></tr></tbody>";
          var isBuggy = typeof el.tBodies[0] == "undefined";
          el = null;
          return isBuggy;
        }
      } catch (e) {
        return true;
      }
    })();

    var LINK_ELEMENT_INNERHTML_BUGGY = (function() {
      try {
        var el = document.createElement('div');
        el.innerHTML = "<link>";
        var isBuggy = (el.childNodes.length === 0);
        el = null;
        return isBuggy;
      } catch(e) {
        return true;
      }
    })();

    var ANY_INNERHTML_BUGGY = SELECT_ELEMENT_INNERHTML_BUGGY ||
     TABLE_ELEMENT_INNERHTML_BUGGY || LINK_ELEMENT_INNERHTML_BUGGY;

    var SCRIPT_ELEMENT_REJECTS_TEXTNODE_APPENDING = (function () {
      var s = document.createElement("script"),
          isBuggy = false;
      try {
        s.appendChild(document.createTextNode(""));
        isBuggy = !s.firstChild ||
          s.firstChild && s.firstChild.nodeType !== 3;
      } catch (e) {
        isBuggy = true;
      }
      s = null;
      return isBuggy;
    })();


    function update(element, content) {
      element = $(element);
      var purgeElement = Element._purgeElement;

      var descendants = element.getElementsByTagName('*'),
       i = descendants.length;
      while (i--) purgeElement(descendants[i]);

      if (content && content.toElement)
        content = content.toElement();

      if (Object.isElement(content))
        return element.update().insert(content);

      content = Object.toHTML(content);

      var tagName = element.tagName.toUpperCase();

      if (tagName === 'SCRIPT' && SCRIPT_ELEMENT_REJECTS_TEXTNODE_APPENDING) {
        element.text = content;
        return element;
      }

      if (ANY_INNERHTML_BUGGY) {
        if (tagName in Element._insertionTranslations.tags) {
          while (element.firstChild) {
            element.removeChild(element.firstChild);
          }
          Element._getContentFromAnonymousElement(tagName, content.stripScripts())
            .each(function(node) {
              element.appendChild(node)
            });
        } else if (LINK_ELEMENT_INNERHTML_BUGGY && Object.isString(content) && content.indexOf('<link') > -1) {
          while (element.firstChild) {
            element.removeChild(element.firstChild);
          }
          var nodes = Element._getContentFromAnonymousElement(tagName, content.stripScripts(), true);
          nodes.each(function(node) { element.appendChild(node) });
        }
        else {
          element.innerHTML = content.stripScripts();
        }
      }
      else {
        element.innerHTML = content.stripScripts();
      }

      content.evalScripts.bind(content).defer();
      return element;
    }

    return update;
  })(),

  replace: function(element, content) {
    element = $(element);
    if (content && content.toElement) content = content.toElement();
    else if (!Object.isElement(content)) {
      content = Object.toHTML(content);
      var range = element.ownerDocument.createRange();
      range.selectNode(element);
      content.evalScripts.bind(content).defer();
      content = range.createContextualFragment(content.stripScripts());
    }
    element.parentNode.replaceChild(content, element);
    return element;
  },

  insert: function(element, insertions) {
    element = $(element);

    if (Object.isString(insertions) || Object.isNumber(insertions) ||
        Object.isElement(insertions) || (insertions && (insertions.toElement || insertions.toHTML)))
          insertions = {bottom:insertions};

    var content, insert, tagName, childNodes;

    for (var position in insertions) {
      content  = insertions[position];
      position = position.toLowerCase();
      insert = Element._insertionTranslations[position];

      if (content && content.toElement) content = content.toElement();
      if (Object.isElement(content)) {
        insert(element, content);
        continue;
      }

      content = Object.toHTML(content);

      tagName = ((position == 'before' || position == 'after')
        ? element.parentNode : element).tagName.toUpperCase();

      childNodes = Element._getContentFromAnonymousElement(tagName, content.stripScripts());

      if (position == 'top' || position == 'after') childNodes.reverse();
      childNodes.each(insert.curry(element));

      content.evalScripts.bind(content).defer();
    }

    return element;
  },

  wrap: function(element, wrapper, attributes) {
    element = $(element);
    if (Object.isElement(wrapper))
      $(wrapper).writeAttribute(attributes || { });
    else if (Object.isString(wrapper)) wrapper = new Element(wrapper, attributes);
    else wrapper = new Element('div', wrapper);
    if (element.parentNode)
      element.parentNode.replaceChild(wrapper, element);
    wrapper.appendChild(element);
    return wrapper;
  },

  inspect: function(element) {
    element = $(element);
    var result = '<' + element.tagName.toLowerCase();
    $H({'id': 'id', 'className': 'class'}).each(function(pair) {
      var property = pair.first(),
          attribute = pair.last(),
          value = (element[property] || '').toString();
      if (value) result += ' ' + attribute + '=' + value.inspect(true);
    });
    return result + '>';
  },

  recursivelyCollect: function(element, property, maximumLength) {
    element = $(element);
    maximumLength = maximumLength || -1;
    var elements = [];

    while (element = element[property]) {
      if (element.nodeType == 1)
        elements.push(Element.extend(element));
      if (elements.length == maximumLength)
        break;
    }

    return elements;
  },

  ancestors: function(element) {
    return Element.recursivelyCollect(element, 'parentNode');
  },

  descendants: function(element) {
    return Element.select(element, "*");
  },

  firstDescendant: function(element) {
    element = $(element).firstChild;
    while (element && element.nodeType != 1) element = element.nextSibling;
    return $(element);
  },

  immediateDescendants: function(element) {
    var results = [], child = $(element).firstChild;
    while (child) {
      if (child.nodeType === 1) {
        results.push(Element.extend(child));
      }
      child = child.nextSibling;
    }
    return results;
  },

  previousSiblings: function(element, maximumLength) {
    return Element.recursivelyCollect(element, 'previousSibling');
  },

  nextSiblings: function(element) {
    return Element.recursivelyCollect(element, 'nextSibling');
  },

  siblings: function(element) {
    element = $(element);
    return Element.previousSiblings(element).reverse()
      .concat(Element.nextSiblings(element));
  },

  match: function(element, selector) {
    element = $(element);
    if (Object.isString(selector))
      return Prototype.Selector.match(element, selector);
    return selector.match(element);
  },

  up: function(element, expression, index) {
    element = $(element);
    if (arguments.length == 1) return $(element.parentNode);
    var ancestors = Element.ancestors(element);
    return Object.isNumber(expression) ? ancestors[expression] :
      Prototype.Selector.find(ancestors, expression, index);
  },

  down: function(element, expression, index) {
    element = $(element);
    if (arguments.length == 1) return Element.firstDescendant(element);
    return Object.isNumber(expression) ? Element.descendants(element)[expression] :
      Element.select(element, expression)[index || 0];
  },

  previous: function(element, expression, index) {
    element = $(element);
    if (Object.isNumber(expression)) index = expression, expression = false;
    if (!Object.isNumber(index)) index = 0;

    if (expression) {
      return Prototype.Selector.find(element.previousSiblings(), expression, index);
    } else {
      return element.recursivelyCollect("previousSibling", index + 1)[index];
    }
  },

  next: function(element, expression, index) {
    element = $(element);
    if (Object.isNumber(expression)) index = expression, expression = false;
    if (!Object.isNumber(index)) index = 0;

    if (expression) {
      return Prototype.Selector.find(element.nextSiblings(), expression, index);
    } else {
      var maximumLength = Object.isNumber(index) ? index + 1 : 1;
      return element.recursivelyCollect("nextSibling", index + 1)[index];
    }
  },


  select: function(element) {
    element = $(element);
    var expressions = Array.prototype.slice.call(arguments, 1).join(', ');
    return Prototype.Selector.select(expressions, element);
  },

  adjacent: function(element) {
    element = $(element);
    var expressions = Array.prototype.slice.call(arguments, 1).join(', ');
    return Prototype.Selector.select(expressions, element.parentNode).without(element);
  },

  identify: function(element) {
    element = $(element);
    var id = Element.readAttribute(element, 'id');
    if (id) return id;
    do { id = 'anonymous_element_' + Element.idCounter++ } while ($(id));
    Element.writeAttribute(element, 'id', id);
    return id;
  },

  readAttribute: function(element, name) {
    element = $(element);
    if (Prototype.Browser.IE) {
      var t = Element._attributeTranslations.read;
      if (t.values[name]) return t.values[name](element, name);
      if (t.names[name]) name = t.names[name];
      if (name.include(':')) {
        return (!element.attributes || !element.attributes[name]) ? null :
         element.attributes[name].value;
      }
    }
    return element.getAttribute(name);
  },

  writeAttribute: function(element, name, value) {
    element = $(element);
    var attributes = { }, t = Element._attributeTranslations.write;

    if (typeof name == 'object') attributes = name;
    else attributes[name] = Object.isUndefined(value) ? true : value;

    for (var attr in attributes) {
      name = t.names[attr] || attr;
      value = attributes[attr];
      if (t.values[attr]) name = t.values[attr](element, value);
      if (value === false || value === null)
        element.removeAttribute(name);
      else if (value === true)
        element.setAttribute(name, name);
      else element.setAttribute(name, value);
    }
    return element;
  },

  getHeight: function(element) {
    return Element.getDimensions(element).height;
  },

  getWidth: function(element) {
    return Element.getDimensions(element).width;
  },

  classNames: function(element) {
    return new Element.ClassNames(element);
  },

  hasClassName: function(element, className) {
    if (!(element = $(element))) return;
    var elementClassName = element.className;
    return (elementClassName.length > 0 && (elementClassName == className ||
      new RegExp("(^|\\s)" + className + "(\\s|$)").test(elementClassName)));
  },

  addClassName: function(element, className) {
    if (!(element = $(element))) return;
    if (!Element.hasClassName(element, className))
      element.className += (element.className ? ' ' : '') + className;
    return element;
  },

  removeClassName: function(element, className) {
    if (!(element = $(element))) return;
    element.className = element.className.replace(
      new RegExp("(^|\\s+)" + className + "(\\s+|$)"), ' ').strip();
    return element;
  },

  toggleClassName: function(element, className) {
    if (!(element = $(element))) return;
    return Element[Element.hasClassName(element, className) ?
      'removeClassName' : 'addClassName'](element, className);
  },

  cleanWhitespace: function(element) {
    element = $(element);
    var node = element.firstChild;
    while (node) {
      var nextNode = node.nextSibling;
      if (node.nodeType == 3 && !/\S/.test(node.nodeValue))
        element.removeChild(node);
      node = nextNode;
    }
    return element;
  },

  empty: function(element) {
    return $(element).innerHTML.blank();
  },

  descendantOf: function(element, ancestor) {
    element = $(element), ancestor = $(ancestor);

    if (element.compareDocumentPosition)
      return (element.compareDocumentPosition(ancestor) & 8) === 8;

    if (ancestor.contains)
      return ancestor.contains(element) && ancestor !== element;

    while (element = element.parentNode)
      if (element == ancestor) return true;

    return false;
  },

  scrollTo: function(element) {
    element = $(element);
    var pos = Element.cumulativeOffset(element);
    window.scrollTo(pos[0], pos[1]);
    return element;
  },

  getStyle: function(element, style) {
    element = $(element);
    style = style == 'float' ? 'cssFloat' : style.camelize();
    var value = element.style[style];
    if (!value || value == 'auto') {
      var css = document.defaultView.getComputedStyle(element, null);
      value = css ? css[style] : null;
    }
    if (style == 'opacity') return value ? parseFloat(value) : 1.0;
    return value == 'auto' ? null : value;
  },

  getOpacity: function(element) {
    return $(element).getStyle('opacity');
  },

  setStyle: function(element, styles) {
    element = $(element);
    var elementStyle = element.style, match;
    if (Object.isString(styles)) {
      element.style.cssText += ';' + styles;
      return styles.include('opacity') ?
        element.setOpacity(styles.match(/opacity:\s*(\d?\.?\d*)/)[1]) : element;
    }
    for (var property in styles)
      if (property == 'opacity') element.setOpacity(styles[property]);
      else
        elementStyle[(property == 'float' || property == 'cssFloat') ?
          (Object.isUndefined(elementStyle.styleFloat) ? 'cssFloat' : 'styleFloat') :
            property] = styles[property];

    return element;
  },

  setOpacity: function(element, value) {
    element = $(element);
    element.style.opacity = (value == 1 || value === '') ? '' :
      (value < 0.00001) ? 0 : value;
    return element;
  },

  makePositioned: function(element) {
    element = $(element);
    var pos = Element.getStyle(element, 'position');
    if (pos == 'static' || !pos) {
      element._madePositioned = true;
      element.style.position = 'relative';
      if (Prototype.Browser.Opera) {
        element.style.top = 0;
        element.style.left = 0;
      }
    }
    return element;
  },

  undoPositioned: function(element) {
    element = $(element);
    if (element._madePositioned) {
      element._madePositioned = undefined;
      element.style.position =
        element.style.top =
        element.style.left =
        element.style.bottom =
        element.style.right = '';
    }
    return element;
  },

  makeClipping: function(element) {
    element = $(element);
    if (element._overflow) return element;
    element._overflow = Element.getStyle(element, 'overflow') || 'auto';
    if (element._overflow !== 'hidden')
      element.style.overflow = 'hidden';
    return element;
  },

  undoClipping: function(element) {
    element = $(element);
    if (!element._overflow) return element;
    element.style.overflow = element._overflow == 'auto' ? '' : element._overflow;
    element._overflow = null;
    return element;
  },

  clonePosition: function(element, source) {
    var options = Object.extend({
      setLeft:    true,
      setTop:     true,
      setWidth:   true,
      setHeight:  true,
      offsetTop:  0,
      offsetLeft: 0
    }, arguments[2] || { });

    source = $(source);
    var p = Element.viewportOffset(source), delta = [0, 0], parent = null;

    element = $(element);

    if (Element.getStyle(element, 'position') == 'absolute') {
      parent = Element.getOffsetParent(element);
      delta = Element.viewportOffset(parent);
    }

    if (parent == document.body) {
      delta[0] -= document.body.offsetLeft;
      delta[1] -= document.body.offsetTop;
    }

    if (options.setLeft)   element.style.left  = (p[0] - delta[0] + options.offsetLeft) + 'px';
    if (options.setTop)    element.style.top   = (p[1] - delta[1] + options.offsetTop) + 'px';
    if (options.setWidth)  element.style.width = source.offsetWidth + 'px';
    if (options.setHeight) element.style.height = source.offsetHeight + 'px';
    return element;
  }
};

Object.extend(Element.Methods, {
  getElementsBySelector: Element.Methods.select,

  childElements: Element.Methods.immediateDescendants
});

Element._attributeTranslations = {
  write: {
    names: {
      className: 'class',
      htmlFor:   'for'
    },
    values: { }
  }
};

if (Prototype.Browser.Opera) {
  Element.Methods.getStyle = Element.Methods.getStyle.wrap(
    function(proceed, element, style) {
      switch (style) {
        case 'height': case 'width':
          if (!Element.visible(element)) return null;

          var dim = parseInt(proceed(element, style), 10);

          if (dim !== element['offset' + style.capitalize()])
            return dim + 'px';

          var properties;
          if (style === 'height') {
            properties = ['border-top-width', 'padding-top',
             'padding-bottom', 'border-bottom-width'];
          }
          else {
            properties = ['border-left-width', 'padding-left',
             'padding-right', 'border-right-width'];
          }
          return properties.inject(dim, function(memo, property) {
            var val = proceed(element, property);
            return val === null ? memo : memo - parseInt(val, 10);
          }) + 'px';
        default: return proceed(element, style);
      }
    }
  );

  Element.Methods.readAttribute = Element.Methods.readAttribute.wrap(
    function(proceed, element, attribute) {
      if (attribute === 'title') return element.title;
      return proceed(element, attribute);
    }
  );
}

else if (Prototype.Browser.IE) {
  Element.Methods.getStyle = function(element, style) {
    element = $(element);
    style = (style == 'float' || style == 'cssFloat') ? 'styleFloat' : style.camelize();
    var value = element.style[style];
    if (!value && element.currentStyle) value = element.currentStyle[style];

    if (style == 'opacity') {
      if (value = (element.getStyle('filter') || '').match(/alpha\(opacity=(.*)\)/))
        if (value[1]) return parseFloat(value[1]) / 100;
      return 1.0;
    }

    if (value == 'auto') {
      if ((style == 'width' || style == 'height') && (element.getStyle('display') != 'none'))
        return element['offset' + style.capitalize()] + 'px';
      return null;
    }
    return value;
  };

  Element.Methods.setOpacity = function(element, value) {
    function stripAlpha(filter){
      return filter.replace(/alpha\([^\)]*\)/gi,'');
    }
    element = $(element);
    var currentStyle = element.currentStyle;
    if ((currentStyle && !currentStyle.hasLayout) ||
      (!currentStyle && element.style.zoom == 'normal'))
        element.style.zoom = 1;

    var filter = element.getStyle('filter'), style = element.style;
    if (value == 1 || value === '') {
      (filter = stripAlpha(filter)) ?
        style.filter = filter : style.removeAttribute('filter');
      return element;
    } else if (value < 0.00001) value = 0;
    style.filter = stripAlpha(filter) +
      'alpha(opacity=' + (value * 100) + ')';
    return element;
  };

  Element._attributeTranslations = (function(){

    var classProp = 'className',
        forProp = 'for',
        el = document.createElement('div');

    el.setAttribute(classProp, 'x');

    if (el.className !== 'x') {
      el.setAttribute('class', 'x');
      if (el.className === 'x') {
        classProp = 'class';
      }
    }
    el = null;

    el = document.createElement('label');
    el.setAttribute(forProp, 'x');
    if (el.htmlFor !== 'x') {
      el.setAttribute('htmlFor', 'x');
      if (el.htmlFor === 'x') {
        forProp = 'htmlFor';
      }
    }
    el = null;

    return {
      read: {
        names: {
          'class':      classProp,
          'className':  classProp,
          'for':        forProp,
          'htmlFor':    forProp
        },
        values: {
          _getAttr: function(element, attribute) {
            return element.getAttribute(attribute);
          },
          _getAttr2: function(element, attribute) {
            return element.getAttribute(attribute, 2);
          },
          _getAttrNode: function(element, attribute) {
            var node = element.getAttributeNode(attribute);
            return node ? node.value : "";
          },
          _getEv: (function(){

            var el = document.createElement('div'), f;
            el.onclick = Prototype.emptyFunction;
            var value = el.getAttribute('onclick');

            if (String(value).indexOf('{') > -1) {
              f = function(element, attribute) {
                attribute = element.getAttribute(attribute);
                if (!attribute) return null;
                attribute = attribute.toString();
                attribute = attribute.split('{')[1];
                attribute = attribute.split('}')[0];
                return attribute.strip();
              };
            }
            else if (value === '') {
              f = function(element, attribute) {
                attribute = element.getAttribute(attribute);
                if (!attribute) return null;
                return attribute.strip();
              };
            }
            el = null;
            return f;
          })(),
          _flag: function(element, attribute) {
            return $(element).hasAttribute(attribute) ? attribute : null;
          },
          style: function(element) {
            return element.style.cssText.toLowerCase();
          },
          title: function(element) {
            return element.title;
          }
        }
      }
    }
  })();

  Element._attributeTranslations.write = {
    names: Object.extend({
      cellpadding: 'cellPadding',
      cellspacing: 'cellSpacing'
    }, Element._attributeTranslations.read.names),
    values: {
      checked: function(element, value) {
        element.checked = !!value;
      },

      style: function(element, value) {
        element.style.cssText = value ? value : '';
      }
    }
  };

  Element._attributeTranslations.has = {};

  $w('colSpan rowSpan vAlign dateTime accessKey tabIndex ' +
      'encType maxLength readOnly longDesc frameBorder').each(function(attr) {
    Element._attributeTranslations.write.names[attr.toLowerCase()] = attr;
    Element._attributeTranslations.has[attr.toLowerCase()] = attr;
  });

  (function(v) {
    Object.extend(v, {
      href:        v._getAttr2,
      src:         v._getAttr2,
      type:        v._getAttr,
      action:      v._getAttrNode,
      disabled:    v._flag,
      checked:     v._flag,
      readonly:    v._flag,
      multiple:    v._flag,
      onload:      v._getEv,
      onunload:    v._getEv,
      onclick:     v._getEv,
      ondblclick:  v._getEv,
      onmousedown: v._getEv,
      onmouseup:   v._getEv,
      onmouseover: v._getEv,
      onmousemove: v._getEv,
      onmouseout:  v._getEv,
      onfocus:     v._getEv,
      onblur:      v._getEv,
      onkeypress:  v._getEv,
      onkeydown:   v._getEv,
      onkeyup:     v._getEv,
      onsubmit:    v._getEv,
      onreset:     v._getEv,
      onselect:    v._getEv,
      onchange:    v._getEv
    });
  })(Element._attributeTranslations.read.values);

  if (Prototype.BrowserFeatures.ElementExtensions) {
    (function() {
      function _descendants(element) {
        var nodes = element.getElementsByTagName('*'), results = [];
        for (var i = 0, node; node = nodes[i]; i++)
          if (node.tagName !== "!") // Filter out comment nodes.
            results.push(node);
        return results;
      }

      Element.Methods.down = function(element, expression, index) {
        element = $(element);
        if (arguments.length == 1) return element.firstDescendant();
        return Object.isNumber(expression) ? _descendants(element)[expression] :
          Element.select(element, expression)[index || 0];
      }
    })();
  }

}

else if (Prototype.Browser.Gecko && /rv:1\.8\.0/.test(navigator.userAgent)) {
  Element.Methods.setOpacity = function(element, value) {
    element = $(element);
    element.style.opacity = (value == 1) ? 0.999999 :
      (value === '') ? '' : (value < 0.00001) ? 0 : value;
    return element;
  };
}

else if (Prototype.Browser.WebKit) {
  Element.Methods.setOpacity = function(element, value) {
    element = $(element);
    element.style.opacity = (value == 1 || value === '') ? '' :
      (value < 0.00001) ? 0 : value;

    if (value == 1)
      if (element.tagName.toUpperCase() == 'IMG' && element.width) {
        element.width++; element.width--;
      } else try {
        var n = document.createTextNode(' ');
        element.appendChild(n);
        element.removeChild(n);
      } catch (e) { }

    return element;
  };
}

if ('outerHTML' in document.documentElement) {
  Element.Methods.replace = function(element, content) {
    element = $(element);

    if (content && content.toElement) content = content.toElement();
    if (Object.isElement(content)) {
      element.parentNode.replaceChild(content, element);
      return element;
    }

    content = Object.toHTML(content);
    var parent = element.parentNode, tagName = parent.tagName.toUpperCase();

    if (Element._insertionTranslations.tags[tagName]) {
      var nextSibling = element.next(),
          fragments = Element._getContentFromAnonymousElement(tagName, content.stripScripts());
      parent.removeChild(element);
      if (nextSibling)
        fragments.each(function(node) { parent.insertBefore(node, nextSibling) });
      else
        fragments.each(function(node) { parent.appendChild(node) });
    }
    else element.outerHTML = content.stripScripts();

    content.evalScripts.bind(content).defer();
    return element;
  };
}

Element._returnOffset = function(l, t) {
  var result = [l, t];
  result.left = l;
  result.top = t;
  return result;
};

Element._getContentFromAnonymousElement = function(tagName, html, force) {
  var div = new Element('div'),
      t = Element._insertionTranslations.tags[tagName];

  var workaround = false;
  if (t) workaround = true;
  else if (force) {
    workaround = true;
    t = ['', '', 0];
  }

  if (workaround) {
    div.innerHTML = '&nbsp;' + t[0] + html + t[1];
    div.removeChild(div.firstChild);
    for (var i = t[2]; i--; ) {
      div = div.firstChild;
    }
  }
  else {
    div.innerHTML = html;
  }
  return $A(div.childNodes);
};

Element._insertionTranslations = {
  before: function(element, node) {
    element.parentNode.insertBefore(node, element);
  },
  top: function(element, node) {
    element.insertBefore(node, element.firstChild);
  },
  bottom: function(element, node) {
    element.appendChild(node);
  },
  after: function(element, node) {
    element.parentNode.insertBefore(node, element.nextSibling);
  },
  tags: {
    TABLE:  ['<table>',                '</table>',                   1],
    TBODY:  ['<table><tbody>',         '</tbody></table>',           2],
    TR:     ['<table><tbody><tr>',     '</tr></tbody></table>',      3],
    TD:     ['<table><tbody><tr><td>', '</td></tr></tbody></table>', 4],
    SELECT: ['<select>',               '</select>',                  1]
  }
};

(function() {
  var tags = Element._insertionTranslations.tags;
  Object.extend(tags, {
    THEAD: tags.TBODY,
    TFOOT: tags.TBODY,
    TH:    tags.TD
  });
})();

Element.Methods.Simulated = {
  hasAttribute: function(element, attribute) {
    attribute = Element._attributeTranslations.has[attribute] || attribute;
    var node = $(element).getAttributeNode(attribute);
    return !!(node && node.specified);
  }
};

Element.Methods.ByTag = { };

Object.extend(Element, Element.Methods);

(function(div) {

  if (!Prototype.BrowserFeatures.ElementExtensions && div['__proto__']) {
    window.HTMLElement = { };
    window.HTMLElement.prototype = div['__proto__'];
    Prototype.BrowserFeatures.ElementExtensions = true;
  }

  div = null;

})(document.createElement('div'));

Element.extend = (function() {

  function checkDeficiency(tagName) {
    if (typeof window.Element != 'undefined') {
      var proto = window.Element.prototype;
      if (proto) {
        var id = '_' + (Math.random()+'').slice(2),
            el = document.createElement(tagName);
        proto[id] = 'x';
        var isBuggy = (el[id] !== 'x');
        delete proto[id];
        el = null;
        return isBuggy;
      }
    }
    return false;
  }

  function extendElementWith(element, methods) {
    for (var property in methods) {
      var value = methods[property];
      if (Object.isFunction(value) && !(property in element))
        element[property] = value.methodize();
    }
  }

  var HTMLOBJECTELEMENT_PROTOTYPE_BUGGY = checkDeficiency('object');

  if (Prototype.BrowserFeatures.SpecificElementExtensions) {
    if (HTMLOBJECTELEMENT_PROTOTYPE_BUGGY) {
      return function(element) {
        if (element && typeof element._extendedByPrototype == 'undefined') {
          var t = element.tagName;
          if (t && (/^(?:object|applet|embed)$/i.test(t))) {
            extendElementWith(element, Element.Methods);
            extendElementWith(element, Element.Methods.Simulated);
            extendElementWith(element, Element.Methods.ByTag[t.toUpperCase()]);
          }
        }
        return element;
      }
    }
    return Prototype.K;
  }

  var Methods = { }, ByTag = Element.Methods.ByTag;

  var extend = Object.extend(function(element) {
    if (!element || typeof element._extendedByPrototype != 'undefined' ||
        element.nodeType != 1 || element == window) return element;

    var methods = Object.clone(Methods),
        tagName = element.tagName.toUpperCase();

    if (ByTag[tagName]) Object.extend(methods, ByTag[tagName]);

    extendElementWith(element, methods);

    element._extendedByPrototype = Prototype.emptyFunction;
    return element;

  }, {
    refresh: function() {
      if (!Prototype.BrowserFeatures.ElementExtensions) {
        Object.extend(Methods, Element.Methods);
        Object.extend(Methods, Element.Methods.Simulated);
      }
    }
  });

  extend.refresh();
  return extend;
})();

if (document.documentElement.hasAttribute) {
  Element.hasAttribute = function(element, attribute) {
    return element.hasAttribute(attribute);
  };
}
else {
  Element.hasAttribute = Element.Methods.Simulated.hasAttribute;
}

Element.addMethods = function(methods) {
  var F = Prototype.BrowserFeatures, T = Element.Methods.ByTag;

  if (!methods) {
    Object.extend(Form, Form.Methods);
    Object.extend(Form.Element, Form.Element.Methods);
    Object.extend(Element.Methods.ByTag, {
      "FORM":     Object.clone(Form.Methods),
      "INPUT":    Object.clone(Form.Element.Methods),
      "SELECT":   Object.clone(Form.Element.Methods),
      "TEXTAREA": Object.clone(Form.Element.Methods),
      "BUTTON":   Object.clone(Form.Element.Methods)
    });
  }

  if (arguments.length == 2) {
    var tagName = methods;
    methods = arguments[1];
  }

  if (!tagName) Object.extend(Element.Methods, methods || { });
  else {
    if (Object.isArray(tagName)) tagName.each(extend);
    else extend(tagName);
  }

  function extend(tagName) {
    tagName = tagName.toUpperCase();
    if (!Element.Methods.ByTag[tagName])
      Element.Methods.ByTag[tagName] = { };
    Object.extend(Element.Methods.ByTag[tagName], methods);
  }

  function copy(methods, destination, onlyIfAbsent) {
    onlyIfAbsent = onlyIfAbsent || false;
    for (var property in methods) {
      var value = methods[property];
      if (!Object.isFunction(value)) continue;
      if (!onlyIfAbsent || !(property in destination))
        destination[property] = value.methodize();
    }
  }

  function findDOMClass(tagName) {
    var klass;
    var trans = {
      "OPTGROUP": "OptGroup", "TEXTAREA": "TextArea", "P": "Paragraph",
      "FIELDSET": "FieldSet", "UL": "UList", "OL": "OList", "DL": "DList",
      "DIR": "Directory", "H1": "Heading", "H2": "Heading", "H3": "Heading",
      "H4": "Heading", "H5": "Heading", "H6": "Heading", "Q": "Quote",
      "INS": "Mod", "DEL": "Mod", "A": "Anchor", "IMG": "Image", "CAPTION":
      "TableCaption", "COL": "TableCol", "COLGROUP": "TableCol", "THEAD":
      "TableSection", "TFOOT": "TableSection", "TBODY": "TableSection", "TR":
      "TableRow", "TH": "TableCell", "TD": "TableCell", "FRAMESET":
      "FrameSet", "IFRAME": "IFrame"
    };
    if (trans[tagName]) klass = 'HTML' + trans[tagName] + 'Element';
    if (window[klass]) return window[klass];
    klass = 'HTML' + tagName + 'Element';
    if (window[klass]) return window[klass];
    klass = 'HTML' + tagName.capitalize() + 'Element';
    if (window[klass]) return window[klass];

    var element = document.createElement(tagName),
        proto = element['__proto__'] || element.constructor.prototype;

    element = null;
    return proto;
  }

  var elementPrototype = window.HTMLElement ? HTMLElement.prototype :
   Element.prototype;

  if (F.ElementExtensions) {
    copy(Element.Methods, elementPrototype);
    copy(Element.Methods.Simulated, elementPrototype, true);
  }

  if (F.SpecificElementExtensions) {
    for (var tag in Element.Methods.ByTag) {
      var klass = findDOMClass(tag);
      if (Object.isUndefined(klass)) continue;
      copy(T[tag], klass.prototype);
    }
  }

  Object.extend(Element, Element.Methods);
  delete Element.ByTag;

  if (Element.extend.refresh) Element.extend.refresh();
  Element.cache = { };
};


document.viewport = {

  getDimensions: function() {
    return { width: this.getWidth(), height: this.getHeight() };
  },

  getScrollOffsets: function() {
    return Element._returnOffset(
      window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft,
      window.pageYOffset || document.documentElement.scrollTop  || document.body.scrollTop);
  }
};

(function(viewport) {
  var B = Prototype.Browser, doc = document, element, property = {};

  function getRootElement() {
    if (B.WebKit && !doc.evaluate)
      return document;

    if (B.Opera && window.parseFloat(window.opera.version()) < 9.5)
      return document.body;

    return document.documentElement;
  }

  function define(D) {
    if (!element) element = getRootElement();

    property[D] = 'client' + D;

    viewport['get' + D] = function() { return element[property[D]] };
    return viewport['get' + D]();
  }

  viewport.getWidth  = define.curry('Width');

  viewport.getHeight = define.curry('Height');
})(document.viewport);


Element.Storage = {
  UID: 1
};

Element.addMethods({
  getStorage: function(element) {
    if (!(element = $(element))) return;

    var uid;
    if (element === window) {
      uid = 0;
    } else {
      if (typeof element._prototypeUID === "undefined")
        element._prototypeUID = Element.Storage.UID++;
      uid = element._prototypeUID;
    }

    if (!Element.Storage[uid])
      Element.Storage[uid] = $H();

    return Element.Storage[uid];
  },

  store: function(element, key, value) {
    if (!(element = $(element))) return;

    if (arguments.length === 2) {
      Element.getStorage(element).update(key);
    } else {
      Element.getStorage(element).set(key, value);
    }

    return element;
  },

  retrieve: function(element, key, defaultValue) {
    if (!(element = $(element))) return;
    var hash = Element.getStorage(element), value = hash.get(key);

    if (Object.isUndefined(value)) {
      hash.set(key, defaultValue);
      value = defaultValue;
    }

    return value;
  },

  clone: function(element, deep) {
    if (!(element = $(element))) return;
    var clone = element.cloneNode(deep);
    clone._prototypeUID = void 0;
    if (deep) {
      var descendants = Element.select(clone, '*'),
          i = descendants.length;
      while (i--) {
        descendants[i]._prototypeUID = void 0;
      }
    }
    return Element.extend(clone);
  },

  purge: function(element) {
    if (!(element = $(element))) return;
    var purgeElement = Element._purgeElement;

    purgeElement(element);

    var descendants = element.getElementsByTagName('*'),
     i = descendants.length;

    while (i--) purgeElement(descendants[i]);

    return null;
  }
});

(function() {

  function toDecimal(pctString) {
    var match = pctString.match(/^(\d+)%?$/i);
    if (!match) return null;
    return (Number(match[1]) / 100);
  }

  function getPixelValue(value, property, context) {
    var element = null;
    if (Object.isElement(value)) {
      element = value;
      value = element.getStyle(property);
    }

    if (value === null) {
      return null;
    }

    if ((/^(?:-)?\d+(\.\d+)?(px)?$/i).test(value)) {
      return window.parseFloat(value);
    }

    var isPercentage = value.include('%'), isViewport = (context === document.viewport);

    if (/\d/.test(value) && element && element.runtimeStyle && !(isPercentage && isViewport)) {
      var style = element.style.left, rStyle = element.runtimeStyle.left;
      element.runtimeStyle.left = element.currentStyle.left;
      element.style.left = value || 0;
      value = element.style.pixelLeft;
      element.style.left = style;
      element.runtimeStyle.left = rStyle;

      return value;
    }

    if (element && isPercentage) {
      context = context || element.parentNode;
      var decimal = toDecimal(value);
      var whole = null;
      var position = element.getStyle('position');

      var isHorizontal = property.include('left') || property.include('right') ||
       property.include('width');

      var isVertical =  property.include('top') || property.include('bottom') ||
        property.include('height');

      if (context === document.viewport) {
        if (isHorizontal) {
          whole = document.viewport.getWidth();
        } else if (isVertical) {
          whole = document.viewport.getHeight();
        }
      } else {
        if (isHorizontal) {
          whole = $(context).measure('width');
        } else if (isVertical) {
          whole = $(context).measure('height');
        }
      }

      return (whole === null) ? 0 : whole * decimal;
    }

    return 0;
  }

  function toCSSPixels(number) {
    if (Object.isString(number) && number.endsWith('px')) {
      return number;
    }
    return number + 'px';
  }

  function isDisplayed(element) {
    var originalElement = element;
    while (element && element.parentNode) {
      var display = element.getStyle('display');
      if (display === 'none') {
        return false;
      }
      element = $(element.parentNode);
    }
    return true;
  }

  var hasLayout = Prototype.K;
  if ('currentStyle' in document.documentElement) {
    hasLayout = function(element) {
      if (!element.currentStyle.hasLayout) {
        element.style.zoom = 1;
      }
      return element;
    };
  }

  function cssNameFor(key) {
    if (key.include('border')) key = key + '-width';
    return key.camelize();
  }

  Element.Layout = Class.create(Hash, {
    initialize: function($super, element, preCompute) {
      $super();
      this.element = $(element);

      Element.Layout.PROPERTIES.each( function(property) {
        this._set(property, null);
      }, this);

      if (preCompute) {
        this._preComputing = true;
        this._begin();
        Element.Layout.PROPERTIES.each( this._compute, this );
        this._end();
        this._preComputing = false;
      }
    },

    _set: function(property, value) {
      return Hash.prototype.set.call(this, property, value);
    },

    set: function(property, value) {
      throw "Properties of Element.Layout are read-only.";
    },

    get: function($super, property) {
      var value = $super(property);
      return value === null ? this._compute(property) : value;
    },

    _begin: function() {
      if (this._prepared) return;

      var element = this.element;
      if (isDisplayed(element)) {
        this._prepared = true;
        return;
      }

      var originalStyles = {
        position:   element.style.position   || '',
        width:      element.style.width      || '',
        visibility: element.style.visibility || '',
        display:    element.style.display    || ''
      };

      element.store('prototype_original_styles', originalStyles);

      var position = element.getStyle('position'),
       width = element.getStyle('width');

      if (width === "0px" || width === null) {
        element.style.display = 'block';
        width = element.getStyle('width');
      }

      var context = (position === 'fixed') ? document.viewport :
       element.parentNode;

      element.setStyle({
        position:   'absolute',
        visibility: 'hidden',
        display:    'block'
      });

      var positionedWidth = element.getStyle('width');

      var newWidth;
      if (width && (positionedWidth === width)) {
        newWidth = getPixelValue(element, 'width', context);
      } else if (position === 'absolute' || position === 'fixed') {
        newWidth = getPixelValue(element, 'width', context);
      } else {
        var parent = element.parentNode, pLayout = $(parent).getLayout();

        newWidth = pLayout.get('width') -
         this.get('margin-left') -
         this.get('border-left') -
         this.get('padding-left') -
         this.get('padding-right') -
         this.get('border-right') -
         this.get('margin-right');
      }

      element.setStyle({ width: newWidth + 'px' });

      this._prepared = true;
    },

    _end: function() {
      var element = this.element;
      var originalStyles = element.retrieve('prototype_original_styles');
      element.store('prototype_original_styles', null);
      element.setStyle(originalStyles);
      this._prepared = false;
    },

    _compute: function(property) {
      var COMPUTATIONS = Element.Layout.COMPUTATIONS;
      if (!(property in COMPUTATIONS)) {
        throw "Property not found.";
      }

      return this._set(property, COMPUTATIONS[property].call(this, this.element));
    },

    toObject: function() {
      var args = $A(arguments);
      var keys = (args.length === 0) ? Element.Layout.PROPERTIES :
       args.join(' ').split(' ');
      var obj = {};
      keys.each( function(key) {
        if (!Element.Layout.PROPERTIES.include(key)) return;
        var value = this.get(key);
        if (value != null) obj[key] = value;
      }, this);
      return obj;
    },

    toHash: function() {
      var obj = this.toObject.apply(this, arguments);
      return new Hash(obj);
    },

    toCSS: function() {
      var args = $A(arguments);
      var keys = (args.length === 0) ? Element.Layout.PROPERTIES :
       args.join(' ').split(' ');
      var css = {};

      keys.each( function(key) {
        if (!Element.Layout.PROPERTIES.include(key)) return;
        if (Element.Layout.COMPOSITE_PROPERTIES.include(key)) return;

        var value = this.get(key);
        if (value != null) css[cssNameFor(key)] = value + 'px';
      }, this);
      return css;
    },

    inspect: function() {
      return "#<Element.Layout>";
    }
  });

  Object.extend(Element.Layout, {
    PROPERTIES: $w('height width top left right bottom border-left border-right border-top border-bottom padding-left padding-right padding-top padding-bottom margin-top margin-bottom margin-left margin-right padding-box-width padding-box-height border-box-width border-box-height margin-box-width margin-box-height'),

    COMPOSITE_PROPERTIES: $w('padding-box-width padding-box-height margin-box-width margin-box-height border-box-width border-box-height'),

    COMPUTATIONS: {
      'height': function(element) {
        if (!this._preComputing) this._begin();

        var bHeight = this.get('border-box-height');
        if (bHeight <= 0) {
          if (!this._preComputing) this._end();
          return 0;
        }

        var bTop = this.get('border-top'),
         bBottom = this.get('border-bottom');

        var pTop = this.get('padding-top'),
         pBottom = this.get('padding-bottom');

        if (!this._preComputing) this._end();

        return bHeight - bTop - bBottom - pTop - pBottom;
      },

      'width': function(element) {
        if (!this._preComputing) this._begin();

        var bWidth = this.get('border-box-width');
        if (bWidth <= 0) {
          if (!this._preComputing) this._end();
          return 0;
        }

        var bLeft = this.get('border-left'),
         bRight = this.get('border-right');

        var pLeft = this.get('padding-left'),
         pRight = this.get('padding-right');

        if (!this._preComputing) this._end();

        return bWidth - bLeft - bRight - pLeft - pRight;
      },

      'padding-box-height': function(element) {
        var height = this.get('height'),
         pTop = this.get('padding-top'),
         pBottom = this.get('padding-bottom');

        return height + pTop + pBottom;
      },

      'padding-box-width': function(element) {
        var width = this.get('width'),
         pLeft = this.get('padding-left'),
         pRight = this.get('padding-right');

        return width + pLeft + pRight;
      },

      'border-box-height': function(element) {
        if (!this._preComputing) this._begin();
        var height = element.offsetHeight;
        if (!this._preComputing) this._end();
        return height;
      },

      'border-box-width': function(element) {
        if (!this._preComputing) this._begin();
        var width = element.offsetWidth;
        if (!this._preComputing) this._end();
        return width;
      },

      'margin-box-height': function(element) {
        var bHeight = this.get('border-box-height'),
         mTop = this.get('margin-top'),
         mBottom = this.get('margin-bottom');

        if (bHeight <= 0) return 0;

        return bHeight + mTop + mBottom;
      },

      'margin-box-width': function(element) {
        var bWidth = this.get('border-box-width'),
         mLeft = this.get('margin-left'),
         mRight = this.get('margin-right');

        if (bWidth <= 0) return 0;

        return bWidth + mLeft + mRight;
      },

      'top': function(element) {
        var offset = element.positionedOffset();
        return offset.top;
      },

      'bottom': function(element) {
        var offset = element.positionedOffset(),
         parent = element.getOffsetParent(),
         pHeight = parent.measure('height');

        var mHeight = this.get('border-box-height');

        return pHeight - mHeight - offset.top;
      },

      'left': function(element) {
        var offset = element.positionedOffset();
        return offset.left;
      },

      'right': function(element) {
        var offset = element.positionedOffset(),
         parent = element.getOffsetParent(),
         pWidth = parent.measure('width');

        var mWidth = this.get('border-box-width');

        return pWidth - mWidth - offset.left;
      },

      'padding-top': function(element) {
        return getPixelValue(element, 'paddingTop');
      },

      'padding-bottom': function(element) {
        return getPixelValue(element, 'paddingBottom');
      },

      'padding-left': function(element) {
        return getPixelValue(element, 'paddingLeft');
      },

      'padding-right': function(element) {
        return getPixelValue(element, 'paddingRight');
      },

      'border-top': function(element) {
        return getPixelValue(element, 'borderTopWidth');
      },

      'border-bottom': function(element) {
        return getPixelValue(element, 'borderBottomWidth');
      },

      'border-left': function(element) {
        return getPixelValue(element, 'borderLeftWidth');
      },

      'border-right': function(element) {
        return getPixelValue(element, 'borderRightWidth');
      },

      'margin-top': function(element) {
        return getPixelValue(element, 'marginTop');
      },

      'margin-bottom': function(element) {
        return getPixelValue(element, 'marginBottom');
      },

      'margin-left': function(element) {
        return getPixelValue(element, 'marginLeft');
      },

      'margin-right': function(element) {
        return getPixelValue(element, 'marginRight');
      }
    }
  });

  if ('getBoundingClientRect' in document.documentElement) {
    Object.extend(Element.Layout.COMPUTATIONS, {
      'right': function(element) {
        var parent = hasLayout(element.getOffsetParent());
        var rect = element.getBoundingClientRect(),
         pRect = parent.getBoundingClientRect();

        return (pRect.right - rect.right).round();
      },

      'bottom': function(element) {
        var parent = hasLayout(element.getOffsetParent());
        var rect = element.getBoundingClientRect(),
         pRect = parent.getBoundingClientRect();

        return (pRect.bottom - rect.bottom).round();
      }
    });
  }

  Element.Offset = Class.create({
    initialize: function(left, top) {
      this.left = left.round();
      this.top  = top.round();

      this[0] = this.left;
      this[1] = this.top;
    },

    relativeTo: function(offset) {
      return new Element.Offset(
        this.left - offset.left,
        this.top  - offset.top
      );
    },

    inspect: function() {
      return "#<Element.Offset left: #{left} top: #{top}>".interpolate(this);
    },

    toString: function() {
      return "[#{left}, #{top}]".interpolate(this);
    },

    toArray: function() {
      return [this.left, this.top];
    }
  });

  function getLayout(element, preCompute) {
    return new Element.Layout(element, preCompute);
  }

  function measure(element, property) {
    return $(element).getLayout().get(property);
  }

  function getDimensions(element) {
    element = $(element);
    var display = Element.getStyle(element, 'display');

    if (display && display !== 'none') {
      return { width: element.offsetWidth, height: element.offsetHeight };
    }

    var style = element.style;
    var originalStyles = {
      visibility: style.visibility,
      position:   style.position,
      display:    style.display
    };

    var newStyles = {
      visibility: 'hidden',
      display:    'block'
    };

    if (originalStyles.position !== 'fixed')
      newStyles.position = 'absolute';

    Element.setStyle(element, newStyles);

    var dimensions = {
      width:  element.offsetWidth,
      height: element.offsetHeight
    };

    Element.setStyle(element, originalStyles);

    return dimensions;
  }

  function getOffsetParent(element) {
    element = $(element);

    if (isDocument(element) || isDetached(element) || isBody(element) || isHtml(element))
      return $(document.body);

    var isInline = (Element.getStyle(element, 'display') === 'inline');
    if (!isInline && element.offsetParent) return $(element.offsetParent);

    while ((element = element.parentNode) && element !== document.body) {
      if (Element.getStyle(element, 'position') !== 'static') {
        return isHtml(element) ? $(document.body) : $(element);
      }
    }

    return $(document.body);
  }


  function cumulativeOffset(element) {
    element = $(element);
    var valueT = 0, valueL = 0;
    if (element.parentNode) {
      do {
        valueT += element.offsetTop  || 0;
        valueL += element.offsetLeft || 0;
        element = element.offsetParent;
      } while (element);
    }
    return new Element.Offset(valueL, valueT);
  }

  function positionedOffset(element) {
    element = $(element);

    var layout = element.getLayout();

    var valueT = 0, valueL = 0;
    do {
      valueT += element.offsetTop  || 0;
      valueL += element.offsetLeft || 0;
      element = element.offsetParent;
      if (element) {
        if (isBody(element)) break;
        var p = Element.getStyle(element, 'position');
        if (p !== 'static') break;
      }
    } while (element);

    valueL -= layout.get('margin-top');
    valueT -= layout.get('margin-left');

    return new Element.Offset(valueL, valueT);
  }

  function cumulativeScrollOffset(element) {
    var valueT = 0, valueL = 0;
    do {
      valueT += element.scrollTop  || 0;
      valueL += element.scrollLeft || 0;
      element = element.parentNode;
    } while (element);
    return new Element.Offset(valueL, valueT);
  }

  function viewportOffset(forElement) {
    element = $(element);
    var valueT = 0, valueL = 0, docBody = document.body;

    var element = forElement;
    do {
      valueT += element.offsetTop  || 0;
      valueL += element.offsetLeft || 0;
      if (element.offsetParent == docBody &&
        Element.getStyle(element, 'position') == 'absolute') break;
    } while (element = element.offsetParent);

    element = forElement;
    do {
      if (element != docBody) {
        valueT -= element.scrollTop  || 0;
        valueL -= element.scrollLeft || 0;
      }
    } while (element = element.parentNode);
    return new Element.Offset(valueL, valueT);
  }

  function absolutize(element) {
    element = $(element);

    if (Element.getStyle(element, 'position') === 'absolute') {
      return element;
    }

    var offsetParent = getOffsetParent(element);
    var eOffset = element.viewportOffset(),
     pOffset = offsetParent.viewportOffset();

    var offset = eOffset.relativeTo(pOffset);
    var layout = element.getLayout();

    element.store('prototype_absolutize_original_styles', {
      left:   element.getStyle('left'),
      top:    element.getStyle('top'),
      width:  element.getStyle('width'),
      height: element.getStyle('height')
    });

    element.setStyle({
      position: 'absolute',
      top:    offset.top + 'px',
      left:   offset.left + 'px',
      width:  layout.get('width') + 'px',
      height: layout.get('height') + 'px'
    });

    return element;
  }

  function relativize(element) {
    element = $(element);
    if (Element.getStyle(element, 'position') === 'relative') {
      return element;
    }

    var originalStyles =
     element.retrieve('prototype_absolutize_original_styles');

    if (originalStyles) element.setStyle(originalStyles);
    return element;
  }

  if (Prototype.Browser.IE) {
    getOffsetParent = getOffsetParent.wrap(
      function(proceed, element) {
        element = $(element);

        if (isDocument(element) || isDetached(element) || isBody(element) || isHtml(element))
          return $(document.body);

        var position = element.getStyle('position');
        if (position !== 'static') return proceed(element);

        element.setStyle({ position: 'relative' });
        var value = proceed(element);
        element.setStyle({ position: position });
        return value;
      }
    );

    positionedOffset = positionedOffset.wrap(function(proceed, element) {
      element = $(element);
      if (!element.parentNode) return new Element.Offset(0, 0);
      var position = element.getStyle('position');
      if (position !== 'static') return proceed(element);

      var offsetParent = element.getOffsetParent();
      if (offsetParent && offsetParent.getStyle('position') === 'fixed')
        hasLayout(offsetParent);

      element.setStyle({ position: 'relative' });
      var value = proceed(element);
      element.setStyle({ position: position });
      return value;
    });
  } else if (Prototype.Browser.Webkit) {
    cumulativeOffset = function(element) {
      element = $(element);
      var valueT = 0, valueL = 0;
      do {
        valueT += element.offsetTop  || 0;
        valueL += element.offsetLeft || 0;
        if (element.offsetParent == document.body)
          if (Element.getStyle(element, 'position') == 'absolute') break;

        element = element.offsetParent;
      } while (element);

      return new Element.Offset(valueL, valueT);
    };
  }


  Element.addMethods({
    getLayout:              getLayout,
    measure:                measure,
    getDimensions:          getDimensions,
    getOffsetParent:        getOffsetParent,
    cumulativeOffset:       cumulativeOffset,
    positionedOffset:       positionedOffset,
    cumulativeScrollOffset: cumulativeScrollOffset,
    viewportOffset:         viewportOffset,
    absolutize:             absolutize,
    relativize:             relativize
  });

  function isBody(element) {
    return element.nodeName.toUpperCase() === 'BODY';
  }

  function isHtml(element) {
    return element.nodeName.toUpperCase() === 'HTML';
  }

  function isDocument(element) {
    return element.nodeType === Node.DOCUMENT_NODE;
  }

  function isDetached(element) {
    return element !== document.body &&
     !Element.descendantOf(element, document.body);
  }

  if ('getBoundingClientRect' in document.documentElement) {
    Element.addMethods({
      viewportOffset: function(element) {
        element = $(element);
        if (isDetached(element)) return new Element.Offset(0, 0);

        var rect = element.getBoundingClientRect(),
         docEl = document.documentElement;
        return new Element.Offset(rect.left - docEl.clientLeft,
         rect.top - docEl.clientTop);
      }
    });
  }
})();
window.$$ = function() {
  var expression = $A(arguments).join(', ');
  return Prototype.Selector.select(expression, document);
};

Prototype.Selector = (function() {

  function select() {
    throw new Error('Method "Prototype.Selector.select" must be defined.');
  }

  function match() {
    throw new Error('Method "Prototype.Selector.match" must be defined.');
  }

  function find(elements, expression, index) {
    index = index || 0;
    var match = Prototype.Selector.match, length = elements.length, matchIndex = 0, i;

    for (i = 0; i < length; i++) {
      if (match(elements[i], expression) && index == matchIndex++) {
        return Element.extend(elements[i]);
      }
    }
  }

  function extendElements(elements) {
    for (var i = 0, length = elements.length; i < length; i++) {
      Element.extend(elements[i]);
    }
    return elements;
  }


  var K = Prototype.K;

  return {
    select: select,
    match: match,
    find: find,
    extendElements: (Element.extend === K) ? K : extendElements,
    extendElement: Element.extend
  };
})();
Prototype._original_property = window.Sizzle;
/*!
 * Sizzle CSS Selector Engine - v1.0
 *  Copyright 2009, The Dojo Foundation
 *  Released under the MIT, BSD, and GPL Licenses.
 *  More information: http://sizzlejs.com/
 */
(function(){

var chunker = /((?:\((?:\([^()]+\)|[^()]+)+\)|\[(?:\[[^[\]]*\]|['"][^'"]*['"]|[^[\]'"]+)+\]|\\.|[^ >+~,(\[\\]+)+|[>+~])(\s*,\s*)?((?:.|\r|\n)*)/g,
	done = 0,
	toString = Object.prototype.toString,
	hasDuplicate = false,
	baseHasDuplicate = true;

[0, 0].sort(function(){
	baseHasDuplicate = false;
	return 0;
});

var Sizzle = function(selector, context, results, seed) {
	results = results || [];
	var origContext = context = context || document;

	if ( context.nodeType !== 1 && context.nodeType !== 9 ) {
		return [];
	}

	if ( !selector || typeof selector !== "string" ) {
		return results;
	}

	var parts = [], m, set, checkSet, check, mode, extra, prune = true, contextXML = isXML(context),
		soFar = selector;

	while ( (chunker.exec(""), m = chunker.exec(soFar)) !== null ) {
		soFar = m[3];

		parts.push( m[1] );

		if ( m[2] ) {
			extra = m[3];
			break;
		}
	}

	if ( parts.length > 1 && origPOS.exec( selector ) ) {
		if ( parts.length === 2 && Expr.relative[ parts[0] ] ) {
			set = posProcess( parts[0] + parts[1], context );
		} else {
			set = Expr.relative[ parts[0] ] ?
				[ context ] :
				Sizzle( parts.shift(), context );

			while ( parts.length ) {
				selector = parts.shift();

				if ( Expr.relative[ selector ] )
					selector += parts.shift();

				set = posProcess( selector, set );
			}
		}
	} else {
		if ( !seed && parts.length > 1 && context.nodeType === 9 && !contextXML &&
				Expr.match.ID.test(parts[0]) && !Expr.match.ID.test(parts[parts.length - 1]) ) {
			var ret = Sizzle.find( parts.shift(), context, contextXML );
			context = ret.expr ? Sizzle.filter( ret.expr, ret.set )[0] : ret.set[0];
		}

		if ( context ) {
			var ret = seed ?
				{ expr: parts.pop(), set: makeArray(seed) } :
				Sizzle.find( parts.pop(), parts.length === 1 && (parts[0] === "~" || parts[0] === "+") && context.parentNode ? context.parentNode : context, contextXML );
			set = ret.expr ? Sizzle.filter( ret.expr, ret.set ) : ret.set;

			if ( parts.length > 0 ) {
				checkSet = makeArray(set);
			} else {
				prune = false;
			}

			while ( parts.length ) {
				var cur = parts.pop(), pop = cur;

				if ( !Expr.relative[ cur ] ) {
					cur = "";
				} else {
					pop = parts.pop();
				}

				if ( pop == null ) {
					pop = context;
				}

				Expr.relative[ cur ]( checkSet, pop, contextXML );
			}
		} else {
			checkSet = parts = [];
		}
	}

	if ( !checkSet ) {
		checkSet = set;
	}

	if ( !checkSet ) {
		throw "Syntax error, unrecognized expression: " + (cur || selector);
	}

	if ( toString.call(checkSet) === "[object Array]" ) {
		if ( !prune ) {
			results.push.apply( results, checkSet );
		} else if ( context && context.nodeType === 1 ) {
			for ( var i = 0; checkSet[i] != null; i++ ) {
				if ( checkSet[i] && (checkSet[i] === true || checkSet[i].nodeType === 1 && contains(context, checkSet[i])) ) {
					results.push( set[i] );
				}
			}
		} else {
			for ( var i = 0; checkSet[i] != null; i++ ) {
				if ( checkSet[i] && checkSet[i].nodeType === 1 ) {
					results.push( set[i] );
				}
			}
		}
	} else {
		makeArray( checkSet, results );
	}

	if ( extra ) {
		Sizzle( extra, origContext, results, seed );
		Sizzle.uniqueSort( results );
	}

	return results;
};

Sizzle.uniqueSort = function(results){
	if ( sortOrder ) {
		hasDuplicate = baseHasDuplicate;
		results.sort(sortOrder);

		if ( hasDuplicate ) {
			for ( var i = 1; i < results.length; i++ ) {
				if ( results[i] === results[i-1] ) {
					results.splice(i--, 1);
				}
			}
		}
	}

	return results;
};

Sizzle.matches = function(expr, set){
	return Sizzle(expr, null, null, set);
};

Sizzle.find = function(expr, context, isXML){
	var set, match;

	if ( !expr ) {
		return [];
	}

	for ( var i = 0, l = Expr.order.length; i < l; i++ ) {
		var type = Expr.order[i], match;

		if ( (match = Expr.leftMatch[ type ].exec( expr )) ) {
			var left = match[1];
			match.splice(1,1);

			if ( left.substr( left.length - 1 ) !== "\\" ) {
				match[1] = (match[1] || "").replace(/\\/g, "");
				set = Expr.find[ type ]( match, context, isXML );
				if ( set != null ) {
					expr = expr.replace( Expr.match[ type ], "" );
					break;
				}
			}
		}
	}

	if ( !set ) {
		set = context.getElementsByTagName("*");
	}

	return {set: set, expr: expr};
};

Sizzle.filter = function(expr, set, inplace, not){
	var old = expr, result = [], curLoop = set, match, anyFound,
		isXMLFilter = set && set[0] && isXML(set[0]);

	while ( expr && set.length ) {
		for ( var type in Expr.filter ) {
			if ( (match = Expr.match[ type ].exec( expr )) != null ) {
				var filter = Expr.filter[ type ], found, item;
				anyFound = false;

				if ( curLoop == result ) {
					result = [];
				}

				if ( Expr.preFilter[ type ] ) {
					match = Expr.preFilter[ type ]( match, curLoop, inplace, result, not, isXMLFilter );

					if ( !match ) {
						anyFound = found = true;
					} else if ( match === true ) {
						continue;
					}
				}

				if ( match ) {
					for ( var i = 0; (item = curLoop[i]) != null; i++ ) {
						if ( item ) {
							found = filter( item, match, i, curLoop );
							var pass = not ^ !!found;

							if ( inplace && found != null ) {
								if ( pass ) {
									anyFound = true;
								} else {
									curLoop[i] = false;
								}
							} else if ( pass ) {
								result.push( item );
								anyFound = true;
							}
						}
					}
				}

				if ( found !== undefined ) {
					if ( !inplace ) {
						curLoop = result;
					}

					expr = expr.replace( Expr.match[ type ], "" );

					if ( !anyFound ) {
						return [];
					}

					break;
				}
			}
		}

		if ( expr == old ) {
			if ( anyFound == null ) {
				throw "Syntax error, unrecognized expression: " + expr;
			} else {
				break;
			}
		}

		old = expr;
	}

	return curLoop;
};

var Expr = Sizzle.selectors = {
	order: [ "ID", "NAME", "TAG" ],
	match: {
		ID: /#((?:[\w\u00c0-\uFFFF-]|\\.)+)/,
		CLASS: /\.((?:[\w\u00c0-\uFFFF-]|\\.)+)/,
		NAME: /\[name=['"]*((?:[\w\u00c0-\uFFFF-]|\\.)+)['"]*\]/,
		ATTR: /\[\s*((?:[\w\u00c0-\uFFFF-]|\\.)+)\s*(?:(\S?=)\s*(['"]*)(.*?)\3|)\s*\]/,
		TAG: /^((?:[\w\u00c0-\uFFFF\*-]|\\.)+)/,
		CHILD: /:(only|nth|last|first)-child(?:\((even|odd|[\dn+-]*)\))?/,
		POS: /:(nth|eq|gt|lt|first|last|even|odd)(?:\((\d*)\))?(?=[^-]|$)/,
		PSEUDO: /:((?:[\w\u00c0-\uFFFF-]|\\.)+)(?:\((['"]*)((?:\([^\)]+\)|[^\2\(\)]*)+)\2\))?/
	},
	leftMatch: {},
	attrMap: {
		"class": "className",
		"for": "htmlFor"
	},
	attrHandle: {
		href: function(elem){
			return elem.getAttribute("href");
		}
	},
	relative: {
		"+": function(checkSet, part, isXML){
			var isPartStr = typeof part === "string",
				isTag = isPartStr && !/\W/.test(part),
				isPartStrNotTag = isPartStr && !isTag;

			if ( isTag && !isXML ) {
				part = part.toUpperCase();
			}

			for ( var i = 0, l = checkSet.length, elem; i < l; i++ ) {
				if ( (elem = checkSet[i]) ) {
					while ( (elem = elem.previousSibling) && elem.nodeType !== 1 ) {}

					checkSet[i] = isPartStrNotTag || elem && elem.nodeName === part ?
						elem || false :
						elem === part;
				}
			}

			if ( isPartStrNotTag ) {
				Sizzle.filter( part, checkSet, true );
			}
		},
		">": function(checkSet, part, isXML){
			var isPartStr = typeof part === "string";

			if ( isPartStr && !/\W/.test(part) ) {
				part = isXML ? part : part.toUpperCase();

				for ( var i = 0, l = checkSet.length; i < l; i++ ) {
					var elem = checkSet[i];
					if ( elem ) {
						var parent = elem.parentNode;
						checkSet[i] = parent.nodeName === part ? parent : false;
					}
				}
			} else {
				for ( var i = 0, l = checkSet.length; i < l; i++ ) {
					var elem = checkSet[i];
					if ( elem ) {
						checkSet[i] = isPartStr ?
							elem.parentNode :
							elem.parentNode === part;
					}
				}

				if ( isPartStr ) {
					Sizzle.filter( part, checkSet, true );
				}
			}
		},
		"": function(checkSet, part, isXML){
			var doneName = done++, checkFn = dirCheck;

			if ( !/\W/.test(part) ) {
				var nodeCheck = part = isXML ? part : part.toUpperCase();
				checkFn = dirNodeCheck;
			}

			checkFn("parentNode", part, doneName, checkSet, nodeCheck, isXML);
		},
		"~": function(checkSet, part, isXML){
			var doneName = done++, checkFn = dirCheck;

			if ( typeof part === "string" && !/\W/.test(part) ) {
				var nodeCheck = part = isXML ? part : part.toUpperCase();
				checkFn = dirNodeCheck;
			}

			checkFn("previousSibling", part, doneName, checkSet, nodeCheck, isXML);
		}
	},
	find: {
		ID: function(match, context, isXML){
			if ( typeof context.getElementById !== "undefined" && !isXML ) {
				var m = context.getElementById(match[1]);
				return m ? [m] : [];
			}
		},
		NAME: function(match, context, isXML){
			if ( typeof context.getElementsByName !== "undefined" ) {
				var ret = [], results = context.getElementsByName(match[1]);

				for ( var i = 0, l = results.length; i < l; i++ ) {
					if ( results[i].getAttribute("name") === match[1] ) {
						ret.push( results[i] );
					}
				}

				return ret.length === 0 ? null : ret;
			}
		},
		TAG: function(match, context){
			return context.getElementsByTagName(match[1]);
		}
	},
	preFilter: {
		CLASS: function(match, curLoop, inplace, result, not, isXML){
			match = " " + match[1].replace(/\\/g, "") + " ";

			if ( isXML ) {
				return match;
			}

			for ( var i = 0, elem; (elem = curLoop[i]) != null; i++ ) {
				if ( elem ) {
					if ( not ^ (elem.className && (" " + elem.className + " ").indexOf(match) >= 0) ) {
						if ( !inplace )
							result.push( elem );
					} else if ( inplace ) {
						curLoop[i] = false;
					}
				}
			}

			return false;
		},
		ID: function(match){
			return match[1].replace(/\\/g, "");
		},
		TAG: function(match, curLoop){
			for ( var i = 0; curLoop[i] === false; i++ ){}
			return curLoop[i] && isXML(curLoop[i]) ? match[1] : match[1].toUpperCase();
		},
		CHILD: function(match){
			if ( match[1] == "nth" ) {
				var test = /(-?)(\d*)n((?:\+|-)?\d*)/.exec(
					match[2] == "even" && "2n" || match[2] == "odd" && "2n+1" ||
					!/\D/.test( match[2] ) && "0n+" + match[2] || match[2]);

				match[2] = (test[1] + (test[2] || 1)) - 0;
				match[3] = test[3] - 0;
			}

			match[0] = done++;

			return match;
		},
		ATTR: function(match, curLoop, inplace, result, not, isXML){
			var name = match[1].replace(/\\/g, "");

			if ( !isXML && Expr.attrMap[name] ) {
				match[1] = Expr.attrMap[name];
			}

			if ( match[2] === "~=" ) {
				match[4] = " " + match[4] + " ";
			}

			return match;
		},
		PSEUDO: function(match, curLoop, inplace, result, not){
			if ( match[1] === "not" ) {
				if ( ( chunker.exec(match[3]) || "" ).length > 1 || /^\w/.test(match[3]) ) {
					match[3] = Sizzle(match[3], null, null, curLoop);
				} else {
					var ret = Sizzle.filter(match[3], curLoop, inplace, true ^ not);
					if ( !inplace ) {
						result.push.apply( result, ret );
					}
					return false;
				}
			} else if ( Expr.match.POS.test( match[0] ) || Expr.match.CHILD.test( match[0] ) ) {
				return true;
			}

			return match;
		},
		POS: function(match){
			match.unshift( true );
			return match;
		}
	},
	filters: {
		enabled: function(elem){
			return elem.disabled === false && elem.type !== "hidden";
		},
		disabled: function(elem){
			return elem.disabled === true;
		},
		checked: function(elem){
			return elem.checked === true;
		},
		selected: function(elem){
			elem.parentNode.selectedIndex;
			return elem.selected === true;
		},
		parent: function(elem){
			return !!elem.firstChild;
		},
		empty: function(elem){
			return !elem.firstChild;
		},
		has: function(elem, i, match){
			return !!Sizzle( match[3], elem ).length;
		},
		header: function(elem){
			return /h\d/i.test( elem.nodeName );
		},
		text: function(elem){
			return "text" === elem.type;
		},
		radio: function(elem){
			return "radio" === elem.type;
		},
		checkbox: function(elem){
			return "checkbox" === elem.type;
		},
		file: function(elem){
			return "file" === elem.type;
		},
		password: function(elem){
			return "password" === elem.type;
		},
		submit: function(elem){
			return "submit" === elem.type;
		},
		image: function(elem){
			return "image" === elem.type;
		},
		reset: function(elem){
			return "reset" === elem.type;
		},
		button: function(elem){
			return "button" === elem.type || elem.nodeName.toUpperCase() === "BUTTON";
		},
		input: function(elem){
			return /input|select|textarea|button/i.test(elem.nodeName);
		}
	},
	setFilters: {
		first: function(elem, i){
			return i === 0;
		},
		last: function(elem, i, match, array){
			return i === array.length - 1;
		},
		even: function(elem, i){
			return i % 2 === 0;
		},
		odd: function(elem, i){
			return i % 2 === 1;
		},
		lt: function(elem, i, match){
			return i < match[3] - 0;
		},
		gt: function(elem, i, match){
			return i > match[3] - 0;
		},
		nth: function(elem, i, match){
			return match[3] - 0 == i;
		},
		eq: function(elem, i, match){
			return match[3] - 0 == i;
		}
	},
	filter: {
		PSEUDO: function(elem, match, i, array){
			var name = match[1], filter = Expr.filters[ name ];

			if ( filter ) {
				return filter( elem, i, match, array );
			} else if ( name === "contains" ) {
				return (elem.textContent || elem.innerText || "").indexOf(match[3]) >= 0;
			} else if ( name === "not" ) {
				var not = match[3];

				for ( var i = 0, l = not.length; i < l; i++ ) {
					if ( not[i] === elem ) {
						return false;
					}
				}

				return true;
			}
		},
		CHILD: function(elem, match){
			var type = match[1], node = elem;
			switch (type) {
				case 'only':
				case 'first':
					while ( (node = node.previousSibling) )  {
						if ( node.nodeType === 1 ) return false;
					}
					if ( type == 'first') return true;
					node = elem;
				case 'last':
					while ( (node = node.nextSibling) )  {
						if ( node.nodeType === 1 ) return false;
					}
					return true;
				case 'nth':
					var first = match[2], last = match[3];

					if ( first == 1 && last == 0 ) {
						return true;
					}

					var doneName = match[0],
						parent = elem.parentNode;

					if ( parent && (parent.sizcache !== doneName || !elem.nodeIndex) ) {
						var count = 0;
						for ( node = parent.firstChild; node; node = node.nextSibling ) {
							if ( node.nodeType === 1 ) {
								node.nodeIndex = ++count;
							}
						}
						parent.sizcache = doneName;
					}

					var diff = elem.nodeIndex - last;
					if ( first == 0 ) {
						return diff == 0;
					} else {
						return ( diff % first == 0 && diff / first >= 0 );
					}
			}
		},
		ID: function(elem, match){
			return elem.nodeType === 1 && elem.getAttribute("id") === match;
		},
		TAG: function(elem, match){
			return (match === "*" && elem.nodeType === 1) || elem.nodeName === match;
		},
		CLASS: function(elem, match){
			return (" " + (elem.className || elem.getAttribute("class")) + " ")
				.indexOf( match ) > -1;
		},
		ATTR: function(elem, match){
			var name = match[1],
				result = Expr.attrHandle[ name ] ?
					Expr.attrHandle[ name ]( elem ) :
					elem[ name ] != null ?
						elem[ name ] :
						elem.getAttribute( name ),
				value = result + "",
				type = match[2],
				check = match[4];

			return result == null ?
				type === "!=" :
				type === "=" ?
				value === check :
				type === "*=" ?
				value.indexOf(check) >= 0 :
				type === "~=" ?
				(" " + value + " ").indexOf(check) >= 0 :
				!check ?
				value && result !== false :
				type === "!=" ?
				value != check :
				type === "^=" ?
				value.indexOf(check) === 0 :
				type === "$=" ?
				value.substr(value.length - check.length) === check :
				type === "|=" ?
				value === check || value.substr(0, check.length + 1) === check + "-" :
				false;
		},
		POS: function(elem, match, i, array){
			var name = match[2], filter = Expr.setFilters[ name ];

			if ( filter ) {
				return filter( elem, i, match, array );
			}
		}
	}
};

var origPOS = Expr.match.POS;

for ( var type in Expr.match ) {
	Expr.match[ type ] = new RegExp( Expr.match[ type ].source + /(?![^\[]*\])(?![^\(]*\))/.source );
	Expr.leftMatch[ type ] = new RegExp( /(^(?:.|\r|\n)*?)/.source + Expr.match[ type ].source );
}

var makeArray = function(array, results) {
	array = Array.prototype.slice.call( array, 0 );

	if ( results ) {
		results.push.apply( results, array );
		return results;
	}

	return array;
};

try {
	Array.prototype.slice.call( document.documentElement.childNodes, 0 );

} catch(e){
	makeArray = function(array, results) {
		var ret = results || [];

		if ( toString.call(array) === "[object Array]" ) {
			Array.prototype.push.apply( ret, array );
		} else {
			if ( typeof array.length === "number" ) {
				for ( var i = 0, l = array.length; i < l; i++ ) {
					ret.push( array[i] );
				}
			} else {
				for ( var i = 0; array[i]; i++ ) {
					ret.push( array[i] );
				}
			}
		}

		return ret;
	};
}

var sortOrder;

if ( document.documentElement.compareDocumentPosition ) {
	sortOrder = function( a, b ) {
		if ( !a.compareDocumentPosition || !b.compareDocumentPosition ) {
			if ( a == b ) {
				hasDuplicate = true;
			}
			return 0;
		}

		var ret = a.compareDocumentPosition(b) & 4 ? -1 : a === b ? 0 : 1;
		if ( ret === 0 ) {
			hasDuplicate = true;
		}
		return ret;
	};
} else if ( "sourceIndex" in document.documentElement ) {
	sortOrder = function( a, b ) {
		if ( !a.sourceIndex || !b.sourceIndex ) {
			if ( a == b ) {
				hasDuplicate = true;
			}
			return 0;
		}

		var ret = a.sourceIndex - b.sourceIndex;
		if ( ret === 0 ) {
			hasDuplicate = true;
		}
		return ret;
	};
} else if ( document.createRange ) {
	sortOrder = function( a, b ) {
		if ( !a.ownerDocument || !b.ownerDocument ) {
			if ( a == b ) {
				hasDuplicate = true;
			}
			return 0;
		}

		var aRange = a.ownerDocument.createRange(), bRange = b.ownerDocument.createRange();
		aRange.setStart(a, 0);
		aRange.setEnd(a, 0);
		bRange.setStart(b, 0);
		bRange.setEnd(b, 0);
		var ret = aRange.compareBoundaryPoints(Range.START_TO_END, bRange);
		if ( ret === 0 ) {
			hasDuplicate = true;
		}
		return ret;
	};
}

(function(){
	var form = document.createElement("div"),
		id = "script" + (new Date).getTime();
	form.innerHTML = "<a name='" + id + "'/>";

	var root = document.documentElement;
	root.insertBefore( form, root.firstChild );

	if ( !!document.getElementById( id ) ) {
		Expr.find.ID = function(match, context, isXML){
			if ( typeof context.getElementById !== "undefined" && !isXML ) {
				var m = context.getElementById(match[1]);
				return m ? m.id === match[1] || typeof m.getAttributeNode !== "undefined" && m.getAttributeNode("id").nodeValue === match[1] ? [m] : undefined : [];
			}
		};

		Expr.filter.ID = function(elem, match){
			var node = typeof elem.getAttributeNode !== "undefined" && elem.getAttributeNode("id");
			return elem.nodeType === 1 && node && node.nodeValue === match;
		};
	}

	root.removeChild( form );
	root = form = null; // release memory in IE
})();

(function(){

	var div = document.createElement("div");
	div.appendChild( document.createComment("") );

	if ( div.getElementsByTagName("*").length > 0 ) {
		Expr.find.TAG = function(match, context){
			var results = context.getElementsByTagName(match[1]);

			if ( match[1] === "*" ) {
				var tmp = [];

				for ( var i = 0; results[i]; i++ ) {
					if ( results[i].nodeType === 1 ) {
						tmp.push( results[i] );
					}
				}

				results = tmp;
			}

			return results;
		};
	}

	div.innerHTML = "<a href='#'></a>";
	if ( div.firstChild && typeof div.firstChild.getAttribute !== "undefined" &&
			div.firstChild.getAttribute("href") !== "#" ) {
		Expr.attrHandle.href = function(elem){
			return elem.getAttribute("href", 2);
		};
	}

	div = null; // release memory in IE
})();

if ( document.querySelectorAll ) (function(){
	var oldSizzle = Sizzle, div = document.createElement("div");
	div.innerHTML = "<p class='TEST'></p>";

	if ( div.querySelectorAll && div.querySelectorAll(".TEST").length === 0 ) {
		return;
	}

	Sizzle = function(query, context, extra, seed){
		context = context || document;

		if ( !seed && context.nodeType === 9 && !isXML(context) ) {
			try {
				return makeArray( context.querySelectorAll(query), extra );
			} catch(e){}
		}

		return oldSizzle(query, context, extra, seed);
	};

	for ( var prop in oldSizzle ) {
		Sizzle[ prop ] = oldSizzle[ prop ];
	}

	div = null; // release memory in IE
})();

if ( document.getElementsByClassName && document.documentElement.getElementsByClassName ) (function(){
	var div = document.createElement("div");
	div.innerHTML = "<div class='test e'></div><div class='test'></div>";

	if ( div.getElementsByClassName("e").length === 0 )
		return;

	div.lastChild.className = "e";

	if ( div.getElementsByClassName("e").length === 1 )
		return;

	Expr.order.splice(1, 0, "CLASS");
	Expr.find.CLASS = function(match, context, isXML) {
		if ( typeof context.getElementsByClassName !== "undefined" && !isXML ) {
			return context.getElementsByClassName(match[1]);
		}
	};

	div = null; // release memory in IE
})();

function dirNodeCheck( dir, cur, doneName, checkSet, nodeCheck, isXML ) {
	var sibDir = dir == "previousSibling" && !isXML;
	for ( var i = 0, l = checkSet.length; i < l; i++ ) {
		var elem = checkSet[i];
		if ( elem ) {
			if ( sibDir && elem.nodeType === 1 ){
				elem.sizcache = doneName;
				elem.sizset = i;
			}
			elem = elem[dir];
			var match = false;

			while ( elem ) {
				if ( elem.sizcache === doneName ) {
					match = checkSet[elem.sizset];
					break;
				}

				if ( elem.nodeType === 1 && !isXML ){
					elem.sizcache = doneName;
					elem.sizset = i;
				}

				if ( elem.nodeName === cur ) {
					match = elem;
					break;
				}

				elem = elem[dir];
			}

			checkSet[i] = match;
		}
	}
}

function dirCheck( dir, cur, doneName, checkSet, nodeCheck, isXML ) {
	var sibDir = dir == "previousSibling" && !isXML;
	for ( var i = 0, l = checkSet.length; i < l; i++ ) {
		var elem = checkSet[i];
		if ( elem ) {
			if ( sibDir && elem.nodeType === 1 ) {
				elem.sizcache = doneName;
				elem.sizset = i;
			}
			elem = elem[dir];
			var match = false;

			while ( elem ) {
				if ( elem.sizcache === doneName ) {
					match = checkSet[elem.sizset];
					break;
				}

				if ( elem.nodeType === 1 ) {
					if ( !isXML ) {
						elem.sizcache = doneName;
						elem.sizset = i;
					}
					if ( typeof cur !== "string" ) {
						if ( elem === cur ) {
							match = true;
							break;
						}

					} else if ( Sizzle.filter( cur, [elem] ).length > 0 ) {
						match = elem;
						break;
					}
				}

				elem = elem[dir];
			}

			checkSet[i] = match;
		}
	}
}

var contains = document.compareDocumentPosition ?  function(a, b){
	return a.compareDocumentPosition(b) & 16;
} : function(a, b){
	return a !== b && (a.contains ? a.contains(b) : true);
};

var isXML = function(elem){
	return elem.nodeType === 9 && elem.documentElement.nodeName !== "HTML" ||
		!!elem.ownerDocument && elem.ownerDocument.documentElement.nodeName !== "HTML";
};

var posProcess = function(selector, context){
	var tmpSet = [], later = "", match,
		root = context.nodeType ? [context] : context;

	while ( (match = Expr.match.PSEUDO.exec( selector )) ) {
		later += match[0];
		selector = selector.replace( Expr.match.PSEUDO, "" );
	}

	selector = Expr.relative[selector] ? selector + "*" : selector;

	for ( var i = 0, l = root.length; i < l; i++ ) {
		Sizzle( selector, root[i], tmpSet );
	}

	return Sizzle.filter( later, tmpSet );
};


window.Sizzle = Sizzle;

})();

;(function(engine) {
  var extendElements = Prototype.Selector.extendElements;

  function select(selector, scope) {
    return extendElements(engine(selector, scope || document));
  }

  function match(element, selector) {
    return engine.matches(selector, [element]).length == 1;
  }

  Prototype.Selector.engine = engine;
  Prototype.Selector.select = select;
  Prototype.Selector.match = match;
})(Sizzle);

window.Sizzle = Prototype._original_property;
delete Prototype._original_property;

var Form = {
  reset: function(form) {
    form = $(form);
    form.reset();
    return form;
  },

  serializeElements: function(elements, options) {
    if (typeof options != 'object') options = { hash: !!options };
    else if (Object.isUndefined(options.hash)) options.hash = true;
    var key, value, submitted = false, submit = options.submit, accumulator, initial;

    if (options.hash) {
      initial = {};
      accumulator = function(result, key, value) {
        if (key in result) {
          if (!Object.isArray(result[key])) result[key] = [result[key]];
          result[key].push(value);
        } else result[key] = value;
        return result;
      };
    } else {
      initial = '';
      accumulator = function(result, key, value) {
        return result + (result ? '&' : '') + encodeURIComponent(key) + '=' + encodeURIComponent(value);
      }
    }

    return elements.inject(initial, function(result, element) {
      if (!element.disabled && element.name) {
        key = element.name; value = $(element).getValue();
        if (value != null && element.type != 'file' && (element.type != 'submit' || (!submitted &&
            submit !== false && (!submit || key == submit) && (submitted = true)))) {
          result = accumulator(result, key, value);
        }
      }
      return result;
    });
  }
};

Form.Methods = {
  serialize: function(form, options) {
    return Form.serializeElements(Form.getElements(form), options);
  },

  getElements: function(form) {
    var elements = $(form).getElementsByTagName('*'),
        element,
        arr = [ ],
        serializers = Form.Element.Serializers;
    for (var i = 0; element = elements[i]; i++) {
      arr.push(element);
    }
    return arr.inject([], function(elements, child) {
      if (serializers[child.tagName.toLowerCase()])
        elements.push(Element.extend(child));
      return elements;
    })
  },

  getInputs: function(form, typeName, name) {
    form = $(form);
    var inputs = form.getElementsByTagName('input');

    if (!typeName && !name) return $A(inputs).map(Element.extend);

    for (var i = 0, matchingInputs = [], length = inputs.length; i < length; i++) {
      var input = inputs[i];
      if ((typeName && input.type != typeName) || (name && input.name != name))
        continue;
      matchingInputs.push(Element.extend(input));
    }

    return matchingInputs;
  },

  disable: function(form) {
    form = $(form);
    Form.getElements(form).invoke('disable');
    return form;
  },

  enable: function(form) {
    form = $(form);
    Form.getElements(form).invoke('enable');
    return form;
  },

  findFirstElement: function(form) {
    var elements = $(form).getElements().findAll(function(element) {
      return 'hidden' != element.type && !element.disabled;
    });
    var firstByIndex = elements.findAll(function(element) {
      return element.hasAttribute('tabIndex') && element.tabIndex >= 0;
    }).sortBy(function(element) { return element.tabIndex }).first();

    return firstByIndex ? firstByIndex : elements.find(function(element) {
      return /^(?:input|select|textarea)$/i.test(element.tagName);
    });
  },

  focusFirstElement: function(form) {
    form = $(form);
    var element = form.findFirstElement();
    if (element) element.activate();
    return form;
  },

  request: function(form, options) {
    form = $(form), options = Object.clone(options || { });

    var params = options.parameters, action = form.readAttribute('action') || '';
    if (action.blank()) action = window.location.href;
    options.parameters = form.serialize(true);

    if (params) {
      if (Object.isString(params)) params = params.toQueryParams();
      Object.extend(options.parameters, params);
    }

    if (form.hasAttribute('method') && !options.method)
      options.method = form.method;

    return new Ajax.Request(action, options);
  }
};

/*--------------------------------------------------------------------------*/


Form.Element = {
  focus: function(element) {
    $(element).focus();
    return element;
  },

  select: function(element) {
    $(element).select();
    return element;
  }
};

Form.Element.Methods = {

  serialize: function(element) {
    element = $(element);
    if (!element.disabled && element.name) {
      var value = element.getValue();
      if (value != undefined) {
        var pair = { };
        pair[element.name] = value;
        return Object.toQueryString(pair);
      }
    }
    return '';
  },

  getValue: function(element) {
    element = $(element);
    var method = element.tagName.toLowerCase();
    return Form.Element.Serializers[method](element);
  },

  setValue: function(element, value) {
    element = $(element);
    var method = element.tagName.toLowerCase();
    Form.Element.Serializers[method](element, value);
    return element;
  },

  clear: function(element) {
    $(element).value = '';
    return element;
  },

  present: function(element) {
    return $(element).value != '';
  },

  activate: function(element) {
    element = $(element);
    try {
      element.focus();
      if (element.select && (element.tagName.toLowerCase() != 'input' ||
          !(/^(?:button|reset|submit)$/i.test(element.type))))
        element.select();
    } catch (e) { }
    return element;
  },

  disable: function(element) {
    element = $(element);
    element.disabled = true;
    return element;
  },

  enable: function(element) {
    element = $(element);
    element.disabled = false;
    return element;
  }
};

/*--------------------------------------------------------------------------*/

var Field = Form.Element;

var $F = Form.Element.Methods.getValue;

/*--------------------------------------------------------------------------*/

Form.Element.Serializers = (function() {
  function input(element, value) {
    switch (element.type.toLowerCase()) {
      case 'checkbox':
      case 'radio':
        return inputSelector(element, value);
      default:
        return valueSelector(element, value);
    }
  }

  function inputSelector(element, value) {
    if (Object.isUndefined(value))
      return element.checked ? element.value : null;
    else element.checked = !!value;
  }

  function valueSelector(element, value) {
    if (Object.isUndefined(value)) return element.value;
    else element.value = value;
  }

  function select(element, value) {
    if (Object.isUndefined(value))
      return (element.type === 'select-one' ? selectOne : selectMany)(element);

    var opt, currentValue, single = !Object.isArray(value);
    for (var i = 0, length = element.length; i < length; i++) {
      opt = element.options[i];
      currentValue = this.optionValue(opt);
      if (single) {
        if (currentValue == value) {
          opt.selected = true;
          return;
        }
      }
      else opt.selected = value.include(currentValue);
    }
  }

  function selectOne(element) {
    var index = element.selectedIndex;
    return index >= 0 ? optionValue(element.options[index]) : null;
  }

  function selectMany(element) {
    var values, length = element.length;
    if (!length) return null;

    for (var i = 0, values = []; i < length; i++) {
      var opt = element.options[i];
      if (opt.selected) values.push(optionValue(opt));
    }
    return values;
  }

  function optionValue(opt) {
    return Element.hasAttribute(opt, 'value') ? opt.value : opt.text;
  }

  return {
    input:         input,
    inputSelector: inputSelector,
    textarea:      valueSelector,
    select:        select,
    selectOne:     selectOne,
    selectMany:    selectMany,
    optionValue:   optionValue,
    button:        valueSelector
  };
})();

/*--------------------------------------------------------------------------*/


Abstract.TimedObserver = Class.create(PeriodicalExecuter, {
  initialize: function($super, element, frequency, callback) {
    $super(callback, frequency);
    this.element   = $(element);
    this.lastValue = this.getValue();
  },

  execute: function() {
    var value = this.getValue();
    if (Object.isString(this.lastValue) && Object.isString(value) ?
        this.lastValue != value : String(this.lastValue) != String(value)) {
      this.callback(this.element, value);
      this.lastValue = value;
    }
  }
});

Form.Element.Observer = Class.create(Abstract.TimedObserver, {
  getValue: function() {
    return Form.Element.getValue(this.element);
  }
});

Form.Observer = Class.create(Abstract.TimedObserver, {
  getValue: function() {
    return Form.serialize(this.element);
  }
});

/*--------------------------------------------------------------------------*/

Abstract.EventObserver = Class.create({
  initialize: function(element, callback) {
    this.element  = $(element);
    this.callback = callback;

    this.lastValue = this.getValue();
    if (this.element.tagName.toLowerCase() == 'form')
      this.registerFormCallbacks();
    else
      this.registerCallback(this.element);
  },

  onElementEvent: function() {
    var value = this.getValue();
    if (this.lastValue != value) {
      this.callback(this.element, value);
      this.lastValue = value;
    }
  },

  registerFormCallbacks: function() {
    Form.getElements(this.element).each(this.registerCallback, this);
  },

  registerCallback: function(element) {
    if (element.type) {
      switch (element.type.toLowerCase()) {
        case 'checkbox':
        case 'radio':
          Event.observe(element, 'click', this.onElementEvent.bind(this));
          break;
        default:
          Event.observe(element, 'change', this.onElementEvent.bind(this));
          break;
      }
    }
  }
});

Form.Element.EventObserver = Class.create(Abstract.EventObserver, {
  getValue: function() {
    return Form.Element.getValue(this.element);
  }
});

Form.EventObserver = Class.create(Abstract.EventObserver, {
  getValue: function() {
    return Form.serialize(this.element);
  }
});
(function() {

  var Event = {
    KEY_BACKSPACE: 8,
    KEY_TAB:       9,
    KEY_RETURN:   13,
    KEY_ESC:      27,
    KEY_LEFT:     37,
    KEY_UP:       38,
    KEY_RIGHT:    39,
    KEY_DOWN:     40,
    KEY_DELETE:   46,
    KEY_HOME:     36,
    KEY_END:      35,
    KEY_PAGEUP:   33,
    KEY_PAGEDOWN: 34,
    KEY_INSERT:   45,

    cache: {}
  };

  var docEl = document.documentElement;
  var MOUSEENTER_MOUSELEAVE_EVENTS_SUPPORTED = 'onmouseenter' in docEl
    && 'onmouseleave' in docEl;



  var isIELegacyEvent = function(event) { return false; };

  if (window.attachEvent) {
    if (window.addEventListener) {
      isIELegacyEvent = function(event) {
        return !(event instanceof window.Event);
      };
    } else {
      isIELegacyEvent = function(event) { return true; };
    }
  }

  var _isButton;

  function _isButtonForDOMEvents(event, code) {
    return event.which ? (event.which === code + 1) : (event.button === code);
  }

  var legacyButtonMap = { 0: 1, 1: 4, 2: 2 };
  function _isButtonForLegacyEvents(event, code) {
    return event.button === legacyButtonMap[code];
  }

  function _isButtonForWebKit(event, code) {
    switch (code) {
      case 0: return event.which == 1 && !event.metaKey;
      case 1: return event.which == 2 || (event.which == 1 && event.metaKey);
      case 2: return event.which == 3;
      default: return false;
    }
  }

  if (window.attachEvent) {
    if (!window.addEventListener) {
      _isButton = _isButtonForLegacyEvents;
    } else {
      _isButton = function(event, code) {
        return isIELegacyEvent(event) ? _isButtonForLegacyEvents(event, code) :
         _isButtonForDOMEvents(event, code);
      }
    }
  } else if (Prototype.Browser.WebKit) {
    _isButton = _isButtonForWebKit;
  } else {
    _isButton = _isButtonForDOMEvents;
  }

  function isLeftClick(event)   { return _isButton(event, 0) }

  function isMiddleClick(event) { return _isButton(event, 1) }

  function isRightClick(event)  { return _isButton(event, 2) }

  function element(event) {
    event = Event.extend(event);

    var node = event.target, type = event.type,
     currentTarget = event.currentTarget;

    if (currentTarget && currentTarget.tagName) {
      if (type === 'load' || type === 'error' ||
        (type === 'click' && currentTarget.tagName.toLowerCase() === 'input'
          && currentTarget.type === 'radio'))
            node = currentTarget;
    }

    if (node.nodeType == Node.TEXT_NODE)
      node = node.parentNode;

    return Element.extend(node);
  }

  function findElement(event, expression) {
    var element = Event.element(event);

    if (!expression) return element;
    while (element) {
      if (Object.isElement(element) && Prototype.Selector.match(element, expression)) {
        return Element.extend(element);
      }
      element = element.parentNode;
    }
  }

  function pointer(event) {
    return { x: pointerX(event), y: pointerY(event) };
  }

  function pointerX(event) {
    var docElement = document.documentElement,
     body = document.body || { scrollLeft: 0 };

    return event.pageX || (event.clientX +
      (docElement.scrollLeft || body.scrollLeft) -
      (docElement.clientLeft || 0));
  }

  function pointerY(event) {
    var docElement = document.documentElement,
     body = document.body || { scrollTop: 0 };

    return  event.pageY || (event.clientY +
       (docElement.scrollTop || body.scrollTop) -
       (docElement.clientTop || 0));
  }


  function stop(event) {
    Event.extend(event);
    event.preventDefault();
    event.stopPropagation();

    event.stopped = true;
  }


  Event.Methods = {
    isLeftClick:   isLeftClick,
    isMiddleClick: isMiddleClick,
    isRightClick:  isRightClick,

    element:     element,
    findElement: findElement,

    pointer:  pointer,
    pointerX: pointerX,
    pointerY: pointerY,

    stop: stop
  };

  var methods = Object.keys(Event.Methods).inject({ }, function(m, name) {
    m[name] = Event.Methods[name].methodize();
    return m;
  });

  if (window.attachEvent) {
    function _relatedTarget(event) {
      var element;
      switch (event.type) {
        case 'mouseover':
        case 'mouseenter':
          element = event.fromElement;
          break;
        case 'mouseout':
        case 'mouseleave':
          element = event.toElement;
          break;
        default:
          return null;
      }
      return Element.extend(element);
    }

    var additionalMethods = {
      stopPropagation: function() { this.cancelBubble = true },
      preventDefault:  function() { this.returnValue = false },
      inspect: function() { return '[object Event]' }
    };

    Event.extend = function(event, element) {
      if (!event) return false;

      if (!isIELegacyEvent(event)) return event;

      if (event._extendedByPrototype) return event;
      event._extendedByPrototype = Prototype.emptyFunction;

      var pointer = Event.pointer(event);

      Object.extend(event, {
        target: event.srcElement || element,
        relatedTarget: _relatedTarget(event),
        pageX:  pointer.x,
        pageY:  pointer.y
      });

      Object.extend(event, methods);
      Object.extend(event, additionalMethods);

      return event;
    };
  } else {
    Event.extend = Prototype.K;
  }

  if (window.addEventListener) {
    Event.prototype = window.Event.prototype || document.createEvent('HTMLEvents').__proto__;
    Object.extend(Event.prototype, methods);
  }

  function _createResponder(element, eventName, handler) {
    var registry = Element.retrieve(element, 'prototype_event_registry');

    if (Object.isUndefined(registry)) {
      CACHE.push(element);
      registry = Element.retrieve(element, 'prototype_event_registry', $H());
    }

    var respondersForEvent = registry.get(eventName);
    if (Object.isUndefined(respondersForEvent)) {
      respondersForEvent = [];
      registry.set(eventName, respondersForEvent);
    }

    if (respondersForEvent.pluck('handler').include(handler)) return false;

    var responder;
    if (eventName.include(":")) {
      responder = function(event) {
        if (Object.isUndefined(event.eventName))
          return false;

        if (event.eventName !== eventName)
          return false;

        Event.extend(event, element);
        handler.call(element, event);
      };
    } else {
      if (!MOUSEENTER_MOUSELEAVE_EVENTS_SUPPORTED &&
       (eventName === "mouseenter" || eventName === "mouseleave")) {
        if (eventName === "mouseenter" || eventName === "mouseleave") {
          responder = function(event) {
            Event.extend(event, element);

            var parent = event.relatedTarget;
            while (parent && parent !== element) {
              try { parent = parent.parentNode; }
              catch(e) { parent = element; }
            }

            if (parent === element) return;

            handler.call(element, event);
          };
        }
      } else {
        responder = function(event) {
          Event.extend(event, element);
          handler.call(element, event);
        };
      }
    }

    responder.handler = handler;
    respondersForEvent.push(responder);
    return responder;
  }

  function _destroyCache() {
    for (var i = 0, length = CACHE.length; i < length; i++) {
      Event.stopObserving(CACHE[i]);
      CACHE[i] = null;
    }
  }

  var CACHE = [];

  if (Prototype.Browser.IE)
    window.attachEvent('onunload', _destroyCache);

  if (Prototype.Browser.WebKit)
    window.addEventListener('unload', Prototype.emptyFunction, false);


  var _getDOMEventName = Prototype.K,
      translations = { mouseenter: "mouseover", mouseleave: "mouseout" };

  if (!MOUSEENTER_MOUSELEAVE_EVENTS_SUPPORTED) {
    _getDOMEventName = function(eventName) {
      return (translations[eventName] || eventName);
    };
  }

  function observe(element, eventName, handler) {
    element = $(element);

    var responder = _createResponder(element, eventName, handler);

    if (!responder) return element;

    if (eventName.include(':')) {
      if (element.addEventListener)
        element.addEventListener("dataavailable", responder, false);
      else {
        element.attachEvent("ondataavailable", responder);
        element.attachEvent("onlosecapture", responder);
      }
    } else {
      var actualEventName = _getDOMEventName(eventName);

      if (element.addEventListener)
        element.addEventListener(actualEventName, responder, false);
      else
        element.attachEvent("on" + actualEventName, responder);
    }

    return element;
  }

  function stopObserving(element, eventName, handler) {
    element = $(element);

    var registry = Element.retrieve(element, 'prototype_event_registry');
    if (!registry) return element;

    if (!eventName) {
      registry.each( function(pair) {
        var eventName = pair.key;
        stopObserving(element, eventName);
      });
      return element;
    }

    var responders = registry.get(eventName);
    if (!responders) return element;

    if (!handler) {
      responders.each(function(r) {
        stopObserving(element, eventName, r.handler);
      });
      return element;
    }

    var i = responders.length, responder;
    while (i--) {
      if (responders[i].handler === handler) {
        responder = responders[i];
        break;
      }
    }
    if (!responder) return element;

    if (eventName.include(':')) {
      if (element.removeEventListener)
        element.removeEventListener("dataavailable", responder, false);
      else {
        element.detachEvent("ondataavailable", responder);
        element.detachEvent("onlosecapture", responder);
      }
    } else {
      var actualEventName = _getDOMEventName(eventName);
      if (element.removeEventListener)
        element.removeEventListener(actualEventName, responder, false);
      else
        element.detachEvent('on' + actualEventName, responder);
    }

    registry.set(eventName, responders.without(responder));

    return element;
  }

  function fire(element, eventName, memo, bubble) {
    element = $(element);

    if (Object.isUndefined(bubble))
      bubble = true;

    if (element == document && document.createEvent && !element.dispatchEvent)
      element = document.documentElement;

    var event;
    if (document.createEvent) {
      event = document.createEvent('HTMLEvents');
      event.initEvent('dataavailable', bubble, true);
    } else {
      event = document.createEventObject();
      event.eventType = bubble ? 'ondataavailable' : 'onlosecapture';
    }

    event.eventName = eventName;
    event.memo = memo || { };

    if (document.createEvent)
      element.dispatchEvent(event);
    else
      element.fireEvent(event.eventType, event);

    return Event.extend(event);
  }

  Event.Handler = Class.create({
    initialize: function(element, eventName, selector, callback) {
      this.element   = $(element);
      this.eventName = eventName;
      this.selector  = selector;
      this.callback  = callback;
      this.handler   = this.handleEvent.bind(this);
    },

    start: function() {
      Event.observe(this.element, this.eventName, this.handler);
      return this;
    },

    stop: function() {
      Event.stopObserving(this.element, this.eventName, this.handler);
      return this;
    },

    handleEvent: function(event) {
      var element = Event.findElement(event, this.selector);
      if (element) this.callback.call(this.element, event, element);
    }
  });

  function on(element, eventName, selector, callback) {
    element = $(element);
    if (Object.isFunction(selector) && Object.isUndefined(callback)) {
      callback = selector, selector = null;
    }

    return new Event.Handler(element, eventName, selector, callback).start();
  }

  Object.extend(Event, Event.Methods);

  Object.extend(Event, {
    fire:          fire,
    observe:       observe,
    stopObserving: stopObserving,
    on:            on
  });

  Element.addMethods({
    fire:          fire,

    observe:       observe,

    stopObserving: stopObserving,

    on:            on
  });

  Object.extend(document, {
    fire:          fire.methodize(),

    observe:       observe.methodize(),

    stopObserving: stopObserving.methodize(),

    on:            on.methodize(),

    loaded:        false
  });

  if (window.Event) Object.extend(window.Event, Event);
  else window.Event = Event;
})();

(function() {
  /* Support for the DOMContentLoaded event is based on work by Dan Webb,
     Matthias Miller, Dean Edwards, John Resig, and Diego Perini. */

  var timer;

  function fireContentLoadedEvent() {
    if (document.loaded) return;
    if (timer) window.clearTimeout(timer);
    document.loaded = true;
    document.fire('dom:loaded');
  }

  function checkReadyState() {
    if (document.readyState === 'complete') {
      document.stopObserving('readystatechange', checkReadyState);
      fireContentLoadedEvent();
    }
  }

  function pollDoScroll() {
    try { document.documentElement.doScroll('left'); }
    catch(e) {
      timer = pollDoScroll.defer();
      return;
    }
    fireContentLoadedEvent();
  }

  if (document.addEventListener) {
    document.addEventListener('DOMContentLoaded', fireContentLoadedEvent, false);
  } else {
    document.observe('readystatechange', checkReadyState);
    if (window == top)
      timer = pollDoScroll.defer();
  }

  Event.observe(window, 'load', fireContentLoadedEvent);
})();

Element.addMethods();

/*------------------------------- DEPRECATED -------------------------------*/

Hash.toQueryString = Object.toQueryString;

var Toggle = { display: Element.toggle };

Element.Methods.childOf = Element.Methods.descendantOf;

var Insertion = {
  Before: function(element, content) {
    return Element.insert(element, {before:content});
  },

  Top: function(element, content) {
    return Element.insert(element, {top:content});
  },

  Bottom: function(element, content) {
    return Element.insert(element, {bottom:content});
  },

  After: function(element, content) {
    return Element.insert(element, {after:content});
  }
};

var $continue = new Error('"throw $continue" is deprecated, use "return" instead');

var Position = {
  includeScrollOffsets: false,

  prepare: function() {
    this.deltaX =  window.pageXOffset
                || document.documentElement.scrollLeft
                || document.body.scrollLeft
                || 0;
    this.deltaY =  window.pageYOffset
                || document.documentElement.scrollTop
                || document.body.scrollTop
                || 0;
  },

  within: function(element, x, y) {
    if (this.includeScrollOffsets)
      return this.withinIncludingScrolloffsets(element, x, y);
    this.xcomp = x;
    this.ycomp = y;
    this.offset = Element.cumulativeOffset(element);

    return (y >= this.offset[1] &&
            y <  this.offset[1] + element.offsetHeight &&
            x >= this.offset[0] &&
            x <  this.offset[0] + element.offsetWidth);
  },

  withinIncludingScrolloffsets: function(element, x, y) {
    var offsetcache = Element.cumulativeScrollOffset(element);

    this.xcomp = x + offsetcache[0] - this.deltaX;
    this.ycomp = y + offsetcache[1] - this.deltaY;
    this.offset = Element.cumulativeOffset(element);

    return (this.ycomp >= this.offset[1] &&
            this.ycomp <  this.offset[1] + element.offsetHeight &&
            this.xcomp >= this.offset[0] &&
            this.xcomp <  this.offset[0] + element.offsetWidth);
  },

  overlap: function(mode, element) {
    if (!mode) return 0;
    if (mode == 'vertical')
      return ((this.offset[1] + element.offsetHeight) - this.ycomp) /
        element.offsetHeight;
    if (mode == 'horizontal')
      return ((this.offset[0] + element.offsetWidth) - this.xcomp) /
        element.offsetWidth;
  },


  cumulativeOffset: Element.Methods.cumulativeOffset,

  positionedOffset: Element.Methods.positionedOffset,

  absolutize: function(element) {
    Position.prepare();
    return Element.absolutize(element);
  },

  relativize: function(element) {
    Position.prepare();
    return Element.relativize(element);
  },

  realOffset: Element.Methods.cumulativeScrollOffset,

  offsetParent: Element.Methods.getOffsetParent,

  page: Element.Methods.viewportOffset,

  clone: function(source, target, options) {
    options = options || { };
    return Element.clonePosition(target, source, options);
  }
};

/*--------------------------------------------------------------------------*/

if (!document.getElementsByClassName) document.getElementsByClassName = function(instanceMethods){
  function iter(name) {
    return name.blank() ? null : "[contains(concat(' ', @class, ' '), ' " + name + " ')]";
  }

  instanceMethods.getElementsByClassName = Prototype.BrowserFeatures.XPath ?
  function(element, className) {
    className = className.toString().strip();
    var cond = /\s/.test(className) ? $w(className).map(iter).join('') : iter(className);
    return cond ? document._getElementsByXPath('.//*' + cond, element) : [];
  } : function(element, className) {
    className = className.toString().strip();
    var elements = [], classNames = (/\s/.test(className) ? $w(className) : null);
    if (!classNames && !className) return elements;

    var nodes = $(element).getElementsByTagName('*');
    className = ' ' + className + ' ';

    for (var i = 0, child, cn; child = nodes[i]; i++) {
      if (child.className && (cn = ' ' + child.className + ' ') && (cn.include(className) ||
          (classNames && classNames.all(function(name) {
            return !name.toString().blank() && cn.include(' ' + name + ' ');
          }))))
        elements.push(Element.extend(child));
    }
    return elements;
  };

  return function(className, parentElement) {
    return $(parentElement || document.body).getElementsByClassName(className);
  };
}(Element.Methods);

/*--------------------------------------------------------------------------*/

Element.ClassNames = Class.create();
Element.ClassNames.prototype = {
  initialize: function(element) {
    this.element = $(element);
  },

  _each: function(iterator) {
    this.element.className.split(/\s+/).select(function(name) {
      return name.length > 0;
    })._each(iterator);
  },

  set: function(className) {
    this.element.className = className;
  },

  add: function(classNameToAdd) {
    if (this.include(classNameToAdd)) return;
    this.set($A(this).concat(classNameToAdd).join(' '));
  },

  remove: function(classNameToRemove) {
    if (!this.include(classNameToRemove)) return;
    this.set($A(this).without(classNameToRemove).join(' '));
  },

  toString: function() {
    return $A(this).join(' ');
  }
};

Object.extend(Element.ClassNames.prototype, Enumerable);

/*--------------------------------------------------------------------------*/

(function() {
  window.Selector = Class.create({
    initialize: function(expression) {
      this.expression = expression.strip();
    },

    findElements: function(rootElement) {
      return Prototype.Selector.select(this.expression, rootElement);
    },

    match: function(element) {
      return Prototype.Selector.match(element, this.expression);
    },

    toString: function() {
      return this.expression;
    },

    inspect: function() {
      return "#<Selector: " + this.expression + ">";
    }
  });

  Object.extend(Selector, {
    matchElements: function(elements, expression) {
      var match = Prototype.Selector.match,
          results = [];

      for (var i = 0, length = elements.length; i < length; i++) {
        var element = elements[i];
        if (match(element, expression)) {
          results.push(Element.extend(element));
        }
      }
      return results;
    },

    findElement: function(elements, expression, index) {
      index = index || 0;
      var matchIndex = 0, element;
      for (var i = 0, length = elements.length; i < length; i++) {
        element = elements[i];
        if (Prototype.Selector.match(element, expression) && index === matchIndex++) {
          return Element.extend(element);
        }
      }
    },

    findChildElements: function(element, expressions) {
      var selector = expressions.toArray().join(', ');
      return Prototype.Selector.select(selector, element || document);
    }
  });
})();


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

if(typeof Baze != "undefined")
{
	Baze.provide("web.Style");
	
	// Assumindo que o jext est� sempre l�!
	// Baze.require("system.jext");
}

/**
 * @class Style
 * @alias Style
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @param {Object} style
 */
Style = function Style(style) {
	this.realObject = style;
};

Object.extend(Style.prototype, {

	realObject : null,
	
	_owner : null,
	
	/**
	 * @method get
	 * @param {String} prop
	 */
	get : function get(prop) {
		return this.realObject[prop];
	},
	
	/**
	 * @method set
	 * @param {String} name
	 * @param {Object} value
	 */
	set : function set(name, value) {
		if(typeof this.realObject[name] != "undefined")
			var oldValue = this.realObject[name];
		else
			var oldValue = undefined;
		
		if(value != oldValue)
		{
			this.realObject[name] = value;
			if(this._owner)
			{
				var oldCss = this.realElement.style.cssText;
				this._owner.onPropertyChange.raise(this, { changeType : ChangeType.PROPERTY_CHANGED, propertyName : "style", oldValue : oldCss});
			}
		}
	},
	
	/**
	 * @method setOwner
	 * @param {Component} comp
	 * @return {boolean}
	 */
	setOwner : function setOwner(comp)
	{
		if(!Baze.isComponent(comp))
		{
			Baze.raise("Par�metro incorreto", new Error("A fun��o Style.setOwner espera um Component como par�metro mas recebeu um " + (typeof comp)));
			return false;
		}
			
		this._owner = comp;
		return true;
	},
	
	/**
	 * @method getOwner
	 * @return {Component} comp
	 */
	getOwner : function getOwner()
	{		
		return this._owner;
	}
});
if(typeof Baze != "undefined") {	
	Baze.provide("web.Component");
	
	Baze.require("system.Event");
	Baze.require("system.util");
	Baze.require("web.Style");
	// Assumir que j� est� inclu�do
	// Baze.require("system.jext");
}

/**
 * @class Component
 * @alias Component
 * @namespace Baze
 * @author Saulo Vallory
 * 
 * @requires system.jext
 * @requires system.Event
 * @requires system.util
 * @requires web.Style
 * 
 * @constructor
 */
Component = function Component()
{
	this.isComponent = true;
	
	this.onPropertyChange = new Baze.Event();
	
	this.id = uid("cmp_");
};

Object.extend(Component.prototype,  {

	id : "",
	
	onPropertyChange : null,
	
	style : null,
	
	realElement : null,
		
	phpClass : "",

	initialize : function Component_initialize(elem) {
		this.id = elem.id;
		this.style = new Style(elem.style);
	},
	
	get : function Component_get(name) {

		if(typeof this["get"+name.capitalize()] == "function") {
			return this["get"+name.capitalize()](); }
		else {
			if(this[name])
				return this[name];
				
			if(this["realElement"] == null || typeof this["realElement"] == "undefined") {
				return this[name];
			}
			
			return this.realElement[name];
		}
	},
	
	getHTML : function Component_getHTML() {
		
		if(this.realElement.nodeType == DOCUMENT_FRAGMENT_NODE)
		{
			var temp = document.createElement("div");
			
			temp.appendChild(this.realElement);
			
			var html = temp.innerHTML;
			delete temp;
			
			return html;
		}	
		else
			return this.realElement.outerHTML;
	},
	
	/**
	 * @memberOf {Component}
	 * @alias getXML
	 */
	getSyncObj : function Component_getSyncObj()
	{
		var node = this.realElement;
		
		obj = {
			klass: this.phpClass,
			id: this.id,
			properties: [] 
		};
		
		if(node != null && node.attributes)
		{
			for(var i=0; i < node.attributes.length; i++)
			{
				var att = node.attributes[i];
				
				if(att.nodeValue)
				{
					obj.properties.push({n: att.nodeName, v: att.nodeValue});
				}
			}
		}
		
		return obj;
	},

	getId : function Component_getId() {
		return this.id;
	},

	set : function Component_set(name, value) {
	
		// s� � necess�rio fazer o tracking para elementos na p�gina
		if(this["realElement"] == null || typeof this["realElement"] == "undefined") {
			this[name] = value;
			return;
		}
	
		var oldValue = this.get(name);
			
		if(value != oldValue)
		{				
			if(typeof this["set"+name.capitalize()] == "function")
				return this["set"+name.capitalize()](value);
			else
				this.realElement[name] = value;
			
			this.onPropertyChange.raise(this, { changeType : ChangeType.PROPERTY_CHANGED, propertyName : name, oldValue : oldValue});
		}
	},

	/**
	 * @memberOf Component
	 * @param {string} id
	 */
	setId : function Component_setId(id){
		this.id = id;
		
		if(this.realElement)
		{
			this.realElement["id"] = id;
		}
	},
	
	getStyle : function Component_getStyle()
	{
		return this.realElement.style.cssText;
	},
	
	/**
	 * Nota para desenvolvedores do framewor: Essa fun��o N�O deve utilizar a fun��o set do objeto style.
	 * A fun��o Style.set j� checa a exist�ncia do Componente que a cont�m e comunica a altera��o
	 * @memberOf {Component}
	 * @method setStyle
	 * @param {string} txt
	 */
	setStyle : function setStyle(prop, value) {
		if(value == null)
			this.realElement.style.cssText = prop;
		else
		{
			var oldValue = this.realElement.style[prop];
			
			if(value != oldValue)
			{
				var oldCss = this.realElement.style.cssText;
				this.realElement.style[prop] = value;
				this.onPropertyChange.raise(this, { changeType : ChangeType.PROPERTY_CHANGED, propertyName : "style", oldValue : oldCss});
			}
		}
	}
});

/**
 * 
 * @param {String} phpClass
 * @param {HTMLElement} node
 * @return {Component}
 */
Component.factory = function factory(phpClass, node)
{
	if(phpClass.toLowerCase() == "image")
		phpClass = "Picture";
	
	var validClasses = ["body", "button", "dropdownlist", "datepicker", "icon", "menu", "style", "page",
						"checkbox", "form", "hiddenfield", "hyperlink", "formbutton", "formimage", 
						"htmltag", "image", "inputfile", "label", "listbox", "listitem", "literal",
						"optionitem", "panel", "password", "radio", "radiobutton", "radiogroup", 
						"reset", "slider", "span", "submit", "textarea", "textbox", "uidig", "picture", "ulist"];

	if(validClasses.indexOf(phpClass.toLowerCase()) != -1) {
		var __comp = null;
		var classExists = false;
		var __constructor = null;

		eval('classExists = (typeof ' + phpClass + ' == "function");');

		if(!classExists) {
			Baze.raise("constructor for component " + phpClass + " not found. (Component.factory)", new Error());
			return null;
		}

		eval('__constructor = ' + phpClass + ';');

		__comp = new __constructor(node);

		return __comp;
	}
	else
	{
		alert ('O componente ' + phpClass + ' n�o existe!');
		return null;
	}
};
if(typeof Baze != "undefined")
{
	Baze.provide("web.VisualComponent");
	
	Baze.require("web.Component");
}

/**
 * @class VisualComponent
 * @alias VisualComponent
 * @namespace Baze
 * @classDescription A classe VisualComponent define m�todos 
 * inerentes a qualquer componente vis�vel como show, hide e toogle
 * @author Saulo Vallory 
 * @version 0.9
 * 
 * @requires Component
 * 
 * @constructor
 */
VisualComponent = function VisualComponent() {
	this.isVisualComponent = true;
	// chamando o construtor da classe Component
	(Component.bind(this))();
};

Object.extend(VisualComponent.prototype, Component.prototype);
	
Object.extend(VisualComponent.prototype, {
	
	show : function () {
		this.style.set('display','');
	},
	
	hide : function () {
		this.style.set('display','none');
	},
	
	toogle : function () {
		this[this.isVisible() ? 'hide' : 'show']();
	},
	
	isVisible: function() {
	    return this.realElement.style.display != 'none';
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.Container");
	
	Baze.require("web.Component");
	Baze.require("system.commands.Command");
}
/**
 * @class Container
 * @alias Container
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Component
 * @requires Command
 * 
 * @constructor
 */	
Container = function Container() 
{
	this.isContainer = true;
	(Component.bind(this))();
	
	this.onChildAdd = new Baze.Event();
	this.onChildRemove = new Baze.Event();
	this.children = new Collection();
};

Object.extend(Container.prototype, Component);
	
Object.extend(Container.prototype, 
{
	/**
	 * @type {Collection}
	 */
	children : null,
	
	/**
	 * @type {Baze.Event}
	 */
	onChildAdd : null,
	
	/**
	 * @type {Baze.Event}
	 */
	onChildRemove : null,
	
	/**
	 * 
	 * @param {mixed} obj
	 * @param {Object} noRaise
	 */
	addChild : function Container_addChild(obj, noRaise)
	{
		/* Ainda n�o t� implementado
		if(obj instanceof HTMLElement)
		{
			if(document != document.body.ownerDocument)
				document.importNode(obj, true);
			
			if(obj.hasAttribute("phpClass")) {
				var comp = Component.factory(obj.getAttribute("phpClass"), obj); }
			else
			{
				var phpClass = Component.guessType(obj);					
			}
		}
		*/
		
		if(Baze.isComponent(obj))
		{
			var childNode = obj.realElement;
			
			if(obj.realElement.ownerDocument !== document)
			{
				if(typeof document.importNode != "undefined")
					childNode = document.importNode(obj.realElement, true);
			}

			//Adicionando Objeto
			window.damn = this;
			this.children.add(obj.getId(),obj);
			
			
			Baze.addComponent(obj);
			
			try 
			{
				//Adicionando Elemento HTML
				if (childNode.parentNode !== this.realElement)
				{
					this.realElement.appendChild(childNode);
				}
			}
			catch(e) {
				Baze.raise("N�o foi poss�vel adicionar o componente " + obj.getId() + " ao container " + this.id + " ", e);
				return;
			}

			if (noRaise != true) {
				this.onChildAdd.raise(this,{ changeType : ChangeType.CHILD_ADDED, child : obj});
			}
		}
		else if (typeof obj == "string" || typeof obj == "number")
		{
			var lit = new Literal(obj);
			
			//Adicionando Objeto
			this.children.add(lit.getId(), lit);
			
			Baze.addComponent(lit);
			
			//Adicionando Elemento HTML
			this.realElement.appendChild(lit.realElement);
			
			if (noRaise != true)
			{
				this.onChildAdd.raise(this,{ changeType : ChangeType.CHILD_ADDED, child: lit });
			}
		}
	},
			
	/**
	 * @param {Component} obj
	 * @param {boolean} noRaise
	 */
	removeChild : function Panel_removeChild(obj, noRaise)
	{
		if(typeof obj == "string")
			obj = $C(obj);
			
		if(obj == null || !(typeof obj == "object" && Baze.isComponent(obj)))
			return;
		
		if(this.children.get(obj.get("id")) == null)
			return false;
		
		if(obj.constructor === Literal)
		{
			this.children.remove(obj.get("id"));
			
			for(var i=0; i < obj.childNodes.length; i++)
			{
				this.realElement.removeChild(obj.childNodes[i]);
			}
			
			if (noRaise != true)
				this.onChildRemove.raise(this,{ changeType : ChangeType.CHILD_REMOVED, child: obj });
			
			return true;
		}
		else
		{
			this.children.remove(obj.get("id"));
			
			this.realElement.removeChild(obj.realElement);
			
			if (noRaise != true)
				this.onChildRemove.raise(this,{ changeType : ChangeType.CHILD_REMOVED, child: obj });

			return true;
		}

		return false;
	},
	
	/**
	 * @param {HTMLElement} i
	 * @param {boolean} noRaise
	 */
	removeChildByIndex : function Panel_removeChildByIndex (i, noRaise)
	{
		if (i >= 0 && i < this.children.length)
		{
			var aux = this.children[i];
			var auxId = aux.get("id");
			
			this.children.splice(i,i+1);
			aux.realElement = aux.realElement.parentNode.removeChild(aux.realElement);
			
			if (noRaise != true)
			{
				this.onChildRemove.raise(this, {changeType : ChangeType.CHILD_REMOVED, child : aux} );
			}
			
			return true;
		}
		return false;			
	},
	
	removeChildren : function (noRaise) {
		this.children.removeAll();
		this.realElement.innerHTML = '';
	}
});
// Creating commands

Container.CommandEnum = {
	RemoveChildren : 'RemoveContainerChildren' 
};

Baze.registerCommand(new Baze.Command(
{
	id : Container.CommandEnum.RemoveChildren,
		
	name : "RemoveContainerChildren",
	
	/**
	 * 
	 * @param {Container} cont
	 */
	action : function (cont) {
		var comp = null;
		
		if(typeof cont == "string") {
			comp = $C(cont);
		}
		else
			comp = cont;
		
		if(!(typeof comp == "object" && Baze.isContainer(comp)))
		{
			Baze.raise("Erro removendo filhos de um container, container ("+cont+") n�o encontrado.", 
							new Error("Container with id "+cont+" couldn't be found."));
		}
				
		comp.removeChildren(true);
	},

	checkArgumentTypes : false
	//argumentTypes : [Object,"string"]
}));
/**
 * @author saulo
 * @version
 */
if(typeof Baze != "undefined") 
{
	Baze.provide("web.Body");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");
}

/**
 * @class Body
 * @alias Body
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.Container
 * 
 * @param {HTMLElement} elem
 */
Body = function Body(elem)
{
	(VisualComponent.bind(this))();
	(Container.bind(this))();

	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('div');
	}

	this.initialize(elem);
};

Object.extend(Body.prototype, VisualComponent.prototype);
Object.extend(Body.prototype, Container.prototype);	

Object.extend(Body.prototype,
{
	parent : VisualComponent,
	
	parentObject : null,
	
	phpClass : "Body",

	/**
	 * @param {HTMLElement}elem
	 */
	initialize : function initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		if (typeof elem == "undefined" || elem == null)
		{
			Baze.raise("N�o � poss�vel criar um componente Body sem um Body! O par�metro recebido foi " + (typeof elem));
		}
		
		this.realElement = elem;
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.Button");
	
	Baze.require("web.VisualComponent");
}

/**
 * @class Button
 * @alias Button
 * @namespace Baze.web
 * @author Saulo Vallory 
 * @version 0.9
 * 
 * @requires VisualComponent
 * 
 * @constructor
 */
Button = function Button(elem)
{
	(VisualComponent.bind(this))();
	
	if (typeof elem == "undefined")
	{
		var elem = document.createElement('button');
		elem.type = 'button';
	}
	this.initialize(elem);
};

Object.extend(Button.prototype, VisualComponent.prototype);

Object.extend(Button.prototype,
{	
	items : null,

	phpClass : "Button",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function Button_initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		this.realElement = elem;
		this.items = [];
	},
	
	/**
	 * @param {Object} item
	 */
	addItem : function Button_addItem(item, noRaise)
	{
		if (typeof(item) == "object" && item.getAttribute("phpclass") == "Image")
		{
			//Adicionando Objeto
			this.items[this.items.length] = item;
			
			//Adicionando Elemento HTML
			this.realElement.appendChild(item.realElement);
			
			if (typeof(noRaise) !== "undefined" || noRaise == false)
			{
				this.onChildAdd.raise(this,{ changeType : ChangeType.CHILD_ADDED, child : item});
			}
		}
		else if (typeof(item) == "string")
		{
			this.items[this.items.length] = item;
			
			if (typeof(noRaise) !== "undefined" || noRaise == false)
			{
				this.onChildAdd.raise(this,{ changeType : ChangeType.CHILD_ADDED, child: item });
			}
		}
	},
	
	/**
	 * @param {Object} item
	 * @param {boolean} noRaise
	 */
	removeItem : function Button_removeItem(item, noRaise)
	{
		var found = false;
		
		for (var i = 0; (i < this.items.length) && found == false; i++)
		{
			if (this.items[i] == item)
				found == true;
		}
					
		if (found == true)
		{
			return this.removeItemByIndex(i, noRaise);
		}
		
		return false;
	},
	
	/**
	 * @param {HTMLElement} i
	 * @param {boolean} noRaise
	 */
	removeItemByIndex : function Button_removeItemByIndex (i, noRaise)
	{
		if (0<=i && i<this.items.length)
		{
			var aux = this.items[i];
			var auxId = aux.get("id");
			
			this.items.splice(i,i+1);
			aux.realElement.parentNode.removeChild(aux.realElement);
			
			if (typeof(noRaise) == "undefined" || noRaise == false)
			{
				this.onChildRemove.raise(this, {changeType : ChangeType.CHILD_REMOVED, child : aux} );
			}
			
			return true;
		} 
			
		return false;			
	}
});
if(typeof Baze != "undefined") 
{
	Baze.provide("web.HTMLTag");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");
}

/**
 * Class HTMLTag
 * 
 * @author Saulo
 * @version 0.1
 * 
 * @param {HTMLElement} elem
 */
HTMLTag = function HTMLTag(elem)
{
	(VisualComponent.bind(this))();
	(Container.bind(this))();

	if (typeof elem == "undefined" || elem == null) {
		elem = document.createElement('htmltag');
	}
	
	this.initialize(elem);
};	
	
Object.extend(HTMLTag.prototype, VisualComponent.prototype);
Object.extend(HTMLTag.prototype, Container.prototype);

Object.extend(HTMLTag.prototype,
{	
	parentObject : null,
	
	phpClass : "HTMLTag",
			
	tagName : '',
	
	/**
	 * 
	 * @param {HTMLElement} elem
	 */
	intialize : function(elem)
	{
		this.tagName = elem.localName;
		
		this.realElement = elem;
	},
	
	/**
	 * Transforma este elemento no elemento da tag passada
	 *  
	 * ATEN��O: Essa fun��o cria um novo elemento e sobrescreve
	 * o elemento original. Todos os atributos ser�o perdidos
	 * 
	 * @param {String} tag
	 */
	setTagName : function HTMLTag_setTagName(tag) 
	{
		var elem = document.createElement(tag);
		elem.attributes = this.realElement.attributes;
		while(this.realElement.childNodes[0])
		{
			elem.appendChild(this.realElement.childNodes[0]);
		}
		
		if(this.realElement != null)
			this.realElement.parent.replaceChild(elem, this.realElement);
		
		(Container.initialize.bind(this, elem))();
		
		this.initialize(elem);
	},
	
	getTagName : function HTMLTag_setTagName()
	{
		return this.tagName;
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.HyperLink");
	
	Baze.require("web.VisualComponent");	
	Baze.require("web.Container");	
}

/**
 * @class HyperLink
 * @alias HyperLink
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @param {HTMLElement} elem
 */
HyperLink = function HyperLink(elem)
{
	(VisualComponent.bind(this))();
	(Container.bind(this))();
	
	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('a');
	}
	
	this.initialize(elem);
};
		
Object.extend(HyperLink.prototype, VisualComponent.prototype);
Object.extend(HyperLink.prototype, Container.prototype);
	
Object.extend(HyperLink.prototype,
{
	parent : VisualComponent,
	
	phpClass : "HyperLink",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function HyperLink_initialize(elem)
	{
		(Component.prototype.initialize.bind(this, elem))();

		if (typeof elem == 'undefined')
		{
			Baze.raise("Erro criando HyperLink", new Error("Param elem is not defined in HyperLink_initialize"));
		}
		else
		{
			this.realElement = elem;
		}
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.ListItem");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");	
}

/**
 * @class ListItem
 * @alias ListItem
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 */
ListItem = function ListItem(elem) 
{
	(VisualComponent.bind(this))();
	(Container.bind(this))();

	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('li');
	}
	
	this.initialize(elem);
};
	
Object.extend(ListItem.prototype, VisualComponent.prototype);
Object.extend(ListItem.prototype, Container.prototype);	

Object.extend(ListItem.prototype, 
{	
	parent : VisualComponent,
	
	parentObject : null,
	
	phpClass : "ListItem",
	
	/**
	 * @param {HTMLElement} elem
	 * 
	 * @return boolean
	 */
	initialize : function ListItem_initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		if (typeof elem == "undefined" || elem == null)
		{
			var elem = document.createElement('li');
		}
			
		this.realElement = elem;
	},
	
	/**
	 * @return {UList}
	 */
	getParentObject : function ListItem_getParentObject ()
	{
		return this.parentObject;
	},

	/**
	 * @param {UList} uList
	 * @return boolean
	 */		
	setParentObject : function ListItem_setParentObject (uList)
	{
		if (uList.realElement.tagName.toLowerCase() == 'ul' || uList.realElement.tagName.toLowerCase() == 'ol')
		{
			this.parentObject = uList;
			return true;
		}
		
		return false;
	}		
});
if(typeof Baze != "undefined") {
	Baze.provide("web.Literal");
	
	Baze.require("web.Component");
}

/**
 * @class Literal
 * @alias Literal
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @param {HTMLElement} elem
 */
Literal = function Literal(elem)
{
	(Component.bind(this))();
	
	if (typeof elem == "undefined") {
		var txtN;
		
		try {
			this.realElement = document.createDocumentFragment("");
		}
		catch(e) {
			Baze.raise("Text node could not be created.", e);
		}
	}
	else if(typeof elem.nodeType != "undefined") {
		switch(elem.nodeType)
		{
			case DOCUMENT_FRAGMENT_NODE :
				this.realElement = elem;
				
				for(var i=0; i < elem.childNodes.length; i++) {
					this.childNodes[i] = elem.childNodes[i];
				}
				
			break;
		
			case TEXT_NODE :
				this.realElement = this.parseHtml(elem.text || elem.textContent);
				
				for(var i=0; i < this.realElement.childNodes.length; i++) {
					this.childNodes[i] = this.realElement.childNodes[i];
				}
			break;
		}	
	}
	else if(typeof elem == "string" || typeof elem == "number")
	{
		this.realElement = this.parseHtml(elem);
		
		for(var i=0; i < this.realElement.childNodes.length; i++) {
			this.childNodes[i] = this.realElement.childNodes[i];
		}
	}
};
	
Object.extend(Literal.prototype, Component.prototype);

Object.extend(Literal.prototype, {

	childNodes : [],
	
	value : null,
	
	phpClass : "Literal",

	getId : function Literal_getId() { return this.id; },
	
	getValue : function Literal_getValue() { return this.text || this.textContent; },

	get : function Literal_get(name) 
	{
		switch(name.toLowerCase()) 
		{
			case "value" :
				return this.getValue();
				break;
				
			case "id" :
				return this.getId();
				break;
				
			default :

				if(this["realElement"] != null && typeof this["realElement"] != "undefined") {
					if(typeof this.realElement[name] != "undefined" && this.realElement[name] != null)
						return this.realElement[name];
				}

				return this[name];
		}
	},

	set : function Literal_set(name, value) {

		var oldValue = this.get(name);
		
		if(value == oldValue) return;
			
		switch(name.toLowerCase())
		{
			case "value" :
				this.setValue(value);
				break;

			case "id" :
				this.setId(value);
				break;

			default :

				if(this.realElement[name] == null || typeof this.realElement[name] == "undefined")
					this[name] = value;
				else
					this.realElement[name] = value;
		}

		if(this.get(name) != oldValue) {
			this.onPropertyChange.raise(this, { changeType : ChangeType.PROPERTY_CHANGED, propertyName : name, oldValue : oldValue});
		}
	},

	setId : function Literal_setId(id) { this.id = id; },
	
	setValue : function Literal_setValue(val) {
		this.realElement = this.parseHtml(val);
		
		this.childNodes = [];
		
		for(var i=0; i < this.realElement.childNodes.length; i++) {
			this.childNodes[i] = this.realElement.childNodes[i];
		}
	},
	
	/**
	 * 
	 * @param {String} html
	 * @return DocumentFragment
	 */
	parseHtml : function Literal_parseHtml(html)
	{	
		var tempEl = document.createElement("div");
		tempEl.innerHTML = html;
		
		Baze._findServerObjects(tempEl, Baze._serverObjs);
		
		var doc = document.createDocumentFragment();
				
		while(tempEl.hasChildNodes()) {	
		    doc.appendChild(tempEl.childNodes[0]);
		}
		
		return doc;
	}
});
if(typeof Baze != "undefined") 
{
	Baze.provide("web.Panel");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");
}

/**
 * @class Style
 * @alias Style
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @param {HTMLElement} elem
 */
Panel = function Panel(elem)
{
	(VisualComponent.bind(this))();
	(Container.bind(this))();

	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('div');
	}

	this.initialize(elem);
};

Object.extend(Panel.prototype, VisualComponent.prototype);
Object.extend(Panel.prototype, Container.prototype);	

Object.extend(Panel.prototype,
{
	parent : VisualComponent,
	
	parentObject : null,
	
	phpClass : "Panel",

	/**
	 * @param {HTMLElement}elem
	 */
	initialize : function initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		if (typeof elem == "undefined" || elem == null)
		{
			var elem = document.createElement('panel');
		}
		
		this.realElement = elem;
		
	}
});
if(typeof Baze != "undefined") 
{
	Baze.provide("web.Span");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");
}

/**
 * @class Span
 * @alias Span
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
	 * @param {HTMLElement} elem
	 */
Span = function Span(elem)
{
	(VisualComponent.bind(this))();
	(Container.bind(this))();
	
	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('span');
	}

	this.initialize(elem);
};

Object.extend(Span.prototype, VisualComponent.prototype);
Object.extend(Span.prototype, Container.prototype);	

Object.extend(Span.prototype,
{
	parent : VisualComponent,
	
	parentObject : null,
	
	phpClass : "Span",

	/**
	 * @param {HTMLElement}elem
	 */
	initialize : function Span_initialize(elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		if (typeof elem == "undefined" || elem == null)
		{
			var elem = document.createElement('span');
		}
		
		this.realElement = elem;
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.UList");
	
	Baze.require("web.ListItem");
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");
}

/**
 * @class UList
 * @alias UList
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 *
 * @param {HTMLElement} elem
 */
UList = function UList(elem)
{
	(VisualComponent.bind(this))();
	(Container.bind(this))();
	
	if (typeof elem == "undefined")
	{
		var elem = document.createElement('ul');
	}
	this.initialize(elem);
};
	
Object.extend(UList.prototype, VisualComponent.prototype);
Object.extend(UList.prototype, Container.prototype);

Object.extend(UList.prototype, 
{	
	parent : VisualComponent,
	
	parentObject : null,
	
	listItems : null, 
	
	phpClass : "UList",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function UList_initialize(elem) 
	{
		if (elem.tagName.toLowerCase() == 'ul')
		{
			(Component.prototype.initialize.bind(this, elem))();
			
			this.realElement = elem;
			
			this.listItems = [];
			
			var numChildren = elem.childNodes.length;
			
			for (var i=0; i < numChildren; i++)
			{
				if (typeof elem.childNodes[i] == "object")
				{
					if (elem.childNodes[i].nodeName.toLowerCase() == 'li')
					{
						var listItem = Baze.getComponentById(elem.childNodes[i].id);
					
						if (typeof listItem == "undefined" || listItem == null)
						{
							listItem = new ListItem(elem.childNodes[i]);
							Baze.addComponent(listItem);	
						} 
			
						this.listItems[this.listItems.length] = listItem;
						this.addChild(listItem, true);
						listItem.setParentObject(this);
					}
				}					
			}
							
			return true;
		}
		return false;
	},
	
	/**
	 * @classDescription Criando e adicionando um ListItem recebendo um HTMLElement
	 * @param {HTMLElement} elem
	 * @return boolean
	 */
	addItem : function UList_addItem(elem, noRaise)
	{
		if (elem.tagName.toLowerCase() == "li")
		{
			var myListItem = new ListItem(elem);
			
			this.addListItem(myListItem, noRaise);
			return true;
		}
		return false;
	},

	/**
	 * @param {ListItem} listItem
	 */
	addListItem : function UList_addListItem(listItem, noRaise)
	{
		if (listItem.get("tagName").toLowerCase() == "li")
		{
			
			//Adicionando Objeto	
			this.listItems[this.listItems.length] = listItem;
			
			//Adicionando Elemento HTML
			this.realElement.add(listItem.realElement, null);
			
			//Adjustando propriedade "parentObject"
			listItem.setParentObject(this);	
			
			if (noRaise == undefined || noRaise == false)
			{
				this.onChildAdd.raise(this, {changeType : ChangeType.CHILD_ADDED, child : listItem});
			}	
			return true;
		}
		
		return false;
	},

	/** 
	 * @param {HTMLElement} elem
	 * @return int
	 */
	getListItemIndex : function UList_getListItemIndex(elem)
	{
		var numItems = this.listItems.length;
		
		var j = -1;
		
		for (var i = 0; i < numItems && (j == -1); i++)
		{
			if (this.listItems[i].get('id') == elem.id)
			{
				j = i;
			}
		}
		
		return j;
	},
	
	/**
	 * @classDescription Removendo, por �ndice, um ListItem do array "listItems" 
	 * @param {int} i
	 */
	removeListItemByIndex : function UList_removeListItemByIndex(i, noRaise)
	{
		if ( 0 <= i && i<this.listItems.length)
		{
			var aux = this.listItems[i];
			var auxId = aux.get("id");
			
			this.listItems.splice(i,i+1);
							
			this.realElement.removeChild(aux);
			
			if (noRaise == undefined || noRaise == false)
			{
				this.onChildRemove.raise(this, {changeType : ChangeType.CHILD_REMOVED, child : aux} );
			}
			
			return true;
		} 
		
		return false;
	},
	
	/**
	 * @param {HTMLElement} elem
	 * @return {boolean}
	 */
	removeListItemByHTMLElement : function UList_removeListItemByHTMLElement(elem, noRaise)
	{
		if (elem.tagName.toLowerCase() == 'li')
		{
			var i = this.getListItemIndex(elem);
			
			if (i != -1)
			{
				return this.removeListItemByIndex(i, noRaise);					
			}
		}
		return false;
	},
	
	/**
	 * @param {ListItem} listItem
	 */
	removeListItem : function UList_removeListItem(listItem, noRaise)
	{
		return this.removeListItemByHTMLElement(listItem.realElement, noRaise);
	},
	
	/**
	 * @return {Object}
	 */
	getParentObject : function ListItem_getParentObject()
	{
		return this.parentObject;
	},

	/**
	 * @param {Object} obj
	 */		
	setParentObject : function ListItem_setParentObject(obj)
	{
		this.parentObject = obj;
	}		
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.widget.Slider");
	Baze.require("web.VisualComponent");
}

/**
 * @class Slider
 * @alias Slider
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @param {HTMLElement} elem
 */
Slider = function Slider(elem)
{
	(VisualComponent.bind(this))();

	if (typeof elem != "undefined")
	{
		//this.initialize(elem);
	}
};

Object.extend(Slider.prototype, VisualComponent.prototype);

Object.extend(Slider.prototype,
{
	parent : VisualComponent,
	
	leftUp : "",
	
	rightDown : "",
	
	oldXValue : "",
	
	oldYValue : "",
	
	tick : "",

	phpClass : "Slider",
	
	/**
	 * @param {HTMLElement}elem
	 * @return {boolean}
	 */
	initialize : function initialize (elem)
	{
		(VisualComponent.prototype.initialize.bind(this, elem))();

		var sliderbgID = elem.id;
		var sliderthumbID = elem.getElementsByTagName('div')[0].id;
		
		this.oldXValue 	= elem.getAttribute('xvalue');
		this.oldYValue 	= elem.getAttribute('yvalue');
		this.leftUp		= elem.getAttribute('leftUp');
		this.rightDown	= elem.getAttribute('rightDown');
		this.tick		= elem.getAttribute('tick');
		this.locked		= elem.getAttribute('locked');
		
		alert(	"xvalue: " + this.oldXValue + 
				' - yvalue: ' + this.oldYValue + 
				' - leftUp: ' + this.leftUp + 
				' - rightDown: ' + this.rightDown + 
				' - tick: ' + this.tick + 
				' - locked: ' + this.locked );
		alert(sliderbgID + " > " + sliderthumbID);
		
		this.realElement = YAHOO.widget.Slider.getHorizSlider(sliderbgID, sliderthumbID, this.leftUp, this.rightDown, this.tick);
		this.realElement.setValue(this.oldXValue);
		
		if (this.locked == 1)
		{			
			this.realElement.subscribe("change", this.setLock.bind(this));
			this.realElement.setValue(this.oldXValue);
		}
		
		this.realElement.subscribe("change", this.raiseChange.bind(this));
	},
	
	raiseChange : function Slider_raiseChange(offSet)
	{	
		alert ("raiseChange");
		this.onPropertyChange.raise(this, {changeType : ChangeType.PROPERTY_CHANGED, propertyName : "xvalue", oldValue : this.oldXValue});
		this.oldXValue = offSet;
	},
	
	/**
	 * @param {int} offSet
	 */
	setLock : function Slider_setLock(offSet)
	{
		this.oldXValue = offSet;
		
		this.realElement.lock();
	}
});
if(typeof Baze != 'undefined')
{
	Baze.provide("web.image.Image");
	Baze.require("web.Component");
}

/**
 * @class Picture
 * @alias Picture
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @param {HTMLElement} elem
 */
Picture = function Picture(elem)
{
	(VisualComponent.bind(this))();
	
	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('img');
	}

	this.initialize(elem);
};
	
Object.extend(Picture.prototype, VisualComponent.prototype);

Object.extend(Picture.prototype,
{
	parent : VisualComponent,
	
	parentObject : null,
	
	phpClass : "Image",

	/**
	 * @param {HTMLElement}elem
	 */
	initialize : function Image_initialize(elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		if (typeof elem == "undefined" || elem == null)
		{
			var elem = document.createElement('img');
		}
		
		this.realElement = elem;
	}
});
if(typeof Baze != "undefined")
{
	Baze.provide("web.form.FormField");
}

FormField = function(){};

Object.extend(FormField.prototype,  
{
	checkChanges : function TextBox_checkChanges()
	{
		// this function should be overwrited in child class
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.CheckBox");
	
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class CheckBox
 * @alias CheckBox
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
CheckBox = function CheckBox(elem) 
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	
	if (typeof elem == 'undefined' || elem == null)
	{
		var elem = document.createElement("input");
		elem.type = "checkbox";
	}	
	
	this.initialize(elem);
};

Object.extend(CheckBox.prototype, VisualComponent.prototype);
Object.extend(CheckBox.prototype, FormField.prototype);

Object.extend(CheckBox.prototype, 
{
	parent : VisualComponent,
	
	phpClass : "CheckBox",
	
	/**
	 * @private
	 * @type {Boolean} elem
	 */
	_oldCheckedValue : "",
	
	actualCheckedValue : "",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function CheckBox_initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		this.realElement = elem;
		
		this.actualCheckedValue = elem.checked;
		this._oldCheckedValue = elem.checked;
		
		elem.onclick = this._raiseChange.bind(this);
	},
	
	/**
	 * @method _raiseChange
	 * @private
	 * @param {Event} e
	 */
	_raiseChange: function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "checked", oldValue : this._oldCheckedValue});
		
		this._oldCheckedValue = this.actualCheckedValue; 
		this.actualCheckedValue = this.get("checked");
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.FieldSet");	
	
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class FieldSet
 * @alias FieldSet
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
FieldSet = function FieldSet(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == 'undefined' || elem == null)
	{
		var elem = document.createElement('fieldset');
	}
	
	this.initialize(elem);
};
	
Object.extend(FieldSet.prototype, VisualComponent.prototype);
Object.extend(FieldSet.prototype, FormField.prototype);

Object.extend(FieldSet.prototype,
{
	parent : VisualComponent,
	
	legend : null,
	
	items : null,
	
	phpClass : "FieldSet",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function FieldSet_initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		this.realElement = elem;
		
		//Por padr�o, a propriedade "id" do elemento LEGEND � o id do elemento FIELDSET mais uma constante string "Legend" 
		this.legend = document.getElementById(elem.id + "Legend" );

		this.items = [];
	},
	
	/**
	 * @param {Object} obj
	 */
	addItem : function FieldSet_addItem (obj, noRaise)
	{
		//Adicionando Objeto
		this.items[this.items.length] = obj;
		
		//Adicionando Elemento HTML
		this.realElement.appendChild(obj.realElement);
		
		if (noRaise == undefined)
			this.onChildAdd.raise(this,{changeType : ChangeType.CHILD_ADDED, child : obj});
	},
	
	/**
	 * @param {Object} obj
	 * @param {boolean} noRaise
	 */
	removeItem : function FieldSet_removeItem (obj, noRaise)
	{
		j = false;
		
		for (var i = 0; i<this.items.length && j!=false; i++)
		{
			if (this.items[i].get("id") == objectItem.get("id"))
				j = i;
		}
		
		if(j != false)
			return this.removeItemByIndex(j, noRaise);
		
		return false;	
	},
	
	/**
	 * @param {int} i
	 * @param {boolean} noRaise
	 */
	removeItemByIndex : function FieldSet_removeItemByIndex(i, noRaise)
	{
		if (i>0 && i<(this.items.length - 1))
		{
			var aux = this.items[i];
			var auxId = aux.get("id");
			
			//Removendo Objeto
			this.items.splice(i,i+1);
			
			//Removendo Elemento HTML
			this.realElement.removeChild(aux.realElement);
			
			if ( noRaise == undefined || noRaise == false)
				this.onChildRemove.raise( this, {changeType : ChangeType.CHILD_REMOVED, child : aux} ); 
			
			return true;
		}
		return false;
	},
	
	/**
	 * @return {boolean}
	 */
	removeLegend : function FieldSet_removeLegend()
	{
		if (this.legend !== null)
		{
			this.legend.parentNode.removeChild(this.legend);
			this.legend = null;
			
			return true;
		}
		
		return false;
	},
	
	/**
	 * @param {HTMLElement} legend
	 */
	setLegend : function FieldSet_setLegend(legend)
	{
		if (typeof(legend) == "object")
		{
			this.legend = legend;
		}
		else if (typeof(legend) == "string")
		{
			legend = document.getElementById(legend);
		}
		
		this.realElement.appendChild(legend);
	}
	
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.Form");	
	
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");	
	Baze.require("web.form.FormField");
}

/**
 * @class Style
 * @alias Style
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
Form =function Form(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	(Container.bind(this))();		
	
	this.items = [];
	this.modifiedItems = [];
	
	if (typeof elem == 'undefined' || elem == null)
	{
		var elem = document.createElement('form');
	}
	
	this.initialize(elem);
};
	
Object.extend(Form.prototype, VisualComponent.prototype);
Object.extend(Form.prototype, Container.prototype);	
Object.extend(Form.prototype, FormField.prototype);

Object.extend(Form.prototype,
{
	parent : VisualComponent,
	
	items : null,
	
	modifiedItems : null,
	
	phpClass : "Form",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function Form_initialize (elem)
	{
		if (elem.tagName.toLowerCase() == 'form')
		{
			(Component.prototype.initialize.bind(this, elem))();
			this.realElement = elem;
			
			if (window.attachEvent) // IE
			{
				var oldSubmit = this.realElement.onsubmit; // estranhamente, se jogar direto pra this.onChangeListeners n�o funciona no IE
				this.realElement.onsubmit = null;
				
				// no IE o �ltimo evento adicionado USANDO attachEvent � o primeiro 
				// a ser executado. Eventos adicionados pelo html s�o executados 
				// antes dos eventos adicionados por attachEvent
				this.realElement.attachEvent('onsubmit', oldSubmit);
				this.realElement.attachEvent('onsubmit', this.checkFields.bind(this));
			}
			
			return true;
		}
		
		return false;
	},
	
	/**
	 * Check fields for modifications
	 */
	checkFields : function Form_recheckFields()
	{
		var fields = this.realElement.elements;
		
		for(var i=0; i < fields.length; i++)
		{
			var comp = $C(fields[i].id);
			
			if(comp != null)
				comp.checkChanges();
		}
	},
	 
	/**
	 * @param {Object} item
	 */
	addItem : function Form_addItem (item, noRaise)
	{
		//Adicionando Objeto
		this.items[this.optionItems.length] = item;
		
		//Adicionando Element HTML
		this.realElement.appendChild(item.realElement);
		
		if (noRaise == undefined || noRaise == false)
			this.onChildAdd.raise(this, {changeType : ChangeType.CHILD_ADDED, child : item } );
	},
	
	/**
	 * @param {Object} item
	 * @return boolean
	 */
	removeItem : function Form_removeItem (objectItem, noRaise)
	{
		var j = false;
		
		for (var i = 0; i<this.items.length && j!=false; i++)
		{
			if (this.items[i].get("id") == objectItem.get("id"))
				j = i;
		}
		
		if(j != false)
			return this.removeItemByIndex(j, noRaise);
		
		return false;			
	},
	
	/**
	 * @param {int} i
	 * @return boolean
	 */
	removeItemByIndex : function Form_removeItemByIndex (i, noRaise)
	{
		if (i>0 && i<(this.items.length - 1))
		{
			var aux = this.items[i];
			var auxId = aux.get("id");
			
			//Removendo Objeto
			this.items.splice(i,i+1);
			
			//Removendo Elemento HTML
			aux.realElement.parentNode.removeChild(aux.realElement);
			
			if ( noRaise == undefined || noRaise == false )
				this.onChildRemove.raise( this, {changeType : ChangeType.CHILD_REMOVED, child : aux} ); 
			
			return true;
		}
		return false;
	},

	/**
	 * @private
	 * @param {Event} e
	 */	
	_raiseChange : function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, propertyName : "value", oldValue : this.oldValue});
		this.oldValue = this.realElement.value;
	}
});
/**
 * @author Luciano
 */
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.FormButton");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class FormButton
 * @alias FormButton
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
FormButton = function FormButton(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == 'undefined' || elem == null)
	{
		var elem = document.createElement('input');
		elem.type = 'button';
	}
	
	this.initialize(elem);
};
	
Object.extend(FormButton.prototype, VisualComponent.prototype);
Object.extend(FormButton.prototype, FormField.prototype);

Object.extend(FormButton.prototype,
{
	parent : VisualComponent,
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function FormButton_initialize (elem)
	{
		if (elem.tagName.toLowerCase() == "input" && elem.type.toLowerCase() == "button")
		{
			(Component.prototype.initialize.bind(this, elem))();
			this.realElement = elem;
		}
		else
		{
			alert ("Element " + elem.id + " not a FormButtom Type!");
		}
	}
	
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.FormImage");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class FormImage
 * @alias FormImage
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
FormImage = function FormImage(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('input');
		elem.type = 'image';
	}
	
	this.initialize(elem);
};
	
Object.extend(FormImage.prototype, VisualComponent.prototype);
Object.extend(FormImage.prototype, FormField.prototype);

Object.extend(FormImage.prototype,
{
	parent : VisualComponent,
	
	phpClass : "FormImage",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function FormImage_initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		if (typeof elem == 'undefined')
		{
			Baze.raise("Erro criando HyperLink", new Error("Param elem is not defined in HyperLink_initialize"));
		}
		else
		{
			this.realElement = elem;
		}
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.HiddenField");	
	
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class HiddenField
 * @alias HiddenField
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
HiddenField = function HiddenField(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == 'undefined' || elem == null)
	{
		var elem = document.createElement('input');
		elem.type = 'hidden';
	}
	
	this.initialize(elem);
};

Object.extend(HiddenField.prototype, VisualComponent.prototype);
Object.extend(HiddenField.prototype, FormField.prototype);

Object.extend(HiddenField.prototype,
{
	parent : VisualComponent,
	
	oldValue : "",
	
	phpClass : "HiddenField",

	/**
	 * @param {HTMLElement}elem
	 */
	initialize : function HiddenField_initialize (elem)
	{
		if (elem.type.toLowerCase() == "hidden")
		{
			(Component.initialize.bind(this, elem))();
			this.oldValue = elem.value;
			this.realElement = elem;
			
			Event.observe(elem, "change", this._raiseChange.bind(this));
			
			return true;
		}
		
		return false; 
	},

	/**
	 * @param {Event} e
	 */
	_raiseChange : function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "value", oldValue : this.oldValue});
		this.oldValue = this.realElement.value;
	}
});
/**
 * @author Luciano
 */
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.InputFile");	
	
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class InputFile
 * @alias InputFile
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
InputFile = function InputFile(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == 'undefined')
	{
		var elem  = document.createElement('input');
		elem.type = 'file';
	}
	
	this.initialize(elem);
};

Object.extend(InputFile.prototype, VisualComponent.prototype);
Object.extend(InputFile.prototype, FormField.prototype);

Object.extend(InputFile.prototype,
{	
	phpClass : "FileUpload",
	
	/**
	 * 
	 * @param {HTMLElement} elem
	 */
	initialize : function InputFile_initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		this.realElement = elem;
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.Label");
	
	Baze.require("web.VisualComponent");	
	Baze.require("web.Container");	
	Baze.require("web.form.FormField");
}

Label = function Label(elem)
{
	(FormField.bind(this))();
	(VisualComponent.bind(this))();
	(Container.bind(this))();
	
	if (typeof elem == "undefined")
	{
		var elem = document.createElement('label');
	}
	
	this.initialize(elem);
};
	
Object.extend(Label.prototype, VisualComponent.prototype);
Object.extend(Label.prototype, Container.prototype);
Object.extend(Label.prototype, FormField.prototype);
	
Object.extend(Label.prototype,
{
	parent : VisualComponent,
	
	phpClass : "Label",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function Label_initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		if (typeof elem == "undefined" || elem == null)
		{
			Baze.raise("Error in initialize Label component.", new Error("Param elem is not defined in Label__initialize."));
		}
		else
		{	
			this.realElement = elem;
		}
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.OptionItem");
	
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");	
	Baze.require("web.form.FormField");
}

/**
 * @class OptionItem
 * @alias OptionItem
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
OptionItem = function OptionItem(elem) 
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	(Container.bind(this))();		
	
	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('option');
	}
	this.initialize(elem);
};
	
Object.extend(OptionItem.prototype, VisualComponent.prototype);
Object.extend(OptionItem.prototype, Container.prototype);	
Object.extend(OptionItem.prototype, FormField.prototype);

Object.extend(OptionItem.prototype, 
{
	parent : VisualComponent,
	
	parentObject : null,
	
	phpClass : "OptionItem",
	
	oldSelectedValue : null,
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function OptionItem_initialize(elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		if (typeof elem == "undefined" || elem == null)
		{
			var elem = document.createElement('option');
		}
		this.realElement = elem;
				
		this.oldSelectedValue = this.realElement.selected;
		
		elem.onchange = this._raiseChange.bind(this);
	},
	
	getParentObject : function OptionItem_getParentObject()
	{
		return this.parentObject;
	},

	/**
	 * @param {Object} obj
	 */		
	setParentObject : function OptionItem_setParentObject(obj)
	{
		this.parentObject = obj;
	},
	
	/**
	 * @return boolean
	 */
	isSelected : function OptionItem_isSelected()
	{
		return (this.realElement.selected === true);
	},
	
	/**
	 * @param {Boolean} trueOrFalse
	 *
	setSelected : function OptionItem_setSelected(trueOrFalse, noRaise)
	{
		this.realElement.selected = trueOrFalse;
		this.set('selected',trueOrFalse);
	},*/
	
	/**
	 * @param string
	 */
	setText : function OptionItem_setText(textValue)
	{
		this.removeChildren();
		
		this.addChild(textValue, true);
		
		this.realElement.innerHTML = textValue;
	},
	
	/**
	 * @param {Event} e
	 */
	_raiseChange: function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "selected", oldValue : this.oldSelectedValue});
		this.oldSelectedValue = this.realElement.selected;
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.DropDownList");
	
	Baze.require("web.form.OptionItem");
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");
	Baze.require("web.form.FormField");
}

/**
 * @class DropDownList
 * @alias DropDownList
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.Container
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * @requires Baze.web.form.OptionItem
 * 
 * @param {HTMLElement} elem
 */
DropDownList = function DropDownList(elem) 
{
	(VisualComponent.bind(this))();
	(Container.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == "undefined")
		elem = document.createElement("select");
	
	this.initialize(elem);
	this.options = this.children;
};

Object.extend(DropDownList.prototype, VisualComponent.prototype);
Object.extend(DropDownList.prototype, FormField.prototype);
Object.extend(DropDownList.prototype, Container.prototype);
	
Object.extend(DropDownList.prototype, 
{	
	parent : VisualComponent,
	
	options : null,
	
	oldSelectedIndex : null,
	
	phpClass : "DropDownList",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function DropDownList_initialize(elem) 
	{
		if(typeof elem["tagName"] == "undefined" || elem.tagName.toLowerCase() != 'select')
		{
			console.error("DropDownList::addOption :> First parameter should be an HTMLSelectElement");
			return false;
		}
		
		(Component.prototype.initialize.bind(this, elem))();
		
		this.realElement = elem;

		// carregando os filhos
		for(var i=0; i < elem.options.length; i++)
		{
			var op = Baze.getComponentById(elem.options[i].id);
			
			if (op == null)
			{
				op = new OptionItem(elem.options[i]);
				Baze.addComponent(op);
			} 
			
			this.addChild(op, true);
			op.setParentObject(this);
		}
			
		//definindo valores iniciais
		this.oldSelectedIndex = elem.selectedIndex;
		
		var oldOnChange = this.realElement.onchange; // estranhamente, se jogar direto pra this.onChangeListeners n�o funciona no IE
		this.realElement.onchange = null;
		
		if (window.addEventListener) // Mozilla like
		{
			if(oldOnChange)
				this.onChangeListeners = oldOnChange;
				
			this.realElement.addEventListener('change', this._raiseChange.bind(this),false);
		}
		else if (window.attachEvent) // IE
		{				
			if(oldOnChange)
				this.onChangeListeners = oldOnChange;
				
			this.realElement.attachEvent('onchange', this._raiseChange.bind(this));
		}
		
		return true;
	},
	
	/**
	 * @param {OptionItem, HTMLElement} opt
	 * @return boolean
	 */
	addOption : function DropDownList_addOptionItem (opt, noRaise)
	{
		if(!opt || typeof opt != "object" || (Baze.isComponent(opt) && opt.get("phpClass") != "OptionItem"))
		{
			console.error("DropDownList::addOption :> First parameter should be an HTMLOptionElement or an OptionItem");
			return false;
		}
		
		if(typeof opt["tagName"] != "undefined")
		{
			if(opt.tagName.toLowerCase() != "option")
			{
				console.error("DropDownList::addOption :> First parameter should be an HTMLOptionElement or an OptionItem");
				return false;
			}

			if(!opt.id)
			{
				console.error("DropDownList::select :> It's impossible to find the component for an element without id");
				return false;
			}

			var comp = Baze.getComponentById(opt.id);
			
			if(!comp)
			{
				console.warn("DropDownList::select :> Component for the element " + opt.id + " could not be found. Creating a new one.");
				opt = new OptionItem(opt);
			}
			else
				opt = comp;
		}
					
		//Adicionando Objeto	
		this.options.add(this.options.count(),opt);
		
		//Adicionando Elemento HTML
		if(Baze.environment.browser.name == "IE")
			this.realElement.add(opt.realElement);
		else
			this.realElement.add(opt.realElement, null);
		
		//Adjustando propriedade "parentObject"
		opt.setParentObject(this);
		
		if (noRaise == undefined || noRaise == false)
		{
			this.onChildAdd.raise(this, {changeType : ChangeType.CHILD_ADDED, child : opt});
		}
		
		return true;
	},

	/**
	 * @param {int} i
	 */
	getOption : function DropDownList_getOption(i)
	{
		return this.options.get(i);
	},
	
	getSelectedOption : function DropDownList_getSelectedOption()
	{
		var index = this.realElement.selectedIndex;
		
		if(index < 0) return null;
		
		return this.options.get(this.realElement.selectedIndex);
	},
	
	/**
	 * Remove uma opc�o por �ndice, pelo Elemento HTML ou pelo pr�prio objeto
	 * @param {int,HTMLElement,OptionItem} obj
	 * @return OptionItem
	 */
	removeOption : function DropDownList_removeOption(elem, noRaise)
	{
		var index = -1;
		
		if(typeof elem == "number") {
			index = elem;
		}
		else if(typeof obj == "object" && Baze.isComponent(obj)) {
			index = elem.get("index");
		}
		else if(typeof elem.tagName == "undefined" && elem.tagName.toLowerCase() == 'option')	{
			index = elem.index;
		}
		
		if(index < 0 || index > this.options.count())
			return null;

		this.realElement.remove(index);
		var opt = this.options.remove(index);
		
		if (noRaise == undefined || noRaise == false)
		{
			this.onChildRemove.raise(this, {changeType : ChangeType.CHILD_REMOVED, child : opt} );
		}
			
		return opt;
	},
	
	/**
	 * @param {OptionItem,int} opt
	 * @return {boolean}
	 */
	select : function DropDownList_select(opt)
	{
		if(typeof opt == "number")
		{
			if(opt < 0) {
				console.warn("DropDownList::select :> The index should positive");
				return false;
			}
			if(opt > this.options.count()) {
				console.warn("DropDownList::select :> Index out of bounds");
				return false;
			}

			opt = this.options.get(opt);
		}
		else if(typeof opt != "object") {
			return false;
		}		
		else if(typeof opt["tagName"] != "undefined" && opt.tagName.toLowerCase() == "option")
		{
			if(!opt.id)
			{
				console.error("DropDownList::select :> It's impossible to find the component for an element without id");
				return false;
			}
				
			var comp = Baze.getComponentById(opt.id);
			
			if(!comp)
			{
				console.warn("DropDownList::select :> Component for the element " + opt.id + " could not be found. Creating a new one.");
				opt = new OptionItem(opt);
			}
			else
				opt = comp;
		}
		
		this.oldSelectedIndex = this.realElement.selectedIndex;
		this.realElement.selectedIndex = opt.get("index");
		
		this.onPropertyChange.raise(this, {changeType : ChangeType.PROPERTY_CHANGED, propertyName : "selectedIndex", oldValue : this.oldSelectedIndex} );
		
		return true;
	},
	
	/**
	 * @param {Event} e
	 * @private
	 */
	_raiseChange: function _raiseChange(e)
	{	
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "selectedIndex", oldValue : this.oldSelectedIndex} );
	
		this.oldSelectedIndex = this.realElement.selectedIndex;
		
		if(this.onChangeListeners)
			this.onChangeListeners(e);
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.ListBox");
	
	Baze.require("web.form.OptionItem");
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");
	Baze.require("web.form.FormField");
}

/**
 * @class ListBox
 * @alias ListBox
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
ListBox = function ListBox(elem) 
{
	(VisualComponent.bind(this))();
	(Container.bind(this))();
	(FormField.bind(this))();
	
	if ( (typeof elem == "undefined") || elem == null)
	{
		var elem = document.createElement('select');
	}
	
	this.initialize(elem);
};

Object.extend(ListBox.prototype, VisualComponent.prototype);
Object.extend(ListBox.prototype, FormField.prototype);
Object.extend(ListBox.prototype, Container.prototype);	

Object.extend(ListBox.prototype, 
{	
	parent : VisualComponent,
	
	isMultiple : null,
	
	optionItems : [],
	oldSelectedOptionItems : [],
	selectedOptionItems : null,
	
	oldSelectedIndex : null,
	selectedIndex : null,
	
	phpClass : "ListBox",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function ListBox_initialize (elem)
	{
		if ( (elem != null)  &&  elem.tagName.toUpperCase() == 'SELECT')
		{
			// construtor da classe pai 
			(Component.prototype.initialize.bind(this, elem))();
			
			this.realElement = elem;
			
			// instanciando arrays de op��es e �ndices
			this.optionItems = [];
			
			if( elem.multiple )
			{
				this.oldSelectedOptionItems = [];
				this.selectedOptionItems = [];
				
				this.oldSelectedIndex = [];
				this.selectedIndex = [];
			}
			else
			{
				this.oldSelectedOptionItems = null;
				this.selectedOptionItems = elem.selectedIndex > -1 ? elem.options[elem.selectedIndex] : null;
				
				this.oldSelectedIndex = null;
				this.selectedIndex = elem.selectedIndex;
			}
			
			this.isMultiple = elem.multiple;

			// instanciando as op��es do select
			for (var i=0; i < elem.options.length; i++)
			{
				// verifica se o componente j� foi instanciado
				var op = Baze.getComponentById(elem.options[i].id);
				
				// se n�o foi, cria e adiciona
				if (typeof op == "undefined" || op == null)
				{
					op = new OptionItem(elem.options[i]);
					Baze.addComponent(op);
				} 
				
				// adiciono o filho e a referencia para o pai					
				this.optionItems[this.optionItems.length] = op;					
				op.setParentObject(this);					
				
				// pegando o array de itens selecionados, caso o valor seja "multiple"
				if (this.isMultiple)
				{
					if (op.realElement.selected == true)
					{
						this.selectedIndex.push(i);
						this.selectedOptionItems.push(elem.options[i]);
					}						
				}
			}
/*
			if ((this.selectedOptionItems == null || this.selectedOptionItems.length == 0) 
					&& this.optionItems.length > 0 )
			{
				this.setSelectedOption(this.optionItems[0]);
			}
*/

			var oldOnChange = this.realElement.onchange; // estranhamente, se jogar direto pra this.onChangeListeners n�o funciona no IE
			this.realElement.onchange = null;
			
			if (window.addEventListener) // Mozilla like
			{
				if(oldOnChange)
					this.onChangeListeners = oldOnChange;
					
				this.realElement.addEventListener('change', this._raiseChange.bind(this),false);
			}
			else if (window.attachEvent) // IE
			{				
				if(oldOnChange)
					this.onChangeListeners = oldOnChange;
					
				this.realElement.attachEvent('onchange', this._raiseChange.bind(this));
			}
			
			return true;
		}
		return false;
	},

	/**
	 * @param {HTMLElement} elem
	 * @param {boolean} noRaise
	 * 
	 * @return {boolean}
	 */		
	addOption : function ListBox_addOption (elem, noRaise)
	{
		if (elem.tagName.toLowerCase() == "option")
		{
			var op = new OptionItem(elem);
			
			return this.addOptionItem(op, noRaise);
		}

		return false;
	},

	/**
	 * @classDescription Adicionando um novo OptionItem
	 * @param {OptionItem} op
	 * @param {boolean} noRaise
	 */
	addOptionItem : function ListBox_addOptionItem (op, noRaise)
	{
		if (op.get("tagName") == "OPTION")
		{
			if (op.get("selected"))
				this.setSelectedOption(op);
		
			//Adicionando Objeto
			this.optionItems[this.optionItems.length] = op;
			
			//Adicionando Elemento HTML
			this.realElement.add(op.realElement, null);
			
			//Setando propriedade "parentObject"
			op.setParentObject(this);
			
			if (typeof(noRaise) == "undefined" || noRaise == false)
				this.onChildAdd.raise(this, {changeType : Change.CHILD_ADDED, child : op} );
			
			return true;	
		}
		return false;
	},
	
	changeSelected : function ListBox_changeSelected ()
	{
		if(!this.isMultiple)
		{
			this.oldSelectedIndex = this.selectedIndex;
			this.selectedIndex = this.realElement.selectedIndex;

			if(this.selectedIndex != -1)
			{
				this.oldSelectedOptionItems = this.selectedOptionItems;
				this.selectedOptionItems = this.realElement.options[this.selectedIndex];
			}
		}
		else
		{
			var newSelected = [];
			var newSelectedIndex = [];

			for (var i = 0; i < this.realElement.options.length; i++) {
				if (this.realElement.options[i].selected == true) {
					newSelected.push(this.optionItems[i]);
					newSelectedIndex.push(i);
				}
			}
			
			this.oldSelectedIndex = this.selectedIndex;
			this.selectedIndex = newSelectedIndex;

			this.oldSelectedOptionItems = this.selectedOptionItems;
			this.selectedOptionItems = newSelected;
		}
	},

	/**
	 * @return {[int] | [array]}
	 */
	getOldValue : function ListBox_getOldValue ()
	{
		if (this.isMultiple == false)
		{
			return this.oldSelectedIndex;
		}
		
		var arraySelInd = [];
		
		for (var i = 0; i < this.oldSelectedOptionItems.length; i++)
		{
			arraySelInd[i] = this.oldSelectedOptionItems[i].get("index");
		}
		
		return arraySelInd;
	},
	
	getSelectedIndex : function ListBox_getSelectedIndex()
	{
		return this.realElement.selectedIndex;
	},
	
	/**
	 * @param {OptionItem} op
	 * @return {boolean}
	 */
	isChild : function ListBox_isChild (op)
	{
		var found = false;
		
		for (var i = 0; (i < this.optionItems.length) && found == false; i++)
		{
			if (this.optionItems[i].get("id") == op.get("id"))
				found = true;
		}
		
		return found;
	},

	/**
	 * @classDescription Se o objeto estiver selecionado ent�o retorna o seu indice no array de elementos selecionados
	 * @param {OptionItem} op
	 * @return {boolean}
	 */
	isSelected : function ListBox_isSelected (op)
	{
		var indexSelected = -1;
		
		if (this.isMultiple)
		{
			
			for (var i=0; (i < this.selectedOptionItems.length) && (indexSelected == -1); i++)
			{
				if (this.selectedOptionItems[i].get("id") == op.get("id"))
					indexSelected = i;
			}
		}
		else
		{
			if (this.isChild(op) && this.selectedIndex == op.get("index"))
				indexSelected = op.get("index");
		}
		
		return indexSelected;
	},

	/**
	 * @classDescription Removendo, por �ndice, um OptionItem do array "optionItems"
	 * @param {int} i
	 * @return {boolean}
	 */
	removeOptionByIndex : function ListBox_removeOptionByIndex (i, noRaise)
	{
		if (0<=i && i<this.optionItems.length)
		{
			var aux = this.optionItems[i];
			var auxId = aux.get("id");
			
			//Removendo Objeto
			this.optionItems.splice(i,i+1);
			
			if (aux.get("selected"))
				this.setUnselectedOption(aux);
			
			//Removendo Elemento HTML
			this.realElement.remove(i);
			
			if (noRaise == undefined || noRaise == false)
				this.onChildRemove.raise(this, {changeType : Change.CHILD_REMOVED, child : aux});
			
			return true;
		} 
		return false;
	},

	
	/**
	 * @classDescription Removendo, por OptionItem, um OptionItem do array "optionItems"
	 * @param {OptionItem} op
	 * @return {boolean}
	 */
	removeOptionItem : function ListBox_removeOptionItem (op, noRaise)
	{
		if (op.get("tagName") == 'OPTION')
		{
			var ind = this.isSelected(op);
			 
			if (ind != -1)
				this.setUnselectByIndex(ind);
			
			return this.removeByIndex(op.realElement.get("index"), noRaise);
		}
		return false;
	},
	
	/**
	 * @param {OptionItem} op
	 * @param {array} args
	 * @return {boolean}
	 */
	setSelectedOption : function ListBox_setSelectedOption(op)
	{
		if (op.get("tagName").toLowerCase() == "option")
		{
			op.set("selected",true);
			
			if (this.isMultiple == true)
			{
				this.oldSelectedOptionItems = this.selectedOptionsItems;
				this.selectedOptionsItems[this.selectedOptionsItems.length] = op;
			}
			else
			{
				this.oldSelectedIndex = this.selectedIndex;
				this.selectedIndex = op.get("index");
			}
			
			return true;
		}
		return false;
	},

	
	/**
	 * @param {OptionItem} op
	 * @return {boolean}
	 */
	setUnselect : function ListBox_setUnselect(op)
	{			
		if (op.get("tagName").toLowerCase() == "option")
		{
			op.set("selected",false);
			
			if (this.isMultiple == true)
			{
				var ind = this.isSelected(op);
				
				if (ind != -1)
				{
					this.oldSelectedOptionItems = this.SelectedOptionsItems;
					this.selectedOptionsItems.splice(ind,1);
				}
			}
			else
			{
				this.oldSelectedIndex = this.selectedIndex;
				this.selectedIndex = this.get("selectedIndex");
			}
			
			return true;
		}
		return false;
	},
	
	checkChanges : function TextBox_checkChanges()
	{
		if(this.oldSelectedIndex !== this.realElement.selectedIndex)
		{
			this.changeSelected();
		
			this.onPropertyChange.raise(this, {changeType : ChangeType.PROPERTY_CHANGED, propertyName : "selectedIndex", oldValue : this.oldSelectedIndex} );
		}
	},
	
	/**
	 * @param {Event} e
	 */
	_raiseChange : function ListBox_raiseChange(e)
	{
		this.changeSelected();
		
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "selectedIndex", oldValue : this.oldSelectedIndex} );
		
		if(this.onChangeListeners)
			this.onChangeListeners(e);
		
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.Password");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class Password
 * @alias Password
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
Password = function Password(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('input');
		elem.type = 'password'; 
	}
	
	this.initialize(elem);
};
	
Object.extend(Password.prototype, VisualComponent.prototype);
Object.extend(Password.prototype, FormField.prototype);

Object.extend(Password.prototype,
{
	parent : VisualComponent,
	
	oldValue : "",
	
	phpClass : "PasswordField",
	
	/**
	 * @param {HTMLElement} elem
	 * @return {boolean}
	 */
	initialize : function (elem)
	{
		if (elem.tagName.toLowerCase() == "input" && elem.type.toLowerCase() == "password")
		{
			(Component.prototype.initialize.bind(this, elem))();
			
			this.oldValue = elem.value;
			this.realElement = elem;
			
			elem.onchange = this._raiseChange.bind(this);
			
			return true;
		}
		return false;
	},
	
	/**
	 * @private
	 * @param {Event} e
	 */
	_raiseChange: function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "value", oldValue : this.oldValue});
		this.oldValue = this.get("value");
	}
});
if(typeof Baze !== 'undefined')
{
	Baze.provide("web.form.RadioGroup");

	Baze.require("web.Component");
	Baze.require("web.form.FormField");
	Baze.require("web.Container");
	
	// Note: Possui uma depend�ncia circular com RadioButton
}

/**
 * @class RadioGroup
 * @alias RadioGroup
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * @requires Baze.web.Container
 * @requires Baze.web.form.Radio
 */
RadioGroup = function RadioGroup()
{
	(Component.bind(this))();
	(Container.bind(this))();		
	(FormField.bind(this))();		
	
	this.radios = [];		
};

Object.extend(RadioGroup.prototype, Component.prototype);
Object.extend(RadioGroup.prototype, FormField.prototype);	
Object.extend(RadioGroup.prototype, Container.prototype);


Object.extend(RadioGroup.prototype,
{
	radios : null,
	
	groupName : null,
	
	oldCheckedRadio : null,
	
	phpClass : "RadioGroup",
	
	/**
	 * @param {Array} radios
	 */
	initialize : function RadioGroup_initialize (groupName)
	{
//		(Component.prototype.initialize.bind(this))();
		
		this.setGroupName(groupName);
		
		//Chamada abaixo comentada, pois cada radio � respons�vel em se inscrever em seu RadioGroup 
		//this.findRadios(groupName);	
	},
	
	/**
	 * @classDescription Adiciona novo membro ao grupo. O flag "forceChangeName" � boleano, mudar� a propriedade "name" do Radio recebido
	 *  
	 * @param {Radio} rb
	 * @param {boolean} forceChangeName
	 * @return {boolean}
	 */
	addRadio : function RadioGroup_addRadio (rb, forceChangeName, noRaise)
	{
		//Somente objeto Radio com o mesmo valor na propriedade "name" podem ser adicionado ao RadioGroup.
		//Caso necessite, o par�metro "forceChangeName" altera a propriedade "name" do elemento HTML
		if ( (forceChangeName == null || forceChangeName == 0) && rb.get("name") != this.groupName)
			return false;
		
		rb.set("name", this.groupName);
		
		//Se Radio estiver marcado, atualizar valor antigo e atual
		if (rb.get("checked"))
		{	
			this.oldCheckedRadio = rb;
		}
		
		//Adicionando Objeto Radio
		this.radios[this.radios.length] = rb;

		//Setando o grupo no Objeto Radio			
		rb.setRadioGroup(this);
		
		
		if (noRaise == undefined || noRaise == false)
		{
			this.onChildAdd.raise(this, {changeType : ChangeType.CHILD_ADDED, child : rb});
		}
		
		return true;
	},
	
	
	/**
	 * @classDescription Percorre todo o documento buscando os 'radios' que cont�m o nome da propriedade "groupName" 
	 * @param {String} groupName
	 */
	findRadios : function RadioGroup_findRadios (groupName)
	{
		this.radios.splice(0);

		if (groupName == undefined)			
			var radios = document.getElementsByName(this.groupName);
		else
		{
			this.setGroupName(groupName);
			var radios = document.getElementsByName(groupName);
		}
		
		var numRadios  = radios.length;
		
		for (var i=0; i < numRadios; i++)
		{
			var rb = Baze.getComponentById(radios[i].id);
			
			if (typeof(rb) !== "object")
			{
				var rb = new RadioButton(radios[i]);
				Baze.addComponent(rb);
			}
			this.addRadio(rb,0,true);
		}
	},
	
	/**
	 * @classDescription Um dos 'radios' sofreu um evento de altera��o, geralmente um "onclick" ("onchange" por ter recebido um "onclick").
	 * O elemnto avisa ao seu RadioGroup que mudou. O RadioGroup chama o 'raiseChange' do elemento "elementChecked",
	 * chama o 'raiseChange' do elemento que mudou e guarda este novo elemento em "elementChecked"
	 *  
	 * @param {Event} e
	 * @param {Radio} rb
	 * @return {boolean}
	 */
	_raiseChange : function RadioGroup_raiseChange (rb, e)
	{
		if (rb == this.oldCheckedRadio)
		{
			return false;
		}

		this.oldCheckedRadio.forceRaiseChange(e);
		
		this.onPropertyChange.raise(this.oldCheckedRadio, {event : e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "checked", oldValue : this.oldCheckedRadio.get("id") });
		
		this.oldCheckedRadio = rb;
		
		return true;
	},

	/**
	 * @return {string}
	 */		
	getOldValue : function RadioGroup_getOldValue ()
	{
		return this.oldCheckedRadio.get("value");
	},
	
	/**
	 * @param {Radio} r
	 * @return {boolean}
	 */
	removeRadio : function RadioGroup_removeRadio (r, noRaise)
	{
		var found = false;

		for (var i = 0; i<this.radios.length && found == false; i++)
		{
			if (this.radios[i].get("id") == r.get("id"))
				found == true;
		}
		
		if (found == true)
		{				
			var aux = this.radios[i];
			var auxId = aux.get("id");

			//Removendo Objeto				
			this.radios.splice(i,i+1);
			
			//Removendo Elemento HTML
			aux.realElement.parentNode.removeChild(aux.realElement);
			
			if (noRaise == undefined || noRaise == false)
			{
				this.onChildRemove.raise(this, {changeType : ChangeType.CHILD_REMOVED, child : aux} );
			}
			
			return true;
		}
		
		return false;
	},
	
	/**
	 * @classDescription define novo nome do grupo de 'radios' para o RadioGroup
	 * @param {String} newGroupName
	 */
	setGroupName : function RadioGroup_setGroupName ( newGroupName )
	{
		this.groupName = newGroupName;
	}		
});
if(typeof Baze !== 'undefined')
{
	Baze.provide("web.form.RadioButton");
	
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
	Baze.require("web.form.RadioGroup");
}

/**
 * @class Style
 * @alias Style
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 *
 * @param {Object} elem
 */
RadioButton = function RadioButton(elem) 
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == 'undefined' || elem == null)
	{
		var elem = document.createElement('input');
		elem.type = 'radio';
	}
	
	this.initialize(elem);
};

Object.extend(RadioButton.prototype, VisualComponent.prototype);
Object.extend(RadioButton.prototype, FormField.prototype);

Object.extend(RadioButton.prototype,
{
	parent : VisualComponent,
	
	radioGroup : null,
	
	oldValue : null,
	
	phpClass : "RadioButton",

	/**
	 * @method initialize
	 * @param {HTMLElement} elem
	 * @return {boolean}
	 */
	initialize : function Radio_initialize (elem)
	{
		if (elem.tagName.toLowerCase() == "input" && elem.type.toLowerCase() == "radio")
		{
			(Component.prototype.initialize.bind(this, elem))();
			
			this.oldValue = elem.checked;	
			this.realElement = elem;
			
			if (this.radioGroup == null)
			{
				var rdg = Baze.getComponentById(elem.name);
				
				if (rdg == null)
				{
					rdg = new RadioGroup();
					
					rdg.initialize(elem.name);
					
					rdg.setId(elem.name);
					
					Baze.addComponent(rdg);
				}
				
				this.radioGroup = rdg;
			}
			
			elem.onchange = this._raiseChange.bind(this);
			
			//Adicionando o radio criado ao seu grupo
			return this.radioGroup.addRadio(this, false, true);
		}			
		return false;
	},
	
	/**
	 * @param {Event} e
	 */
	forceRaiseChange : function Radio_forceRaiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "checked", oldValue : this.oldValue});
		this.oldValue = this.get("checked"); 
	},
	
	/**
	 * @method getRadioGroup
	 * @return {RadioGroup}
	 */
	getRadioGroup : function Radio_getRadioGroup()
	{
		return this.radioGroup;
	},

	/**
	 * @method setRadioGroup
	 * @param {RadioGroup} radioGroup
	 */		
	setRadioGroup : function Radio_setRadioGroup(radioGroup)
	{
		this.radioGroup = radioGroup;
	},
	
	/**
	 * @param {Event} e
	 * @private
	 */
	_raiseChange: function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "checked", oldValue : this.oldValue});

		if (this.radioGroup !== null)
		{		
			this.radioGroup._raiseChange(this, e);
		}
		else
		{
			alert('radioGroup � nulo');
		}
		
		this.oldValue = this.get("checked");
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.Reset");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class Reset
 * @alias Reset
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
Reset = function Reset(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('input');
		elem.type = 'reset';
	}
	
	this.initialize(elem);
};

Object.extend(Reset.prototype, VisualComponent.prototype);
Object.extend(Reset.prototype, FormField.prototype);

Object.extend(Reset.prototype,
{
	parent : VisualComponent,
	
	phpClass : "Reset",
	
	/**
	 * @param {HTMLElement} elem
	 */		
	initialize : function Reset_initialize (elem)
	{
		if (elem.tagName.toLowerCase() == "input" && elem.type.toLowerCase() == "reset")
		{
			(Component.prototype.initialize.bind(this, elem))();
			
			this.realElement = elem;
			
			return true;
		}
		
		return false;
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.Submit");
	
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class Submit
 * @alias Submit
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
Submit = function Submit(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == "undefined")
	{
		var elem = document.createElement('input');
		elem.type = 'submit';
	}
	
	this.initialize(elem);
};

Object.extend(Submit.prototype, VisualComponent.prototype);
Object.extend(Submit.prototype, FormField.prototype);

Object.extend(Submit.prototype,
{
	parent : VisualComponent,
	
	phpClass : "Submit",
	
	/**
	 * @param {HTMLElement} elem
	 * @return {boolean}
	 */
	initialize : function (elem)
	{
		if (elem.tagName.toLowerCase() == "input" && elem.type.toLowerCase() == "submit")
		{
			(Component.prototype.initialize.bind(this, elem))();
			this.realElement = elem;
			
			return true;
		}
		return false;
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.TextArea");	
	
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");
	Baze.require("web.form.FormField");
}

/**
 * @class TextArea
 * @alias TextArea
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
TextArea = function TextArea(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	(Container.bind(this))();
	
	if (typeof elem == "undefined")
	{
		var elem = document.createElement('textarea');
	}
	
	this.initialize(elem);	
};

Object.extend(TextArea.prototype, VisualComponent.prototype);
Object.extend(TextArea.prototype, FormField.prototype);
Object.extend(TextArea.prototype, Container.prototype);

Object.extend(TextArea.prototype,  
{
	parent : VisualComponent,
	
	actualValue : "",
	
	oldValue : "",
	
	phpClass : "TextArea",
	
	/**
	 * @param {HTMLElement} elem
	 * @return {boolean}
	 */
	initialize : function TextArea_initialize(elem)
	{		
		if (elem.tagName.toLowerCase() == "textarea")
		{
			(Component.prototype.initialize.bind(this, elem))();
			
			this.oldValue = elem.value;
			this.actualValue = elem.value;
			this.realElement = elem;
			
			elem.onchange = this._raiseChange.bind(this);
			
			return true;
		}
		
		Baze.raise("n�o foi poss�vel criar o textarea");
		
		return false;
	},
	
	removeChildren : function TextArea_removeChildren()
	{
		this.realElement.innerHTML = '';
		this.realElement.value = '';
		
		this._raiseChange.bind(this);
	},

	/**
	 * @private
	 * @param {Event} e
	 */
	_raiseChange: function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "value", oldValue : this.oldValue});
		this.oldValue = this.actualValue;
		this.actualValue = this.realElement.value;
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.TextBox");	
	
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class TextBox
 * @alias TextBox
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
TextBox = function TextBox(elem)
{
	(FormField.bind(this))();
	(VisualComponent.bind(this))();
	
	if (typeof elem == "undefined") 
	{
		elem = document.createElement("input");
		elem.type = "text";
	}
	
	this.initialize(elem);		
};

Object.extend(TextBox.prototype, VisualComponent.prototype);
Object.extend(TextBox.prototype, FormField.prototype);

Object.extend(TextBox.prototype,
{
	parent : VisualComponent,
	
	oldValue : "",
	
	actualValue : "",

	phpClass : "TextBox",
	
	/**
	 * @param {HTMLElement}elem
	 * @return {boolean}
	 */
	initialize : function TextBox_initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		this.actualValue = elem.value;
		this.realElement = elem;
		
		var oldOnChange = this.realElement.onchange;
		
		this.realElement.onchange = null;
		
		if (window.addEventListener) // Mozilla like
		{
			this.realElement.addEventListener('change', this._raiseChange.bind(this),false);
			
			if(oldOnChange)
				this.realElement.addEventListener('change', oldOnChange,false);
		}
		else if (window.attachEvent) // IE
		{
			this.realElement.attachEvent('onchange', this._raiseChange.bind(this));
			
			if(oldOnChange)
				this.realElement.attachEvent('onchange', oldOnChange);
		}
		
		
		return true;
	},
	
	checkChanges : function TextBox_checkChanges()
	{
		if(this.oldValue != this.realElement.value)
			this._raiseChange();
	},
	
	/**
	 * @private
	 * @param {Event} e
	 */
	_raiseChange : function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "value", oldValue : this.oldValue});
		this.oldValue = this.actualValue;
		this.actualValue = this.realElement.value;
	}
});
if(typeof Baze != "undefined")
{
	Baze.provide("web.form.validator.BazeValidator");
}
	
BazeValidator = function BazeValidator()
{};
if(typeof Baze != "undefined")
{
	Baze.provide("web.form.validator.CompareValidator"); // informa que esse arquivo foi carregado
	
	Baze.require("web.form.validator.BazeValidator"); // require normal, � tipo o import do Baze
}

/**
 * @class CompareValidator
 * @alias CompareValidator
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @param {FormField} fieldToCompare
 */
CompareValidator = function CompareValidator(fieldToCompare)
{
	// chamando o construtor da classe pai
	(BazeValidator.bind(this))();
	
	if(fieldToCompare instanceof FormField)
	{
		this.fieldToCompare = fieldToCompare;
	}
};

// extends Component
Object.extend(CompareValidator.prototype, BazeValidator.prototype);

// defini��o de m�todos e propriedades
Object.extend(CompareValidator.prototype, 
{
	_EQUAL: 1,
	_NOT_EQUAL: 2,
	_LESS_THAN: 3,
	_GREATER_THAN: 4,
	_LESS_OR_EQUAL: 5,
	_GREATER_OR_EQUAL: 6,
	
	fieldToCompare: null,
	comparationType: 1,
	
	setComparationType: function CompareValidator_setComparationType(type)
	{
		this.comparationType = type;
		this.setLastValidationField(3, false);
	},
	
	getComparationType: function CompareValidator_getComparationType()
	{
		return this.comparationType;
	},
	
	setFieldToCompare: function CompareValidator_setFieldToCompare(toCompare)
	{
		if(toCompare instanceof FormField)
		{
			this.fieldToCompare = toCompare;
			this.setLastValidationField(3, false);
		}
	},
	
	setFieldToCompare: function CompareValidator_getFieldToCompare()
	{
		return this.fieldToCompare;
	},
	
	doValidation: function CompareValidator_doValidation() {
		var fieldValue = this.getFieldToValidate.get(1);
		var toCompareValue = this.fieldToCompare.get('value');
	
		var validTest = this.getLastValidationField(3);
		
		if(validTest === true)
		{
			var lastValue = this.getLastValidationField(1);
			if(lastValue === fieldValue)
			{
				var result1 = this.getLastValidationField(2);
				if((typeof result1) === 'boolean')
				{
					return result1;
				}
			}
		}
		
		this.setLastValidationField(1, fieldValue);
		this.setLastValidationField(3, true);
		
		var result = false;
		
		switch(this.comparationType)
		{
			case this._NOT_EQUAL:
				if(fieldValue !== toCompareValue)
				{
					result = true;
				}
				break;
			case this._LESS_THAN:
				if(fieldValue < toCompareValue)
				{
					result = true;
				}
				break;
			case this._GREATER_THAN:
				if(fieldValue > toCompareValue)
				{
					result = true;
				}
				break;
			case this._LESS_OR_EQUAL:
				if(fieldValue <= toCompareValue)
				{
					result = true;
				}
				break;
			case this._GREATER_OR_EQUAL:
				if(fieldValue >= toCompareValue)
				{
					result = true;
				}
				break;
			default:
				if(fieldValue === toCompareValue)
				{
					result = true;
				}
		}
		
		return result;
	}
});
if(typeof Baze != "undefined")
{
	Baze.provide("web.form.validator.CustomValidator");
	
	Baze.require("web.form.validator.BazeValidator");
}
/**
 * @class CustomValidator
 * @alias CustomValidator
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 */
CustomValidator = function CustomValidator(){
	// chamando o construtor da classe pai
	(BazeValidator.bind(this))();
};

// extends Component
Object.extend(CustomValidator.prototype, BazeValidator.prototype);

// defini��o de m�todos e propriedades
Object.extend(CustomValidator.prototype, {
	jsFunction: null,
	validateFunction: null,
	
	setJSFunction: function CustomValidator_setJSFunction(jsFunction)
	{
		this.jsFunction = jsFunction;
		this.setLastValidationField(3, false);
	},
	
	getJSFunction: function CustomValidator_getJSFunction()
	{
		return this.jsFunction;
	},
	
	doValidation: function RegExValidator_doValidation() {
		var fieldValue = this.getFieldToValidate.get(1);
	
		var validTest = this.getLastValidationField(3);
		
		if(validTest === true)
		{
			var lastValue = this.getLastValidationField(1);
			if(lastValue === fieldValue)
			{
				var result1 = this.getLastValidationField(2);
				if((typeof result1) === 'boolean')
				{
					return result1;
				}
			}
		}
		
		var result;
		eval("result = " + this.jsFunction + "('" + fieldValue + "');");
		
		this.setLastValidationField(1, fieldValue);
		this.setLastValidationField(2, result);
		this.setLastValidationField(3, true);			
		
		return result;
	}
});
if(typeof Baze != "undefined")
{
	Baze.provide("web.form.validator.RangeValidator");
	
	Baze.require("web.form.validator.BazeValidator");
}

/**
 * @class RangeValidator
 * @alias RangeValidator
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 */
RangeValidator = function RangeValidator(){
	// chamando o construtor da classe pai
	(BazeValidator.bind(this))();
};

// extends Component
Object.extend(RangeValidator.prototype, BazeValidator.prototype);

// defini��o de m�todos e propriedades
Object.extend(RangeValidator.prototype, {
	minValue: 0,
	maxValue: -1,
	strictComparison: false,
	
	setMinValue: function RangeValidator_setMinValue(minValue) {
		if(((typeof minValue) === 'number') && (minValue !== this.minValue))
		{
			this.minValue = minValue;
			this.setLastValidationField(3, false);
		}
	},
	
	getMinValue: function RangeValidator_getMinValue() {
		return this.minValue;
	},

	setMaxValue: function RangeValidator_setMaxValue(maxValue) {
		if(((typeof maxValue) === 'number') && (maxValue !== this.maxValue))
		{
			this.maxValue = maxValue;
			this.setLastValidationField(3, false);
		}
	},

	getMaxValue: function RangeValidator_getMaxValue() {
		return this.maxValue;
	},
	
	setStrictComparison: function RangeValidator_setStrictComparison(strictComparison) {
		if(((typeof strictComparison) === 'boolean') && (this.strictComparison !== strictComparison))
		{
			this.strictComparison = strictComparison;
			this.setLastValidationField(3, false);
		}
	},
	
	getStrictComparison: function RangeValidator_getStrictComparison() {
		return this.strictComparison;
	},
	
	doValidation: function RangeValidator_doValidation() {
		var fieldValue = this.getFieldToValidate.get(1);
	
		var validTest = this.getLastValidationField(3);
		
		if(validTest === true)
		{
			var lastValue = this.getLastValidationField(1);
			if(lastValue === fieldValue)
			{
				var result1 = this.getLastValidationField(2);
				if((typeof result1) === 'boolean')
				{
					return result1;
				}
			}
		}
		
		this.setLastValidationField(1, fieldValue);
		this.setLastValidationField(3, true);
		
		var result = false;
		
		if(this.strictComparison)
		{
			if(this.minValue >= 0)
			{
				if(fieldValue.length > this.minValue)
				{
					if(this.maxValue > this.minValue)
					{
						if(fieldValue.length < this.maxValue)
						{
							result = true;
						}
					}
					else
					{
						result = true;
					}
				}
			}
			else
			{
				if(this.maxValue > 0)
				{
					if(tamValue < this.maxValue)
					{
						result = true;
					}
				}
				else
				{
					result = true;
				}
			}
		}
		else
		{
			if(this.minValue >= 0)
			{
				if(fieldValue.length >= this.minValue)
				{
					if(this.maxValue >= this.minValue)
					{
						if(fieldValue.length <= this.maxValue)
						{
							result = true;
						}
					}
					else
					{
						result = true;
					}
				}
			}
			else
			{
				if(this.maxValue >= 0)
				{
					if(fieldValue.length <= this.maxValue)
					{
						result = true;
					}
				}
				else
				{
					result = true;
				}
			}
		}
		
		this.setLastValidationField(2, result);
		return result;
	}
});

if(typeof Baze != "undefined")
{
	Baze.provide("web.form.validator.RegExValidator"); // informa que esse arquivo foi carregado
	
	Baze.require("web.form.validator.BazeValidator"); // require normal, � tipo o import do Baze
}

/**
 * @class RegExValidator
 * @alias RegExValidator
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 */
RegExValidator = function()
{
	// chamando o construtor da classe pai
	(BazeValidator.bind(this))();
};

// extends Component
Object.extend(RegExValidator.prototype, BazeValidator.prototype);

// defini��o de m�todos e propriedades
Object.extend(RegExValidator.prototype, {
	expression: '',
	
	setExpression: function RegExValidator_setExpression(newExpression) {
		this.expression = newExpression.toString();
		this.setLastValidationField(3, false);
	},
	
	getExpression: function RegExValidator_getExpression() {
		return this.expression;
	},
	
	doValidation: function RegExValidator_doValidation() {
		var fieldValue = this.getFieldToValidate.get(1);
	
		var validTest = this.getLastValidationField(3);
		
		if(validTest === true)
		{
			var lastValue = this.getLastValidationField(1);
			if(lastValue === fieldValue)
			{
				var result1 = this.getLastValidationField(2);
				if((typeof result1) === 'boolean')
				{
					return result1;
				}
			}
		}
		
		this.setLastValidationField(1, fieldValue);
		this.setLastValidationField(3, true);
		
		var result = false;
		
		var re = new RegExp(this.expression);
		if(fieldValue.match(re))
		{
			result = true;
		}
		
		this.setLastValidationField(2, result);
		return result;
	}
});

RegExValidator.CommonExp = {

	Date : /(0[1-9]|[12][0-9]|3[01])([- \/.])(0[1-9]|1[012])\2(19|20)\d\d$/,
	
	Time : /^(\d{1,2})\:(\d{1,2})\:(\d{1,2})$/,
	
	Alpha : /^[a-zA-Z\.\-]*$/,
	
	AlphaNum : /^\w+$/,
	
	UnsignedInt : /^\d+$/,
	
	Integer : /^[\+\-]?\d*$/,
	
	Real : /^[\+\-]?\d*\.?\d*$/,
	
	UnsignedReal : /^\d*\.?\d*$/,
	
	Email : /^[\w-\.]+\@[\w\.-]+\.[a-z]{2,6}$/,
	
	Phone : /^[\d\.\s\-]+$/
};
if(typeof Baze != "undefined")
{
	Baze.provide("web.form.validator.RequiredFieldValidator");
	
	Baze.require("web.form.validator.BazeValidator");
}

/**
 * @class Style
 * @alias Style
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 */
RequiredFieldValidator = function RequiredFieldValidator(){
	// chamando o construtor da classe pai
	(BazeValidator.bind(this))();
};

// extends Component
Object.extend(RequiredFieldValidator.prototype, BazeValidator.prototype);

// defini��o de m�todos e propriedades
Object.extend(RequiredFieldValidator.prototype, {
	
	doValidation : function RequiredFieldValidator_doValidation() {
		fieldValue = this.getFieldToValidate.get('value');
		fieldValue = fieldValue.toString();
		
		result = false;
		if(fieldValue.length > 0)
		{
			result = true;
		}
				
		return result;
	}
});


