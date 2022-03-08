<?php
// Reads a single menu item for a restaurant.  Possibly for clicking on a menu for more detail

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
$menu->dishId = isset($_GET['dishId'])
    ? $_GET['dishId']
    : null;

// Return early if parameters missing
if (
    empty($menu->restaurantId)
    || empty($menu->languageId)
    || empty($menu->dishId)
) {
    echo json_encode(['message' => 'Missing Required Parameters']);
    return;
}

// Get menu data
$result = $menu->readSingle();

// Check if a menu was returned for the specific ID (menu exists in DB)
if ($result->rowCount() > 0) {
    // Read operation found a menu
    // Fetch the single row of data from the db and extract fields
    $row = $result->fetch(PDO::FETCH_ASSOC);
    extract($row);

    // Create results array
    $menuArr = [
        'dishId' => $dishId,
        'price' => $price,
        'nameZHTW' => $nameZHTW,
        'dishName' => $dishName,
        'dishDescrip' => $dishDescrip,
        'categoryId' => $categoryId,
        'categoryName' => $categoryName,
        'meatId' => $meatId,
        'meatName' => $meatName,
    ];

    // Convert to JSON and output
    echo json_encode($menuArr);
} else {
    // Read operation returned no results
    echo json_encode(['message' => 'No Menu Items Found']);
}
