<?php

class RecensusWidgetTest extends PHPUnit_Framework_TestCase {

    // Boiler plate tests to ensure default properties and getters and setters

    public function testGetSetMerchantTokenShouldReturnCorrectValue() {

        $data = $this->getWellFormedData();

        $object = new RecensusWidget('00000', '11111', $data);

        $this->assertEquals('00000', $object->getMerchantToken());

        $object->setMerchantToken('33333');

        $this->assertEquals('33333', $object->getMerchantToken());
    }

    public function testGetSetSharedSecretShouldReturnCorrectValue() {

        $data = $this->getWellFormedData();

        $object = new RecensusWidget('00000', '11111', $data);

        $this->assertEquals('11111', $object->getSharedSecret());

        $object->setSharedSecret('33333');

        $this->assertEquals('33333', $object->getSharedSecret());
    }

    public function testGetSetProductDataShouldReturnCorrectValue() {

        $data = $this->getWellFormedData();

        $object = new RecensusWidget('00000', '11111', $data);

        $this->assertEquals($data, $object->getProductData());

        $data['url'] = "http://IChangedIt.com";

        $object->setProductData($data);

        $this->assertEquals($data, $object->getProductData());
    }

    public function testGetSetFragURLShouldReturnCorrectValue() {

        $data = $this->getWellFormedData();

        $object = new RecensusWidget('00000', '11111', $data);

        $this->assertEquals("http://app.recensus.com/widget/api/get", $object->getFragEndpointURL());

        $object->setFragEndpointURL("http://IChangedIt.com");

        $this->assertEquals("http://IChangedIt.com", $object->getFragEndpointURL());
    }

    public function testWillThrowExceptions() {

        $data = $this->getWellFormedData();

        $object = new RecensusWidget('00000', '11111', $data);

        $this->assertFalse($object->willThrowExceptions());

        $object->setThrowExceptions(true);

        $this->assertTrue($object->willThrowExceptions());

        $object = new RecensusWidget('00000', '11111', $data, true);

        $this->assertTrue($object->willThrowExceptions());
    }

    public function testGetSetHttpClient() {

        $data = $this->getWellFormedData();

        $object = new RecensusWidget('00000', '11111', $data);

        $client = $object->getHttpClient();

        $this->assertInstanceOf('Zend_Http_Client', $client);

        $mClient = $this->getMock("Zend_Http_Client");

        $object->setHttpClient($mClient);

        $this->assertEquals($mClient, $object->getHttpClient());
    }

    // Recensus needs either GTIN or Brand + MPN to be passed in the productData
    // array to identify a product.

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testConstructShouldErrorWhenGTINBrandMpnAreNotPassed() {

        $data = $this->getWellFormedData(array('gtin', 'brand', 'mpn'));

        $object = new RecensusWidget('0000000', '000000', $data);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testConstructShouldErrorWhenOnlyBrandIsPassed() {

        $data = $this->getWellFormedData(array('gtin', 'mpn'));

        $object = new RecensusWidget('0000000', '000000', $data);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testConstructShouldErrorWhenOnlyMPNIsPassed() {

        $data = $this->getWellFormedData(array('gtin', 'brand'));

        $object = new RecensusWidget('0000000', '000000', $data);
    }

    /**
     * @expectedException RecensusWidgetException
     */
    public function testConstructShouldThrowWhenGTINBrandMpnAreNotPassed() {

        $data = $this->getWellFormedData(array('gtin', 'mpn', 'brand'));

        $object = new RecensusWidget('0000000', '000000', $data, true);
    }

    /**
     * @expectedException RecensusWidgetException
     */
    public function testConstructShouldThrowWhenOnlyBrandIsPassed() {

        $data = $this->getWellFormedData(array('gtin', 'mpn'));

        $object = new RecensusWidget('0000000', '000000', $data, true);
    }

    /**
     * @expectedException RecensusWidgetException
     */
    public function testConstructShouldThrowWhenOnlyMPNIsPassed() {
        $data = $this->getWellFormedData(array('gtin', 'brand'));

        $object = new RecensusWidget('0000000', '000000', $data, true);
    }

    // Recensus requires the URL of the product to be passed in the productData
    // array.

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testConstructShouldErrorWhenUrlIsAbsent() {

        $data = $this->getWellFormedData(array('url'));

        $object = new RecensusWidget('0000000', '000000', $data);
    }

    /**
     * @expectedException RecensusWidgetException
     */
    public function testConstructShouldThrowWhenUrlIsAbsent() {
        $data = $this->getWellFormedData(array('url'));

        $object = new RecensusWidget('0000000', '000000', $data, true);
    }

    // Remaining productData fields are optional.

    public function testConstructShouldNotErrorWhenOptionalParamsAreAbsent() {

        $data = $this->getWellFormedData(array('type', 'lang', 'title', 'info'));

        $object = new RecensusWidget('0000000', '000000', $data);

        $this->assertInstanceOf('RecensusWidget', $object);
    }

    // Exceptions or errors are generated during the objects construction 
    // however it is possible to set the productData array using the setter.
    // Internally the same validation is used however we should still check it's
    // being used on the setProductData method.

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetProductDataShouldErrorOnInvalidData() {

        $data = $this->getWellFormedData(array('gtin', 'brand', 'mpn'));

        $object = new RecensusWidget('0000000', '000000', $data);
    }

    /**
     * @expectedException RecensusWidgetException
     */
    public function testSetProductDataShouldThrowOnInvalidData() {

        $data = $this->getWellFormedData(array('gtin', 'brand', 'mpn'));

        $object = new RecensusWidget('0000000', '000000', $data, true);
    }

    public function testConstructShouldNotThrowWhenOptionalParamsAreAbsent() {

        $data = $this->getWellFormedData(array('type', 'lang', 'title', 'info'));

        $object = new RecensusWidget('0000000', '000000', $data, true);

        $this->assertInstanceOf('RecensusWidget', $object);
    }

    // If no product data is set then an error should be triggered
    
    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testGetDataPropertyShouldErrorIfNoProductDataPresent() {

        $object = new RecensusWidget('0000000', '000000', null);

        $object->getDataProperty();
    }

    /**
     * @expectedException RecensusWidgetException
     */
    public function testGetDataPropertyShouldThrowIfNoProductDataPresent() {

        $object = new RecensusWidget('0000000', '000000', null, true);

        $object->getDataProperty();
    }
    
    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testGetHTMLFragmentShouldErrorIfNoProductDataPresent() {

        $object = new RecensusWidget('0000000', '000000', null);

        $object->getHTMLFragment();
    }

    /**
     * @expectedException RecensusWidgetException
     */
    public function testGetHTMLFragmentShouldThrowIfNoProductDataPresent() {

        $object = new RecensusWidget('0000000', '000000', null, true);

        $object->getHTMLFragment();
    }
    
    

    // The iframe URL should contain elements passed into the object at 
    // construct time. Also the URL should contain the correct identifying 
    // hash.

    public function testGetDataPropertyShouldReturnStringWithCorrectParametersAndHash() {

        $data = $this->getWellFormedData();

        $object = new RecensusWidget('00000', '11111', $data, true);

        $dataProp = $object->getDataProperty();

        $expectedDataProp = "url=http://cool-shoes.com/product/cool-shoe-1&mid=00000&brand=Cool Shoe Maker&mpn=Cool Shoes&gtin=00000000000&type=p&lang=en&title=Super Cool Shoes&info=These shoes are off the hook!&hash=47a126ea30cfd0dbc26cd9b33bd0e8cc";

        $this->assertEquals($expectedDataProp, $dataProp, "Expected $expectedDataProp but got $dataProp");
    }

    // The HTTP client should be used to query recensus for product review html
    // and should return the string.

    public function testGetHTMLFragmentUsesCorrectURLAndHash() {

        $expectedUrl = "http://app.recensus.com/widget/api/get?url=http%3A%2F%2Fcool-shoes.com%2Fproduct%2Fcool-shoe-1&mid=00000&brand=Cool+Shoe+Maker&mpn=Cool+Shoes&gtin=00000000000&type=p&lang=en&title=Super+Cool+Shoes&info=These+shoes+are+off+the+hook%21&hash=47a126ea30cfd0dbc26cd9b33bd0e8cc";

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

        $object = new RecensusWidget('00000', '11111', $data);
        $object->setHttpClient($client);

        $html = $object->getHTMLFragment();

        $this->assertEquals('<p>Some HTML String</p>', $html);
    }

    // A failed HTTP request or a request error should trigger the appropriate error

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testGetHTMLFragmentErrorsOnBadResponse() {

        $expectedDataProp = "http://app.recensus.com/widget/api/get?url=http%3A%2F%2Fcool-shoes.com%2Fproduct%2Fcool-shoe-1&mid=00000&brand=Cool+Shoe+Maker&mpn=Cool+Shoes&gtin=00000000000&type=p&lang=en&title=Super+Cool+Shoes&info=These+shoes+are+off+the+hook%21&hash=47a126ea30cfd0dbc26cd9b33bd0e8cc";

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

        $object = new RecensusWidget('00000', '11111', $data);
        $object->setHttpClient($client);

        $html = $object->getHTMLFragment();
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testGetHTMLFragmentErrorsOnRequestError() {

        $expectedDataProp = "http://app.recensus.com/widget/api/get?url=http%3A%2F%2Fcool-shoes.com%2Fproduct%2Fcool-shoe-1&mid=00000&brand=Cool+Shoe+Maker&mpn=Cool+Shoes&gtin=00000000000&type=p&lang=en&title=Super+Cool+Shoes&info=These+shoes+are+off+the+hook%21&hash=47a126ea30cfd0dbc26cd9b33bd0e8cc";

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

        $object = new RecensusWidget('00000', '11111', $data);
        $object->setHttpClient($client);

        $html = $object->getHTMLFragment();
    }

    /**
     * @expectedException RecensusWidgetException
     */
    public function testGetHTMLFragmentThrowsOnBadResponse() {
        $expectedDataProp = "http://app.recensus.com/widget/api/get?url=http%3A%2F%2Fcool-shoes.com%2Fproduct%2Fcool-shoe-1&mid=00000&brand=Cool+Shoe+Maker&mpn=Cool+Shoes&gtin=00000000000&type=p&lang=en&title=Super+Cool+Shoes&info=These+shoes+are+off+the+hook%21&hash=47a126ea30cfd0dbc26cd9b33bd0e8cc";

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

        $object = new RecensusWidget('00000', '11111', $data, true);
        $object->setHttpClient($client);

        $html = $object->getHTMLFragment();
    }

    /**
     * @expectedException RecensusWidgetException
     */
    public function testGetHTMLFragmentThrowsOnRequestError() {
        $expectedDataProp = "http://app.recensus.com/widget/api/get?url=http%3A%2F%2Fcool-shoes.com%2Fproduct%2Fcool-shoe-1&mid=00000&brand=Cool+Shoe+Maker&mpn=Cool+Shoes&gtin=00000000000&type=p&lang=en&title=Super+Cool+Shoes&info=These+shoes+are+off+the+hook%21&hash=47a126ea30cfd0dbc26cd9b33bd0e8cc";

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

        $object = new RecensusWidget('00000', '11111', $data, true);
        $object->setHttpClient($client);

        $html = $object->getHTMLFragment();
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
            'gtin' => "00000000000",
            'mpn' => 'Cool Shoes',
            'brand' => 'Cool Shoe Maker',
            'url' => 'http://cool-shoes.com/product/cool-shoe-1',
            'title' => 'Super Cool Shoes',
            'lang' => 'en',
            'type' => 'p',
            'info' => 'These shoes are off the hook!'
        );

        foreach ($omitFields as $field) {
            unset($data[$field]);
        }

        return $data;
    }

}

