<?php

class Recensus_WidgetTest extends PHPUnit_Framework_TestCase {

    // Boiler plate tests to ensure default properties and getters and setters

    public function testGetSetMerchantTokenShouldReturnCorrectValue() {

        $data = $this->getWellFormedData();

        $object = new Recensus_Widget('00000', '11111', $data);

        $this->assertEquals('00000', $object->getMerchantToken());

        $object->setMerchantToken('33333');

        $this->assertEquals('33333', $object->getMerchantToken());
    }

    public function testGetSetSharedSecretShouldReturnCorrectValue() {

        $data = $this->getWellFormedData();

        $object = new Recensus_Widget('00000', '11111', $data);

        $this->assertEquals('11111', $object->getSharedSecret());

        $object->setSharedSecret('33333');

        $this->assertEquals('33333', $object->getSharedSecret());
    }

    public function testGetSetProductDataShouldReturnCorrectValue() {

        $data = $this->getWellFormedData();

        $object = new Recensus_Widget('00000', '11111', $data);

        $this->assertEquals($data, $object->getProductData());

        $data['url'] = "http://IChangedIt.com";

        $object->setProductData($data);

        $this->assertEquals($data, $object->getProductData());
    }
    
    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testGetSetProductDataShouldErrorIfNoName() {

        $data = $this->getWellFormedData(array('name'));

        $object = new Recensus_Widget('00000', '11111');

        $object->setProductData($data);
        
    }
    
    /**
     * @expectedException Recensus_Widget_Exception
     */
    public function testGetSetProductDataShouldThrowIfNoName() {

        $data = $this->getWellFormedData(array('name'));

        $object = new Recensus_Widget('00000', '11111', null, true);

        $object->setProductData($data);
        
    }
    
    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetProductDataShouldErrorIfNoUrl() {

        $data = $this->getWellFormedData(array('url'));

        $object = new Recensus_Widget('00000', '11111');

        $object->setProductData($data);
        
    }
    
    /**
     * @expectedException Recensus_Widget_Exception
     */
    public function testSetProductDataShouldThrowIfNoUrl() {

        $data = $this->getWellFormedData(array('url'));

        $object = new Recensus_Widget('00000', '11111', null, true);

        $object->setProductData($data);
        
    }

    public function testGetSetHtmlURLShouldReturnCorrectValue() {

        $data = $this->getWellFormedData();

        $object = new Recensus_Widget('00000', '11111', $data);

        $this->assertEquals("http://app.recensus.com/widget/api/get", $object->getHtmlEndpointUrl());

        $object->setHtmlEndpointUrl("http://IChangedIt.com");

        $this->assertEquals("http://IChangedIt.com", $object->getHtmlEndpointUrl());
    }

    public function testGetSetWillThrowExceptions() {

        $data = $this->getWellFormedData();

        $object = new Recensus_Widget('00000', '11111', $data);

        $this->assertFalse($object->willThrowExceptions());

        $object->setThrowExceptions(true);

        $this->assertTrue($object->willThrowExceptions());

        $object = new Recensus_Widget('00000', '11111', $data, true);

        $this->assertTrue($object->willThrowExceptions());
    }

    public function testGetSetHttpClient() {

        $data = $this->getWellFormedData();

        $object = new Recensus_Widget('00000', '11111', $data);

        $client = $object->getHttpClient();

        $this->assertInstanceOf('Zend_Http_Client', $client);

        $mClient = $this->getMock("Zend_Http_Client");

        $object->setHttpClient($mClient);

        $this->assertEquals($mClient, $object->getHttpClient());
    }
    
    // Recensus Requires Name and URL be present in the data property used
    // to generate the widget.
    

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testConstructShouldErrorWhenUrlIsAbsent() {

        $data = $this->getWellFormedData(array('url'));

        $object = new Recensus_Widget('0000000', '000000', $data);
    }

    /**
     * @expectedException Recensus_Widget_Exception
     */
    public function testConstructShouldThrowWhenUrlIsAbsent() {
        $data = $this->getWellFormedData(array('url'));

        $object = new Recensus_Widget('0000000', '000000', $data, true);
    }
    
    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testConstructShouldErrorWhenNameIsAbsent() {

        $data = $this->getWellFormedData(array('name'));

        $object = new Recensus_Widget('0000000', '000000', $data);
    }

    /**
     * @expectedException Recensus_Widget_Exception
     */
    public function testConstructShouldThrowWhenNameIsAbsent() {
        $data = $this->getWellFormedData(array('name'));

        $object = new Recensus_Widget('0000000', '000000', $data, true);
    }

    // Remaining productData fields are optional.

    public function testConstructShouldNotErrorWhenOptionalParamsAreAbsent() {

        $data = $this->getWellFormedData(array('type', 'lang', 'info', 'price'));

        $object = new Recensus_Widget('0000000', '000000', $data);

        $this->assertInstanceOf('Recensus_Widget', $object);
    }


    public function testConstructShouldNotThrowWhenOptionalParamsAreAbsent() {

        $data = $this->getWellFormedData(array('type', 'lang', 'info', 'price'));

        $object = new Recensus_Widget('0000000', '000000', $data, true);

        $this->assertInstanceOf('Recensus_Widget', $object);
    }

    // If no product data is set then an error should be triggered
    
    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testGetDataPropertyShouldErrorIfNoProductDataPresent() {

        $object = new Recensus_Widget('0000000', '000000', null);

        $object->getDataProperty();
    }

    /**
     * @expectedException Recensus_Widget_Exception
     */
    public function testGetDataPropertyShouldThrowIfNoProductDataPresent() {

        $object = new Recensus_Widget('0000000', '000000', null, true);

        $object->getDataProperty();
    }
    
    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testGetHTMLFragmentShouldErrorIfNoProductDataPresent() {

        $object = new Recensus_Widget('0000000', '000000', null);

        $object->getHTML();
    }

    /**
     * @expectedException Recensus_Widget_Exception
     */
    public function testGetHTMLFragmentShouldThrowIfNoProductDataPresent() {

        $object = new Recensus_Widget('0000000', '000000', null, true);

        $object->getHTML();
    }
    

    // The iframe URL should contain elements passed into the object at 
    // construct time. Also the URL should contain the correct identifying 
    // hash.

    public function testGetDataPropertyShouldReturnStringWithCorrectParametersAndHash() {

        $data = $this->getWellFormedData();

        $object = new Recensus_Widget('00000', '11111', $data, true);

        $dataProp = $object->getDataProperty();

        $expectedDataProp = "name=Hello&url=http%3A%2F%2Fcool-shoes.com%2Fproduct%2Fcool-shoe-1&mid=00000&brand=Cool+Shoe+Maker&mpn=Cool+Shoes&gtin=00000000000&type=p&lang=en&title=Super+Cool+Shoes&info=These+shoes+are+off+the+hook%21&price=2.99&hash=bf1e3ae40da1c72f8da319914595c354";

        $this->assertEquals($expectedDataProp, $dataProp, "Expected $expectedDataProp but got $dataProp");
    }

    // The HTTP client should be used to query recensus for product review html
    // and should return the string.

    public function testGetHTMLFragmentUsesCorrectURLAndHash() {

        $expectedUrl = "http://app.recensus.com/widget/api/get?name=Hello&url=http%3A%2F%2Fcool-shoes.com%2Fproduct%2Fcool-shoe-1&mid=00000&brand=Cool+Shoe+Maker&mpn=Cool+Shoes&gtin=00000000000&type=p&lang=en&title=Super+Cool+Shoes&info=These+shoes+are+off+the+hook%21&price=2.99&hash=bf1e3ae40da1c72f8da319914595c354";

        $client = $this->getMock('Zend_Http_Client', array(), array(), '', false, false);

        $client->expects($this->once())
                ->method('setUri')
                ->with($expectedUrl)
                ->will($this->returnValue($client));

        $client->expects($this->once())
                ->method('setMethod')
                ->with('GET')
                ->will($this->returnValue($client));

        $response = $this->getMock('Zend_Http_Response', array(), array(), '', false, false);

        $response->expects($this->once())
                ->method('isSuccessful')
                ->will($this->returnValue(true));

        $response->expects($this->once())
                ->method('getBody')
                ->will($this->returnValue('<p>Some HTML String</p>'));

        $client->expects($this->once())
                ->method('request')
                ->will($this->returnValue($response));

        $data = $this->getWellFormedData();

        $object = new Recensus_Widget('00000', '11111', $data);
        $object->setHttpClient($client);

        $html = $object->getHTML();

        $this->assertEquals('<p>Some HTML String</p>', $html);
    }

    // A failed HTTP request or a request error should trigger the appropriate error

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testGetHTMLFragmentErrorsOnBadResponse() {

        $expectedDataProp = "http://app.recensus.com/widget/api/get?name=Hello&url=http%3A%2F%2Fcool-shoes.com%2Fproduct%2Fcool-shoe-1&mid=00000&brand=Cool+Shoe+Maker&mpn=Cool+Shoes&gtin=00000000000&type=p&lang=en&title=Super+Cool+Shoes&info=These+shoes+are+off+the+hook%21&price=2.99&hash=bf1e3ae40da1c72f8da319914595c354";

        $client = $this->getMock('Zend_Http_Client', array(), array(), '', false, false);

        $client->expects($this->once())
                ->method('setUri')
                ->with($expectedDataProp)
                ->will($this->returnValue($client));

        $client->expects($this->once())
                ->method('setMethod')
                ->with('GET')
                ->will($this->returnValue($client));

        $response = $this->getMock('Zend_Http_Response', array(), array(), '', false, false);

        $response->expects($this->once())
                ->method('isSuccessful')
                ->will($this->returnValue(false));

        $client->expects($this->once())
                ->method('request')
                ->will($this->returnValue($response));

        $data = $this->getWellFormedData();

        $object = new Recensus_Widget('00000', '11111', $data);
        $object->setHttpClient($client);

        $html = $object->getHTML();
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testGetHTMLFragmentErrorsOnRequestError() {

        $expectedDataProp = "http://app.recensus.com/widget/api/get?name=Hello&url=http%3A%2F%2Fcool-shoes.com%2Fproduct%2Fcool-shoe-1&mid=00000&brand=Cool+Shoe+Maker&mpn=Cool+Shoes&gtin=00000000000&type=p&lang=en&title=Super+Cool+Shoes&info=These+shoes+are+off+the+hook%21&price=2.99&hash=bf1e3ae40da1c72f8da319914595c354";

        $client = $this->getMock('Zend_Http_Client', array(), array(), '', false, false);

        $client->expects($this->once())
                ->method('setUri')
                ->with($expectedDataProp)
                ->will($this->returnValue($client));

        $client->expects($this->once())
                ->method('setMethod')
                ->with('GET')
                ->will($this->returnValue($client));

        $client->expects($this->once())
                ->method('request')
                ->will($this->throwException(new Zend_Http_Client_Exception('error')));

        $data = $this->getWellFormedData();

        $object = new Recensus_Widget('00000', '11111', $data);
        $object->setHttpClient($client);

        $html = $object->getHTML();
    }

    /**
     * @expectedException Recensus_Widget_Exception
     */
    public function testGetHTMLFragmentThrowsOnBadResponse() {
        $expectedDataProp = "http://app.recensus.com/widget/api/get?name=Hello&url=http%3A%2F%2Fcool-shoes.com%2Fproduct%2Fcool-shoe-1&mid=00000&brand=Cool+Shoe+Maker&mpn=Cool+Shoes&gtin=00000000000&type=p&lang=en&title=Super+Cool+Shoes&info=These+shoes+are+off+the+hook%21&price=2.99&hash=bf1e3ae40da1c72f8da319914595c354";

        $client = $this->getMock('Zend_Http_Client', array(), array(), '', false, false);

        $client->expects($this->once())
                ->method('setUri')
                ->with($expectedDataProp)
                ->will($this->returnValue($client));

        $client->expects($this->once())
                ->method('setMethod')
                ->with('GET')
                ->will($this->returnValue($client));

        $response = $this->getMock('Zend_Http_Response', array(), array(), '', false, false);

        $response->expects($this->once())
                ->method('isSuccessful')
                ->will($this->returnValue(false));

        $client->expects($this->once())
                ->method('request')
                ->will($this->returnValue($response));

        $data = $this->getWellFormedData();

        $object = new Recensus_Widget('00000', '11111', $data, true);
        $object->setHttpClient($client);

        $html = $object->getHTML();
    }

    /**
     * @expectedException Recensus_Widget_Exception
     */
    public function testGetHTMLFragmentThrowsOnRequestError() {
        $expectedDataProp = "http://app.recensus.com/widget/api/get?name=Hello&url=http%3A%2F%2Fcool-shoes.com%2Fproduct%2Fcool-shoe-1&mid=00000&brand=Cool+Shoe+Maker&mpn=Cool+Shoes&gtin=00000000000&type=p&lang=en&title=Super+Cool+Shoes&info=These+shoes+are+off+the+hook%21&price=2.99&hash=bf1e3ae40da1c72f8da319914595c354";

        $client = $this->getMock('Zend_Http_Client', array(), array(), '', false, false);

        $client->expects($this->once())
                ->method('setUri')
                ->with($expectedDataProp)
                ->will($this->returnValue($client));

        $client->expects($this->once())
                ->method('setMethod')
                ->with('GET')
                ->will($this->returnValue($client));

        $client->expects($this->once())
                ->method('request')
                ->will($this->throwException(new Zend_Http_Client_Exception('error')));

        $data = $this->getWellFormedData();

        $object = new Recensus_Widget('00000', '11111', $data, true);
        $object->setHttpClient($client);

        $html = $object->getHTML();
    }

    // Utiliy data supply functions.

    /**
     * Returns a well formed $productData array which tests can manipulate to 
     * cause errors or use to test success.
     * 
     * @return array
     */
    private function getWellFormedData($omitFields = array()) {
        // str Cool Shoe MakerCool Shoes0000000000011111
        $data = array(
            'name' => "Hello",
            'gtin' => "00000000000",
            'mpn' => 'Cool Shoes',
            'brand' => 'Cool Shoe Maker',
            'url' => 'http://cool-shoes.com/product/cool-shoe-1',
            'title' => 'Super Cool Shoes',
            'lang' => 'en',
            'type' => 'p',
            'info' => 'These shoes are off the hook!',
            'price' => '2.99'
        );

        foreach ($omitFields as $field) {
            unset($data[$field]);
        }

        return $data;
    }

}

