<?php
// Inicia a sessão
session_start();

// Verifica se o funcionário está logado
if (!isset($_SESSION['funcionario_nome'])) {
    header("Location: index.php");
    exit;
}

// Inclui a configuração de conexão ao banco de dados
require 'config.php';

// Verifica se o ID do cliente foi enviado
if (isset($_POST['id_cliente'])) {
    $clienteId = $_POST['id_cliente'];

    // Consulta para remover o cliente
    $sqlDelete = "DELETE FROM clientes WHERE id = :id";
    $stmtDelete = $pdo->prepare($sqlDelete);
    $stmtDelete->bindParam(':id', $clienteId);

    if ($stmtDelete->execute()) {
        header("Location: clientes.php?mensagem=removido");
        exit;
    } else {
        echo "Erro ao remover cliente.";
    }
} else {
    header("Location: clientes.php");
    exit;
}
?>
