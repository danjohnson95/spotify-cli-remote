<?php namespace Danj\Spotify;

class Http{

	/**
	 * The URL in which a request will be made onto
	 * @var string
	 */
    protected $url;

	/**
	 * The HTTP method to use for a request
	 * @var string
	 */
    protected $method = "GET";

	/**
	 * An array of HTTP headers to send with the request
	 * @var array
	 */
    protected $headers = [];

	/**
	 * An array of data to pass into the request
	 * @var array
	 */
    protected $data = [];

	/**
	 * The cURL object
	 * @var resource
	 */
    protected $curl;

	/**
	 * An object containing the decoded response
	 * @var \stdClass
	 */
    protected $response;

	/**
	 * A string containing the raw encoded response
	 * @var string
	 */
    protected $rawResponse;

	/**
	 * Creates an instance of the HTTP request
	 * @return void
	 */
    public function __construct()
    {
        $this->curl = curl_init();
    }

	/**
	 * Gets the contents of the response
	 * @return \stdClass
	 */
    public function getResponse()
    {
        return $this->response;
    }

	/**
	 * Gets the contents of the raw response
	 * @return string
	 */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }

	/**
	 * Sets the URL to use for the request
	 * @param string $url
	 * @return $this
	 */
    public function setUrl($url)
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        $this->url = $url;
        return $this;
    }

	/**
	 * Sets the HTTP method to use for the request
	 * @param string $method
	 * @return $this
	 */
    public function setMethod($method)
    {
        switch($method){
            case "POST":
                curl_setopt($this->curl, CURLOPT_POST, true);
                break;
            case "PUT":
            case "PATCH":
            case "DELETE":
                curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);
                break;
        }

        $this->method = $method;
        return $this;
    }

	/**
	 * Includes data to send with the request, and sets the content type header
	 * to application/x-www-form-urlencoded
	 * @param array $data
	 * @return $this
	 */
    public function withForm(array $data)
    {
        $this->headers[] = "Content-Type: application/x-www-form-urlencoded";
        $this->data = $data;
        return $this;
    }

	/**
	 * Includes data to send with the request, and sets the content type header
	 * to application/json
	 * @param array $data
	 * @return $this
	 */
    public function withJson(array $data)
    {
        $this->headers[] = "Content-Type: application/json";
        $this->data = $data;
        return $this;
    }

	/**
	 * Sets the bearer authorisation header tag
	 * @param string $bearer
	 * @return $this
	 */
    public function setBearer($bearer)
    {
        $this->headers[] = "Authorization: Bearer ".$bearer;
        return $this;
    }

	/**
	 * Sets the HTTP header to accept a JSON response
	 * @return $this
	 */
    public function expectJson()
    {
        $this->headers[] = "Accept: application/json";
        return $this;
    }

	/**
	 * Sets the authorisation tag to a username and password specified
	 * @param string $user
	 * @param string $pass
	 * @return $this
	 */
    public function withAuth($user, $pass)
    {
        curl_setopt($this->curl, CURLOPT_USERPWD, $user.":".$pass);
        return $this;
    }

	/**
	 * Executes the HTTP request
	 * @return $this
	 */
    public function exec()
    {
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

		$this->prepareData();

        $response = curl_exec($this->curl);
        $this->rawResponse = $response;
        $this->response = json_decode($this->rawResponse);
		return $this;
    }

	/**
	 * Sets the data for the request according to the method used
	 * @return void
	 */
	private function prepareData()
	{
		switch($this->method){
			case "POST":
			case "PUT":
			case "PATCH":
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($this->data));
				break;
			default:
				$this->setUrl($this->url.http_build_query($this->data));
				break;
		}
	}

	/**
	 * Returns info about the HTTP request
	 * @return array
	 */
    public function getInfo()
    {
        return curl_getinfo($this->curl);
    }

	/**
	 * Returns the HTTP status code the response gave
	 * @return integer
	 */
    public function getHttpCode()
    {
        return curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
    }

}
