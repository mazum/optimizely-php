<?php
/**
 * @abstract API client for Optimizely.
 * @author Oleg Krivtsov <oleg@webmarketingroi.com.au>
 * @date 03 October 2016
 * @copyright (c) 2016, Web Marketing ROI
 */
namespace WebMarketingROI\OptimizelyPHP;

/**
 * Client for Optimizely REST API https://developers.optimizely.com/rest/v2/.
 */
class OptimizelyApiClient 
{
    /**
     * API key.
     * @var string
     */
    private $apiKey;
    
    /**
     * API version.
     * @var string
     */
    private $apiVersion;
    
    /**
     * CURL handle.
     * @var resource 
     */
    private $curlHandle;
    
    /**
     * Instantiated services (used internally).
     * @var array
     */
    private $services = array();
    
    /**
     * Constructor.
     * @param string $apiKey Optimizely API key.
     * @param string $apiVersion Optional. Currently supported 'v2' only.
     */
    public function __construct($apiKey, $apiVersion='v2')
    {
        if (strlen($apiKey)!=41) {
            throw new \Exception('API key is of wrong length');            
        }
        
        if ($apiVersion!='v2') {
            throw new \Exception('Invalid API version passed');
        }
        
        $this->apiKey = $apiKey;
        $this->apiVersion = $apiVersion;
        
        $this->curlHandle = curl_init();
        if (!$this->curlHandle) {
            throw new \Exception('Error initializing CURL');
        }
    }
    
    /**
     * Returns API version (currently it is always 'v2').
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }
    
    /**
     * Sends an HTTP request to the given URL and returns response in form of array. 
     * @param string $url The URL of Optimizely endpoint (relative, without host and API version).
     * @return array Optimizely response in form of array.
     * @throws \Exception
     */
    public function sendHttpRequest($url)
    {
        // Check if CURL is initialized (it should have been initialized in 
        // constructor).
        if ($this->curlHandle==false) {
            throw new \Exception('CURL is not initialized');
        }
        
        // Produce absolute URL
        $url = 'https://api.optimizely.com/' . $this->apiVersion . $url;
        
        // Set HTTP options.
        curl_setopt($this->curlHandle, CURLOPT_URL, $url);
        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlHandle, CURLOPT_HEADER, false);
        
        // Execute HTTP request and get response.
        $result = curl_exec($this->curlHandle);
        if ($result === false) {
            $code = curl_errno($this->curlHandle);
            $error = curl_error($this->curlHandle);
            throw new \Exception("Failed to send HTTP request to '$url', the error code was $code, error message was: '$error'");
        }        
        
        // Check HTTP response code (it should be equal to 200).
        $info = curl_getinfo($this->curlHandle);
        if ($info['http_code']!=200) {
            throw new \Exception('Unexpected HTTP response code: ' . $info['http_code']);
        }
        
        // JSON-decode response.
        $decodedResult = json_decode($result, true);
        if (!is_array($decodedResult)) {
            throw new \Exception('Could not JSON-decode the Optimizely response. The response was: "' . $result . '"');
        }
        
        // Return the response in form of array.
        return $decodedResult;
    }
    
    /**
     * Provides access to API services (experiments, campaigns, etc.)
     */
    public function __call($name, $arguments)
    {
        $allowedServiceNames = array(
            'audiences',
            'campaigns',
            'events',
            'experiments',
            'pages',
            'projects'
        );
        
        // Check if the service name is valid
        if (!in_array($name, $allowedServiceNames)) {
            throw new \Exception("Unexpected service name: $name");
        }
        
        // Check if such service already instantiated
        if (isset($this->services[$this->apiVersion][$name])) {
            $service = $this->services[$this->apiVersion][$name];
        } else {
            // Instantiate the service
            $apiVersion = $this->apiVersion;
            $serviceName = ucwords($name);
            $className = "\\WebMarketingROI\\OptimizelyPHP\\Service\\$apiVersion\\$serviceName";
            $service = new $className($this); 
            $this->services[$apiVersion][$name] = $service;
        }

        return $service;
    }
}