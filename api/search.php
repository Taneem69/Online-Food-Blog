<?php
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
require_once '../config/database.php';
/** @var \PDO $pdo */
$q        = trim($_GET['q']        ?? '');
$location = trim($_GET['location'] ?? '');
$area     = trim($_GET['area']     ?? '');


$restaurantSQL    = "SELECT 
                        id, 
                        name, 
                        location, 
                        area, 
                        short_background
                     FROM restaurants
                     WHERE 1=1";
$restaurantParams = [];

if (!empty($q)) {
    $restaurantSQL     .= " AND name LIKE ?";
    $restaurantParams[] = "%{$q}%";
}

// Add location filter
if (!empty($location)) {
    $restaurantSQL     .= " AND location LIKE ?";
    $restaurantParams[] = "%{$location}%";
}

// Add area filter
if (!empty($area)) {
    $restaurantSQL     .= " AND area = ?";
    $restaurantParams[] = $area;
}

$restaurantSQL .= " ORDER BY name ASC";

$stmt = $pdo->prepare($restaurantSQL);
$stmt->execute($restaurantParams);
$restaurants = $stmt->fetchAll();

$menuSQL    = "SELECT 
                    mi.id,
                    mi.name,
                    mi.description,
                    mi.price,
                    mi.image_path,
                    r.name     AS restaurant_name,
                    r.id       AS restaurant_id,
                    r.area     AS restaurant_area,
                    r.location AS restaurant_location
               FROM menu_items mi
               JOIN restaurants r ON mi.restaurant_id = r.id
               WHERE 1=1";
$menuParams = [];

if (!empty($q)) {
    $menuSQL     .= " AND mi.name LIKE ?";
    $menuParams[] = "%{$q}%";
}

if (!empty($location)) {
    $menuSQL     .= " AND r.location LIKE ?";
    $menuParams[] = "%{$location}%";
}

if (!empty($area)) {
    $menuSQL     .= " AND r.area = ?";
    $menuParams[] = $area;
}

$menuSQL .= " ORDER BY mi.name ASC";

$stmt2 = $pdo->prepare($menuSQL);
$stmt2->execute($menuParams);
$menuItems = $stmt2->fetchAll();

echo json_encode([
    'success'     => true,
    'restaurants' => $restaurants,
    'menu_items'  => $menuItems,
    'counts'      => [
        'restaurants' => count($restaurants),
        'menu_items'  => count($menuItems),
    ]
]);