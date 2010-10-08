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
 * Apache_Solr_HttpTransport_Abstract Unit Tests
 */
abstract class Apache_Solr_HttpTransport_AbstractTest extends PHPUnit_Framework_TestCase
{	
	abstract public function getFixture();
	
	public function testGetDefaultTimeoutWithDefaultConstructor()
	{
		$fixture = $this->getFixture();
		$timeout = $fixture->getDefaultTimeout();
		
		$this->assertGreaterThan(0, $timeout);
	}
	
	public function testGetDefaultTimeoutSetToSixtyForBadValues()
	{
		// first set our default_socket_timeout ini setting
		$previousValue = ini_get('default_socket_timeout');
		ini_set('default_socket_timeout', 0);
		
		$fixture = $this->getFixture();
		$timeout = $fixture->getDefaultTimeout();
		
		// reset timeout
		ini_set('default_socket_timeout', $previousValue);
		
		$this->assertEquals(60, $timeout);
	}
	
	public function testSetDefaultTimeout()
	{
		$newTimeout = 1234;
		
		$fixture = $this->getFixture();
		$fixture->setDefaultTimeout($newTimeout);
		$timeout = $fixture->getDefaultTimeout();
		
		$this->assertEquals($newTimeout, $timeout);
	}
	
	abstract public function testPerformGetRequest();
	abstract public function testPerformGetRequestWithTimeout();
	abstract public function testPerformHeadRequest();
	abstract public function testPerformHeadRequestWithTimeout();
	abstract public function testPerformPostRequest();
	abstract public function testPerformPostRequestWithTimeout();
	
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