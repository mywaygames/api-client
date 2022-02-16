# MywayGames API Client
## _A client for MywayGames shop API_

MywayGames API Client is a PHP library that helps you integrate your MywayGames account into your own e-commerce system, allowing you to place orders, query balance, list products, sync prices automatically.

For more info please [view MywayGames API Documentation](https://mywaygames.docs.apiary.io/)

## Installation
Using composer

```sh
composer require mywaygames/api-client
```

**If you receive an error `Could not find a version of package mywaygames/api-client` please upgrade to composer 2**

From github

```sh
git clone https://github.com/mywaygames/api-client.git
cd api-client
composer install
```
## Usage

After including vendor/autoload.php you can initiate the client like this:

```php
$apiClient = new MywayGames\ApiClient;

// Set the API base URI, if you are not sure please contact your admin
$apiClient->setBaseUri('https://test.myway-games.com/api/v1/');
```

To get a test account (only available on test.myway-games.com):

```php
var_dump($apiClient->createTestAccount()->response());
```


To authenticate with the API using your username/password:

```php
$apiClient->auth(["username" => "YOUR_USERANEM","password" => "YOUR_USERANEM"]);
```

Authenticate using a valid API token (api_key)
```php
$apiClient->auth(["api_key" => "YOUR_API_KEY"]);
```

Get your account info
```php
$apiClient->getUserData()->response()->userdata;
```

Get your account balance
```php
$apiClient->getUserData()->response()->userdata->balance;
```

Get a list of main products
```php
$apiClient->products()->response();
```

Get a list of ALL denominations 
```php
$apiClient->denominations('all')->response();
```

Get a list of main products
```php
$apiClient->products()->response();
```

Get a list of denominations for a specific category i.e: 2
```php
$apiClient->denominations(2)->response();
```

Create an order
```php
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
```

Check the status of your order "Typically orders take 2 minutes to be processed"
```php
$apiClient->orderDetails(21)->response();
```
## Dependencies

MywayGames API Client uses guzzlehttp to connect to the remote API
| Dependency | README |
| ------ | ------ |
| Guzzlehttp | [Github](https://github.com/guzzle/guzzle/blob/master/README.md) |

