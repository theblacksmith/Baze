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