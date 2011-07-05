if(typeof Base != "undefined") {
	Base.provide("system.EchoDOMUtil");
}

/**
 * Static object/namespace for performing cross-platform DOM-related
 * operations.  Most methods in this object/namespace are provided due
 * to nonstandard behavior in clients.
 */
EchoDomUtil = function () { };

/**
 * An associative array which maps between HTML attributes and HTMLElement
 * property  names.  These values generally match, but multiword attribute
 * names tend to have "camelCase" property names, e.g., the
 * "cellspacing" attribute corresponds to the "cellSpacing" property.
 * This map is required by the Internet Explorer-specific importNode()
 * implementation
 */
EchoDomUtil.attributeToPropertyMap = null;

/**
 * Adds an event listener to an object, using the client's supported event
 * model.
 *
 * @param eventSource the event source
 * @param eventType the type of event (the 'on' prefix should NOT be included
 *        in the event type, i.e., for mouse rollover events, "mouseover" would
 *        be specified instead of "onmouseover")
 * @param eventListener the event listener to be invoked when the event occurs
 * @param useCapture a flag indicating whether the event listener should capture
 *        events in the final phase of propagation (only supported by
 *        DOM Level 2 event model)
 */
EchoDomUtil.addEventListener = function(eventSource, eventType, eventListener, useCapture) {
    if (eventSource.addEventListener) {
        eventSource.addEventListener(eventType, eventListener, useCapture);
    } else if (eventSource.attachEvent) {
        eventSource.attachEvent("on" + eventType, eventListener);
    }
};

/**
 * Initializes the attribute-to-property map required for importing HTML
 * elements into a DOM in Internet Explorer browsers.
 */
 function _initAttributeToPropertyMap() {
    var m = [];
    m["accesskey"] = "accessKey";
    m["cellpadding"] = "cellPadding";
    m["cellspacing"] = "cellSpacing";
    m["class"] = "className";
    m["codebase"] = "codeBase";
    m["codetype"] = "codeType";
    m["colspan"] = "colSpan";
    m["datetime"] = "dateTime";
    m["frameborder"] = "frameBorder";
    m["longdesc"] = "longDesc";
    m["marginheight"] = "marginHeight";
    m["marginwidth"] = "marginWidth";
    m["maxlength"] = "maxLength";
    m["noresize"] = "noResize";
    m["noshade"] = "noShade";
    m["nowrap"] = "noWrap";
    m["readonly"] = "readOnly";
    m["rowspan"] = "rowSpan";
    m["tabindex"] = "tabIndex";
    m["usemap"] = "useMap";
    m["valign"] = "vAlign";
    m["valueType"] = "valueType";
    EchoDomUtil.attributeToPropertyMap = m;
};
EchoDomUtil.initAttributeToPropertyMap = _initAttributeToPropertyMap();

/**
 * Creates a new XML DOM.
 *
 * @param namespaceUri the unique URI of the namespace of the root element in
 *        the created document (not supported for
 *        Internet Explorer 6 clients, null may be specified for all clients)
 * @param qualifiedName the name of the root element of the new document (this
 *        element will be created automatically)
 * @return the created DOM
 */
EchoDomUtil.createDocument = function(namespaceUri, qualifiedName) {
    if (document.implementation && document.implementation.createDocument) {
        // DOM Level 2 Browsers
        var dom = document.implementation.createDocument(namespaceUri, qualifiedName, null);
        if (!dom.documentElement) {
            dom.appendChild(dom.createElement(qualifiedName));
        }
        return dom;
    } else if (window.ActiveXObject) {
        // Internet Explorer
        var createdDocument = new ActiveXObject("Microsoft.XMLDOM");
        var documentElement = createdDocument.createElement(qualifiedName);
        createdDocument.appendChild(documentElement);
        return createdDocument;
    } else {
        throw "Unable to create new Document.";
    }
};

/**
 * Converts a hyphen-separated CSS attribute name into a camelCase
 * property name.
 *
 * @param attribute the CSS attribute name, e.g., border-color
 * @return the style property name, e.g., borderColor
 */
EchoDomUtil.cssAttributeNameToPropertyName = function(attribute) {
    var segments = attribute.split("-");
    var out = segments[0];
    for (var i = 1; i < segments.length; ++i) {
        out += segments[i].substring(0, 1).toUpperCase();
        out += segments[i].substring(1);
    }
    return out;
};

/**
 * Recursively fixes broken element attributes in a Safari DOM.
 * Safari2 does not properly unescape attributes when retrieving
 * them from an XMLHttpRequest's response DOM.  For example, the attribute
 * abc="x&y" would return the value "X&#38;y" in Safari2.  This simply scans
 * the DOM for any attributes containing "&#38;" and replaces instances of it
 * with a simple ampersand ("&").  This method should be invoked once per DOM
 * if it has been determined that a version of Safari that suffers this bug is
 * in use.
 *
 * @param node the starting node whose attributes are to be fixed (child
 *        elements will be fixed recursively)
 */
EchoDomUtil.fixSafariAttrs = function(node) {
    if (node.nodeType == 1) {
        for (i = 0; i < node.attributes.length; ++i) {
            var attribute = node.attributes[i];
            node.setAttribute(attribute.name, attribute.value.replace("\x26\x2338\x3B", "&"));
        }
    }

    for (var childNode = node.firstChild; childNode; childNode = childNode.nextSibling) {
        EchoDomUtil.fixSafariAttrs(childNode);
    }
};

/**
 * Returns the base component id of an extended id.
 * Example: for value "c_333_foo", "c_333" would be returned.
 *
 * @param elementId the extended id
 * @return the component id, or null if it cannot be determined
 */
EchoDomUtil.getComponentId = function(elementId) {
    if ("c_" != elementId.substring(0, 2)) {
        // Not a component id.
        return null;
    }
    var extensionStart = elementId.indexOf("_", 2);
    if (extensionStart == -1) {
        // id has now extension.
        return elementId;
    } else {
        return elementId.substring(0, extensionStart);
    }
};

/**
 * Cross-platform method to retrieve the CSS text of a DOM element.
 *
 * @param element the element
 * @return cssText the CSS text
 */
EchoDomUtil.getCssText = function(element) {
    if (EchoClientProperties.get("quirkOperaNoCssText")) {
        return element.getAttribute("style");
    } else {
        return element.style.cssText;
    }
};

/**
 * Returns the target of an event, using the client's supported event model.
 * On clients which support the W3C DOM Level 2 event specification,
 * the <code>target</code> property of the event is returned.
 * On clients which support only the Internet Explorer event model,
 * the <code>srcElement</code> property of the event is returned.
 *
 * @param e the event
 * @return the target
 */
EchoDomUtil.getEventTarget = function(e) {
    return e.target ? e.target : e.srcElement;
};

/**
 * Imports a node into a document.  This method is a
 * cross-browser replacement for DOMImplementation.importNode, as Internet
 * Explorer 6 clients do not provide such a method.
 * This method will directly invoke targetDocument.importNode() in the event
 * that a client provides such a method.
 *
 * @param targetDocument the document into which the node/hierarchy is to be
 *        imported
 * @param sourceNode the node to import
 * @param importChildren a boolean flag indicating whether child nodes should
 *        be recursively imported
 */
EchoDomUtil.importNode = function(targetDocument, sourceNode, importChildren) {
    if (targetDocument.importNode) {
        // DOM Level 2 Browsers
        return targetDocument.importNode(sourceNode, importChildren);
    } else {
        // Internet Explorer Browsers
        return EchoDomUtil.importNodeImpl(targetDocument, sourceNode, importChildren);
    }
};

/**
 * Manual implementation of DOMImplementation.importNode() for clients that do
 * not provide their own (i.e., Internet Explorer 6).
 *
 * @param targetDocument the document into which the node/hierarchy is to be
 *        imported
 * @param sourceNode the node to import
 * @param importChildren a boolean flag indicating whether child nodes should
 *        be recursively imported
 */
EchoDomUtil.importNodeImpl = function(targetDocument, sourceNode, importChildren) {
    var targetNode, i;
    switch (sourceNode.nodeType) {
    case 1:
        targetNode = targetDocument.createElement(sourceNode.nodeName.toLowerCase());
        for (i = 0; i < sourceNode.attributes.length; ++i) {
            var attribute = sourceNode.attributes[i];

            if(typeof attribute.nodeValue == "function" || typeof attribute.nodeValue == "object") {
            	continue; }

            if ("style" == attribute.name) {
                targetNode.style.cssText = attribute.value;
            } else {
                if (EchoDomUtil.attributeToPropertyMap === null) {
                    EchoDomUtil.initAttributeToPropertyMap();
                }

                var propertyName = EchoDomUtil.attributeToPropertyMap[attribute.name];
                if (propertyName) {
                    targetNode.setAttribute(propertyName, attribute.nodeValue);
                } else {
                    targetNode.setAttribute(attribute.name, attribute.nodeValue);
                }
            }
        }
        break;
    case 3:
        targetNode = targetDocument.createTextNode(sourceNode.nodeValue);
        break;
    }

    if (importChildren && sourceNode.hasChildNodes()) {
        for (var sourceChildNode = sourceNode.firstChild; sourceChildNode; sourceChildNode = sourceChildNode.nextSibling) {
            var targetChildNode = EchoDomUtil.importNodeImpl(targetDocument, sourceChildNode, true);
            if (targetChildNode) {
	            targetNode.appendChild(targetChildNode);
            }
        }
    }
    return targetNode;
};

/**
 * Determines if <code>ancestorNode</code> is or is an ancestor of
 * <code>descendantNode</code>.
 *
 * @param ancestorNode the potential ancestor node
 * @param descendantNode the potential descendant node
 * @return true if <code>ancestorNode</code> is or is an ancestor of
 *         <code>descendantNode</code>
 */
EchoDomUtil.isAncestorOf = function(ancestorNode, descendantNode) {
    var testNode = descendantNode;
    while (testNode !== null) {
        if (testNode == ancestorNode) {
            return true;
        }
        testNode = testNode.parentNode;
    }
    return false;
};

/**
 * Prevents the default action of an event from occurring, using the
 * client's supported event model.
 * On clients which support the W3C DOM Level 2 event specification,
 * the preventDefault() method of the event is invoked.
 * On clients which support only the Internet Explorer event model,
 * the 'returnValue' property of the event is set to false.
 *
 * @param e the event
 */
EchoDomUtil.preventEventDefault = function(e) {
    if (e.preventDefault) {
        e.preventDefault();
    } else {
        e.returnValue = false;
    }
};

/**
 * Removes an event listener from an object, using the client's supported
 * event model.
 *
 * @param eventSource the event source
 * @param eventType the type of event (the 'on' prefix should NOT be included
 *        in the event type, i.e., for mouse rollover events, "mouseover" would
 *        be specified instead of "onmouseover")
 * @param eventListener the event listener to be invoked when the event occurs
 * @param useCapture a flag indicating whether the event listener should capture
 *        events in the final phase of propagation (only supported by
 *        DOM Level 2 event model)
 */
EchoDomUtil.removeEventListener = function(eventSource, eventType, eventListener, useCapture) {
    if (eventSource.removeEventListener) {
        eventSource.removeEventListener(eventType, eventListener, useCapture);
    } else if (eventSource.detachEvent) {
        eventSource.detachEvent("on" + eventType, eventListener);
    }
};

/**
 * Cross-platform method to set the CSS text of a DOM element.
 *
 * @param element the element
 * @param cssText the new CSS text
 */
EchoDomUtil.setCssText = function(element, cssText) {
    if (EchoClientProperties.get("quirkOperaNoCssText")) {
	    element.setAttribute("style", cssText);
    } else {
	    element.style.cssText = cssText;
    }
};

/**
 * Stops an event from propagating ("bubbling") to parent nodes in the DOM,
 * using the client's supported event model.
 * On clients which support the W3C DOM Level 2 event specification,
 * the stopPropagation() method of the event is invoked.
 * On clients which support only the Internet Explorer event model,
 * the 'cancelBubble' property of the event is set to true.
 *
 * @param e the event
 */
EchoDomUtil.stopPropagation = function(e) {
    if (e.stopPropagation) {
        e.stopPropagation();
    } else {
        e.cancelBubble = true;
    }
};
