<?php
session_start();
require 'config.php';

// Verifica se o funcionário está logado
if (!isset($_SESSION['funcionario_id'])) {
    header("Location: index.php");
    exit;
}

// Captura os dados do funcionário logado
$funcionario_id = $_SESSION['funcionario_id'];
$funcionario_nome = $_SESSION['funcionario_nome'];

// Captura os dados do pedido da URL
$pedido_id = $_GET['id'];
$quantidade_pedido = $_GET['quantidade'];
$tipo_produto = $_GET['tipo_produto'];
$nome_cliente = isset($_GET['nome_cliente']) ? $_GET['nome_cliente'] : 'Cliente Anônimo';

// Verifica a quantidade em estoque usando a coluna correta (quantidade_total)
$sql_estoque = "SELECT quantidade_total FROM stock_carro WHERE tipo_produto = :tipo_produto";
$stmt_estoque = $pdo->prepare($sql_estoque);
$stmt_estoque->bindParam(':tipo_produto', $tipo_produto);
$stmt_estoque->execute();
$estoque = $stmt_estoque->fetch(PDO::FETCH_ASSOC);

if ($estoque && $estoque['quantidade_total'] >= $quantidade_pedido) {
    // Atualiza o estoque diminuindo a quantidade
    $nova_quantidade = $estoque['quantidade_total'] - $quantidade_pedido;
    $sql_update_estoque = "UPDATE stock_carro SET quantidade_total = :nova_quantidade WHERE tipo_produto = :tipo_produto";
    $stmt_update_estoque = $pdo->prepare($sql_update_estoque);
    $stmt_update_estoque->bindParam(':nova_quantidade', $nova_quantidade);
    $stmt_update_estoque->bindParam(':tipo_produto', $tipo_produto);
    
    if ($stmt_update_estoque->execute()) {
        // Atualiza o status do pedido para 'finalizado' e define a data da venda
        $sql_finalizar_pedido = "UPDATE pedidos SET status = 'finalizado', data_venda = NOW() WHERE id = :pedido_id";
        $stmt_finalizar_pedido = $pdo->prepare($sql_finalizar_pedido);
        $stmt_finalizar_pedido->bindParam(':pedido_id', $pedido_id);
        
        if ($stmt_finalizar_pedido->execute()) {
                        // Insere os dados na tabela de vendas
                        $sql_venda = "INSERT INTO vendas (funcionario_id, funcionario_nome, nome_cliente, tipo_produto, quantidade, hora_venda)
                        VALUES (:funcionario_id, :funcionario_nome, :nome_cliente, :tipo_produto, :quantidade, NOW())";
          $stmt_venda = $pdo->prepare($sql_venda);
          $stmt_venda->bindParam(':funcionario_id', $funcionario_id);
          $stmt_venda->bindParam(':funcionario_nome', $funcionario_nome);
          $stmt_venda->bindParam(':nome_cliente', $nome_cliente);
          $stmt_venda->bindParam(':tipo_produto', $tipo_produto);
          $stmt_venda->bindParam(':quantidade', $quantidade_pedido); // Use a variável correta

          if ($stmt_venda->execute()) {
            header("Location: pedido.php?success=Pedido+finalizado+com+sucesso!");
            exit;
        } else {
            echo "Erro ao registrar a venda.";
        }
    } else {
        echo "Erro ao atualizar o status do pedido.";
    }
} else {
    echo "Erro ao atualizar o estoque.";
}
} else {
echo "Estoque insuficiente para completar o pedido.";
}
?>
