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

// Processa a remoção do estoque
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['estoque_id'])) {
    $estoque_id = $_POST['estoque_id'];
    $quantidade_remover = $_POST['quantidade_remover'];

    // Verifica se a quantidade a ser removida não excede o estoque atual
    $sql_verificar = "SELECT quantidade_total FROM stock WHERE id = :id";
    $stmt_verificar = $pdo->prepare($sql_verificar);
    $stmt_verificar->bindParam(':id', $estoque_id, PDO::PARAM_INT);
    $stmt_verificar->execute();
    $estoque_atual = $stmt_verificar->fetchColumn();

    if ($quantidade_remover <= $estoque_atual) {
        // Subtrai a quantidade removida do estoque atual
        $sql_update = "UPDATE stock SET quantidade_total = quantidade_total - :quantidade_remover WHERE id = :id";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->bindParam(':quantidade_remover', $quantidade_remover, PDO::PARAM_INT);
        $stmt_update->bindParam(':id', $estoque_id, PDO::PARAM_INT);
        $stmt_update->execute();

        // Redireciona com mensagem de sucesso
    header("Location: stock.php?success=remove");
    exit;
    } else {
        // Redireciona com mensagem de erro
        header("Location: stock.php?error=insufficient_stock");
        exit;
    }
}
?>
