/**
 * @author Saulo
 */

/**
 * Automata specification
 * 
 * (|) - empty
 * ( ) - space
 * 
	// S ->	(|) | <T
	// T -> a-zTB | /TE
	// TB -> a-zTB | BPE
	// TE -> a-zTE | >S

	// BPE -> ( )BPE | P | E
	// BEQ -> ( )BEQ | EQ
	// BV -> ( )BV | BV
	 
	
	// P -> a-zP | BEQ
	 
	// EQ -> =V | BV
	 
	// V -> "VQ | (|)VNQ
	// VNQ -> a-zVQ | 0-9VQ | BPE
	// VQ -> a-zVQ | 0-9VQ | "BPE
	
	// E -> >TC | />
	
	// TC -> a-zTC | <T
 * 
 * Se espera " e acha outra coisa, bota aspas + outra coisa
 * 		if(state == V && (c = read(data)) != '"') 
 * 			write('"');
 * 		write(c);
 * 
 * Se espera = e vem outra coisa, escreve = + "nome da ultima propriedade lida"
 * 		if(estate == I && (c = read(data)) != '=')
 * 			write('="'+lastProperty+'"');
 * 		else
 * 			write('=');
 *  
 * @param {Object} data
 */

// this function intend to fix missing quotes and "=name" for boolean properties 
function fixHTML(data)
{
	var StateType = {
		START : 		1,	// S ->	(|) | <T
		TAG : 			2,	// T -> a-zTB | /TE
		TAG_BEGIN :		3,	// TB -> a-zTB | ( )BPE | >TC
		TAG_END : 		4,	// TE -> a-zTE | >S
		TAG_CONTENT :	5,	// TC -> a-zTC | <T
		BLANK_PN_E : 	5,	// BPE -> ( )BPE | P | E
		PNAME : 		6,	// P -> a-zP | ( )BEQ
		BLANK_EQ : 		7,	// BEQ -> ( )BEQ | EQ
		EQUAL : 		8,	// EQ -> =V | BV
		BLANK_PV : 		9,	// BV -> ( )BV | V
		PVALUE : 		10,	// V -> "VQ | (|)VNQ
		QUOTED_VALUE : 	11,	// VQ -> a-zVQ | 0-9VQ | "BPE
		NO_QUOTED_VALUE : 12, // VNQ -> a-zVQ | 0-9VQ | ( )BPE
		END : 			13,	// E -> >TC | />
		RETURN :		-1
	};

	var state = StateType.START;	
	
	var out = "";
	
	// reads the next character in data 
	var read = (function (){
		var i=0;
		return function(data){ return (i < data.length ) ? data[i++] : "";};
	})();
	
	// write a character to out
	function write(c) { out += c; };

	function run()
	{		
		while(state != StateType.RETURN) {
			callState(state);
		}
		
		return out;
	}
	
	function parserError()
	{
		
	}
	
	// initial state - S ->	(|) | <T
	function START()
	{
		var c = read(data);
		
		if(c == "") {
			state = StateType.RETURN;
		}
		else if(c == "<") {
			state = StateType.TAG;
			write(c);
		}
	}
	
	// T -> a-zTB | /TE
	function TAG()
	{
		var c = read(data);
		
		if(c == '/')
			state = StateType.TAG_END;
		else if( ('A' <= c && c <= 'Z') || ('a' <= c && c <= 'z'))
			state = StateType.TAG_BEGIN;
		else {
			state = StateType.RETURN;
			parserError();
			return;
		}
		
		write(c);
		
	}

	// TB -> a-zTB | ( )BPE | >TC
	function TAG_BEGIN()
	{
		var c = read(data);
		
		if( ('A' <= c && c <= 'Z') || ('a' <= c && c <= 'z'))
			state = StateType.TAG_BEGIN;
		else if(c == " ")
			state = StateType.BLANK_PN_E;
		else if(c == ">")
			state = StateType.TAG_CONTENT;
		else 
		{
			state = StateType.RETURN;
			parserError();
			return;
		}
		
		write(c);
	}
	
	// TE -> a-zTE | >S
	function TAG_END()
	{
		var c = read(data);
		
		if( ('A' <= c && c <= 'Z') || ('a' <= c && c <= 'z'))
			state = StateType.TAG_BEGIN;
		else if(c == ">")
			state = StateType.BLANK_PN_E;
		else 
		{
			state = StateType.RETURN;
			parserError();
			return;
		}
		
		write(c);
	}

	// BPE -> ( )BPE | P | E
	function BLANK_PN_E(){}
	
	// TC -> a-zTC | <T
	function TAG_CONTENT() {}
	
	// BEQ -> ( )BEQ | EQ
	function BLANK_EQ(){}
	
	// BV -> ( )BV | BV
	function BLANK_PV(){}	 
	
	// P -> a-zP | BEQ
	function PNAME(){}
	 
	// EQ -> =V | BV
	function EQUAL(){}
	 
	// V -> "VQ | (|)VNQ
	function PVALUE(){}
	
	// VNQ -> a-zVQ | 0-9VQ | BPE
	function QUOTED_VALUE(){}
	
	// VQ -> a-zVQ | 0-9VQ | "BPE
	function NO_QUOTED_VALUE(){}
	
	// E -> >TC | />
	function END(){}

	
	return 
} 