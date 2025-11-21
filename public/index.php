<?php
require __DIR__ . "/../vendor/autoload.php";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . DIRECTORY_SEPARATOR . "..");
$dotenv->safeLoad();
# CORS
header("Access-Control-Allow-Origin: " . $_ENV["CLIENT_URL"]);
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
# tratamento para o request OPTIONS do Navegador
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}
header('Content-Type: application/json');

# Acima, cabeçalhos
$pdo = new PDO($_ENV["DB_DSN"]);
$pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS tasks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    status TEXT DEFAULT 'Pendente',
    description TEXT,
    dueDate TEXT,
    starred BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)
SQL);
# Abaixo, roteador

// Roteador simples
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $stmt = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC");
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($tasks);
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $title = $input['title'] ?? '';
        if (empty($title)) {
            http_response_code(400);
            echo json_encode(['error' => 'O título é obrigatório']);
            exit;
        }
        $stmt = $pdo->prepare(
            "INSERT INTO tasks (title) VALUES (:title)"
        );
        $stmt->execute([$title]);
        $newId = $pdo->lastInsertId();
        $taskStmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :id");
        $taskStmt->execute(["id" => $newId]);
        $newTask = $taskStmt->fetch(PDO::FETCH_ASSOC);
        http_response_code(201);
        echo json_encode($newTask);
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['error' => 'Método não permitido']);
        break;
}
