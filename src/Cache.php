<?php namespace Danj\Spotify;

use stdClass;

class Cache
{
    /**
     * The path to the file where the cache is stored
     * @var string
     */
    protected $path = __DIR__."/../storage/cache.json";

    /**
     * The cache object
     * @var object
     */
    protected $file;

    /**
     * Initiates an instance of the cache.
     * @return void
     */
    public function __construct()
    {
        $file = $this->getFile();
        $this->file = json_decode($file);
    }

    /**
     * Gets a value out of the cache
     * @param mixed $key
     * @return mixed
     */
    public function get($key)
    {
        if (property_exists($this->file, $key)) {
            return $this->file->$key;
        }
        return null;
    }

    /**
     * Stores a value in the cache under the key specified
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function store($key, $value)
    {
        $this->file->$key = $value;
    }

    /**
     * Stores multiple values in the cache at a time
     * @param array
     * @return void
     */
    public function storeMany(array $values)
    {
        foreach($values as $key=>$value){
            $this->store($key, $value);
        }
    }

    /**
     * Ends the instance of the cache
     * @return void
     */
    public function __destruct()
    {
        $this->updateFile();
    }

    /**
     * Stores the contents of the cache in the file for persistence
     * @return void
     */
    private function updateFile()
    {
        file_put_contents($this->path, json_encode($this->file));
    }

    /**
     * Grabs the cache JSON file
     * @return string JSON encoded string
     */
    private function getFile()
    {
        if (file_exists($this->path) ) {
            $output = file_get_contents($this->path);
            if($output != "") return $output;
        }
        return "{}";
    }
}
