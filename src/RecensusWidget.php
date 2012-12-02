<?php

/**
 * Class used to generate the IFrame URL for the Recensus widget and to obtain
 * the SEO freindly HTML to render alongside the widget.
 * 
 * @author Ben Waine <ben@recensus.com>
 */
class RecensusWidget {

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
     * URL of IFrame enpoint.
     * 
     * @var String
     */
    protected $iframeURL = "http://app.recensus.com/widget/iframe";

    /**
     * URL of the HTML Frag Endpoint
     * 
     * @var string
     */
    protected $fragURL = "";

    /**
     * Initiaise an instance of the RecensusWidget class.
     * 
     * @param string $merchantToken  Identifying token given to merchants by Recensus
     * @param string $merchantSecret Secret shared between merchant and Recensus for hashing requests.
     * @param array  $productData    Array of product data used to render the widget
     * 
     * @return RecensusWidget
     */
    public function __construct($merchantToken, $merchantSecret, array $productData, $throwExceptions = false) {

        $this->throwExceptions = $throwExceptions;

        $this->merchantToken = $merchantToken;
        $this->merchantSecret = $merchantSecret;

        $this->validateProductData($productData);

        $this->productData = $productData;
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
        return $this->sharedSecret;
    }

    /**
     * Sets the shared secret.
     * 
     * @param string $sharedSecret
     * 
     * @return void
     */
    public function setSharedSecret($sharedSecret) {
        $this->sharedSecret = $sharedSecret;
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
        $this->productData = $productData;
    }

    /**
     * Overrides the URL of the IFrame endpoint. Used in testing.
     * 
     * @param string $iframeURL
     * 
     * @return void
     */
    public function setIframeURL($iframeURL) {
        $this->iframeURL = $iframeURL;
    }

    /**
     * Overrides the URL of the HTML Frag endpoint. Used in testing.
     * 
     * @param string $fragURL
     */
    public function setFragURL($fragURL) {
        $this->fragURL = $fragURL;
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
        $this->client = $client;
    }

    /**
     * Sets whether to throw exceptions. The default is not to throw exceptions
     * and instead to create PHP_Notices. This to allow the implementing site
     * to continue to render despite the review widget failing to render.
     *  
     * @param type $throw
     * 
     * @return void
     */
    public function setThrowExceptions($throw) {
        
    }

    /**
     * Uses the current product data and queries Recensus and returns an SEO 
     * friendly HTML fragment which can be embedded on merchnats product page. 
     * 
     * @return string 
     */
    public function getHTMLFragment() {
        
    }

    /**
     * Uses the current product data and generates the URL of the IFrame to
     * embedd the recensus widget on the merchants product page.
     * 
     * @return string 
     */
    public function getIFrameUrl() {

        $base = $this->iframeURL;
        $hashStr = "";
        $parts = array();

        if (isset($this->productData['url']))
            $parts['url'] = $this->productData['url'];        
        
        if (isset($this->merchantToken))
            $parts['mid'] = $this->merchantToken;
        
        if (isset($this->productData['brand'])) {
            $parts['brand'] = $this->productData['brand'];
            $hashStr .= $this->productData['brand'];
        }
        
        if (isset($this->productData['mpn'])) {
            $parts['mpn'] = $this->productData['mpn'];
            $hashStr .= $this->productData['mpn'];
        }
        
        if (isset($this->productData['gtin'])) {
            $parts['gtin'] = $this->productData['gtin'];
            $hashStr .= $this->productData['gtin'];
        }
        
        if (isset($this->productData['type']))
            $parts['type'] = $this->productData['type'];

        if (isset($this->productData['lang']))
            $parts['lang'] = $this->productData['lang'];

        if (isset($this->productData['title']))
            $parts['title'] = $this->productData['title'];

        if (isset($this->productData['info']))
            $parts['info'] = $this->productData['info'];
        
        $parts['hash'] = md5($hashStr.$this->merchantSecret);
        
        $frag = "?";
        
        foreach($parts as $key => $value) {
            $frag .= $key .'=' . urlencode($value) . '&';
        }
        
        $frag = substr($frag, 0, -1);

        return $base . $frag;
    }

    /**
     * Validates the product data used to create Recensus URLS.
     * 
     * @param array $data
     * 
     * @return boolean
     */
    protected function validateProductData($data) {

        // Check required ID params are preset (GTIN or mpn + brand)
        $gtin = (isset($data['gtin']) && !empty($data['gtin'])) ? $data['gtin'] : null;
        $mpn = (isset($data['mpn']) && !empty($data['mpn'])) ? $data['mpn'] : null;
        $brand = (isset($data['brand']) && !empty($data['brand'])) ? $data['brand'] : null;

        if (is_null($gtin) && (is_null($brand) || is_null($mpn))) {
            $this->handleError('Either gtin or brand + mpn must be set in productData array');
        }

        // Check URL is present and valid
        $url = (isset($data['url']) && !empty($data['url'])) ? $data['url'] : null;

        if (is_null($url)) {
            $this->handleError('URL of the product on the merchant site must be passed in productData array');
        }

        return true;
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
            throw new RecensusWidgetException($errorString);
        } else {
            trigger_error('RECENSUS WIDGET ERROR: ' . $errorString);
        }
    }

    // http://app.recensus.com/widget/iframe?url=http://www.williamsbrosbrew.com/&mid=2&brand=Williams Bros. Brewing&mpn=Fraoch 22&gtin=5034743300122&hash=b2f9bec21b8db9a5a2d375db3962c5bc&title=FRAOCH 22#http%3A%2F%2Fapp.recensus.com%2Fdemo%2Fwbb
}