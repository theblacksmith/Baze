<?php

// ADICIONAR ESSA DOCUMENTAÇÃO
/**
 * @package 
 */

/**
 * Class CreateEvaluation
 * 
 * @author 
 * @since data
 * @version 0.1
 * 
 * @copyright Neoconn Networks
 */

define("_NL", "\n");

echo "******************************************************" . _NL;
echo "*                                                    *" . _NL;
echo "*           NeoBase Export Vars script               *" . _NL;
echo "*                                                    *" . _NL;
echo "******************************************************" . _NL . _NL;

$args = isset($argv) ? $argv : $_SERVER['argv'];

// Get the username
$fileKey = array_search("-d", $args) || array_search("--design", $args);

// Make sure both that the token was found and followed by another valid token, AKA not one that begins with a -
if ($fileKey && !empty($argv[$fileKey+1]) && $argv[$fileKey+1]{0} != '-') {
    $file = $argv[$fileKey+1];
}
else {
    exit("No design file provided\n");
}

echo "Loading components in " . $file . "..."._NL;

$doc = new DOMDocument();

if(!$doc->load($file))
	exit(_NL."The selected file ($file) is not a valid xml file.");

$html = $doc->getElementsByTagName("html")->item(0);

class Variable {
	public $id;
	public $visibility;
	public $type;
	
	public function __construct($id, $type = 'unknown', $visibility = 'public')
	{
		$this->id = $id;
		$this->visibility = $visibility;
		$this->type = $type;
	}
	
	public function getCode()
	{
		return 	"	/**" . _NL .
				"	 * @var " . $this->type  . _NL .
				"	 */" . _NL .
				"	" . $this->visibility . " $" . $this->id . ";" . _NL;
	}
}
	
function read ($message='', $length='255')
{
	if (!isset($GLOBALS['StdinPointer']))
	{
		$GLOBALS['StdinPointer'] = fopen ("php://stdin","r");
	}
	
	if($message !== '')
		echo $message . _NL;
		
	$line = fgets($GLOBALS['StdinPointer'],$length);
	
	return trim ($line);
}

function getPHPTags($root, &$phpTags, &$usedComponents = array(), $tabs = '')
{
	$children = $root->childNodes;
	
	if(!$children)
		return $phpTags;
		
	foreach($children as $c)
	{
		//$c = new DOMElement();
		
		if($c->prefix == "php")
		{
			if($c->getAttribute("id") == null)
			{
				trigger_error("Tag " . $c->tagName . " without id found at line ??",E_USER_NOTICE);
			}
			else
			{
				array_push($phpTags, new Variable($c->getAttribute("id"), ucfirst($c->localName)));
				$usedComponents[strtolower($c->localName)] = ucfirst($c->localName);
			}
		}
			
		getPHPTags($c,$phpTags, $usedComponents);
	}
}

$phpTags = array();
$usedComponents = array();

getPHPTags($html, $phpTags, $usedComponents);
	
echo "All components loaded"._NL;
echo count($phpTags) . " components found ";
echo "of " . count($usedComponents) . " different classes"._NL._NL;

$fname = split("\.",$file);
$fname = $fname[0];

if(!file_exists($fname.".code.php"))
{
	
	$answer = read("The file $fname.code does not exists. Create? (y|n)", 2);
	$answer = strtolower($answer);
	if($answer == "y")
	{
		echo "Creating code file $fname.code.php" . _NL;
		
		$content = createCodeFile($fname,$phpTags, $usedComponents);
		
		if(file_put_contents($fname.".code.php",$content))
		{
			echo "File creation finished sucefully"._NL;
			echo "Refreshing project"._NL._NL;
			echo "Done";
		}
		else
			echo "Error creating file. Check write permissions for the directory.";
	}
}
else
{
	echo "Sorry, the code file already exists. Code file update was not implemented yet. It's Saulo fault.";
}

function getQName($classname)
{
	$classes = array(	"button" => "system.web.ui.Button",
						"embeddedobject" => "system.web.ui.EmbeddedObject",
						"htmltag" => "system.web.ui.HTMLTag",
						"hyperlink" => "system.web.ui.Hyperlink",
						"icon" => "system.web.ui.Icon",
						"iframe" => "system.web.ui.Iframe",
						"imagebutton" => "system.web.ui.ImageButton",
						"label" => "system.web.ui.Label",
						"linkbutton" => "system.web.ui.LinkButton",
						"lista" => "system.web.ui.Lista",
						"listitem" => "system.web.ui.ListItem",
						"literal" => "system.web.ui.Literal",
						"menu" => "system.web.ui.Menu",
						"panel" => "system.web.ui.Panel",
						"panelcontainer" => "system.web.ui.PanelContainer",
						"param" => "system.web.ui.Param",
						"script" => "system.web.ui.Script",
						"style" => "system.web.ui.Style",
						"window" => "system.web.ui.Window",
						
						// data
						"datatable" => "system.web.ui.data.DataTable",
						
						// form
						"checkbox" => "system.web.ui.form.CheckBox",
						"checklist" => "system.web.ui.form.CheckList",
						"datepicker" => "system.web.ui.form..",
						"dropdownlist" => "system.web.ui.form.DropDownList",
						"fieldset" => "system.web.ui.form.FieldSet",
						"fieldvalidator" => "system.web.ui.form.FieldValidator",
						"fileuploade" => "system.web.ui.form.FileUpload",
						"form" => "system.web.ui.form.Form",
						"formimage" => "system.web.ui.form.FormImage",
						"hiddenfield" => "system.web.ui.form.HiddenField",
						"listbox" => "system.web.ui.form.ListBox",
						"optionitem" => "system.web.ui.form.OptionItem",
						"passwordfield" => "system.web.ui.form.PasswordField",
						"radiobutton" => "system.web.ui.form.RadioButton",
						"radiogroup" => "system.web.ui.form.RadioGroup",
						"reset" => "system.web.ui.form.Reset",
						"select" => "system.web.ui.form.Select",
						"submit" => "system.web.ui.form.Submit",
						"textbox" => "system.web.ui.form.TextBox",
						"uploadmanager" => "system.web.ui.form.UploadManager",
						
						//image
						"image" => "system.web.ui.image.Image",
						"imagemap" => "system.web.ui.image.ImageMap",
						"maparea" => "system.web.ui.image.MapArea",
						
						//page
						"body" => "system.web.ui.page.Body",
						"head" => "system.web.ui.page.Head",
						"page" => "system.web.ui.page.Page",
						"documentfragment" => "system.web.ui.page.DocumentFragment",
						
						// table
						"columnset" => "system.web.ui.table.ColumnSet",
						"rowset" => "system.web.ui.table.RowSet",
						"table" => "system.web.ui.table.Table",
						"tablecell" => "system.web.ui.table.TableCell",
						"tablecolumn" => "system.web.ui.table.TableColumn",
						"tablerow" => "system.web.ui.table.TableRow");
						
	if(array_key_exists(strtolower($classname), $classes))
		return $classes[strtolower($classname)];
	
	return "not found";
}

function createCodeFile($name, $phpTags, $usedComponents)
{
	$content = "";
	
	$comments1 = '
/**
 * Descrição breve do arquivo
 * 
 * Descrição longa do arquivo
 *
 * LICENÇA: Informações sobre a licença
 *
 * @author 		[NAME]
 * @copyright  	2007 Neoconn Networks
 * @license    	http://baze.saulovallory.com/license
 * @version    	SVN: $Id$
 * @link       	http://intranet.neoconn.com/[PROJECT_NAME]/docs/
 * @since      	[VERSION]
 */
';
	
	$comments2 = '
/**
 * Descrição breve da classe
 * 
 * Descrição longa da classe
 *
 * LICENÇA: Informações sobre a licença
 *
 * @author 		[NAME]
 * @copyright  	2007 Neoconn Networks
 * @license    	http://baze.saulovallory.com/license
 * @version    	Release: @package_version@
 * @link       	http://intranet.neoconn.com/[PROJECT_NAME]/docs/
 * @since      	[VERSION]
 */
';
	
	$content = '<?php' . _NL . $comments1 . _NL;
	
	foreach($usedComponents as $comp)
	{
		$content .= 'import("'.(($pack = getQName($comp)) != "not found" ? $pack : "" ).'");'._NL;
	}
	
	$content .= 'import("system.web.ui.page.Page");'._NL;
	
	$content .= _NL . $comments2 . _NL . 
				"class ".ucfirst($name)." extends Page" . _NL .
				"{" . _NL;
	
	foreach($phpTags as $tag)
		$content .= $tag->getCode() . _NL;
	
	$content .= "	public function Page_Init() {" . _NL . "	}" . _NL . _NL;
	
	$content .= "}" . _NL . "?>";
	
	return $content;
}

//echo "</pre>";

?>