<?php
session_start();
require 'config.php';

// Verifica se o funcionário está logado
if (!isset($_SESSION['funcionario_id'])) {
    header("Location: index.php");
    exit;
}

// Verifica se o formulário foi enviado corretamente
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura os dados do formulário
    $nome_cliente = $_POST['nome_cliente'];
    $contato = $_POST['contato'];
    $quantidade = $_POST['quantidade'];
    $tipo_produto = $_POST['tipo_produto'];
    $endereco_entrega = $_POST['endereco_entrega'];

    // Insere o pedido na tabela 'pedidos' com status 'pendente'
    $sql_pedido = "INSERT INTO pedidos (nome_cliente, contato, tipo_produto, quantidade, endereco_entrega, status, data_pedido)
                   VALUES (:nome_cliente, :contato, :tipo_produto, :quantidade, :endereco_entrega, 'pendente', NOW())";
    $stmt_pedido = $pdo->prepare($sql_pedido);
    $stmt_pedido->bindParam(':nome_cliente', $nome_cliente);
    $stmt_pedido->bindParam(':contato', $contato);
    $stmt_pedido->bindParam(':tipo_produto', $tipo_produto);
    $stmt_pedido->bindParam(':quantidade', $quantidade);
    $stmt_pedido->bindParam(':endereco_entrega', $endereco_entrega);

    // Executa a inserção do pedido
    if ($stmt_pedido->execute()) {
        // Pedido foi adicionado com sucesso
        header("Location: pedido.php?success=Pedido+adicionado+com+sucesso!");
        exit;
    } else {
        // Erro ao adicionar o pedido
        echo "Erro ao adicionar o pedido!";
    }
}
?>
