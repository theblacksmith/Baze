// VARs
var drag_object;
var drag_top;
var drag_left;
var drag_x;
var drag_y;
var drag_str_filter;
var drag_temp_index;

// INITs
drag_object = null;
drag_str_filter = "alpha(Opacity=50)";

// FUNCTIONs

function getEventTarget(e)
{
	var target;

	if(e.target)
	{
		target = e.target;
	}
	else if(e.srcElement)
	{
		target = e.srcElement;
	}

	if(target.nodeType == 3) // corrigindo o problema de apontar quem disparou o evento (como quando em um n? texto)
	{
		target = target.parentNode;
	}
	return target;
}

function getEventButton(e)
{
	var button;

	// aki n?o tem como fazer if(button), logo tem que arrumar um
	// jeito de decidir se pega como padrao ie ou como padrao mozilla

	if(e.target)
	{
		if(e.button == 0)
		{
			button = "left";
		}
		else if(e.button == 1)
		{
			button = "middle";
		}
		else if(e.button == 2)
		{
			button = "right";
		}
	}
	else if(e.srcElement)
	{
		if(e.button == 1)
		{
			button = "left";
		}
		else if(e.button == 4)
		{
			button = "middle";
		}
		else if(e.button == 2)
		{
			button = "right";
		}
	}

	return button;
}

function getX(e)
{
	var posX = 0;
	
	if(e.pageX)
	{
		posX = e.pageX;
	}
	else if(e.clientX)
	{
		posX = e.clientX;
	}
	return posX;
}

function getY(e)
{
	var posY = 0;

	if(e.pageY)
	{
		posY = e.pageY;
	}
	else if(e.clientY)
	{
		posY = e.clientY;
	}
	return posY;
}

function drag_start(e)
{

	var tempLeft;
	var tempTop;

	if(!e)
	{
		e = window.event; // internet explorer
	}

	if(getEventButton(e) == "left")
	{
		if(getEventTarget(e).getAttribute("container") != null)
		{
			drag_object = document.getElementById(getEventTarget(e).getAttribute("container"));
			if(drag_object)
			{
				tempLeft = drag_object.style.left.split("p");
				drag_left = Number(tempLeft[0]);

				tempTop = drag_object.style.top.split("p");
				drag_top = Number(tempTop[0]);

				drag_x = e.clientX; // getX(e); // function cross browser //event.clientX;
				drag_y = e.clientY; // getY(e); // function cross browser //event.clientY;

				document.onmousemove = drag_before_doing;
				document.onmouseup = drag_stop;
			}
		}
	}
	return false;
}

function drag_before_doing(evt)
{
	if(drag_object)
	{
		drag_object.style.zIndex = 1;

		document.onmousemove = drag_doing;

		drag_doing(evt);
	}
	return false;
}

function drag_doing(evt)
{
	var tempX;
	var tempY;

	if(drag_object)
	{
		tempX = drag_left + evt.clientX - drag_x;
		drag_object.style.left = tempX + "px";

		tempY = drag_top + evt.clientY - drag_y;
		drag_object.style.top = tempY + "px";
	}
	
	return false;
}

function drag_stop()
{
	if(drag_object)
	{
		drag_object.style.zIndex = 0;
		drag_object = null;

		document.onmousemove = null;
		document.onmouseup = null;
	}
}

function initializeDrag()
{
	addEvent(document,'mousedown',drag_start);

	document.onmousedown = drag_start;
}

document.onmousedown = drag_start;
window.onmousedown = drag_start;

function ble(e)
{
	if(!e)
	{
		e = window.event; // internet explorer
	}

	if(e.type == "mousemove")
	{
		document.getElementById("panel1").style.left = e.clientX + "px";
		document.getElementById("panel1").style.top = e.clientY + "px";
	}
	else
	{
		document.getElementById("window1_window_body").innerHTML = document.getElementById("window1_window_body").innerHTML + " / " + e.clientX;
	}
}