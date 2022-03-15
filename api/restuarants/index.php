<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Get the request method
$requestMethod = $_SERVER['REQUEST_METHOD'];

// CORS solution
if ($requestMethod === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
}

// If using this with a front-end causes trouble add an else before the switch
// Choose the operation to carry out
switch ($requestMethod) {
    case 'GET':
        // Search for a single restaurant by id
        if (isset($_GET['id'])) require './read_single.php';
        // Search for a single restaurant by name
        else if (isset($_GET['name'])) require './search.php';
        // Search for all results in a given language
        else require './read.php';
        break;
    default:
        echo json_encode(['message' => 'Invalid request method.']);
}
