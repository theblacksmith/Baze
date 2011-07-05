<?php

class NotImplementedException extends BazeException
{
	public function __construct($message=null, $tokens = array(), $guiltyStep = 0)
	{
		if($message == null)
			$message = Msg::NotImplemented;

		$step = BackTrace::step(1);
		$tokens['method'] = $step['function'];

		parent::__construct($message, $tokens, $guiltyStep);
	}
}

?>