function DatePicker(name) {

	this.name = name;
	this.dt = new Date();

	var oT1 = document.createElement('table');
	oT1.id = this.name;
	oT1.className = 'DatePicker';
	document.body.appendChild(oT1);
}

DatePicker.prototype.show = function(dt, x, y, callback) {
	if ( dt ) this.dt = dt;
	this.callback = callback;

	// if not rendered yet, do so
	if ( !this.oSpan ) this.render();

	// set coordinates
	this.oSpan.style.left = x+'px';
	this.oSpan.style.top= y+'px';

	this.fill();

	this.oSpan.style.display = "block";
//	this.oMonth.focus();
};

DatePicker.prototype.hide = function() {
   	this.oSpan.style.display = "none";
};

DatePicker.prototype.render = function() {
	var oT1, oTR1, oTD1, oTH1;
	var oT2, oTR2, oTD2, oTHead, oTBody;

	this.oSpan = document.getElementById(this.name);

	oTHead = this.oSpan.createTHead();

	oTR1 = oTHead.insertRow(oTHead.rows.length);

	oTD1 = oTR1.insertCell(oTR1.cells.length);
	oTD1.title = this.texts.prevMonth;
	oTD1.onclick = function() { this.oDatePicker.onPrev(); }
	oTD1.oDatePicker = this;
	oTD1.className = 'HeadBtn';
	oTD1.innerHTML = "\u00ab";

	oTD1 = oTR1.insertCell(oTR1.cells.length);
	oTD1.colSpan = 5;
	oTD1.className = 'NestedTable';

	oT2 = document.createElement('table');
	oTD1.appendChild(oT2);
	oTR2 = oT2.insertRow(oT2.rows.length);

	oTD2 = oTR2.insertCell(oTR2.cells.length);
	oTD2.className = 'MonthSelect';
	this.oMonth = document.createElement("select");
	this.oMonth.oDatePicker = this;
	this.oMonth.onchange = this.oMonth.onkeyup =
		function() { this.oDatePicker.onMonth(); }
	this.oMonth.className = 'HeadBtn';
	for ( var i = 0; i < 12; i++ ) {
		this.oMonth.add(new Option(this.texts.months[i], i),undefined);
	}
	oTD2.appendChild(this.oMonth);

	this.oYear = oTR2.insertCell(oTR2.cells.length);
	this.oYear.title = this.texts.yearTitle;
	this.oYear.oDatePicker = this;
	this.oYear.onclick = function() { this.oDatePicker.onYear(); }
	this.oYear.className = 'HeadBtn';

	oTD1 = oTR1.insertCell(oTR1.cells.length);
	oTD1.title = this.texts.nextMonth;
	oTD1.onclick = function() { this.oDatePicker.onNext(); }
	oTD1.oDatePicker = this;
	oTD1.className = 'HeadBtn';
	oTD1.innerHTML = "\u00bb";

	oTR1 = oTHead.insertRow(oTHead.rows.length);
	for ( i = 0; i < 7; i++ ) {
		oTH1 = document.createElement('th');
		oTH1.innerHTML = this.texts.days[i];
		oTR1.appendChild(oTH1);
	}

	oTBody = document.createElement('tbody');
	this.aCells = new Array;
	for ( var j = 0; j < 6; j++ ) {
		this.aCells.push(new Array);
		oTR1 = oTBody.insertRow(oTBody.rows.length);
		for ( i = 0; i < 7; i++ ) {
			this.aCells[j][i] = oTR1.insertCell(oTR1.cells.length);
			this.aCells[j][i].oDatePicker = this;
			this.aCells[j][i].onclick =
				function() { this.oDatePicker.onDay(this); }
		}
	}
	this.oSpan.appendChild(oTBody);
};


DatePicker.prototype.fill = function() {
	// first clear all
	this.clear();

	// since we control the text format in callback(), getting the date is easy
	var cDate = null;
	if (this.client) {
		var aDt = this.client.value.split("/");
		if ( aDt && (aDt.length == 3) ) {
			cDate = new Date(parseInt(aDt[2]),parseInt(aDt[1])-1,parseInt(aDt[0]));
		}
	}
	if (cDate === null) {
		cDate = new Date();
	}

	// place the dates in the calendar
	var nRow = 0;
	var d = new Date(this.dt.getTime());
	var n = new Date();
	var m = d.getMonth();
	for ( d.setDate(1); d.getMonth() == m; d.setTime(d.getTime() + 86400000) ) {
		var nCol = d.getDay();
		this.aCells[nRow][nCol].innerHTML = d.getDate();
		this.aCells[nRow][nCol].className = 'DateBtn';
		if ( d.getDate() == n.getDate() && m == n.getMonth() && d.getYear() == n.getYear()) {
			this.aCells[nRow][nCol].className += ' DateToday';
		}
		if ( d.getDate() == this.dt.getDate() && cDate.getMonth() == this.dt.getMonth() && cDate.getYear() == this.dt.getYear()) {
			this.aCells[nRow][nCol].className += ' DateSelected';
		}
		if ( nCol == 6 ) nRow++;
	}

	// set the month combo
	this.oMonth.value = m;

	// set the year text
	this.oYear.innerHTML = this.dt.getFullYear();
};

DatePicker.prototype.clear = function() {
	for ( var j = 0; j < 6; j++ ) {
		for ( var i = 0; i < 7; i++ ) {
			this.aCells[j][i].innerHTML = "&nbsp;";
			this.aCells[j][i].className = '';
		}
	}
};

DatePicker.prototype.onPrev = function() {
	if ( this.dt.getMonth() === 0 ) {
		this.dt.setFullYear(this.dt.getFullYear() - 1);
		this.dt.setMonth(11);
	} else {
		this.dt.setMonth(this.dt.getMonth() - 1);
	}
	this.fill();
};



DatePicker.prototype.onNext = function() {
	if ( this.dt.getMonth() == 11 ) {
		this.dt.setFullYear(this.dt.getFullYear() + 1);
		this.dt.setMonth(0);
	} else {
		this.dt.setMonth(this.dt.getMonth() + 1);
	}
	this.fill();
};

DatePicker.prototype.onMonth = function() {
	this.dt.setMonth(this.oMonth.value);
	this.fill();
};

DatePicker.prototype.onYear = function() {
	var y = parseInt(prompt(this.texts.yearQuestion, this.dt.getFullYear()));
	if ( !isNaN(y) ) {
		this.dt.setFullYear(parseInt(y));
		this.fill();
	}
};

DatePicker.prototype.onDay = function(oCell) {
	var d = parseInt(oCell.innerHTML);
	if ( d > 0 )
	{
		this.dt.setDate(d);
		this.hide();
		this.callback(this.dt);
	}
};

DatePicker.prototype.texts = {
	months: [
		"Jan", "Fev", "Mar",
		"Abr", "Mai", "Jun",
		"Jul", "Ago", "Set",
		"Out", "Nov", "Dez"
	],
	days: ["D", "S", "T", "Q", "Q", "S", "S"],
	prevMonth: "Mes anterior",
	nextMonth: "Mes seguinte",
	yearTitle: "Ano. Clique para modificar.",
	yearQuestion: "Digite um ano:"
};

function showDP(oTxt) {
	if ( !document.getElementById ) return;

	// store the textbox for use in the client
	oDatePicker.client = oTxt;

	oDatePicker.show(oDatePicker.dt, oTxt.offsetLeft, oTxt.offsetTop+oTxt.offsetHeight, callback);
}

function callback(dt)
{
	oDatePicker.client.value =
		dt.getDate() + "/" +
		(dt.getMonth() + 1) + "/" +
		dt.getFullYear();
}

/**
 * Code example
 */

/*
Enter Date:<input type=text title="MM/DD/YYYY" onfocus="showDP(this);">
<script type="text/javascript">
*/

var oDatePicker = null;
function findDPs()
{
	if (oDatePicker === null) oDatePicker = new DatePicker('theDatePicker');

	var inputs = document.getElementsByTagName('input');
	for (var i = 0; i < inputs.length; i++)
	{
		var o = inputs[i];
		if (o.getAttribute('phpclass') == 'php:datepicker')
		{
			o.setAttribute('readonly', 'readonly');
			Event.observe(o, 'focus', function(){showDP(this);});
			Event.observe(o, 'blur', function(ev){
				elem = ev.explicitOriginalTarget;
				blurred = ev.explicitTarget;
				while ((elem = elem.parentNode) !== null)
				{
					if (elem.id == oDatePicker.name)
					{
						break;
					}
				}
				if (elem === null)
				{
					oDatePicker.hide();
				}
			});
		}
	}
}

Baze.addBehaviour(findDPs);

/*
</script>
*/