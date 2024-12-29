<?php
// Inicia a sessão
session_start();

// Verifica se o funcionário está logado
if (!isset($_SESSION['funcionario_nome'])) {
    header("Location: index.php"); // Redireciona para o login se não estiver logado
    exit;
}

// Inclui a configuração de conexão ao banco de dados
require 'config.php';

// Obtém os dados do formulário
$tipoGelo = $_POST['tipoGelo']; // Gelo em Cubo ou Gelo em Barra
$tipoQuebra = $_POST['tipoQuebra']; // Quebra de Gelo ou Quebra de Plástico
$quantidade = $_POST['quantidade']; // Quantidade de quebra
$funcionarioNome = $_SESSION['funcionario_nome']; // Nome do funcionário
$dataRegistro = date('Y-m-d H:i:s'); // Obtém a data e hora atual

// Insere os dados na tabela 'relatorio_quebras'
$sql = "INSERT INTO relatorio_quebras (tipo_gelo, tipo_quebra, quantidade, data_registro, funcionario_nome) 
        VALUES (:tipo_gelo, :tipo_quebra, :quantidade, :data_registro, :funcionario_nome)";
$stmt = $pdo->prepare($sql);

// Bind dos parâmetros
$stmt->bindParam(':tipo_gelo', $tipoGelo);
$stmt->bindParam(':tipo_quebra', $tipoQuebra);
$stmt->bindParam(':quantidade', $quantidade);
$stmt->bindParam(':data_registro', $dataRegistro);
$stmt->bindParam(':funcionario_nome', $funcionarioNome);

// Executa a inserção e verifica se foi bem-sucedida
if ($stmt->execute()) {
    // Atualiza a quantidade do tipo de gelo na tabela 'stock'
    $sqlUpdate = "UPDATE stock SET quantidade_total = quantidade_total - :quantidade WHERE tipo_produto = :tipo";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->bindParam(':quantidade', $quantidade);

    // Verifica qual tipo de gelo foi selecionado para atualizar o estoque
    if ($tipoGelo == "cubos") {
        $stmtUpdate->bindValue(':tipo', 'Gelo em Cubo'); // Nome exato na tabela stock
    } elseif ($tipoGelo == "barras") {
        $stmtUpdate->bindValue(':tipo', 'Gelo em Barra'); // Nome exato na tabela stock
    }

    // Executa a atualização e verifica se foi bem-sucedida
    if ($stmtUpdate->execute()) {
header("Location: quebras.php?success=1");
exit;
    } else {
        echo "Erro ao atualizar o estoque de gelo.";
    }
} else {
    echo "Erro ao registrar a quebra.";
}
?>
