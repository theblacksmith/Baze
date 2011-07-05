<?php


/**
 * - implement basic algorithms to avoid session impersonation
 *
 *
 */
class User
{
	/**
	 * Enter description here...
	 *
	 * @param HttpRequest $sessionId
	 */
	public function factory(HttpRequest $req)
	{
		$cookies = $req->getCookies();


	}
}