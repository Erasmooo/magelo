<?php
// Inclui a configuração de conexão ao banco de dados
require 'config.php';

// Verifica se o ID foi passado pela URL
if (isset($_GET['id'])) {
    // Obtém o ID da rota
    $rotaId = $_GET['id'];

    // Verifica se o ID é válido
    if (!empty($rotaId) && is_numeric($rotaId)) {
        // Prepara a query de exclusão
        $sql = "DELETE FROM rotas WHERE id = :id";
        $stmt = $pdo->prepare($sql);

        // Vincula o valor do ID e executa a exclusão
        $stmt->bindParam(':id', $rotaId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Redireciona de volta para a página de rotas após o sucesso
            header("Location: rotaErasmo.php?mensagem=rota_excluida");
            exit;
        } else {
            echo "Erro ao excluir a rota.";
        }
    } else {
        echo "ID inválido.";
    }
}
?>
