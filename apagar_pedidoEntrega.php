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

// Verifica se o ID do pedido foi enviado
if (!isset($_GET['id'])) {
    header("Location: pedido.php");
    exit;
}

// Obtém o ID do pedido
$pedidoId = $_GET['id'];

// Deleta o pedido
$sqlDelete = "DELETE FROM pedidos WHERE id = :id";
$stmtDelete = $pdo->prepare($sqlDelete);
$stmtDelete->bindParam(':id', $pedidoId);

if ($stmtDelete->execute()) {
    header("Location: pedidoEntrega.php?mensagem=deletado");
    exit;
} else {
    echo "Erro ao deletar pedido.";
}
?>
