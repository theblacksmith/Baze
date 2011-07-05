window.debugLog = "";
window.debugAlerts = false;

function debug(msg, alwaysAlert)
{
	var div = null;

	if(window.debugAlerts || alwaysAlert) {
		alert(msg); }

	window.debugLog += msg + "\n";

	if((div = document.getElementById("debugDiv")) !== null) {
		div.innerText = window.debugLog; }
}

function debugClear()
{
	window.debugLog = '';

	if((div = document.getElementById("debugDiv")) !== null) {
		div.innerText = ''; }
}

function showDebugLog()
{
	var div = document.createElement("div");

	div.id = "debugDiv";
	div.innerText += window.debugLog;

	document.body.appendChild(div);
}