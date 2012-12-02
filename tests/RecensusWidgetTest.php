<?php

class RecensusWidgetTest extends PHPUnit_Framework_TestCase {

    protected $object;
    protected $httpClient;

    public function setUp() {
        
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

    public function testConstructShouldNotThrowWhenOptionalParamsAreAbsent() {

        $data = $this->getWellFormedData(array('type', 'lang', 'title', 'info'));

        $object = new RecensusWidget('0000000', '000000', $data, true);  
        
        $this->assertInstanceOf('RecensusWidget', $object);
    }

    // The iframe URL should contain elements passed into the object at 
    // construct time. Also the URL should contain the correct identifying 
    // hash.
    
    public function testGetIFrameUrlShouldReturnStringWithCorrectParametersAndHash() {
        
        $data = $this->getWellFormedData();
        
        $object = new RecensusWidget('00000', '11111', $data, true);
        
        $url = $object->getIFrameUrl();
        
        $expectedUrl = "http://app.recensus.com/widget/iframe?url=http%3A%2F%2Fcool-shoes.com%2Fproduct%2Fcool-shoe-1&mid=00000&brand=Cool+Shoe+Maker&mpn=Cool+Shoes&gtin=00000000000&type=p&lang=en&title=Super+Cool+Shoes&info=These+shoes+are+off+the+hook%21&hash=47a126ea30cfd0dbc26cd9b33bd0e8cc";
        
        $this->assertEquals($expectedUrl, $url, "Expected $expectedUrl but got $url");
        
    }
    
    // The HTTP client should be used to query recensus for product review html
    // and should return the string.

    public function testGetHTMLFragmentUsesCorrectURLAndHash() {
        
    }

    // A failed HTTP request should trigger the appropriate error

    public function testGetHTMLFragmentErrorsOnBadResponse() {
        
    }

    /**
     * @expectedException RecensusWidgetException
     */
    public function testGetHTMLFragmentThrowsOnBadResponse() {
        
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
        
        foreach($omitFields as $field) {
            unset($data[$field]);
        }
        
        return $data;
    }

}

