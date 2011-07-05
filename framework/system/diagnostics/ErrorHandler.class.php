<?php

require_once 'PhpErrorException.class.php';
/*
 * Interesses quando um erro ocorre
 *
 * - Exibir uma mensagem que ajude a resolver (debug/development)
 * - Exibir uma mensagem amigável (production)
 * - armazenar no log
 * - mandar por email
 * - RSS (secure RSS?)
 */

class ErrorHandler
{
	const SOURCE_LINES_AROUND = 5;

	/**
	 * Enter description here...
	 *
	 * @param Zend_Config $config
	 */
	public static function init($config = null)
	{
		define('_CONSOLE', 'c');
		define('_ENVIRONMENT', _CONSOLE);

		if(_ENVIRONMENT == _CONSOLE)
			error_reporting(E_ALL);
		else
			error_reporting(E_ALL & ~E_STRICT);

		set_error_handler(Array('ErrorHandler','handleError'));
		set_exception_handler(array('ErrorHandler', 'handleException'));
	}

	/**
	 * Handles a PHP error
	 *
	 * @param int		$errno		numero do erro
	 * @param string	$errmsg		mensagem de erro
	 * @param string	$filename	nome do arquivo
	 * @param int		$linenum	número da linha
	 * @param array		$envVars	diversas variáveis do sistema
	 *
	 * @return void
	 */
	public static function handleError($code, $msg, $file, $line, $envVars)
	{
		global $ex;

		$ex = new PhpErrorException($msg);

		$ex->setCode($code);
		$ex->setGuiltyFile($file);
		$ex->setGuiltyLine($line);
		$ex->setEnvVars($envVars);
		$ex->setSourceLines(self::getSourceCode($file, $line));
		
		if(defined('_IS_POSTBACK') && _IS_POSTBACK) {
			echo "Error: $msg \nAt $file line $line";
			debug_print_backtrace();
		}
		else
			require(dirname(dirname(__FILE__)).'/exceptions/templates/debug.php');
			
		exit(-1);
	}

	/**
	 * Handles exceptions
	 *
	 * @param Exception $e
	 * @return void
	 */
	public static function handleException(Exception $e)
	{
		global $ex, $source;

		if(!($e instanceof BazeException))
			$e = BazeException::fromException($e);

		$ex = $e;
		$ex->setSourceLines(self::getSourceCode($e->getGuiltyFile(), $e->getGuiltyLine()));

		if(defined('_IS_POSTBACK') && _IS_POSTBACK) {
			echo "Error: ".$ex->getMessage()." \nAt ".$ex->getFile()." line ".$ex->getLine();
			debug_print_backtrace();
		}
		else
			require(dirname(dirname(__FILE__)).'/exceptions/templates/debug.php');
		
		exit(-1);
	}

	private static function getSourceCode($file, $errorLine)
	{
		if(is_file($file))
		{
			$lines = file($file);

			$start = $errorLine - self::SOURCE_LINES_AROUND > 0 ? $errorLine - self::SOURCE_LINES_AROUND : 0;
			$end = isset($lines[$errorLine + self::SOURCE_LINES_AROUND]) ? $errorLine + self::SOURCE_LINES_AROUND : count($lines);

			$arr = array_slice($lines, $start, $end - $start, true);

			if(count($arr) == 0)
				return $arr;

			foreach ($arr as $n => $ln)
				$arr2[$n+1] = $ln;

			return $arr2;
		}

		return array();
	}

    /**
     * @return String
     * @desc Retorna o nome do tipo do erro
     */
    public static function getErrorDescription($errorNumber)
	{
        // Define uma matriz associativa com as strings dos erros
        $desErro = array(
                         1    => "Error",
                         2    => "Warning",
                         4    => "Parsing Error",
                         8    => "Notice",
						 16   => "Core Error",
                         32   => "Core Warning",
						 64   => "Compile Error",
                         128  => "Compile Warning",
                         256  => "User Error",
                         512  => "User Warning",
                         1024 => "User Notice",
                         2048 => "Change suggestion"
                        );
        return $desErro[$errorNumber];
    }

    /**
     * Function LogError
     *
     * @return void
     *
     * @desc Creates the message, calls the methods writeLogFile and sendEmailAlert
     */
    public function logError()
	{
        $mensagem = str_replace(NL,"",$this->getErrorMessage());

        $logContent  = "<error><q>";
		$logContent .= 	"\t<datetime>". date("d-m-Y H:i:s") . "</date><q>";
		$logContent .= 	"\t<errorNumber>" . $this->getErrorNumber() . "</errorNumber><q>";
		$logContent .= 	"\t<errorType>" . self::getErrorDescription($this->getErrorNumber()) . "</errorType><q>";
		$logContent .= 	"\t<scriptName>" . $this->getLogFileName() . "</scriptName><q>";
		$logContent .= 	"\t<scriptLineNum>" . $this->getErrorLine() . "</scriptLineNum><q>";
		$logContent .= 	"\t<errorMsg><q>\t" . $mensagem . "<q>\t</errorMsg><q>";

		if (in_array($this->getErrorNumber(), $this->userErrors))
		{
			/* to enable vartrace replace "disabled" by: wddx_serialize_value($this->systemVars, "Variables")*/
			$logContent .= "\t<vartrace> disabled </vartrace><q>";
		}

		$logContent .= "</error><q>";

        if(!file_exists(ErrorHandler::$errorSubDir))
		{
            clearstatcache();

			if(!mkdir(ErrorHandler::$errorSubDir,0777,true))
			{
				echo "System was unable to create error logs directory. Please check permissions in site root.";
				exit(-1);
			}
        }

        $file = ErrorHandler::$errorSubDir . date("Y-m-d") . ".txt";
        clearstatcache();

        // gravando no arquivo de log
        $this->writeLogFile($file,$logContent);

        $logContent = str_replace("<q>","<br />".NL,$logContent);
        $logContent = str_replace("<l>","<hr />".NL,$logContent);

        // enviar somente um e-mail de aviso por dia que acontecer algum erro
        if (!(file_exists($file)))
		{
            $this->sendEmailAlert($logContent);
        }
	}

    /**
     * Function WriteLogFile
     *
     * @return void
     * @param string $path		The path in the server where the file log is or should be created
     * @param string $content	The content to be writed in the file
     *
     * @desc Write the received content in the log file.
     */
    public function writeLogFile($path, $content)
	{
        $content = str_replace("<q>",NL, $content);
        $handle = fopen($path, "a");

		if(!file_exists($path))
		{
			echo "System was unable to create the log file. Please check permissions in errors log directory.";
			exit(-1);
		}

        fwrite($handle, $content);
    }

    /**
     * @return void
     * @param string $content        Mensagem de Log
     * @desc Envia o E-mail.
     */
    public function sendEmailAlert($content)
	{
        //mail("saulovallory@neoconn.com","Erro no Sistema",$content,"from:....");
    }

    /**
     * @return void
     * @desc Redireciona o usuário se necessário e possível
     */
    public function redirect()
	{
		//header("location:" . ErrorHandler::$redirectURL);
    }
}