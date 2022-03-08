<?php
class Restaurant
{
    // DB properties
    private $connection;

    // Properties
    public $restaurantId;
    public $languageId = 1;

    /**
     * Constructor
     * @param {PDO} - dbConnection - An active database connection
     */
    public function __construct($dbConnection)
    {
        $this->connection = $dbConnection;
    }

    // Reads all restaurant entries in a given language, or just returns all valid restaurant ids
    public function read()
    {
        return null;
    }

    // Reads a given restaurants name in the given language, or in all languages if none specified
    public function readSingle()
    {
        return null;
    }

    // Searches for all restaurants like the given name in the given language, or in all languages
    // Params: restaurantName (req), languageId (opt)
    // Returns: restaurant id, name in given language and chinese, possibly all names in all languages
    public function search()
    {
        return null;
    }
}
