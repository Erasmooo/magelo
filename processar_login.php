<?php
// Inicia a sessão
session_start();

// Inclui o arquivo de configuração do banco de dados
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['usuario'];
    $senha = $_POST['senha'];

    // Consulta SQL para verificar se o funcionário existe com base no email e senha
    $sql = "SELECT * FROM funcionarios WHERE username = :username AND senha = :senha LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':senha', $senha);
    $stmt->execute();

    // Verifica se o funcionário foi encontrado
    if ($stmt->rowCount() == 1) {
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Armazena os dados do funcionário na sessão
        $_SESSION['funcionario_id'] = $usuario['id'];
        $_SESSION['funcionario_nome'] = $usuario['nome'];
        $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

        // Redireciona com base no tipo de usuário
        switch ($usuario['tipo_usuario']) {
            case 'ADMIN':
                header("Location: admin_dashboard.php"); // Página do admin
                break;
            case 'NORMAL':
                header("Location: stock_dashboard.php"); // Página do controle de estoque
                break;
            default:
                echo "Erro: Tipo de usuário não reconhecido.";
        }
        exit;
    } else {
        echo "Usuário ou senha inválidos!";
    }
}
