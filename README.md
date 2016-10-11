# S.H.R.E.K API Client

This library can be used to pull data from the various public endpoints provided by the SHREK API. The client has been created with ease of use in mind, simply provide your API ID and private key and use the instructions below to proceed.

##Compatibility

| Client Version | PHP Version |
| -------------- | ----------- |
| 1.0.*          | 5.3 - 5.4   |
| 1.2.*          | 5.5+        |
 
## Installation

The suggested installation method is via [composer](https://getcomposer.org/):

```sh
php composer.phar require "cobwebinfo/shrek-api-client:1.2.*"
```

## Requesting access to the API.

Please contact Cobwebinfo at @ enquiries@cobwebinfo.com to access an API key.

## Usage
The ShrekServiceProvider class provides a neat wrapper for instantiating the 
various clients needed to access the API. You can manually instantiate the clients
if you do not wish to use it, however.

You can get an instance as follows:

```php
$provider = new \Cobwebinfo\ShrekApiClient\ShrekServiceProvider([
                'client_id' => 1,
                'client_secret'=>'be7ac9d3752e70953c5716fa31478800'
]);
```

I would suggest adding this as a singleton to your service container. If you use Laravel for example,
you could do the following:

```php
$this->app->singleton(\Cobwebinfo\ShrekApiClient\ShrekServiceProvider::class, function() {
            return new \Cobwebinfo\ShrekApiClient\ShrekServiceProvider([
                'client_id' => 1,
                'client_secret'=>'be7ac9d3752e70953c5716fa31478800'
            ]);
        });

```

The array passed to the provider is used for configuration. To see the available options, refer
to the [Config.yaml file](src/Cobwebinfo/ShrekApiClient/config.yaml).

**Once you have a provider instance, you can access the various clients as follows:**

```php
$keywordClient = $provider->getKeywordClient();
```

You can then access data from the API as follows:

```php
  try {
        $response = $keywordClient->paginate(1, 4, []);
    } catch(IdentityProviderException $e) {
        var_dump('Authentication error: ' . $e->getMessage());
    }

    if($response->wasSuccessful()) {
        foreach($response->body['data']['items'] as $key => $keyword) {
            echo "<h3> $key </h3>";

            var_dump($keyword);
        }
    }

```

#### Caching
**Please note:** By default the app uses the 'NullStore' cache class. This is an implementation of
the null object pattern, and as you may have guessed does not cache anything. If you intend to use
this method, you will need to implement your own caching to avoid hitting API limits. Alternatively,
if your application supports APC or memcache, you can use one of the inbuilt classes to handle
caching automatically. To do so, use the config below:

```php
$provider = new \Cobwebinfo\ShrekApiClient\ShrekServiceProvider([
                'client_id' => 1,
                'client_secret'=>'be7ac9d3752e70953c5716fa31478800',
                'cache_driver' => 'memcache' OR 'apc'
]);
```

If you wish to roll your own cache implementation then create a new class which uses the 'Cobwebinfo\ShrekApiClient\Cache\Contracts\Store'
interface and pass the fully qualified name into the Provider, as follows:

```php
$provider = new \Cobwebinfo\ShrekApiClient\ShrekServiceProvider([
                'client_id' => 1,
                'client_secret'=>'be7ac9d3752e70953c5716fa31478800',
                'cache_driver' => '\Your\Namespace\ClassName'
]);
```

#### Http Clients
By default Guzzle is used as the HTTP client. If you prefer not to use guzzle, then an alternative
implementation is provided. To use this, provide the following config:

```php
$provider = new \Cobwebinfo\ShrekApiClient\ShrekServiceProvider([
                'client_id' => 1,
                'client_secret'=>'be7ac9d3752e70953c5716fa31478800',
                'http_client' => 'asika'
]);
```

As with caching, you can also roll your own HTTP client should you so choose. Simply create a new 
class implementing the 'Cobwebinfo\ShrekApiClient\Support\HttpRequester' interface and pass in
the full qualified name, as follows:

**Please note:** The class should return a '\Psr\Http\Message\ResponseInterface' instance.

```php
$provider = new \Cobwebinfo\ShrekApiClient\ShrekServiceProvider([
                'client_id' => 1,
                'client_secret'=>'be7ac9d3752e70953c5716fa31478800',
                'http_client' => '\Your\Namespace\ClassName'
]);
```

#### Config

The package provides a Yaml reader sħould you want to store your client id, client secret or other
config in a yaml file. It works as follows:

```php
Yaml::parse(file_get_contents(__DIR__ . '/config.yaml'));;
```

The above would return an associative array, which you could then pass into the ShrekServiceProvider.

## Todo

- Clear/bypass cache?
- Packagist

