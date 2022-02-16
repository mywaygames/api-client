<?php

/**
 *
 * A list of example snippets to use MywayGames API Client
 * @package MywayGames
 *
 */
 
include("vendor/autoload.php");

echo "<pre>";
$apiClient = new MywayGames\ApiClient;

// Set the API base URI, if you are not sure please contact your admin
$apiClient->setBaseUri('https://test.myway-games.com/api/v1/');

// uncomment to  guzzlehttp debug
// $apiClient->enableDebug();

// Authenticate using a username and password
$apiClient->auth(["username" => "YOUR_USERANEM","password" => "YOUR_USERANEM"]);

// Authenticate using a valid API token (api_key)
//$apiClient->auth(["api_key" => "YOUR_API_KEY"]);


// Uncomment any of the following examples to test it.


// Get your account info
//var_dump($apiClient->getUserData()->response()->userdata;

// Get your account balance
//var_dump($apiClient->getUserData()->response()->userdata->balance;

// Get a list of main products
//var_dump($apiClient->products()->response());

// Get a list of ALL denominations 
//var_dump($apiClient->denominations('all')->response());

// Get a list of denominations for a specific category
//var_dump($apiClient->denominations(2)->response());


// Create an order

/*
$order_args = [];

// Unuque uuidv4 token that you need to generate to avoid placing duplicate orders.
$order_args['orderToken'] = $apiClient->generateToken();

// The denomination_id you want to buy "which you received by calling ->denominations()"
$order_args['denomination_id'] = 1;

// Optional, default is 1
$order_args['qty'] = 1;

// Required only if the product you want to order is a topup product "i.e has require_playerid = true"
$order_args['args'] = ["playerid" => "111"];


var_dump($apiClient->createOrder($order_args)->response());

*/

// Check the status of your order "Typically orders take 2 minutes to be processed"
//var_dump($apiClient->orderDetails(21)->response());

echo "</pre>";
