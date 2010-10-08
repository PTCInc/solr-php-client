<?php
/**
 * Copyright (c) 2007-2010, Conduit Internet Technologies, Inc.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *  - Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *  - Neither the name of Conduit Internet Technologies, Inc. nor the names of
 *    its contributors may be used to endorse or promote products derived from
 *    this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @copyright Copyright 2007-2010 Conduit Internet Technologies, Inc. (http://conduit-it.com)
 * @license New BSD (http://solr-php-client.googlecode.com/svn/trunk/COPYING)
 * @version $Id: $
 *
 * @package Apache
 * @subpackage Solr
 * @author Timo Schmidt <timo.schmidt@aoemedia.de>, Donovan Jimenez <djimenez@conduit-it.com>
 */

// Require Apache_Solr_HttpTransport_Abstract
require_once(dirname(__FILE__) . '/Abstract.php');

/**
 *
 */
class Apache_Solr_HttpTransport_Curl extends Apache_Solr_HttpTransport_Abstract
{
	/**
	 * SVN Revision meta data for this class
	 */
	const SVN_REVISION = '$Revision:$';

	/**
	 * SVN ID meta data for this class
	 */
	const SVN_ID = '$Id:$';
	
	/**
	 * Curl Session Handle
	 * 
	 * @var resource
	 */
	private $_curl;
	
	/**
	 * Initializes a curl session
	 */
	public function __construct()
	{
		// initialize a CURL session
		$this->_curl = curl_init();
		
		// set common options that will not be changed during the session
		//   first, return the response body from curl_exec
		curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, true);
		
		//  second, get the binary output
		curl_setopt($this->_curl, CURLOPT_BINARYTRANSFER, true);
		
		// third, we do not need the headers in the output, we get everything we need from meta data
		curl_setopt($this->_curl, CURLOPT_HEADER, false);
	}
		
	/**
	 * Closes a curl session
	 */
	function __destruct()
	{
		// close our curl session
		curl_close($this->_curl);
	}
	
	public function performGetRequest($url, $timeout = false)
	{
		// make sure we're returning the body
		curl_setopt($this->_curl, CURLOPT_NOBODY, false);
		
		// make sure we're GET
		curl_setopt($this->_curl, CURLOPT_HTTPGET, true);
		
		// set the URL
		curl_setopt($this->_curl, CURLOPT_URL, $url);
		
		// set the timeout if specified
		if ($timeout !== FALSE && $timeout > 0.0)
		{
			curl_setopt($this->_curl, CURLOPT_TIMEOUT, $timeout);
		}
		else
		{
			curl_setopt($this->_curl, CURLOPT_TIMEOUT, $this->getDefaultTimeout());
		}
		
		// make the request
		$responseBody = curl_exec($this->_curl);
		
		// get info from the transfer
		list($statusCode, $contentType, $encoding) = $this->_getHeaderInformation();
				
		return new Apache_Solr_HttpTransport_Response($statusCode, null, $responseBody, $contentType, $encoding);
	}	
	
	public function performHeadRequest($url, $timeout = false)
	{
		// this both sets the method to HEAD and says not to return a body
		curl_setopt($this->_curl, CURLOPT_NOBODY, true);
		
		// set the URL
		curl_setopt($this->_curl, CURLOPT_URL, $url);
		
		// set the timeout if specified
		if ($timeout !== FALSE && $timeout > 0.0)
		{
			curl_setopt($this->_curl, CURLOPT_TIMEOUT, $timeout);
		}
		else
		{
			curl_setopt($this->_curl, CURLOPT_TIMEOUT, $this->getDefaultTimeout());
		}
		
		// make the request
		$responseBody = curl_exec($this->_curl);
		
		// get info from the transfer
		list($statusCode, $contentType, $encoding) = $this->_getHeaderInformation();
				
		return new Apache_Solr_HttpTransport_Response($statusCode, null, $responseBody, $contentType, $encoding);
	}
	
	public function performPostRequest($url, $postData, $contentType, $timeout = false)
	{
		// make sure we're returning the body
		curl_setopt($this->_curl, CURLOPT_NOBODY, false);
		
		// make sure we're POST
		curl_setopt($this->_curl, CURLOPT_POST, true);
		
		// set the URL
		curl_setopt($this->_curl, CURLOPT_URL, $url);
		
		// set the post data
		curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $postData);
		
		// set the content type
		curl_setopt($this->_curl, CURLOPT_HTTPHEADER, array("Content-Type: {$contentType}"));
		
		// set the timeout if specified
		if ($timeout !== FALSE && $timeout > 0.0)
		{
			curl_setopt($this->_curl, CURLOPT_TIMEOUT, $timeout);
		}
		else
		{
			curl_setopt($this->_curl, CURLOPT_TIMEOUT, $this->getDefaultTimeout());
		}
		
		// make the request
		$responseBody = curl_exec($this->_curl);
		
		// get info from the transfer
		list($statusCode, $contentType, $encoding) = $this->_getHeaderInformation();
				
		return new Apache_Solr_HttpTransport_Response($statusCode, null, $responseBody, $contentType, $encoding);
	}
	
	/**
	 * Get information from the last transfer
	 *
	 * @return array (statusCode, mimetype, encoding)
	 */
	private function _getHeaderInformation()
	{
		// defaults
		$type = 'text/plain';
		$encoding = 'UTF-8';
		
		// first get status code
		$statusCode = curl_getinfo($this->_curl, CURLINFO_HTTP_CODE);
		
		// get the content type
		$contentTypeHeader = curl_getinfo($this->_curl, CURLINFO_CONTENT_TYPE);
		
		if ($contentTypeHeader)
		{
			// now break apart the header to see if there's character encoding
			$contentTypeParts = explode(';', $contentTypeHeader, 2);

			if (isset($contentTypeParts[0]))
			{
				$type = trim($contentTypeParts[0]);
			}
			
			if (isset($contentTypeParts[1]))
			{
				// we have a second part, split it further
				$contentTypeParts = explode('=', $contentTypeParts[1]);
				
				if (isset($contentTypeParts[1]))
				{
					$encoding = trim($contentTypeParts[1]);
				}
			}
		}
		
		return array($statusCode, $type, $encoding);
	}
}