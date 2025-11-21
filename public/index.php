<?php
require __DIR__ . "/../vendor/autoload.php";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . DIRECTORY_SEPARATOR . "..");
$dotenv->safeLoad();

header("Access-Control-Allow-Origin: " . $_ENV["CLIENT_URL"]);
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}
header('Content-Type: application/json');

$pdo = new PDO($_ENV["DB_DSN"]);

$repo = new App\Repository\Task($pdo);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode($repo->getAll());
        break;
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $title = $input['title'] ?? '';
        if (empty($title)) {
            http_response_code(400);
            echo json_encode(['error' => 'O título é obrigatório']);
            exit;
        }
        $newTask = $repo->create($input['title']);
        http_response_code(201);
        echo json_encode($newTask);
        break;
    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['error' => 'Método não permitido']);
        break;
}
