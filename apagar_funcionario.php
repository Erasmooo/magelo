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

// Verifica se o ID do funcionário foi enviado
if (!isset($_GET['id'])) {
    header("Location: funcionario.php");
    exit;
}

// Obtém o ID do funcionário
$funcionarioId = $_GET['id'];

// Deleta o funcionário
$sqlDelete = "DELETE FROM funcionarios WHERE id = :id";
$stmtDelete = $pdo->prepare($sqlDelete);
$stmtDelete->bindParam(':id', $funcionarioId);

if ($stmtDelete->execute()) {
    header("Location: funcionario.php?mensagem=deletado");
    exit;
} else {
    echo "Erro ao deletar funcionário.";
}
?>
