<?php
class Dish
{
    // DB properties
    private $connection;
    private $table = 'dishes';

    // Properties
    public $id;
    public $nameZHTW;
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

    // Read all dishes
    public function read()
    {
        // Build a WHERE clause if a meat or category is set
        $where = "";

        if ($this->meatId || $this->categoryId) {
            // Create the arguments
            $args = [];
            if ($this->meatId) {
                array_push($args, "dishes.meatId = {$this->meatId}");
            }
            if ($this->categoryId) {
                array_push($args, "dishes.categoryId = {$this->categoryId}");
            }
            // Create the WHERE clause
            $where = "WHERE " . implode(" AND ", $args);
        }

        // Create query
        $query =
            "SELECT 
                dishes.id,
                dish_translations.name,
                cat_translations.name,
                meat_translations.name
            FROM (((dishes
            INNER JOIN dish_translations ON dishes.id = dish_translations.dishId)
            INNER JOIN cat_translations ON dishes.categoryId = cat_translations.categoryId)
            INNER JOIN meat_translations ON dishes.meatId = meat_translations.meatId)
            {$where}
            ORDER BY dishes.id";

        // Prepare the statement
        $stmt = $this->connection->prepare($query);

        // Execute the statement
        $stmt->execute();

        return $stmt;
    }
}
