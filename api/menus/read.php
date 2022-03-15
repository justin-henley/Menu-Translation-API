<?php
// Reads the menu from a given restaurant in the given language
// Can choose long or short form
// Params: Restaurant id, language id, a boolean for descriptions

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/Menu.php';

// Instantiate new DB and connect
$database = new Database();
$connection = $database->connect();

// Instantiate menu object
$menu = new Menu($connection);

// Check if an id and language was provided and store them
$menu->restaurantId = isset($_GET['restaurantId'])
    ? $_GET['restaurantId']
    : null;
$menu->languageId = isset($_GET['languageId'])
    ? $_GET['languageId']
    : $menu->languageId;
$menu->verbose = isset($_GET['verbose'])
    ? true
    : false;

// Return early if parameters missing
if (
    empty($menu->restaurantId)
    || empty($menu->languageId)
) {
    echo json_encode(['message' => 'Missing Required Parameters']);
    return;
}

// Get full menu data for the given restaurant in the given language
$result = $menu->read();

// Check if a menu was returned for the specific ID (menu exists in DB)
if ($result->rowCount() > 0) {
    // Read operation found a menu
    // Create an array to store all menu items
    $menuItems = [];

    // Iterate over each returned menu item and store
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $item = [
            'dishId' => $dishId,
            'price' => $price,
            'nameZHTW' => $nameZHTW,
            'dishName' => $dishName,
            'categoryId' => $categoryId,
            'categoryName' => $categoryName,
            'meatId' => $meatId,
            'meatName' => $meatName,
        ];

        // Dish description is optional, set by $verbose = true
        if ($verbose) {
            $item['dishDescrip'] = $dishDescrip;
        }

        // Push entry to the menu
        array_push($menuItems, $item);
    }

    // Convert to JSON and output
    echo json_encode($menuItems);
} else {
    // Read operation returned no results
    echo json_encode(['message' => 'No Menu Items Found']);
}
