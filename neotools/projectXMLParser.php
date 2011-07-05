<?php
/* Exemplo de atributo
 * 
<Model composite="true" considerDefaultProperties="false" displayModelType="Attribute" id="qb.iGkiHgyxQIsHR" modelType="Attribute" name="id">
	<ModelProperties>
		<StringProperty displayName="Name" name="name" value="id"/>
		<StringProperty displayName="Visibility" name="visibility" value="protected"/>
		<StringProperty displayName="Scope" name="scope" value="instance"/>
		<TextModelProperty displayName="Type" name="type">
			<ModelRef id="QSU.IkiD.AAAAQN2"/>
		</TextModelProperty>
		<StringProperty displayName="Initial Value" name="initialValue" value=""/>
		<HTMLProperty displayName="Documentation" name="documentation" plainTextValue="&#10;Object id.&#10;@var string"/>
		<BooleanProperty displayName="Has Setter" name="hasSetter" value="false"/>
		<BooleanProperty displayName="Has Getter" name="hasGetter" value="false"/>
		<ModelProperty displayName="Java Detail" name="javaDetail">
			<Model composite="true" considerDefaultProperties="false" displayModelType="Java Attribute Code Details" id="wqukaMiGAqAAAgp0" modelType="JavaAttributeCodeDetail" name="">
				<ModelProperties>
					<StringProperty displayName="Name" name="name" value=""/>
					<StringProperty displayName="Model Type" name="modelType" value="JavaAttributeCodeDetail"/>
					<BooleanProperty displayName="Java Final" name="javaFinal" value="true"/>
					<BooleanProperty displayName="Java Transient" name="javaTransient" value="true"/>
					<BooleanProperty displayName="Java Volatile" name="javaVolatile" value="true"/>
					<BooleanProperty displayName="Indexer" name="indexer" value="false"/>
					<StringProperty displayName="Indexer Parameter List" name="indexerParameterList"/>
				</ModelProperties>
			</Model>
		</ModelProperty>
	</ModelProperties>
</Model>


Type definition
		<Model composite="false" considerDefaultProperties="false" displayModelType="Data Type" id="QSU.IkiD.AAAAQN2" modelType="DataType" name="string">
			<ModelProperties>
				<StringProperty displayName="Name" name="name" value="string"/>
				<StringProperty displayName="Model Type" name="modelType" value="DataType"/>
				<ModelRefsProperty displayName="Stereotypes" name="stereotypes"/>
				<ModelProperty displayName="Tagged Values" name="taggedValues"/>
				<ModelsProperty displayName="Comments" name="comments"/>
				<HTMLProperty displayName="Documentation" name="documentation" plainTextValue="" value=""/>
				<ModelsProperty displayName="References" name="references"/>
			</ModelProperties>
		</Model>

Type Property 
		<TextModelProperty displayName="Type" name="type">
			<ModelRef id="QSU.IkiD.AAAAQN2"/>
		</TextModelProperty>
	ou
		<TextModelProperty displayName="Type" name="type">
			<StringValue value="InlineStyle"/>
		</TextModelProperty>
*/

function parseAttribute($attNode, $xPath)
{
	$att = new MetaAttribute($attNode->getAttribute('name'));
	
	//visibility
	$nl = $xPath->query('ModelProperties/StringProperty[@name="visibility"]',$attNode);
	if($nl->length > 0 ) {
		$att->visibility = $nl->item(0)->getAttribute('value');
	} 
	
	// type
	$nl = $xPath->query('ModelProperties/TextModelProperty[@name="type"]',$attNode);
	if($nl->length > 0 && $nl->item(0)->childNodes->length > 0)
	{
		$child = $nl->item(0)->firstChild;
		
		if($child->localName == 'ModelRef')
		{
			$typeId = $child->getAttribute('id');
		
			$nl2 = $xPath->query('//Model[@id="' . $typeId . '"]');
			
			if($nl2->length > 0)
			{
				$att->type = $nl2->item(0)->getAttribute('name');
			}
		}
		else
		{
			$att->type = $child->getAttribute('value');
		}
	}

	//initialValue
	$nl = $xPath->query('ModelProperties/StringProperty[@name=\'initialValue\']',$attNode);
	if($nl->length > 0 ) {
		$att->initialValue = $nl->item(0)->getAttribute('value');
	} 

	// isFinal (BooleanProperty javaFinal)

	$nl = $xPath->query('ModelProperties/ModelProperty[@name="javaDetail"]/Model/ModelProperties/BooleanProperty[@name="javaFinal"]',$attNode);
	if($nl->length > 0 && $nl->item(0)->getAttribute('value') == 'true')
		$att->isFinal = true;
	else
		$att->isFinal = false;
	
	// scope
	$nl = $xPath->query('ModelProperties/StringProperty[@name="scope"]',$attNode);
	if($nl->length > 0 ) {
		$att->scope = $nl->item(0)->getAttribute('value');
	} 

	// documentation
	$nl = $xPath->query('ModelProperties/HTMLProperty[@name=\'documentation\']',$attNode);
	if($nl->length > 0 ) {
		$att->doc = $nl->item(0)->getAttribute('plainTextValue');
	}
	
	// hasSetter
	$nl = $xPath->query('ModelProperties/BooleanProperty[@name=\'hasSetter\']',$attNode);
	if($nl->length > 0 && $nl->item(0)->getAttribute('value') == 'true')
		$att->hasSetter = true;
	else
		$att->hasSetter = false;
	
	// hasGetter 
	$nl = $xPath->query('ModelProperties/BooleanProperty[@name=\'hasGetter\']',$attNode);
	if($nl->length > 0 && $nl->item(0)->getAttribute('value') == 'true')
		$att->hasGetter = true;
	else
		$att->hasGetter = false;
	
	return $att;
}

/*
<Model composite="true" considerDefaultProperties="false" displayModelType="Operation" id="lIseiMiGAqAAArkL" modelType="Operation" name="getChildren">
	<ModelProperties>
		<StringProperty displayName="Name" name="name" value="getChildren"/>
		<BooleanProperty displayName="Static" name="static" value="false"/>
		<StringProperty displayName="Visibility" name="visibility" value="public"/>
		<HTMLProperty displayName="Documentation" name="documentation" plainTextValue="" value=""/>
		<TextModelProperty displayName="Return Type" name="returnType"/>
		<StringProperty displayName="Scope" name="scope" value="instance"/>
		<BooleanProperty displayName="Abstract" name="abstract" value="false"/>
	</ModelProperties>
</Model>
 */

function parseMethod($methNode, $xPath)
{
	$meth = new MetaMethod($methNode->getAttribute('name'));
	
	//visibility
	$nl = $xPath->query('ModelProperties/StringProperty[@name="visibility"]',$methNode);
	if($nl->length > 0 ) {
		$meth->visibility = $nl->item(0)->getAttribute('value');
	} 
	
	// returnType
	$nl = $xPath->query('ModelProperties/TextModelProperty[@name="returnType"]',$methNode);
	if($nl->length > 0 && $nl->item(0)->childNodes->length > 0)
	{
		$child = $nl->item(0)->firstChild;
		
		if($child->localName == 'ModelRef')
		{
			$typeId = $child->getAttribute('id');
		
			$nl2 = $xPath->query('//Model[@id="' . $typeId . '"]');
			
			if($nl2->length > 0)
			{
				$meth->type = $nl2->item(0)->getAttribute('name');
			}
		}
		else
		{
			$meth->type = $child->getAttribute('value');
		}
	}

	// isFinal (BooleanProperty javaFinal)

	$nl = $xPath->query('ModelProperties/ModelProperty[@name="javaDetail"]/Model/ModelProperties/BooleanProperty[@name="javaFinal"]',$methNode);
	if($nl->length > 0)
		$meth->isFinal = ($nl->item(0)->getAttribute('value') == 'true');
		
	
	// isAbstract
	$nl = $xPath->query('ModelProperties/BooleanProperty[@name="abstract"]',$methNode);
	if($nl->length > 0 ) {
		$meth->isAbstract = ($nl->item(0)->getAttribute('value') == 'true');
	}
	
	// scope
	$nl = $xPath->query('ModelProperties/StringProperty[@name="scope"]',$methNode);
	if($nl->length > 0 ) {
		$meth->scope = $nl->item(0)->getAttribute('value');
	} 

	// documentation
	$nl = $xPath->query('ModelProperties/HTMLProperty[@name=\'documentation\']',$methNode);
	if($nl->length > 0 ) {
		$meth->doc = $nl->item(0)->getAttribute('plainTextValue');
	}
	
	// Parameters

	$params = $xPath->query('ChildModels/Model[@modelType="Parameter"]', $methNode);

	for($i=0, $length=$params->length; $i < $length ; $i++)
	{
		$pParam = parseParam($params->item($i), $xPath);
		$meth->parameters[] = $pParam;
	}
	
	return $meth;
}

function parseParam($paramNode, $xPath)
{
	$att = parseAttribute($paramNode,$xPath);
	
	$param = new MetaParameter($att->name, $att->visibility, $att->type);
	
	$param->isFinal = $att->isFinal;
	$param->scope = $att->scope;
	$param->doc = $att->doc;

	//initialValue
	$nl = $xPath->query('ModelProperties/StringProperty[@name="defaultValue"]',$paramNode);
	if($nl->length > 0 ) {
		$param->defaultValue = $nl->item(0)->getAttribute('value');
	}
	
	if($param->type == 'array')
	{
		$param->typeHint = true;
	}
	else
	{
		$nl = $xPath->query('//Model[@modelType="Class" and @name="'.$param->type.'"]');
		
		if($nl->length > 0)
			$param->typeHint = true;
		else
			$param->typeHint = false;
	}
		
	return $param;
}