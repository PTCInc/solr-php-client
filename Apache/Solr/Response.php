<?php
/**
 * @copyright Copyright 2007 Conduit Internet Technologies, Inc. (http://conduit-it.com)
 * @license Apache Licence, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package Apache
 * @subpackage Solr
 * @author Donovan Jimenez <djimenez@conduit-it.com>
 */

/**
 * Represents a Solr response.  Parses the raw response into a set of stdClass objects
 * and associative arrays for easy access.
 *
 * Currently requires json_decode which is bundled with PHP >= 5.2.0, Alternatively can be
 * installed with PECL.  Zend Framework also includes a purely PHP solution.
 *
 * @todo When Solr 1.3 is released, possibly convert to use PHP or Serialized PHP output writer
 */
class Apache_Solr_Response
{
	/**
	 * Holds the raw response used in construction
	 *
	 * @var string
	 */
	protected $_rawResponse;

	/**
	 * Parsed values from the passed in http headers
	 *
	 * @var string
	 */
	protected $_httpStatus, $_httpStatusMessage, $_type, $_encoding;

	/**
	 * Whether the raw response has been parsed
	 *
	 * @var boolean
	 */
	protected $_isParsed = false;

	/**
	 * Parsed representation of the data
	 *
	 * @var mixed
	 */
	protected $_parsedData;

	/**
	 * Data parsing flags.  Determines what extra processing should be done
	 * after the data is initially converted to a data structure.
	 *
	 * @var boolean
	 */
	protected $_createDocuments = true,
			$_collapseSingleValueArrays = true;

	/**
	 * Constructor. Takes the raw HTTP response body and the exploded HTTP headers
	 *
	 * @param string $rawResponse
	 * @param array $httpHeaders
	 * @param boolean $createDocuments Whether to convert the documents json_decoded as stdClass instances to Apache_Solr_Document instances
	 * @param boolean $collapseSingleValueArrays Whether to make multivalued fields appear as single values
	 */
	public function __construct($rawResponse, $httpHeaders = array(), $createDocuments = true, $collapseSingleValueArrays = true)
	{
		//Assume 0, 'Communication Error', utf-8, and  text/plain
		$status = 0;
		$statusMessage = 'Communication Error';
		$type = 'text/plain';
		$encoding = 'UTF-8';

		//iterate through headers for real status, type, and encoding
		if (is_array($httpHeaders) && count($httpHeaders) > 0)
		{
			//look at the first headers for the HTTP status code
			//and message (errors are usually returned this way)
			//
			//HTTP 100 Continue response can also be returned before
			//the REAL status header, so we need look until we find
			//the last header starting with HTTP
			//
			//the spec: http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.1
			//
			//Thanks to Daniel Andersson for pointing out this oversight
			while (isset($httpHeaders[0]) && substr($httpHeaders[0], 0, 4) == 'HTTP')
			{
				$parts = split(' ', substr($httpHeaders[0], 9), 2);

				$status = $parts[0];
				$statusMessage = trim($parts[1]);

				array_shift($httpHeaders);
			}

			//Look for the Content-Type response header and determine type
			//and encoding from it (if possible - such as 'Content-Type: text/plain; charset=UTF-8')
			foreach ($httpHeaders as $header)
			{
				if (strncasecmp($header, 'Content-Type:', 13) == 0)
				{
					//split content type value into two parts if possible
					$parts = split(';', substr($header, 13), 2);

					$type = trim($parts[0]);

					if ($parts[1])
					{
						//split the encoding section again to get the value
						$parts = split('=', $parts[1], 2);

						if ($parts[1])
						{
							$encoding = trim($parts[1]);
						}
					}

					break;
				}
			}
		}

		$this->_rawResponse = $rawResponse;
		$this->_type = $type;
		$this->_encoding = $encoding;
		$this->_httpStatus = $status;
		$this->_httpStatusMessage = $statusMessage;
		$this->_createDocuments = (bool) $createDocuments;
		$this->_collapseSingleValueArrays = (bool) $collapseSingleValueArrays;
	}

	/**
	 * Get the HTTP status code
	 *
	 * @return integer
	 */
	public function getHttpStatus()
	{
		return $this->_httpStatus;
	}

	/**
	 * Get the HTTP status message of the response
	 *
	 * @return string
	 */
	public function getHttpStatusMessage()
	{
		return $this->_httpStatusMessage;
	}

	/**
	 * Get content type of this Solr response
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * Get character encoding of this response. Should usually be utf-8, but just in case
	 *
	 * @return string
	 */
	public function getEncoding()
	{
		return $this->_encoding;
	}

	/**
	 * Get the raw response as it was given to this object
	 *
	 * @return string
	 */
	public function getRawResponse()
	{
		return $this->_rawResponse;
	}

	/**
	 * Magic get to expose the parsed data and to lazily load it
	 *
	 * @param unknown_type $key
	 * @return unknown
	 */
	public function __get($key)
	{
		if (!$this->_isParsed)
		{
			$this->_parseData();
			$this->_isParsed = true;
		}

		if (isset($this->_parsedData->$key))
		{
			return $this->_parsedData->$key;
		}

		return null;
	}

	/**
	 * Parse the raw response into the parsed_data array for access
	 */
	protected function _parseData()
	{
		//An alternative would be to use Zend_Json::decode(...)
		$data = json_decode($this->_rawResponse);

		//if we're configured to collapse single valued arrays or to convert them to Apache_Solr_Document objects
		//and we have response documents, then try to collapse the values and / or convert them now
		if (($this->_createDocuments || $this->_collapseSingleValueArrays) && isset($data->response) && is_array($data->response->docs))
		{
			$documents = array();

			foreach ($data->response->docs as $originalDocument)
			{
				if ($this->_createDocuments)
				{
					$document = new Apache_Solr_Document();
				}
				else
				{
					$document = $originalDocument;
				}

				foreach ($originalDocument as $key => $value)
				{
					//If a result is an array with only a single
					//value then its nice to be able to access
					//it as if it were always a single value
					if ($this->_collapseSingleValueArrays && is_array($value) && count($value) <= 1)
					{
						$value = array_shift($value);
					}

					$document->$key = $value;
				}

				$documents[] = $document;
			}

			$data->response->docs = $documents;
		}

		$this->_parsedData = $data;
	}
}