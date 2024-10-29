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
$quantidade_abastecimento = $_POST['quantidade_abastecimento']; // Certifique-se de que o nome do campo esteja correto

// Verifica se o campo quantidade_abastecimento foi preenchido
if (empty($quantidade_abastecimento)) {
    die('A quantidade de abastecimento não pode ser vazia.');
}

// Verifica se já existe um registro do produto na tabela 'stock_carro' (do caminhão)
$sql_verifica_stock_carro = "SELECT quantidade_total FROM stock_carro WHERE tipo_produto = :tipo_produto";
$stmt_verifica_stock_carro = $pdo->prepare($sql_verifica_stock_carro);
$stmt_verifica_stock_carro->bindParam(':tipo_produto', $tipo_produto);
$stmt_verifica_stock_carro->execute();
$estoque_carro = $stmt_verifica_stock_carro->fetch(PDO::FETCH_ASSOC);

if ($estoque_carro) {
    // Se o produto já existe no estoque do caminhão, soma a quantidade ao total existente
    $nova_quantidade_total_carro = $estoque_carro['quantidade_total'] + $quantidade_abastecimento;

    $sql_atualiza_stock_carro = "UPDATE stock_carro SET quantidade_total = :quantidade_total WHERE tipo_produto = :tipo_produto";
    $stmt_atualiza_stock_carro = $pdo->prepare($sql_atualiza_stock_carro);
    $stmt_atualiza_stock_carro->bindParam(':quantidade_total', $nova_quantidade_total_carro);
    $stmt_atualiza_stock_carro->bindParam(':tipo_produto', $tipo_produto);
    $stmt_atualiza_stock_carro->execute();
} else {
    // Se o produto não existe no estoque do caminhão, insere um novo registro com a quantidade fornecida
    $nova_quantidade_total_carro = $quantidade_abastecimento;
    $sql_insere_stock_carro = "INSERT INTO stock_carro (tipo_produto, quantidade_total) VALUES (:tipo_produto, :quantidade_total)";
    $stmt_insere_stock_carro = $pdo->prepare($sql_insere_stock_carro);
    $stmt_insere_stock_carro->bindParam(':tipo_produto', $tipo_produto);
    $stmt_insere_stock_carro->bindParam(':quantidade_total', $quantidade_abastecimento);
    $stmt_insere_stock_carro->execute();
}

// Atualiza o estoque principal, removendo a quantidade que foi abastecida no caminhão
$sql_verifica_stock_principal = "SELECT quantidade_total FROM stock WHERE tipo_produto = :tipo_produto";
$stmt_verifica_stock_principal = $pdo->prepare($sql_verifica_stock_principal);
$stmt_verifica_stock_principal->bindParam(':tipo_produto', $tipo_produto);
$stmt_verifica_stock_principal->execute();
$estoque_principal = $stmt_verifica_stock_principal->fetch(PDO::FETCH_ASSOC);

if ($estoque_principal && $estoque_principal['quantidade_total'] >= $quantidade_abastecimento) {
    // Atualiza o estoque principal
    $nova_quantidade_total_principal = $estoque_principal['quantidade_total'] - $quantidade_abastecimento;

    $sql_atualiza_stock_principal = "UPDATE stock SET quantidade_total = :quantidade_total WHERE tipo_produto = :tipo_produto";
    $stmt_atualiza_stock_principal = $pdo->prepare($sql_atualiza_stock_principal);
    $stmt_atualiza_stock_principal->bindParam(':quantidade_total', $nova_quantidade_total_principal);
    $stmt_atualiza_stock_principal->bindParam(':tipo_produto', $tipo_produto);
    $stmt_atualiza_stock_principal->execute();
} else {
    // Se não há estoque suficiente no depósito principal, redireciona com uma mensagem de erro
    header("Location: stock_entrega2.php?erro=Estoque+insuficiente+no+depósito+principal");
    exit;
}

// Redireciona para a página de estoque do caminhão com mensagem de sucesso
header("Location: stock_entrega2.php?success=Produto+abastecido+no+caminhão");
exit;
?>
