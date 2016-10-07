<?php namespace Cobwebinfo\ShrekApiClient\Auth;

use Cobwebinfo\ShrekApiClient\Support\Arrayable;
use Symfony\Component\Yaml\Yaml;

/**
 * Class OAuthParameters
 * @package Cobwebinfo\ShrekApiClient\Auth
 */
abstract class OAuthParameters implements Arrayable
{
    /**
     * @var array
     */
    protected $clientId = '';
    protected $clientSecret = '';
    protected $redirectUri = '';
    protected $urlAuthorize = '';
    protected $urlAccessToken = '';
    protected $urlResourceOwnerDetails = '';

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'redirectUri' => $this->redirectUri,
            'urlAuthorize' => $this->urlAuthorize,
            'urlAccessToken' => $this->urlAccessToken,
            'urlResourceOwnerDetails' => $this->urlResourceOwnerDetails
        );
    }
}