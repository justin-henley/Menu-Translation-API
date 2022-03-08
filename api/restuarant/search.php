<?php
// Searches for all restaurants like the given name in the given language, or in all languages
// Params: restaurantName (req), languageId (opt)
// Returns: restaurant id, name in given language and chinese, possibly all names in all languages

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

// Check if a restaurant name and language was provided and store it
$restaurant->name = isset($_GET['name'])
    ? $_GET['name']
    : null;
$restaurant->languageId = isset($_GET['languageId'])
    ? $_GET['languageId']
    : $restaurant->languageId;

// Search for all restaurant entries like the given name
$result = $restaurant->search();

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
