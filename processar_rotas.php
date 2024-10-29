<?php
// Inclui a configuração de conexão ao banco de dados
require 'config.php';

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtém os dados do formulário
    $nomeRota = isset($_POST['nome_rota']) ? trim($_POST['nome_rota']) : null;
    $descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : null;

    // Verifica se o nome da rota foi preenchido
    if (!empty($nomeRota)) {
        // Insere os dados na tabela 'rotas'
        $sql = "INSERT INTO rotas (nome_rota, descricao) VALUES (:nome_rota, :descricao)";
        $stmt = $pdo->prepare($sql);

        // Vincula os valores e executa a inserção
        $stmt->bindParam(':nome_rota', $nomeRota);
        $stmt->bindParam(':descricao', $descricao);

        if ($stmt->execute()) {
            // Redireciona de volta para a página de rotas após o sucesso
            header("Location: rotaErasmo.php?mensagem=sucesso");
            exit;
        } else {
            echo "Erro ao inserir a rota.";
        }
    } else {
        echo "O nome da rota é obrigatório.";
    }
}
?>
