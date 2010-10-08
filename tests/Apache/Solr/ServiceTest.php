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
 * Apache_Solr_Service Unit Test
 */
class Apache_Solr_ServiceTest extends Apache_Solr_ServiceAbstractTest
{
	public function getFixture()
	{
		return new Apache_Solr_Service();
	}
	
	
	//================================================================//
	// ATTEMPT TO MOVE THESE TO ServiceAbstractTest AT SOME POINT     //
	//   Apache_Solr_Service_Balancer will need functions added       //
	//================================================================//
	public function testGetHttpTransportWithDefaultConstructor()
	{
		$fixture = new Apache_Solr_Service();
		
		$httpTransport = $fixture->getHttpTransport();
		
		$this->assertInstanceOf('Apache_Solr_HttpTransport_Interface', $httpTransport, 'Default http transport does not implement interface');
		$this->assertInstanceOf('Apache_Solr_HttpTransport_FileGetContents', $httpTransport, 'Default http transport is not URL Wrapper implementation');
	}
	
	
	public function testSetHttpTransport()
	{
		$newTransport = new Apache_Solr_HttpTransport_Curl();
		$fixture = new Apache_Solr_Service();
		
		$fixture->setHttpTransport($newTransport);
		$httpTransport = $fixture->getHttpTransport();
		
		$this->assertInstanceOf('Apache_Solr_HttpTransport_Interface', $httpTransport);
		$this->assertInstanceOf('Apache_Solr_HttpTransport_Curl', $httpTransport);
		$this->assertEquals($newTransport, $httpTransport);
		
	}
	
	public function testSetHttpTransportWithConstructor()
	{
		$newTransport = new Apache_Solr_HttpTransport_Curl();
		
		$fixture = new Apache_Solr_Service('localhost', 8180, '/solr/', $newTransport);
		
		$fixture->setHttpTransport($newTransport);
		$httpTransport = $fixture->getHttpTransport();
		
		$this->assertInstanceOf('Apache_Solr_HttpTransport_Interface', $httpTransport);
		$this->assertInstanceOf('Apache_Solr_HttpTransport_Curl', $httpTransport);
		$this->assertEquals($newTransport, $httpTransport);
	}

	public function testGetCollapseSingleValueArraysWithDefaultConstructor()
	{
		$fixture = $this->getFixture();
		
		$this->assertTrue($fixture->getCollapseSingleValueArrays());
	}
	
	public function testSetCollapseSingleValueArrays()
	{
		$fixture = $this->getFixture();
		
		$fixture->setCollapseSingleValueArrays(false);
		$this->assertFalse($fixture->getCollapseSingleValueArrays());
	}
	
	public function testGetNamedListTreatmetnWithDefaultConstructor()
	{
		$fixture = $this->getFixture();
		
		$this->assertEquals(Apache_Solr_Service::NAMED_LIST_MAP, $fixture->getNamedListTreatment());
	}
	
	public function testSetNamedListTreatment()
	{
		$fixture = $this->getFixture();
		
		$fixture->setNamedListTreatment(Apache_Solr_Service::NAMED_LIST_FLAT);
		
		$this->assertEquals(Apache_Solr_Service::NAMED_LIST_FLAT, $fixture->getNamedListTreatment());
	}
	
	/**
	 * @expectedException Apache_Solr_InvalidArgumentException
	 */
	public function testSetNamedListTreatmentInvalidArgumentException()
	{
		$fixture = $this->getFixture();
		
		$fixture->setNamedListTreatment("broken");
	}
	
	//================================================================//
	// END SECTION OF CODE THAT SHOULD BE MOVED                       //
	//   Apache_Solr_Service_Balancer will need functions added       //
	//================================================================//
	

	public function testConstructorDefaultArguments()
	{
		$fixture = new Apache_Solr_Service();
		
		$this->assertInstanceOf('Apache_Solr_Service', $fixture);
	}

	public function testGetHostWithDefaultConstructor()
	{
		$fixture = new Apache_Solr_Service();
		$host = $fixture->getHost();
		
		$this->assertEquals("localhost", $host);
	}
	
	public function testSetHost()
	{
		$newHost = "example.com";
		
		$fixture = new Apache_Solr_Service();
		$fixture->setHost($newHost);
		$host = $fixture->getHost();
		
		$this->assertEquals($newHost, $host);
	}
	
	/**
	 * @expectedException Apache_Solr_InvalidArgumentException
	 */
	public function testSetEmptyHost()
	{
		$fixture = new Apache_Solr_Service();
		
		// should throw an invalid argument exception
		$fixture->setHost("");
	}
	
	public function testSetHostWithConstructor()
	{
		$newHost = "example.com";
		
		$fixture = new Apache_Solr_Service($newHost);
		$host = $fixture->getHost();
		
		$this->assertEquals($newHost, $host);
	}
	
	public function testGetPortWithDefaultConstructor()
	{
		$fixture = new Apache_Solr_Service();
		$port = $fixture->getPort();
		
		$this->assertEquals(8180, $port);
	}
	
	public function testSetPort()
	{
		$newPort = 12345;
		
		$fixture = new Apache_Solr_Service();
		$fixture->setPort($newPort);
		$port = $fixture->getPort();
		
		$this->assertEquals($newPort, $port);
	}
	
	public function testSetPortWithConstructor()
	{
		$newPort = 12345;
		
		$fixture = new Apache_Solr_Service('locahost', $newPort);
		$port = $fixture->getPort();
		
		$this->assertEquals($newPort, $port);
	}
		
	public function testGetPathWithDefaultConstructor()
	{
		$fixture = new Apache_Solr_Service();
		$path = $fixture->getPath();
		
		$this->assertEquals("/solr/", $path);
	}
	
	public function testSetPath()
	{
		$newPath = "/new/path/";
		
		$fixture = new Apache_Solr_Service();
		$fixture->setPath($newPath);
		$path = $fixture->getPath();
		
		$this->assertEquals($path, $newPath);
	}
	
	public function testSetPathWillAddContainingSlashes()
	{
		$newPath = "new/path";
		$containedPath = "/{$newPath}/";
		
		$fixture = new Apache_Solr_Service();
		$fixture->setPath($newPath);
		$path = $fixture->getPath();
		
		$this->assertEquals($containedPath, $path, 'setPath did not ensure propertly wrapped with slashes');
	}
	
	public function testSetPathWithConstructor()
	{
		$newPath = "/new/path/";
		
		$fixture = new Apache_Solr_Service('localhost', 8180, $newPath);
		$path = $fixture->getPath();
		
		$this->assertEquals($newPath, $path);
	}
	
	
}