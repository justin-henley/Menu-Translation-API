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

// Choose the operation to carry out
switch ($requestMethod) {
    case 'GET':
        // Search for a single result by dish id
        if (isset($_GET['dishId'])) require './read_single.php';
        // Search for any results like the given foreign name
        else if (isset($_GET['nameForeign'])) require './search_foreign.php';
        // Search for any results like the given chinese name
        else if (isset($_GET['nameZHTW'])) require './search_zhtw.php';
        // Search for all results without including translations, possibly filtering by category and meat type
        else require './read.php';
        break;
    default:
        echo json_encode(['message' => 'Invalid request method.']);
}
