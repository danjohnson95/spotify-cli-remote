<?php namespace Danj\Spotify;

use Danj\Spotify\Http;
use Danj\Spotify\Cache;

class AuthHelper{

    /**
     * Instance of the cache helper.
     * @var \Danj\Spotify\Cache
     */
    protected $cache;

    /**
     * The URL a user needs to visit to initiate the OAuth with Spotify.
     * @var string
     */
    protected $baseUrl = "http://spotify-cli-remote.danjohnson.xyz";

	/**
	 * Creates a new instance of the AuthHelper
	 * @return void
	 */
    public function __construct()
    {
        $this->cache = new Cache;
    }

    /**
     * Returns the base url property
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Returns the instance of the cache
     * @return \Danj\Spotify\Cache;
     */
    public function getCache()
    {
        return $this->cache;
    }

	/**
	 * Logs the user out by destroying their stored tokens
	 * @return void
	 */
	 public function logOut()
	 {
		 $this->cache->destroyMany([
			 'access_token',
			 'refresh_token',
			 'expires_at'
		 ]);
	 }

    /**
     * Do they have a valid access token? If not, do they have a refresh token?
     * If so, try to refresh it.
     * @return bool
     */
    public function attemptOrRefreshAuthentication()
    {
        if($this->isAuthorised()) return true;
        if($this->hasRefreshToken() && $this->refreshAccessToken()) return true;
        return false;
    }

    /**
     * Stores the access token, refresh token and time of expiry in the cache.
     * @param  \stdClass $tokens
     * @return void
     */
    public function storeNewTokens($tokens)
    {
        $this->cache->storeMany([
            "access_token"  => $tokens->access_token,
            "refresh_token" => $tokens->refresh_token,
            "expires_at"    => $this->calculateExpiryTimeFromNow($tokens->expires_in)
        ]);
    }

    /**
     * Returns a unix timestamp of now, plus the number of seconds specified.
     * @param  int $seconds
     * @return int
     */
    private function calculateExpiryTimeFromNow($seconds)
    {
        if(!is_int($seconds)) throw new AuthenticationException("Expiry time must be in seconds");
        return time() + $seconds;
    }

    /**
     * Requests the refresh and access tokens from the Spotify API. Calls the
     * API using a proxy in order to hide the application key and secret.
     * @param  string $authKey Authorisation key from the /authorize endpoint
     * @throws \Danj\Spotify\AuthenticationException
     * @return \stdClass
     */
    public function requestRefreshAndAccessToken($authKey)
    {
        $req = new Http;
        $req->setUrl($this->baseUrl.'/token/index.php')
            ->setMethod('POST')
            ->expectJson()
            ->withForm([
                'code' => $authKey
            ])->exec();

        $resp = $req->getResponse();
        $this->handleAPIResponseErrors($resp);

        return $resp;
    }

	/**
	 * Requests a new access token from the Spotify API using the refresh token
	 * @return \stdClass
	 */
    public function requestAccessTokenFromRefreshToken()
    {
        $req = new Http;
        $req->setUrl($this->baseUrl.'/refresh/index.php')
            ->setMethod('POST')
            ->expectJson()
            ->withForm([
                'refresh_token' => $this->getRefreshToken()
            ])->exec();

        $resp = $req->getResponse();
        $this->handleAPIResponseErrors($resp);

        return $resp;
    }

    /**
     * Uses the refresh_token to obtain a new access_token, and then stores them
     * @return void
     */
    public function refreshAccessToken()
    {
        $tokens = $this->requestAccessTokenFromRefreshToken();
        $tokens->refresh_token = $this->getRefreshToken();
        $this->storeNewTokens($tokens);
        return true;
    }

	/**
	 * Handles any errors that may come back from the Spotify API and throws
	 * relevant exceptions to describe the error.
	 * @param \stdClass $resp The response from the API
	 * @throws \Danj\Spotify\AuthenticationException
	 * @return void
	 */
    private function handleAPIResponseErrors($resp)
    {
        if (property_exists($resp, "error")) {
            throw new AuthenticationException($resp->error_description);
        }
        if ($resp->scope != "streaming") {
            throw new AuthenticationException("Invalid permissions granted");
        }
    }

    /**
     * Checks if the user has a valid access token
     * @return bool
     */
    public function isAuthorised()
    {
        $token = $this->getAccessToken();
        return $token && $this->getAccessTokenExpiryTime() >= time();
    }

    /**
     * Returns whether or not a refresh token exists in the cache
     * @return bool
     */
    public function hasRefreshToken()
    {
        return $this->getRefreshToken() ? true : false;
    }

    /**
     * Retrieves the contents of the refresh token in the cache
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->cache->get('refresh_token');
    }

    /**
     * Retrieves the access token from the cache
     * @return string
     */
    public function getAccessToken()
    {
        return $this->cache->get('access_token');
    }

    /**
     * Retrieves the time when the access token expires from the cache, as a
     * unix timestamp
     * @return int
     */
    public function getAccessTokenExpiryTime()
    {
        return $this->cache->get('expires_at');
    }

}
