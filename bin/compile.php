#!/usr/bin/env php
<?php

// Compiles the SDK and required Libs into one PHP file.

use Symfony\Component\ClassLoader\ClassCollectionLoader;
use Symfony\Component\Finder\Finder;

require_once __DIR__ . '/../vendor/autoload.php';

// Ensure the build directory is writtable and that any previous 
// compiles are deleted. 

@mkdir(__DIR__ . '/../build', 0777, true);
@unlink(__DIR__ . '/../build/recensus.php');


$classes = array(
    'Zend_Exception',
    'Zend_Registry',
    'Zend_Uri',
    'Zend_Uri_Exception',
    'Zend_Http_Client',
    'Zend_Http_Exception',
    'Zend_Http_Response',
    'Zend_Http_Client_Exception',
    'Zend_Http_Client_Adapter_Curl',
    'Zend_Http_Client_Adapter_Exception',
    'Zend_Http_Client_Adapter_Interface',
    'Zend_Http_Client_Adapter_Proxy',
    'Zend_Http_Client_Adapter_Socket',
    'Zend_Http_Client_Adapter_Stream',
    'Zend_Http_Client_Adapter_Test',
    'Zend_Http_Response_Stream',
    'Zend_Uri_Http',
    'Zend_Validate_Abstract',
    'Zend_Validate_Exception',
    'Zend_Validate_Hostname',
    'Zend_Validate_Interface',
    'Zend_Validate_Ip',
    'Zend_Loader',
    'Zend_Loader_Autoloader',
    'Zend_Validate_Hostname_Biz',
    'Zend_Validate_Hostname_Cn',
    'Zend_Validate_Hostname_Com',
    'Zend_Validate_Hostname_Jp',
    "RecensusApi",
    "RecensusApiException",
    "RecensusWidget",
    "RecensusWidgetException",
    
);

$ccl = new ClassCollectionLoader();

$ccl->load($classes, __DIR__ . '/../build', 'recensus', false);

$debug = getenv('COMPILE_DEBUG');

if (!$debug) {
    file_put_contents(__DIR__ . '/../build/recensus.php', "<?php " . str_replace('<?php', '', php_strip_whitespace(__DIR__ . '/../build/recensus.php'))
    );
}