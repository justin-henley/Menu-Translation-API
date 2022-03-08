<?php
class Restaurant
{
    // DB properties
    private $connection;

    // Properties
    public $id;
    public $name;
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
        // Return early if no language provided
        if (!$this->languageId) return null;

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

        // Clean data
        $this->languageId = htmlspecialchars(strip_tags($this->languageId));

        // Bind parameters
        $stmt->bindValue(':languageId', $this->languageId);

        // Execute the statement
        $stmt->execute();

        return $stmt;
    }

    // Reads a given restaurants name in the given language, or in all languages if none specified
    public function readSingle()
    {
        // Return early if no id provided
        if (!$this->id) return null;


        // Clean data
        $this->languageId = htmlspecialchars(strip_tags($this->languageId));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Create a filter for language if necessary
        $lang = ($this->languageId)
            ? " AND rest_names.languageId = :languageId"
            : "";

        // Create query
        $query =
            "SELECT
                rest_names.name AS restaurantName,
                rest_names.languageId
            FROM restaurants
            INNER JOIN rest_names ON restaurants.id = rest_names.restaurantId
            WHERE restaurants.id = :restaurantId {$lang}";

        // Prepare the statement
        $stmt = $this->connection->prepare($query);

        // Bind parameters
        $stmt->bindValue(':restaurantId', $this->id);
        if ($this->languageId) $stmt->bindValue(':languageId', $this->languageId);

        // Execute the statement
        $stmt->execute();

        return $stmt;
    }

    // Searches for all restaurants like the given name in the given language, or in all languages
    // Params: restaurantName (req), languageId (opt)
    // Returns: restaurant id, name that matched the search term, either in a specified language or possibly in all languages
    public function search()
    {
        // Return early if no name provided
        if (!$this->name) return null;

        // Clean data
        $this->languageId = htmlspecialchars(strip_tags($this->languageId));
        $this->name = htmlspecialchars(strip_tags($this->name));

        // Create a filter for language if necessary
        $lang = ($this->languageId)
            ? " AND rest_names.languageId = :languageId"
            : "";

        // Create query
        $query =
            "SELECT
                restaurants.id AS restaurantId,
                rest_names.name AS restaurantName,
                rest_names.languageId
            FROM restaurants
            INNER JOIN rest_names ON restaurants.id = rest_names.restaurantId
            WHERE rest_names.name LIKE :restaurantName {$lang}";

        // Prepare the statement
        $stmt = $this->connection->prepare($query);

        // Bind parameters
        $stmt->bindValue(':restaurantName', "%" . $this->name . "%");
        if ($this->languageId) $stmt->bindValue(':languageId', $this->languageId);

        // Execute statement
        $stmt->execute();

        return $stmt;
    }
}
