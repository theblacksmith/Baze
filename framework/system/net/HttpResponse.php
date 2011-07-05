<?php
/**
 * HttpResponse class
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2008 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id: HttpResponse.php 2541 2008-10-21 15:05:13Z qiang.xue $
 * @package System.Web
 */

import('system.io.IOutputWriter');

/**
 * HttpResponse class
 *
 * HttpResponse implements the mechanism for sending output to client users.
 *
 * To output a string to client, use {@link write()}. By default, the output is
 * buffered until {@link flush()} is called or the application ends. The output in
 * the buffer can also be cleaned by {@link clear()}. To disable output buffering,
 * set BufferOutput property to false.
 *
 * To send cookies to client, use {@link getCookies()}.
 * To redirect client browser to a new URL, use {@link redirect()}.
 * To send a file to client, use {@link writeFile()}.
 *
 * By default, HttpResponse is registered with {@link TApplication} as the
 * response module. It can be accessed via {@link TApplication::getResponse()}.
 *
 * HttpResponse may be configured in application configuration file as follows
 *
 * <module id="response" class="system.web.ui.HttpResponse" CacheExpire="20" CacheControl="nocache" BufferOutput="true" />
 *
 * where {@link getCacheExpire CacheExpire}, {@link getCacheControl CacheControl}
 * and {@link getBufferOutput BufferOutput} are optional properties of HttpResponse.
 *
 * HttpResponse sends charset header if either {@link setCharset() Charset}
 * or {@link TGlobalization::setCharset() TGlobalization.Charset} is set.
 *
 * Since 3.1.2, HTTP status code can be set with the {@link setStatusCode StatusCode} property.
 *
 * Note: Some HTTP Status codes can require additional header or body information. So, if you use {@link setStatusCode StatusCode}
 * in your application, be sure to add theses informations.
 * E.g : to make an http authentication :
 * <code>
 *  public function clickAuth ($sender, $param)
 *  {
 *     $response=$this->getResponse();
 *     $response->setStatusCode(401);
 *     $response->appendHeader('WWW-Authenticate: Basic realm="Test"');
 *  }
 * </code>
 *
 * This event handler will sent the 401 status code (Unauthorized) to the browser, with the WWW-Authenticate header field. This
 * will force the browser to ask for a username and a password.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: HttpResponse.php 2541 2008-10-21 15:05:13Z qiang.xue $
 * @package System.Web
 * @since 3.0
 */
class HttpResponse implements IOutputWriter
{
	/**
	 * @var boolean whether to buffer output
	 */
	private $bufferOutput=true;

	/**
	 * @var integer response status code.
	 */
	private $status = 200;

	/**
	 * @var string reason correspond to status code. One of HttpStatusCode constants.
	 */
	private $reason = 'OK';

	/**
	 * @var string character set, e.g. UTF-8
	 */
	private $charset = '';

	/**
	 * @var string content type
	 */
	private $contentType = null;

	/**
	 * @var HttpCookieCollection list of cookies to return
	 */
	private $_cookies = null;

	/**
	 * @var string The response content
	 */
	private $content = '';

	/**
	 * @var boolean Whether the output has started or not
	 */
	private $outputStarted = false;

	/**
	 * @return integer time-to-live for cached session pages in minutes, this has no effect for nocache limiter. Defaults to 180.
	 */
	public function getCacheExpire()
	{
		return session_cache_expire();
	}

	/**
	 * @param integer time-to-live for cached session pages in minutes, this has no effect for nocache limiter.
	 */
	public function setCacheExpire($value)
	{
		session_cache_expire((int)$value);
	}

	/**
	 * @return string cache control method to use for session pages
	 */
	public function getCacheControl()
	{
		return session_cache_limiter();
	}

	/**
	 * @param string cache control method to use for session pages. Valid values
	 *               include none/nocache/private/private_no_expire/public
	 */
	public function setCacheControl($value)
	{
		if(!in_array($value, array('none','nocache','private','private_no_expire','public')))
			throw new InvalidArgumentValueException('value', $value, Msg::HttpResponse_invalid_cache_limiter);

		session_cache_limiter($value);
	}

	/**
	 * @return string current content type
	 */
	public function getContentType()
	{
		return $this->contentType;
	}

	/**
	 * @return string content type, default is text/html
	 */
	public function setContentType($type)
	{
		$this->contentType = $type;
	}

	/**
	 * @return string output charset.
	 */
	public function getCharset()
	{
		return $this->charset;
	}

	/**
	 * @param string output charset.
	 */
	public function setCharset($charset)
	{
		$this->charset = $charset;
	}

	/**
	 * @return boolean whether to enable output buffer
	 */
	public function getBufferOutput()
	{
		return $this->bufferOutput;
	}

	/**
	 * @param boolean whether to enable output buffer
	 * @throws InvalidOperationException if the output was already started
	 */
	public function setBufferOutput($value)
	{
		if($value && headers_sent($file, $line))
			throw new InvalidOperationException(Msg::HttpResponse_buffer_output_unchangeable, array($file, $line));
		else
		{
			if($this->bufferOutput)
				$this->flush();

			$this->bufferOutput = (boolean)$value;
		}
	}

	/**
	 * @return integer HTTP status code, defaults to 200
	 */
	public function getStatusCode()
	{
		return $this->status;
	}

	/**
	 * @param string HTTP status reason
	 */
	public function getStatusReason() {
		return $this->reason;
	}

	/**
	 * Set the HTTP status code for the response.
	 * The code and its reason will be sent to client using the currently requested http protocol version (see {@link THttpRequest::getHttpProtocolVersion})
	 * Keep in mind that HTTP/1.0 clients might not understand all status codes from HTTP/1.1
	 *
	 * @param integer $code HTTP status code
	 * @param string $reason HTTP status reason, defaults to standard HTTP reasons
	 */
	public function setStatus($code, $reason = null)
	{
		$code = (int)$code;

		if(defined("HttpStatusCode::_$code")) {
			$this->reason = constant("HttpStatusCode::_$code");
		}
		else
		{
			if($reason === null || trim($reason) === '') {
				throw new InvalidArgumentValueException('reason', $reason, Msg::HttpResponse_status_reason_missing, array($code));
			}

			$reason = (string)$reason;


			if(strpos($reason, "\r") != false || strpos($reason, "\n") != false) {
				throw new InvalidArgumentValueException(Msg::HttpResponse_status_reason_barchars);
			}

			$this->reason = $reason;
		}

		$this->status = $status;
	}

	/**
	 * Redirects the browser to the specified URL.
	 * The current application will be terminated after this method is invoked.
	 * @param string URL to be redirected to. If the URL is a relative one, the base URL of
	 * the current request will be inserted at the beginning.
	 */
	public function redirect($url)
	{
		$this->appendHeader('Location: '.str_replace('&amp;','&',$url));
	}

	/**
	 * Reloads the current page.
	 * The effect of this method call is the same as user pressing the
	 * refresh button on his browser (without post data).
	 **/
	public function reload()
	{
		$this->redirectTo();
	}

	/**
	 * @return HttpCookieCollection list of output cookies
	 */
	public function getCookies()
	{
		if($this->_cookies === null)
			$this->_cookies= new HttpCookieCollection();

		return $this->_cookies;
	}

	/**
	 * Outputs a string.
	 * It may not be sent back to user immediately if output buffer is enabled.
	 * @param string string to be output
	 */
	public function write($str)
	{
		if($this->bufferOutput)
			$this->content .= $str;
		else
			echo $str;
	}

	/**
	 * Sends a file back to user.
	 * Make sure not to output anything else after calling this method.
	 * @param string file name
	 * @param string content to be set. If null, the content will be read from the server file pointed to by $fileName.
	 * @param string mime type of the content.
	 * @param array list of headers to be sent. Each array element represents a header string (e.g. 'Content-Type: text/plain').
	 * @throws TInvalidDataValueException if the file cannot be found
	 */
	public function writeFile($fileName,$content=null,$mimeType=null,$headers=null)
	{
		static $defaultMimeTypes=array(
			'css'=>'text/css',
			'gif'=>'image/gif',
			'jpg'=>'image/jpeg',
			'jpeg'=>'image/jpeg',
			'htm'=>'text/html',
			'html'=>'text/html',
			'js'=>'javascript/js',
			'pdf'=>'application/pdf',
			'xls'=>'application/vnd.ms-excel',
		);

		if($mimeType===null)
		{
			$mimeType='text/plain';
			if(function_exists('mime_content_type'))
				$mimeType=mime_content_type($fileName);
			else if(($ext=strrchr($fileName,'.'))!==false)
			{
				$ext=substr($ext,1);
				if(isset($defaultMimeTypes[$ext]))
					$mimeType=$defaultMimeTypes[$ext];
			}
		}
		$fn=basename($fileName);
		$this->sendHttpHeader();
		if(is_array($headers))
		{
			foreach($headers as $h)
				header($h);
		}
		else
		{
			header('Pragma: public');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		}
		header("Content-type: $mimeType");
		header('Content-Length: '.($content===null?filesize($fileName):strlen($content)));
		header("Content-Disposition: attachment; filename=\"$fn\"");
		header('Content-Transfer-Encoding: binary');
		if($content===null)
			readfile($fileName);
		else
			echo $content;
	}

	/**
	 * Flush the response contents and headers.
	 */
	public function flush()
	{
		if($this->bufferOutput) {
			$this->sendHeaders();
			echo $this->content;
		}
	}

	/**
	 * Send the HTTP header with the status code (defaults to 200) and
	 * status reason (defaults to OK), content type header if charset is not empty,
	 * and all buffered headers.
	 */
	protected function sendHeaders()
	{
		if (($version = System::getApp()->getRequest()->Protocol) === '')
			header (' ', true, $this->status);
		else
			header($version.' '.$this->status.' '.$this->reason, true, $this->status);

		$charset = $this->charset;
		$contentType = $this->contentType;

		$header = '';

		if($contentType == '' && ($contentType = System::getApp()->Config->DefaultContentType) !== null)
			$header .= 'Content-Type: '.$contentType;

		if($charset === '' && ($charset = System::getApp()->Config->DefaultCharset) !== null)
			$header .= ';charset='.$charset;

		if($header != '')
			$this->appendHeader($header);
	}

	/**
	 * Returns the content in the output buffer.
	 * The buffer will NOT be cleared after calling this method.
	 * Use {@link clear()} is you want to clear the buffer.
	 *
	 * @return string output that is in the buffer.
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * Clears any existing buffered content.
	 */
	public function clear()
	{
		$this->content = '';
	}

	/**
	 * Sends a header.
	 *
	 * @param string $str
	 * @param bool $replace
	 * @param int $http_response_code
	 */
	public function appendHeader($str, $replace=null, $http_response_code=null)
	{
		if($this->bufferOutput)
			$this->headers[] = array($str, $replace, $http_response_code);
		else
			header($str, $replace, $http_response_code);
	}

	/**
	 * Sends a cookie.
	 *
	 * @param HttpCookie cook to be sent
	 */
	public function addCookie(HttpCookie $cookie)
	{
		setcookie($cookie->getName(),$cookie->getValue(),$cookie->getExpire(),$cookie->getPath(),$cookie->getDomain(),$cookie->getSecure());
		$this->_cookies->add($cookie);
	}

	/**
	 * Deletes a cookie.
	 *
	 * @param THttpCookie cook to be deleted
	 */
	public function removeCookie(HttpCookie $cookie)
	{
		setcookie($cookie->getName(),null,0,$cookie->getPath(),$cookie->getDomain(),$cookie->getSecure());
		$this->_cookies->remove($cookie);
	}
}

class HttpStatusCode
{
	const _100 = 'Continue';
	const _101 = 'Switching Protocols';

	const _200 = 'OK';
	const _201 = 'Created';
	const _202 = 'Accepted';
	const _203 = 'Non-Authoritative Information';
	const _204 = 'No Content';
	const _205 = 'Reset Content';
	const _206 = 'Partial Content';

	const _300 = 'Multiple Choices';
	const _301 = 'Moved Permanently';
	const _302 = 'Found';
	const _303 = 'See Other';
	const _304 = 'Not Modified';
	const _305 = 'Use Proxy';
	const _307 = 'Temporary Redirect';

	const _400 = 'Bad Request';
	const _401 = 'Unauthorized';
	const _402 = 'Payment Required';
	const _403 = 'Forbidden';
	const _404 = 'Not Found';
	const _405 = 'Method Not Allowed';
	const _406 = 'Not Acceptable';
	const _407 = 'Proxy Authentication Required';
	const _408 = 'Request Time-out';
	const _409 = 'Conflict';
	const _410 = 'Gone';
	const _411 = 'Length Required';
	const _412 = 'Precondition Failed';
	const _413 = 'Request Entity Too Large';
	const _414 = 'Request-URI Too Large';
	const _415 = 'Unsupported Media Type';
	const _416 = 'Requested range not satisfiable';
	const _417 = 'Expectation Failed';

	const _500 = 'Internal Server Error';
	const _501 = 'Not Implemented';
	const _502 = 'Bad Gateway';
	const _503 = 'Service Unavailable';
	const _504 = 'Gateway Time-out';
	const _505 = 'HTTP Version not supported';
}