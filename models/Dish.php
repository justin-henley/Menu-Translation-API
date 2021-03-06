<?php
class Dish
{
    // DB properties
    private $connection;
    private $table = 'dishes';

    // Properties
    public $id;
    public $name;
    public $meatId;
    public $categoryId;
    public $languageId = 1;

    /**
     * Constructor
     * @param {PDO} dbConnection - An active database connection
     */
    public function __construct($dbConnection)
    {
        $this->connection = $dbConnection;
    }

    // Read all dishes for the given language
    public function read()
    {
        // Clean data before building where clause
        $this->languageId = htmlspecialchars(strip_tags($this->languageId));
        $this->meatId = htmlspecialchars(strip_tags($this->meatId));
        $this->categoryId = htmlspecialchars(strip_tags($this->categoryId));

        // Build a WHERE clause if a meat or category is set
        $where = "";

        if ($this->meatId || $this->categoryId) {
            // Create the arguments
            $args = [];
            if ($this->meatId) {
                array_push($args, "dishes.meatId = :meatId");
            }
            if ($this->categoryId) {
                array_push($args, "dishes.categoryId = :categoryId");
            }
            // Create the WHERE clause
            $where = " AND " . implode(" AND ", $args);
        }

        // Create query
        $query =
            "SELECT 
                dishes.id,
                dishes.nameZHTW,
                dish_translations.dishName,
                cat_translations.name AS categoryName,
                meat_translations.name AS meatName
            FROM (((dishes
            INNER JOIN dish_translations ON dishes.id = dish_translations.dishId)
            INNER JOIN cat_translations ON dishes.categoryId = cat_translations.categoryId)
            INNER JOIN meat_translations ON dishes.meatId = meat_translations.meatId)
            WHERE dish_translations.languageId = :languageId {$where}
            ORDER BY dishes.id";

        // Prepare the statement
        $stmt = $this->connection->prepare($query);

        // Bind parameters if needed
        $stmt->bindValue(':languageId', $this->languageId);
        if ($this->meatId) $stmt->bindValue(':meatId', $this->meatId);
        if ($this->categoryId) $stmt->bindValue(':categoryId', $this->categoryId);

        // Execute the statement
        $stmt->execute();

        return $stmt;
    }

    // Read a single dish by id
    public function readSingle()
    {
        // Create query
        $query =
            "SELECT 
                dishes.id,
                dishes.nameZHTW,
                dish_translations.dishName,
                dish_translations.dishDescrip,
                cat_translations.name AS categoryName,
                meat_translations.name AS meatName
            FROM (((dishes
            INNER JOIN dish_translations ON dishes.id = dish_translations.dishId)
            INNER JOIN cat_translations ON dishes.categoryId = cat_translations.categoryId)
            INNER JOIN meat_translations ON dishes.meatId = meat_translations.meatId)
            WHERE dishes.id = :id AND dish_translations.languageId = :languageId
            LIMIT 0,1";

        // Prepare the statement
        $stmt = $this->connection->prepare($query);

        // Clean data
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->languageId = htmlspecialchars(strip_tags($this->languageId));

        // Bind parameters
        $stmt->bindValue(':id', $this->id);
        $stmt->bindValue(':languageId', $this->languageId);

        // Execute the statement
        $stmt->execute();

        // Return the statement
        return $stmt;
    }

    // Search for name matches in both Traditional Chinese and the specified language
    public function search()
    {
        // Return early if no name is set
        if (!$this->name) return null;

        // Create query
        $query =
            "SELECT 
                dishes.id,
                dishes.nameZHTW,
                dish_translations.dishName,
                cat_translations.name AS categoryName,
                meat_translations.name AS meatName
            FROM (((dishes
            INNER JOIN dish_translations ON dishes.id = dish_translations.dishId)
            INNER JOIN cat_translations ON dishes.categoryId = cat_translations.categoryId)
            INNER JOIN meat_translations ON dishes.meatId = meat_translations.meatId)
            WHERE 
                (
                    dishes.nameZHTW LIKE :name 
                    OR dish_translations.dishName LIKE :name
                )
                 AND dish_translations.languageId = :language_id
            ORDER BY dishes.id";

        // Prepare the statement
        $stmt = $this->connection->prepare($query);

        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->languageId = htmlspecialchars(strip_tags($this->languageId));

        // Bind parameters
        $stmt->bindValue(':name', "%" . $this->name . "%");
        $stmt->bindValue(':language_id', $this->languageId);

        // Execute the statement
        $stmt->execute();
        return $stmt;
    }
}
