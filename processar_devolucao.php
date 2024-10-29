<?php
// Inicia a sessão
session_start();

// Verifica se o funcionário está logado
if (!isset($_SESSION['funcionario_nome'])) {
    header("Location: index.php");
    exit;
}

// Captura os dados do funcionário logado
$funcionario_id = $_SESSION['funcionario_id'];

// Inclui a configuração de conexão ao banco de dados
require 'config.php';

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura o tipo de produto e a quantidade a ser devolvida
    $tipo_produto = $_POST['produto'];
    $quantidade_devolucao = (int) $_POST['quantidade_devolucao'];  // Converte para inteiro, por segurança

    // Inicia uma transação para garantir consistência
    $pdo->beginTransaction();

    try {
        // Verifica se há estoque suficiente no caminhão para devolver
        $sql_camiao = "SELECT quantidade_total FROM stock_carro WHERE tipo_produto = :tipo_produto";
        $stmt_camiao = $pdo->prepare($sql_camiao);
        $stmt_camiao->bindParam(':tipo_produto', $tipo_produto);
        $stmt_camiao->execute();
        $estoque_camiao = $stmt_camiao->fetch(PDO::FETCH_ASSOC);

        // Verifica se a quantidade no caminhão é suficiente
        if ($estoque_camiao && $estoque_camiao['quantidade_total'] >= $quantidade_devolucao) {
            
            // Atualiza o estoque principal, somando a quantidade devolvida
            $sql_atualizar_estoque_principal = "
                UPDATE stock 
                SET quantidade_total = quantidade_total + :quantidade_devolucao 
                WHERE tipo_produto = :tipo_produto";
            
            $stmt_atualizar_estoque_principal = $pdo->prepare($sql_atualizar_estoque_principal);
            $stmt_atualizar_estoque_principal->bindParam(':quantidade_devolucao', $quantidade_devolucao, PDO::PARAM_INT);
            $stmt_atualizar_estoque_principal->bindParam(':tipo_produto', $tipo_produto);
            $stmt_atualizar_estoque_principal->execute();

            // Atualiza o estoque do caminhão, subtraindo a quantidade devolvida
            $sql_atualizar_estoque_camiao = "
                UPDATE stock_carro 
                SET quantidade_total = quantidade_total - :quantidade_devolucao 
                WHERE tipo_produto = :tipo_produto";
            
            $stmt_atualizar_estoque_camiao = $pdo->prepare($sql_atualizar_estoque_camiao);
            $stmt_atualizar_estoque_camiao->bindParam(':quantidade_devolucao', $quantidade_devolucao, PDO::PARAM_INT);
            $stmt_atualizar_estoque_camiao->bindParam(':tipo_produto', $tipo_produto);
            $stmt_atualizar_estoque_camiao->execute();

            // Confirma a transação
            $pdo->commit();

            // Redireciona para a página de estoque do caminhão com sucesso
            header("Location: stock_entrega2.php?mensagem=devolvido");
            exit;
        } else {
            // Se não houver estoque suficiente no caminhão, desfaz a transação
            $pdo->rollBack();
            header("Location: stock_entrega2.php?erro=estoque_insuficiente");
            exit;
        }
    } catch (Exception $e) {
        // Em caso de erro, desfaz a transação e registra o erro
        $pdo->rollBack();
        error_log($e->getMessage());  // Registra o erro no log para depuração
        header("Location: stock_entrega2.php?erro=erro_devolucao");
        exit;
    }
}
?>
