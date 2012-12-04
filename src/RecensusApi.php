<?php

/**
 * A PHP Class for interfacing with the Recensus API.
 * 
 * @author ben@recensus.com
 */
class RecensusApi {

    /**
     * Merchant tokwn issued by Recensus to merchants.
     * 
     * @var string
     */
    protected $merchantToken;

    /**
     * Shared secret used to create identifying hashes.
     * 
     * @var string 
     */
    protected $merchantSecret;

    /**
     * Http Client - used to make requests to Recensus.
     * 
     * @var Zend_Http_Client
     */
    protected $httpClient;

    /**
     * If true exceptions are thrown on error. If false a PHP Notice is 
     * generated instead.
     * 
     * @var boolean 
     */
    protected $throwExceptions = false;

    public function __construct($merchantToken, $merchantSecret, $throwExceptions = false) {
        $this->merchantToken = $merchantToken;
        $this->merchantSecret = $merchantSecret;
        $this->throwExceptions = $throwExceptions;
    }

    /**
     * Returns the merchant token issued by Recensus to the merchant.
     * 
     * @return string
     */
    public function getMerchantToken() {
        return $this->merchantToken;
    }

    /**
     * Sets the merchant token issued by recensus to the merchant.
     * 
     * @param string $merchantToken
     * 
     * @return void
     */
    public function setMerchantToken($merchantToken) {
        $this->merchantToken = $merchantToken;
    }

    /**
     * Gets the shared secret.
     * 
     * @return string
     */
    public function getSharedSecret() {
        return $this->merchantSecret;
    }

    /**
     * Sets the shared secret.
     * 
     * @param string $sharedSecret
     * 
     * @return void
     */
    public function setMerchantSecret($sharedSecret) {
        $this->merchantSecret = $sharedSecret;
    }

    /**
     * Returns the HTTP client used to make external HTTP calls to Recensus.
     * 
     * @return Zend_Http_Client
     */
    public function getHttpClient() {
        return $this->httpClient;
    }

    /**
     * Override the instance of Zend_Http_Client used to make calls to the 
     * Recensus service. Used for testing.
     * 
     * @param Zend_Http_Client $client
     * 
     * @return void
     */
    public function setHttpClient(Zend_Http_Client $client) {
        $this->httpClient = $client;
    }

    /**
     * Returns true if the Widget will throw exeptions on Errors.
     * 
     * @return boolean
     */
    public function willThrowExceptions() {
        return $this->throwExceptions;
    }

    /**
     * Sets if to throw exceptions. The default is not to throw exceptions
     * and instead to create PHP_Notices. This to allow the implementing site
     * to continue to render despite the review widget failing to render.
     *  
     * @param type $throw
     * 
     * @return void
     */
    public function setThrowExceptions($throw) {
        $this->throwExceptions = $throw;
    }

    /**
     * Makes a request to Recensus to contact a customer who has made a recent 
     * purchase with a merchant. An email will be sent to the customer asking 
     * them to review the product. Note: The email is not sent immediatley,
     * Recensus will determine the interval.
     * 
     *  
     */
    public function makeCustomerContactRequest($data) {
        
    }
    
    /**
     * Makes a request to the Recensus API.
     * 
     * @return boolean
     */
    protected function makeRequest() {
        
    }

    /**
     * Uses the Request Method, URL and Merchants Secret to produce a hash 
     * which identifies the request as authentic. 
     * 
     * @return string
     */
    protected function signRequest() {
        
    }
    
    /**
     * Parses the JSON encoded request body returned by the Recensus API and 
     * returns a PHP array representing the resourse.
     * 
     * @return array
     */
    protected function parseResponse() {
        
    }

}

