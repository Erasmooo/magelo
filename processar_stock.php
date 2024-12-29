<?php
// Inicia a sessão
session_start();

// Verifica se o funcionário está logado
if (!isset($_SESSION['funcionario_nome'])) {
    header("Location: index.php");
    exit;
}

// Captura os dados do funcionário logado
$funcionario_nome = $_SESSION['funcionario_nome'];
$funcionario_id = $_SESSION['funcionario_id']; // Use este ID para registrar as operações no banco de dados

// Inclui a configuração de conexão ao banco de dados
require 'config.php';

// Captura os dados do formulário
$tipo_produto = $_POST['produto']; // Tipo de gelo: 'Gelo em Cubo' ou 'Gelo em Barra'
$quantidade_producao = $_POST['quantidade-producao'];

// Verifica se já existe um registro do produto na tabela 'stock'
$sql_verifica_stock = "SELECT quantidade_total FROM stock WHERE tipo_produto = :tipo_produto";
$stmt_verifica_stock = $pdo->prepare($sql_verifica_stock);
$stmt_verifica_stock->bindParam(':tipo_produto', $tipo_produto);
$stmt_verifica_stock->execute();
$estoque_atual = $stmt_verifica_stock->fetch(PDO::FETCH_ASSOC);

if ($estoque_atual) {
    // Se o produto já existe no estoque, soma a quantidade ao total existente
    $nova_quantidade_total = $estoque_atual['quantidade_total'] + $quantidade_producao;

    $sql_atualiza_stock = "UPDATE stock SET quantidade_total = :quantidade_total WHERE tipo_produto = :tipo_produto";
    $stmt_atualiza_stock = $pdo->prepare($sql_atualiza_stock);
    $stmt_atualiza_stock->bindParam(':quantidade_total', $nova_quantidade_total);
    $stmt_atualiza_stock->bindParam(':tipo_produto', $tipo_produto);
    $stmt_atualiza_stock->execute();
} else {
    // Se o produto não existe no estoque, insere um novo registro com a quantidade fornecida
    $nova_quantidade_total = $quantidade_producao;
    $sql_insere_stock = "INSERT INTO stock (tipo_produto, quantidade_total) VALUES (:tipo_produto, :quantidade_total)";
    $stmt_insere_stock = $pdo->prepare($sql_insere_stock);
    $stmt_insere_stock->bindParam(':tipo_produto', $tipo_produto);
    $stmt_insere_stock->bindParam(':quantidade_total', $quantidade_producao);
    $stmt_insere_stock->execute();
}

// Insere o registro no relatorio_estoque com todos os detalhes, incluindo o novo total
$sql_insere_relatorio = "INSERT INTO relatorio_estoque (funcionario_id, funcionario_nome, tipo_produto, quantidade_unit, quantidade_total, data_registro)
                         VALUES (:funcionario_id, :funcionario_nome, :tipo_produto, :quantidade_unit, :quantidade_total, NOW())";
$stmt_relatorio = $pdo->prepare($sql_insere_relatorio);
$stmt_relatorio->bindParam(':funcionario_id', $funcionario_id);
$stmt_relatorio->bindParam(':funcionario_nome', $funcionario_nome);
$stmt_relatorio->bindParam(':tipo_produto', $tipo_produto);
$stmt_relatorio->bindParam(':quantidade_unit', $quantidade_producao);
$stmt_relatorio->bindParam(':quantidade_total', $nova_quantidade_total); // Certifique-se de que o novo total atualizado seja inserido
$stmt_relatorio->execute();

// Redireciona para a página de estoque com mensagem de sucesso
header("Location: stock.php?success=1");
exit;
?>
