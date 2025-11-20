<?php
# CORS
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
# tratamento para o request OPTIONS do Navegador
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}
header('Content-Type: application/json');
# Nossas tarefas iniciais
$tasks = [
    ['id' => 1, 'title' => 'Conferir o setup', 'status' => 'Concluída', 'starred' => true],
    ['id' => 2, 'title' => 'Resolver o CORS', 'status' => 'Pendente', 'starred' => false]
];
// Roteador simples
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode($tasks);
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        echo json_encode([
            'message' => 'Tarefa recebida com sucesso!',
            'data' => $input
        ]);
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['error' => 'Método não permitido']);
        break;
}
