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