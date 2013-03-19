<?php

/**
 * Class used to generate the IFrame URL for the Recensus widget and to obtain
 * the SEO freindly HTML to render alongside the widget.
 * 
 * @author Ben Waine <ben@recensus.com>
 */
class Recensus_Widget {

    /**
     * User ID issued by Recensus to merchants.
     * 
     * @var string
     */
    protected $userId;

    /**
     * Merchant ID issued by Recensus to merchants.
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
     * Data about a product used to identify it with the Recensus service.
     * 
     * @var array
     */
    protected $productData;

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
     * URL of the HTML Frag Endpoint
     * 
     * @var string
     */
    protected $fragURL = "https://app.recensus.com/widget/api/get";

    /**
     * Initiaise an instance of the RecensusWidget class.
     * 
     * @param string  $merchantId      Identifying token given to merchants by Recensus
     * @param string  $merchantSecret  Secret shared between merchant and Recensus for hashing requests.
     * @param array   $productData     Array of product data used to render the widget
     * @param boolean $throwExceptions If true will throw exceptions rather than emit notices.
     * 
     * @return RecensusWidget
     */
    public function __construct($userId, $merchantId, $merchantSecret, $productData = null, $throwExceptions = false) {

        $this->throwExceptions = $throwExceptions;
        
        $this->userId = $userId;
        $this->merchantId = $merchantId;
        $this->merchantSecret = $merchantSecret;

        if (!is_null($productData)) {
            $this->validateProductData($productData);
            $this->productData = $productData;
        }

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
    public function getMerchantToken() {
        return $this->merchantId;
    }

    /**
     * Sets the merchant token issued by recensus to the merchant.
     * 
     * @param string $merchantId
     * 
     * @return void
     */
    public function setMerchantToken($merchantId) {
        $this->merchantId = $merchantId;
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
    public function setSharedSecret($sharedSecret) {
        $this->merchantSecret = $sharedSecret;
    }

    /**
     * Gets the product data used to generate the widget.
     * 
     * @return array
     */
    public function getProductData() {
        return $this->productData;
    }

    /**
     * Sets the product data used to render the widget.
     * 
     * @param array $productData
     * 
     * @return void
     */
    public function setProductData($productData) {
        $this->validateProductData($productData);
        $this->productData = $productData;
    }

    /**
     * Returns the URL in used to call the Recensus HTML Fragment endpoint.
     * 
     * @return string
     */
    public function getHtmlEndpointURL() {
        return $this->fragURL;
    }

    /**
     * Overrides the URL of the HTML Frag endpoint. Used in testing.
     * 
     * @param string $fragURL
     */
    public function setHtmlEndpointURL($fragURL) {
        $this->fragURL = $fragURL;
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
     * Uses the current product data and queries Recensus and returns an SEO 
     * friendly HTML fragment which can be embedded on merchnats product page. 
     * 
     * @return string 
     */
    public function getHTML() {

        if (!isset($this->productData)) {
            $this->handleError("No product data set");
        }

        $callingUrl = $this->fragURL . "?" . $this->getUrlFragment();

        try {

            $response = $this->httpClient
                    ->setUri($callingUrl)
                    ->setMethod()
                    ->request();

            if ($response->isSuccessful()) {
                return $response->getBody();
            } else {
                $this->handleError('Recieved ' . $response->responseCodeAsText() . 'from ' . $callingUrl);
            }
        } catch (Exception $e) {
            $this->handleError($e->getMessage());
        }
    }

    /**
     * Uses the current product data and generates the data property used in the
     * <div class="recensus"> to display the relevant widget.
     * 
     * @return string 
     */
    public function getDataProperty() {

        if (!isset($this->productData)) {
            $this->handleError("No product data set");
        }

        $frag = $this->getUrlFragment(true);

        return $frag;
    }

    /**
     * Validates the product data used to create Recensus URLS.
     * 
     * @param array $data
     * 
     * @return boolean
     */
    protected function validateProductData($data) {
       
        if (!isset($data['sku']) || empty($data['sku'])) {
            $this->handleError('SKU must be set in productData');
        }

        if (!isset($data['name']) || empty($data['name'])) {
            $this->handleError('Name must be set in productData');
        }

        if (!isset($data['url']) || empty($data['url'])) {
            $this->handleError('URL must be set in productData');
        }

        return true;
    }

    /**
     * Creates and returns the URL fragment used by getIframeUrl and 
     * getHTMLFragment.
     * 
     * @return string
     */
    protected function getUrlFragment($encode = true) {

        $hashStr = "";
        $parts = array();


        if (isset($this->productData['sku'])) {
            $parts['sku'] = $this->productData['sku'];
            $hashStr .= $this->productData['sku'];
        }
        
        if (isset($this->productData['name'])) {
            $parts['name'] = $this->productData['name'];
            $hashStr .= $this->productData['name'];
        }
        
        if (isset($this->productData['url']))
            $parts['url'] = $this->productData['url'];

        if (isset($this->merchantId))
            $parts['merchantId'] = $this->merchantId;

        if (isset($this->userId))
            $parts['userId'] = $this->userId;

        if (isset($this->productData['brand'])) {
            $parts['brand'] = $this->productData['brand'];
        }

        if (isset($this->productData['mpn'])) {
            $parts['mpn'] = $this->productData['mpn'];
        }

        if (isset($this->productData['gtin'])) {
            $parts['gtin'] = $this->productData['gtin'];
        }

        if (isset($this->productData['type']))
            $parts['type'] = $this->productData['type'];

        if (isset($this->productData['lang']))
            $parts['lang'] = $this->productData['lang'];

        if (isset($this->productData['title']))
            $parts['title'] = $this->productData['title'];

        if (isset($this->productData['info']))
            $parts['info'] = $this->productData['info'];

        if (isset($this->productData['price']))
            $parts['price'] = $this->productData['price'];

        $parts['hash'] = md5($hashStr . $this->merchantSecret);

        $frag = "";

        foreach ($parts as $key => $value) {

            if ($encode) {
                $value = urlencode($value);
            }

            $frag .= $key . '=' . $value . '&';
        }

        $finalFrag = substr($frag, 0, -1);

        return $finalFrag;
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
            throw new Recensus_Widget_Exception($errorString);
        } else {
            trigger_error('RECENSUS WIDGET ERROR: ' . $errorString);
        }
    }

}