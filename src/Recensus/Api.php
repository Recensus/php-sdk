<?php

/**
 * A PHP Class for interfacing with the Recensus API.
 * 
 * @author ben@recensus.com
 */
class Recensus_Api {

    /**
     * Merchant id issued by Recensus to merchants.
     * 
     * @var string
     */
    protected $merchantId;

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

    /**
     * The base URL to use when making API requests.
     * 
     * @var string
     */
    protected $baseUrl = "https://api.recensus.com/";

    /**
     * The end points to use when making requests to the services offered by
     * the Recensus API.
     * 
     * @var array
     */
    protected $endpoints = array(
        'ccr' => 'merchant/{merchantId}/customer-contact-request'
    );

    /**
     * The last Response recieved from the Recensus API
     * 
     * @var Zend_Http_Response
     */
    protected $lastResponse;

    /**
     * Initialises an instance of the RecensusApi class.
     * 
     * @param string  $merchantId      Identifying token given to merchants by Recensus
     * @param string  $merchantSecret  Secret shared between merchant and Recensus for hashing requests.
     * @param boolean $throwExceptions If true will throw exceptions rather than emit notices.
     * 
     * @return void
     */
    public function __construct($merchantId, $merchantSecret, $throwExceptions = false) {
        $this->merchantId = $merchantId;
        $this->merchantSecret = $merchantSecret;
        $this->throwExceptions = $throwExceptions;
        $this->httpClient = new Zend_Http_Client();
        
        $this->httpClient->setConfig(array(
            "timeout" => 3
        ));
    }

    /**
     * Returns the merchant token issued by Recensus to the merchant.
     * 
     * @return string
     */
    public function getMerchantId() {
        return $this->merchantId;
    }

    /**
     * Sets the merchant token issued by recensus to the merchant.
     * 
     * @param string $merchantId
     * 
     * @return void
     */
    public function setMerchantId($merchantId) {
        $this->merchantId = $merchantId;
    }

    /**
     * Gets the shared secret.
     * 
     * @return string
     */
    public function getMerchantSecret() {
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
     * Gets the current baseurl in use when making requests to the Recensus API.
     * 
     * @return string
     */
    public function getBaseUrl() {
        return $this->baseUrl;
    }

    /**
     * Sets the baseurl used to make requests to the Recensus API.
     * Primarily used to override the URL when testing. 
     * 
     * @param string $baseUrl
     * 
     * @return void
     */
    public function setBaseUrl($baseUrl) {
        $this->baseUrl = $baseUrl;
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

        $url = $this->baseUrl . str_replace('{merchantId}', $this->merchantId, $this->endpoints['ccr']);

        $this->makeRequest($url, 'POST', $data);

        if ($this->wasSuccess()) {

            return $this->parseResponse();
        } else {
            $code = $this->lastResponse->getStatus();
            $body = $this->lastResponse->getBody();
            $errorStr = $code . ': ' . $body;
            $this->handleError($errorStr);
        }

        return false;
    }

    /**
     * Makes a request to the Recensus API.
     * 
     * @return boolean
     */
    protected function makeRequest($url, $method, $data = null) {

        // If the SDK is used multiple times ensure all params are reset.
        $this->httpClient->resetParameters(true);

        $this->httpClient->setHeaders('Accept', 'applicaton/json');
        $this->httpClient->setMethod($method);
        $this->httpClient->setUri($url);

        if (!is_null($data)) {

            $formattedRequest = array(
                'data' => $data,
                'signedRequest' => $this->signRequest($method, $url),
            );

            $jsonEncodedData = json_encode($formattedRequest);

            if (!$jsonEncodedData) {
                $this->handleError("Error json encoding before sending request");
            }

            // Content-Type must be sent or 500 returned
            $this->httpClient->setHeaders('Content-Type', 'application/json');
            $this->httpClient->setRawData($jsonEncodedData);
        }
        try {
            $this->lastResponse = $this->httpClient->request();
        } catch (Exception $e) {
            $this->handleError($e->getMessage());
        }
    }

    /**
     * Uses the Request Method, URL and Merchants Secret to produce a hash 
     * which identifies the request as authentic. 
     * 
     * @return string
     */
    protected function signRequest($method, $url) {

        $hashableStr = $method . $url . $this->merchantSecret;

        $signedRequest = array(
            'token' => $this->merchantId,
            'signature' => md5($hashableStr)
        );
        
        return $signedRequest;
    }

    /**
     * Determines if the last request was a success (1** - 2** code).
     * 
     * @return boolean
     */
    protected function wasSuccess() {

        if (!isset($this->lastResponse)) {
            $this->handleError("Request has not been sent to the API");
        }

        return $this->lastResponse->isSuccessful();
    }

    /**
     * Parses the JSON encoded request body returned by the Recensus API and 
     * returns a PHP array representing the resourse.
     * 
     * @return array
     */
    protected function parseResponse() {

        $parseResult = json_decode($this->lastResponse->getBody(), true);

        if (!$parseResult) {
            $this->handleError("Error decoding response from API.");
        }

        return $parseResult;
    }

    /**
     * Takes the error string supplied and if exception throwing is tuned on 
     * throws an exception. If exception throwing is disabled a PHP Notice is 
     * generated. This allows the page to coninue rendering despite the Recensus
     * widget failing to render.
     * 
     * @param string $errorString
     * 
     * @return void
     */
    protected function handleError($errorString) {

        if ($this->throwExceptions) {
            throw new Recensus_Api_Exception($errorString);
        } else {
            trigger_error('RECENSUS API CALL ERROR: ' . $errorString);
        }
    }

}

