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

// Check if a Chinese name and language was provided and store it
$dish->nameZHTW = isset($_GET['name'])
    ? $_GET['name']
    : null;
$dish->languageId = isset($_GET['languageid'])
    ? $_GET['languageid']
    : $dish->languageId;

// Search for all dish entries like the Chinese name
$result = $dish->searchZHTW();

// Check if any dish results were returned
if ($result->rowCount() > 0) {
    // Create an array to store the results
    $dishArr = [];

    // Iterate over the rows
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $dishEntry = [
            'id' => $id,
            'nameZHTW' => $nameZHTW,
            'dishName' => $dishName,
            'categoryName' => $categoryName,
            'meatName' => $meatName
        ];

        // Push entry to array
        array_push($dishArr, $dishEntry);
    }

    // Turn into JSON and output
    echo json_encode($dishArr);
} else {
    // No dishes found
    echo json_encode(['message' => 'No Dishes Found']);
}
