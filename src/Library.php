<?php namespace Danj\Spotify;

use Danj\Spotify\Http;
use Danj\Spotify\AuthHelper;
use Danj\Spotify\AuthenticationException;

class Library{

	/**
	 * The user's access token
	 * @var string
	 */
    private $access_token;

	/**
	 * The base URL for API requests
	 * @var string
	 */
    private $base_url = "https://api.spotify.com";

	/**
	 * Creates an instance of the API Library
	 * @return void
	 */
    public function __construct()
    {
        $this->auth = new AuthHelper;
        if(!$this->auth->isAuthorised()) throw new AuthenticationException("Must be logged in");
        $this->access_token = $this->auth->getAccessToken();
    }

	/**
	 * Makes a HTTP request to the API through the endpoint specified.
	 * @param string 	$url 		The endpoint to call
	 * @param string 	$method 	The HTTP method to use
	 * @param array 	$data 		Any data needed for the request
	 * @return \stdClass
	 */
    private function callApi($url, $method, $data=[])
    {
        $req = new Http;
        $req->setUrl($this->base_url.$url)
            ->setMethod($method)
            ->setBearer($this->access_token);

        if(count($data)){
            $req->withJson($data);
        }

        $req->exec();

        return $req->getResponse();

    }

	/**
	 * Pauses playback on the current device
	 * @return void
	 */
    public function pause()
	{
        $this->callApi('/v1/me/player/pause', 'PUT');
    }

	/**
	 * Resumes/starts playback on the current device
	 * @return void
	 */
    public function play()
	{
        $this->callApi('/v1/me/player/play', 'PUT');
    }

	/**
	 * Skips to the next track on the current device
	 * @return void
	 */
    public function next()
	{
        $this->callApi('/v1/me/player/next', 'POST');
    }

	/**
	 * Plays the previous track on the current device
	 * @return void
	 */
    public function prev()
	{
        $this->callApi('/v1/me/player/previous', 'POST');
    }

	public function setVolume($vol){
		// TODO: Validate.
		//

		// $this->callApi('/v1/me/player/volume', 'PUT', [
		//     'volume_percent' => $vol
		// ]);
	}

	public function current(){
		return $this->callApi('/v1/me/player/currently-playing', 'GET');
	}

	//$resp = $this->callApi('/v1/recommendations?seed_genres=edm,progressive-house,summer,synth-pop,indie&target_tempo=195', 'GET');
	//foreach($resp->tracks as $track){
	//	echo $track->name.PHP_EOL;
	//}
	//print_r($resp);
	//
	//


}
