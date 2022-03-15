<?php
class Menu
{
    // DB properties
    private $connection;

    // Properties
    public $restaurantId;
    public $languageId = 1;
    public $dishId;
    public $verbose = false;

    /**
     * Constructor
     * @param {PDO} - dbConnection - An active database connection
     */
    public function __construct($dbConnection)
    {
        $this->connection = $dbConnection;
    }

    // Read all menu items for a given restaurant
    public function read()
    {
        // Create the description selection if verbose
        $descrip = "";
        if ($this->verbose) {
            $descrip = "dish_translations.dishDescrip";
        }

        // Create query
        // Expanded this to include category and meat type and name in a single query for speed
        $query =
            "SELECT
                menu_items.dishId,
                menu_items.price,
                dishes.nameZHTW,
                dish_translations.dishName,
                categories.id AS categoryId,
                categories.name AS categoryName,
                meats.id AS meatId,
                meats.name AS meatName
                {$descrip}
            FROM ((((menu_items 
            INNER JOIN dishes ON menu_items.dishId = dishes.id)
            INNER JOIN dish_translations ON menu_items.dishId = dish_translations.dishId)
            INNER JOIN meat_translations ON dishes.meatId = meat_translations.meatId)
            INNER JOIN cat_translations ON dishes.categoryId = cat_translations.categoryId)
            WHERE 
                menu_items.restaurantId = :restaurantId 
                AND dish_translations.languageId = :languageId
                AND meat_translations.languageId = :languageId
                AND cat_translations.languageId = :languageId
            ORDER BY menu_items.dishId;
            ";

        // Prepare the statement
        $stmt = $this->connection->prepare($query);

        // Clean data
        $this->restaurantId = htmlspecialchars(strip_tags($this->restaurantId));
        $this->languageId = htmlspecialchars(strip_tags($this->languageId));

        // Bind parameters
        $stmt->bindValue(':restaurantId', $this->restaurantId);
        $stmt->bindValue(':languageId', $this->languageId);

        // Execute the statement
        $stmt->execute();

        return $stmt;
    }

    // Read a single menu item for a given restaurant
    public function readSingle()
    {
        // Create query
        // May want to expand this to include category and meat type and name in a single query for speed
        $query =
            "SELECT
                menu_items.dishId,
                menu_items.price,
                dishes.nameZHTW,
                dish_translations.dishName,
                dish_translations.dishDescrip,
                dishes.meatId,
                dishes.categoryId,
                meat_translations.name AS meatName,
                cat_translations.name AS categoryName
            FROM ((((menu_items
            INNER JOIN dishes ON menu_items.dishId = dishes.id)
            INNER JOIN dish_translations ON menu_items.dishId = dish_translations.dishId)
            INNER JOIN meat_translations ON dishes.meatId = meat_translations.meatId)
            INNER JOIN cat_translations ON dishes.categoryId = cat_translations.categoryId)
            WHERE 
                menu_items.restaurantId = :restaurantId 
                AND menu_items.dishId = :dishId
                AND dish_translations.languageId = :languageId
                AND meat_translations.languageId = :languageId
                AND cat_translations.languageId = :languageId
            ORDER BY menu_items.dishId;
            ";

        // Prepare the statement
        $stmt = $this->connection->prepare($query);

        // Clean data
        $this->restaurantId = htmlspecialchars(strip_tags($this->restaurantId));
        $this->dishId = htmlspecialchars(strip_tags($this->dishId));
        $this->languageId = htmlspecialchars(strip_tags($this->languageId));

        // Bind parameters
        $stmt->bindValue(':restaurantId', $this->restaurantId);
        $stmt->bindValue(':dishId', $this->dishId);
        $stmt->bindValue(':languageId', $this->languageId);

        // Execute the statement
        $stmt->execute();

        return $stmt;
    }
}
