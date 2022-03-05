<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/Dish.php';

// Instantiate new DB and connect
$database = new Database();
$connection = $database->connect();

// Instantiate dish object
$dish = new Dish($connection);

// Check if an id was provided and store it
$dish->id = isset($_GET['id'])
    ? $_GET['id']
    : null;

// Return early if parameters missing
if (empty($dish->id)) {
    echo json_encode(['message' => 'Missing Required Parameters']);
    return;
}

// Get dish data
$result = $dish->readSingle();

// Check if a dish was returned for the specific ID (dish exists in DB)
if ($result->rowCount() > 0) {
    // Read operation found a dish
    // Fetch the single row of data from the db and extract fields
    $row = $result->fetch(PDO::FETCH_ASSOC);
    extract($row);

    // Create results array
    $dishArr = [
        'id' => $id,
        'nameZHTW' => $nameZHTW,
        'categoryName' => $categoryName,
        'meatName' => $meatName,
        'dishName' => $dishName,
        'dishDescrip' => $dishDescrip
    ];

    // Convert to JSON and output
    echo json_encode($dishArr);
} else {
    // Read operation returned no results
    echo json_encode(['message' => 'No Dishes Found']);
}
