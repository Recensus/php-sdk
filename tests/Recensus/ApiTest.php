<?php

class Recensus_ApiTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Recensus_Api
     */
    protected $object;
    protected $http;
    protected $response;

    public function setUp() {

        $this->object = new Recensus_Api('00000', '11111');

        $this->http = $this->getMock('Zend_Http_Client', array(), array(), '', false, false);

        $this->object->setHttpClient($this->http);

        $this->response = $this->getMock('Zend_Http_Response', array(), array(), '', false, false);
    }

    // Boilerplate tests for getters and setters and default values are correct. 

    public function testGetSetMerchantIdShouldReturnCorrectValue() {

        $this->assertEquals('00000', $this->object->getMerchantId());

        $this->object->setMerchantId('33333');

        $this->assertEquals('33333', $this->object->getMerchantId());
    }

    public function testGetSetMerchantSecretShouldReturnCorrectValue() {

        $this->assertEquals('11111', $this->object->getMerchantSecret());

        $this->object->setMerchantSecret('33333');

        $this->assertEquals('33333', $this->object->getMerchantSecret());
    }

    public function testGetSetBaseUrl() {

        $this->assertEquals('http://api.recensus.com/', $this->object->getBaseUrl());

        $this->object->setBaseUrl("http://Ichangedit.com");

        $this->assertEquals("http://Ichangedit.com", $this->object->getBaseUrl());
    }

    public function testWillThrowExceptions() {

        $this->assertFalse($this->object->willThrowExceptions());

        $this->object->setThrowExceptions(true);

        $this->assertTrue($this->object->willThrowExceptions());

        $object = new Recensus_Api('00000', '11111', true);

        $this->assertTrue($object->willThrowExceptions());
    }

    public function testGetSetHttpClient() {

        $client = $this->object->getHttpClient();

        $this->assertInstanceOf('Zend_Http_Client', $client);

        $mClient = $this->getMock("Zend_Http_Client");

        $this->object->setHttpClient($mClient);

        $this->assertEquals($mClient, $this->object->getHttpClient());
    }

    // The SDK has the responsibility of taking an array of Data (defined in the
    // documentation) and JSON formatting it, sending the correct headers and
    // parsing the response. No validation of the incoming data is done client 
    // side (allowing API changes to occur without bumping the SDK version).

    public function testMakeCustomerContactRequestShouldCorrectlyBuildAndSignRequest() {

        $this->configureExpectedRequestForCCR();

        $this->http
                ->expects($this->once())
                ->method("request")
                ->will($this->returnValue($this->response));

        $this->response
                ->expects($this->once())
                ->method('isSuccessful')
                ->will($this->returnValue(true));

        $this->response
                ->expects($this->once())
                ->method('getBody')
                ->will($this->returnValue('{"data": {"test":"one"}}'));

        $data = $this->getValidCCR();

        $resp = $this->object->makeCustomerContactRequest($data);

        $expected = array('data' => array("test" => "one"));

        $this->assertEquals($expected, $resp);
    }

    // Depending on the error settings a non 2** response will trigger a PHP
    // Notice or throw an Exception.

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testMakeCustomerContactRequestShouldErrorOnFailure() {

        $this->object->setThrowExceptions(false);

        $this->configureExpectedRequestForCCR();

        $this->http
                ->expects($this->once())
                ->method("request")
                ->will($this->returnValue($this->response));

        $this->response
                ->expects($this->once())
                ->method('isSuccessful')
                ->will($this->returnValue(false));

        $this->response
                ->expects($this->once())
                ->method('getStatus')
                ->will($this->returnValue(401));

        $this->response
                ->expects($this->once())
                ->method('getBody')
                ->will($this->returnValue('Bad Request'));

        $data = $this->getValidCCR();

        $resp = $this->object->makeCustomerContactRequest($data);

        $this->assertFalse($resp);
    }

    /**
     * @expectedException Recensus_Api_Exception
     */
    public function testMakeCustomerContactRequestShouldThrowOnFailure() {

        $this->object->setThrowExceptions(true);

        $this->configureExpectedRequestForCCR();

        $this->http
                ->expects($this->once())
                ->method("request")
                ->will($this->returnValue($this->response));

        $this->response
                ->expects($this->once())
                ->method('isSuccessful')
                ->will($this->returnValue(false));

        $this->response
                ->expects($this->once())
                ->method('getStatus')
                ->will($this->returnValue(401));

        $this->response
                ->expects($this->once())
                ->method('getBody')
                ->will($this->returnValue('Bad Request'));

        $data = $this->getValidCCR();

        $resp = $this->object->makeCustomerContactRequest($data);
    }

    // The SDK should handle any underlying exceptions from the Zend_Http_Client

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testMakeCustomerContactRequestShouldHandleClientErrorsWithError() {

        $this->object->setThrowExceptions(false);

        $this->configureExpectedRequestForCCR();

        $this->http
                ->expects($this->once())
                ->method("request")
                ->will($this->throwException(new Zend_Http_Client_Exception('Some Error!')));

        $data = $this->getValidCCR();

        $resp = $this->object->makeCustomerContactRequest($data);

        $this->assertFalse($resp);
    }

    /**
     * @expectedException Recensus_Api_Exception
     */
    public function testMakeCustomerContactRequestShouldHandleClientErrorsWithThrow() {

        $this->object->setThrowExceptions(true);

        $this->configureExpectedRequestForCCR();

        $this->http
                ->expects($this->once())
                ->method("request")
                ->will($this->throwException(new Zend_Http_Client_Exception('Some Error!')));

        $data = $this->getValidCCR();

        $resp = $this->object->makeCustomerContactRequest($data);
    }

    protected function getValidCCR() {

        return array(
            "merchant" => 1,
            "customerFirstName" => "BW",
            "customerLastName" => "BW",
            "customerEmail" => "bw@bw.com",
            "purchaseDate" => "2012-12-12T16:00:00+0000",
            "purchases" => array(
                array(
                    "brand" => "TEST",
                    "mpn" => "TEST",
                    "gtin" => 123456,
                    "quantity" => 2,
                    "lang" => "en",
                    "type" => "P",
                    "title" => "TEST")
            ),
        );
    }

    protected function getValidCCRJson() {
        return '{"data":{"merchant":1,"customerFirstName":"BW","customerLastName":"BW","customerEmail":"bw@bw.com","purchaseDate":"2012-12-12T16:00:00+0000","purchases":[{"brand":"TEST","mpn":"TEST","gtin":123456,"quantity":2,"lang":"en","type":"P","title":"TEST"}]},"signedRequest":{"token":"00000","signature":"dc72061955cf24b109fb0b437dee84fa"}}';
    }

    protected function configureExpectedRequestForCCR() {

        // Ensures a clean request is being made 
        $this->http
                ->expects($this->once())
                ->method('resetParameters')
                ->with(true);

        $this->http
                ->expects($this->once())
                ->method('setMethod')
                ->with('POST');

        $this->http
                ->expects($this->once())
                ->method('setUri')
                ->with("http://api.recensus.com/merchant/00000/customer-contact-request");

        $this->http
                ->expects($this->exactly(2))
                ->method('setHeaders')
                ->with($this->anything());

        $this->http
                ->expects($this->once())
                ->method("setRawData")
                ->with($this->getValidCCRJson());
    }

}

