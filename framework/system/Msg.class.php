<?php

/**
 * Enumeration of all framework messages
 *
 * Note: All tokens that hold place for received values must be prefixed with "rec_" (like in {@see Msg::HttpResponse_status_reason_missing}})
 */
class Msg // extends Enumeration
{
	const generic_invalid_argument = '';

	const Collection_ModifingReadOnly = 'Collection_ModifingReadOnly';

	const HttpResponse_invalid_cache_limiter = 'HttpResponse cache limiter value must be one among "none", "nocache", "private", "private_no_expire" and "public". The value received was "{{rec_value}}".';
	const HttpResponse_buffer_output_unchangeable = 'Buffer output cannot be changed. Output already started in {{file_path}} at line {{line_num}}.';
	const HttpResponse_status_reason_missing = 'Http status reason is missing. Neither a reason was passed or the code {{rec_code}} has a default one .';
	const HttpResponse_status_reason_barchars = 'Http status reason must not contain bar chars like \r and \n';

	const System_application_not_found = 'System_application_not_found';

	const Import_NamespaceNotFound = 'The namespace {{ns}} couldn\'t be found.';
	const Import_InvalidPath = 'Invalid import path "{{path}}"';
	const Import_ClassNotFound = 'The class {{qName}} could not be found.';
	const Import_PackageNotFound = 'The package {{pkg}} could not be found.';

	// Argument
	const InvalidEmptyArgument = 'The {{argument name}} is required and cannot be empty.';
	const ArgumentTypeMismatch = 'The argument "{{arg name}} should be a {{arg type}} but a {{type}} was given."';
	const IndexOutOfBounds = 'The index {{0}} is out of bounds.';

	// Access
	const UndefinedProperty = 'Undefined property {{property}}.';
	const ReadOnlyProperty = 'The property {{property}} is read only.';

	const EventNotSupported = 'The event {{evt name}} is not supported by {{class}}.';
	const NotImplemented = 'The method {{method}} is not implemented yet. But I\'m sure it\'ll be soon!';

	// Parser
	const DuplicatedComponentId = 'The page {{page}} already has a component or a property named "{{id}}".';
	const MalformedTag = 'The tag "{{tag}}" is invalid';
	const EmptyXmlDoc = 'The xml source is empty';
	const ParseChildInvalidReturn = 'The method {{component}}::parseChild() must return the child component created (an instance of PageComponent). The return was {{return}}';

	// IO
	const InvalidFolderPath = 'The path "{{path}}" is not a folder or doesn\'t exists.';
	const FileNotFound = 'The file "{{file}}" could not be found.';
	const FileCanNotBeRead = 'The file "{{file}}" cannnot be read.';

	// Config
	const InvalidNamespacePath = 'The application {{app}} is misconfigured. The namespace {{ns}} is configured to a the path "{{path}}" that isn\'t a folder or doesn\'t exists.';

	// Page
	const PageInvalidChild = 'Page only accepts Head and Body components as child. A {{0}} was given.';
}