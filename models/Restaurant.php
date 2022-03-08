<?php
class Restaurant
{
    // DB properties
    private $connection;

    // Properties
    public $restaurantId;
    public $languageId;

    /**
     * Constructor
     * @param {PDO} - dbConnection - An active database connection
     */
    public function __construct($dbConnection)
    {
        $this->connection = $dbConnection;
    }

    // Reads all restaurant entries in a given language
    public function read()
    {
        // Create query
        $query =
            "SELECT
                restaurants.id AS restaurantId,
                rest_names.name AS restaurantName
            FROM restaurants
            INNER JOIN rest_names ON restaurants.id = rest_names.restaurantId
            WHERE rest_names.languageId = :languageId
            ORDER BY restaurantName";

        // Prepare the statement
        $stmt = $this->connection->prepare($query);

        // Bind parameters
        $stmt->bindValue(':languageId', $this->languageId);

        // Execute the statement
        $stmt->execute();

        return $stmt;
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
