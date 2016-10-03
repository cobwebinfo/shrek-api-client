<?php namespace Cobwebinfo\ShrekApiClient;

use GuzzleHttp\ClientInterface;
use League\OAuth2\Client\Token\AccessToken;

interface ApiConnector
{
    public function fetchAccessToken();
    public function httpClient();
    public function clientId();
    public function clientSecret();
}