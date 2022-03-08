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

    // Reads all restaurant entries in a given language, or all name entries in all languages if a language is not specified
    public function read()
    {
        // Clean data
        $this->languageId = htmlspecialchars(strip_tags($this->languageId));

        // Build where clause to filter by language
        $where = ($this->languageId)
            ? "WHERE languageId = :languageId"
            : "";

        // Create query
        $query =
            "SELECT
                restaurantId,
                name AS restaurantName,
                languageId
            FROM rest_names
            {$where}
            ORDER BY restaurantId";

        // Prepare the statement
        $stmt = $this->connection->prepare($query);

        // Bind parameters
        if ($this->languageId) $stmt->bindValue(':languageId', $this->languageId);

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
                rest_names.restaurantId,
                rest_names.name AS restaurantName,
                rest_names.languageId
            FROM rest_names 
            WHERE rest_names.restaurantId = :restaurantId {$lang}";

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
