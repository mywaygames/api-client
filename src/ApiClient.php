<?php
namespace MywayGames;
/**
 *
 * Class ApiClient
 * A client for MywayGames shop API
 * @package MywayGames
 *
 */
class ApiClient
{
    private $base_uri;
    private $endpoint;
    private $http_method = 'GET';
    private $api_key;
    private $user_data;
    private $last_response;
    private $debug = false;
    function __construct(){
        
    }

    public function __call($method,$arguments) {
        if(method_exists($this, $method)) {
            $result = call_user_func_array(array($this,$method),$arguments);
        }else{
            $result = call_user_func_array(array($this,'notFound'),["method" => $method]);
        }
        return $result;
    }
	
    /**
     *
     * Authenticate with the API using either username and password or a bearer token 
     *
     * @param array $args Array containing username and password or a bearer token
     * @return ApiClient
     *
     */
    private function auth($args){
        if(isset($args['username']) AND isset($args['password'])){
            $request_body = '{"username":"' . $args['username'] . '","password":"' . $args['password'] . '"}';
            $this->_setEndpoint('login')->_setHttpMethod('post');
            $request_args = ["request_body" => $request_body];
        }elseif(isset($args['api_key'])){
            $this->api_key = $args['api_key'];
            return $this;
        }else{
            throw new \Exception("Invalid auth data");
        }
        $result = json_decode($this->_request($request_args));
        if($result AND $result->result == 'success'){
            $this->api_key = $result->userdata->api_key;
        }
        return $this;
    }
	
	
    /**
     *
     * Create a test account (Only works on the test server)
     *
     * @return ApiClient
     *
     */
    private function createTestAccount(){
        if(strpos($this->base_uri, "test.myway-games.com") === false){
			return $this;
		}
		$this->_setEndpoint('createTestAccount');
        $result = json_decode($this->_request());
        return $this;
    }
	
	
    /**
     *
     * retrieve currently authenticated user data and store it in the response
     *
     * @return ApiClient
     *
     */
    private function getUserData(){
        $this->_setEndpoint('getUserData');
        $result = json_decode($this->_request());
        return $this;
    }
	
    /**
     *
     * Create an order
     * @param    array $args{
     *    @type string $orderToken The unique order token correspoding to your order
     *    @type int $denomination_id The denomination id of the product you want to purchase
     *    @type int $qty The number of products you want to purcahse
     *    @type array $args{
     *        @type string $playerid The player id in case the order is a topup
     *    }
     * }
     * @return ApiClient
     *
     */
    private function createOrder($args){
        if(!isset($args['denomination_id'], $args['orderToken'])){
            throw new \Exception("Invalid order arguments");
        }
        $request_body = '{
            "orderToken":"' . $args['orderToken'] . '",
            "item":{
            "denomination_id":' . $args['denomination_id'] . ',
            "qty":' . (isset($args['qty']) ? $args['qty'] : 1) . '
            },
            "args":' . (isset($args['args']) ? json_encode($args['args']) : '[]') . '
        }';
        $request_args = ["request_body" => $request_body];
        $this->_setEndpoint('createOrder')->_setHttpMethod('POST');
        $result = json_decode($this->_request($request_args));
        return $this;
    }
	
	/**
     *
     * Get details of a specific order
     *
     * @param int $order_id The ID of the order you want to fetch
     * @return ApiClient
     *
     */
    private function orderDetails($order_id){
        $this->_setEndpoint('orderDetails');
        $request_args = ["query" => ["order_id" => $order_id]];
        $result = json_decode($this->_request($request_args));
        return $this;
    }
	
	/**
     *
     * Get a list of main products
     *
     * @return ApiClient
     *
     */
    private function products(){
        $this->_setEndpoint('products');
        $result = json_decode($this->_request());
        return $this;
    }
	
	/**
     *
     * Get a list of main products
     *
     * @return ApiClient
     *
     */
    private function denominations($product_id){
		$this->_setEndpoint('products');
		if($product_id == 'all'){
			$product_id = -1;
		}
        $request_args = ["query" => ["product_id" => $product_id]];
        $result = json_decode($this->_request($request_args));
        return $this;
    }
	
	/**
     *
     * Get details of all orders
     *
     * @param array $args 
     * @return ApiClient
     *
     */
    private function orders($args = ["limit" => 25, "page" => 1]){
        $this->_setEndpoint('orders');
        $result = json_decode($this->_request($args));
        return $this;
    }
	
	/**
     *
     * Get the response of the last request
     *
     * @return string JSON
     *
     */
    private function response(){
        return json_decode($this->last_response);
    }
	
    /**
     *
     * Check whether or not there is an active API token
     *
     * @return boolean
     *
     */
    private function isLoggedIn(){
        return ($this->api_key ? true : false);
    }
	
	/**
     *
     * Get details of a specific order
     *
     * @param string $uri set the base uri for the API
     * @return ApiClient
     *
     */
    private function setBaseUri($uri){
        $this->base_uri = $uri;
        return $this;
    }
	
    /**
     *
     * Generate a random uuidv4 token to be used when creating an order
     *
     * @return string uuidv4
     *
     */
    private function generateToken(){
        $data = openssl_random_pseudo_bytes(16);
        assert(strlen($data) == 16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
	
    /**
     *
     * Sets the debug flag for Guzzlehttp to true
     *
     * @param boolean $debug
     * @return ApiClient
     *
     */
    private function enableDebug($debug){
        $this->debug = $debug;
        return $this;
    }
	
	/**
     *
     * Throws an exception in case an invalid method was called
     *
     * @param string $method the name of the invalid method
     *
     */
    private function notFound($method){
        throw new \Exception('The method ' . $method . ' cannot be found or is inaccssible');
    }
    
	/**
     *
     * Set the endpoint for the API request
     *
     * @param string $endpoint
     * @return ApiClient
     *
     */
    private function _setEndpoint($endpoint){
        $this->endpoint = $endpoint;
        return $this;
    }
    
	/**
     *
     * Set the HTTP method for the API request
     *
     * @param string $method
     * @return ApiClient
     *
     */
    private function _setHttpMethod($method){
        if(!in_array(strtolower($method), ['get','head','post','delete','put','connect','options','trace','patch'])){
            $method = 'get';
        }
        $method = strtoupper($method);
        $this->http_method = $method;
        return $this;
    }
    
	/**
     *
     * Creates an instance of GuzleHttp and sends a request to the API
     *
     * @param array $args an array containing arguments for GuzleHttp
     * @return string Raw JSON data from the response
     *
     */
    private function _request($args = []){
        $client = new \GuzzleHttp\Client(["base_uri" => $this->base_uri]);
        
        $client_args = [];
        $client_args['debug'] = $this->debug;
        $client_args['headers'] = [];
        if(isset($args['headers'])){
            $client_args['headers'] = $args['headers'];
        }
        if(isset($args['query'])){
            $client_args['query'] = $args['query'];
        }
        $client_args['headers']['Accept'] = 'application/json';
        if($this->api_key){
            $client_args['headers']['Authorization'] = 'Bearer ' . $this->api_key;    
        }
        if($this->http_method == 'POST'){
            $client_args['headers']['Content-Type'] = 'application/json';
            $client_args['body'] = $args['request_body'];
        }
        $res = $client->request($this->http_method, $this->endpoint, $client_args);
        $this->_setHttpMethod("GET");
        $this->last_response = $res->getBody();
        return $this->last_response;
    }
    
}
