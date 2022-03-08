<?php
// Reads a given restaurants name in the given language, or in all languages if none specified
// Parameters: restaurantId (req), languageId(opt)

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/Restaurant.php';

// Instantiate new DB and connect
$database = new Database();
$connection = $database->connect();

// Instantiate restaurant object
$restaurant = new Restaurant($connection);

// Check if an id and language was provided and store them
$restaurant->id = isset($_GET['id'])
    ? $_GET['id']
    : null;
$restaurant->languageId = isset($_GET['languageId'])
    ? $_GET['languageId']
    : $restaurant->languageId;

// Return early if parameters missing
if (empty($restaurant->id)) {
    echo json_encode(['message' => 'Missing Required Parameters']);
    return;
}

// Get restaurant data
$result = $restaurant->readSingle();

// Check if any restaurant results were returned
if ($result?->rowCount() > 0) {
    // Create an array to store the results
    $restaurantArr = [];

    // Iterate over the rows
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $restaurantEntry = [
            'restaurantId' => $restaurantId,
            'restaurantName' => $restaurantName,
            'languageId' => $languageId
        ];

        // Push entry to array
        array_push($restaurantArr, $restaurantEntry);
    }

    // Turn into JSON and output
    echo json_encode($restaurantArr);
} else {
    // No restaurants found
    echo json_encode(['message' => 'No Restaurants Found']);
}
