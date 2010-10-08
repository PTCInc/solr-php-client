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
 *
 * @package Apache
 * @subpackage Solr
 * @author Donovan Jimenez <djimenez@conduit-it.com>
 */

/**
 * Apache_Solr_HttpTransport_FileGetContents Unit Tests
 */
class Apache_Solr_HttpTransport_FileGetContentsTest extends PHPUnit_Framework_TestCase
{
	// request our copyright file from googlecode for GET and HEAD
	const GET_URL = "http://solr-php-client.googlecode.com/svn/trunk/COPYING";
	const GET_RESPONSE_MIME_TYPE = 'text/plain';
	const GET_RESPONSE_ENCODING = 'UTF-8';
	const GET_RESPONSE_MATCH = 'Copyright (c) ';
	
	// post to the issue list page with a search for 'meh'
	const POST_URL = "http://code.google.com/p/solr-php-client/issues/list";
	const POST_DATA = "can=2&q=meh&colspec=ID+Type+Status+Priority+Milestone+Owner+Summary&cells=tiles";
	const POST_REQUEST_CONTENT_TYPE = 'application/x-www-form-urlencoded; charset=UTF-8';
	
	const POST_RESPONSE_MIME_TYPE = 'text/html';
	const POST_RESPONSE_ENCODING = 'UTF-8';
	//const POST_RESPONSE_MATCH = 'not sure';
	
	public function ensureAllowUrlFopen()
	{
		// make sure allow_url_fopen is on
		if (!ini_get("allow_url_fopen"))
		{
			$this->markTestSkipped("allow_url_fopen is not enabled");
		}
	}
	
	public function testGetDefaultTimeoutWithDefaultConstructor()
	{
		$fixture = new Apache_Solr_HttpTransport_FileGetContents();
		$timeout = $fixture->getDefaultTimeout();
		
		$this->assertGreaterThan(0, $timeout);
	}
	
	public function testSetDefaultTimeout()
	{
		$newTimeout = 1234;
		
		$fixture = new Apache_Solr_HttpTransport_FileGetContents();
		$fixture->setDefaultTimeout($newTimeout);
		$timeout = $fixture->getDefaultTimeout();
		
		$this->assertEquals($newTimeout, $timeout);
	}
	
	public function testPerformGetRequest()
	{
		$this->ensureAllowUrlFopen();
		
		$fixture = new Apache_Solr_HttpTransport_FileGetContents();
		$response = $fixture->performGetRequest(self::GET_URL, 1);
		
		$this->assertType('Apache_Solr_HttpTransport_Response', $response);
		
		$this->assertEquals(200, $response->getStatusCode(), 'Status code was not 200');
		$this->assertEquals(self::GET_RESPONSE_MIME_TYPE, $response->getMimeType(), 'mimetype was not correct');
		$this->assertEquals(self::GET_RESPONSE_ENCODING, $response->getEncoding(), 'character encoding was not correct');
		$this->assertStringStartsWith(self::GET_RESPONSE_MATCH, $response->getBody(), 'body did not start with match text');
	}
	
	public function testPerformHeadRequest()
	{
		$this->ensureAllowUrlFopen();
		$this->ensureAllowUrlFopen();
		
		$fixture = new Apache_Solr_HttpTransport_FileGetContents();
		$response = $fixture->performHeadRequest(self::GET_URL, 1);
		
		// we should get everything the same as a get, except the body
		$this->assertType('Apache_Solr_HttpTransport_Response', $response);
		
		$this->assertEquals(200, $response->getStatusCode(), 'Status code was not 200');
		$this->assertEquals(self::GET_RESPONSE_MIME_TYPE, $response->getMimeType(), 'mimetype was not correct');
		$this->assertEquals(self::GET_RESPONSE_ENCODING, $response->getEncoding(), 'character encoding was not correct');
		$this->assertEquals("", $response->getBody(), 'body was not empty');
	}
	
	public function testPerformPostRequest()
	{
		$this->ensureAllowUrlFopen();
		
		$fixture = new Apache_Solr_HttpTransport_FileGetContents();
		$response = $fixture->performPostRequest(self::POST_URL, self::POST_DATA, self::POST_REQUEST_CONTENT_TYPE, 1);
		
		$this->assertType('Apache_Solr_HttpTransport_Response', $response);
		
		$this->assertEquals(200, $response->getStatusCode(), 'Status code was not 200');
		$this->assertEquals(self::POST_RESPONSE_MIME_TYPE, $response->getMimeType(), 'mimetype was not correct');
		$this->assertEquals(self::POST_RESPONSE_ENCODING, $response->getEncoding(), 'character encoding was not correct');
		//$this->assertStringStartsWith(self::POST_RESPONSE_MATCH, $response->getBody(), 'body did not start with match text');
	}
	
	/**
	 * Test one session doing multiple requests in multiple orders
	 */
	public function testMultipleRequests()
	{
		// initial get request
		$this->testPerformGetRequest();
		
		// head following get
		$this->testPerformHeadRequest();
		
		// post following head
		$this->testPerformPostRequest();
		
		// get following post
		$this->testPerformGetRequest();
		
		// post following get
		$this->testPerformPostRequest();
	
		// head following post
		$this->testPerformHeadRequest();
		
		// get following post
		$this->testPerformGetRequest();		
	}
}