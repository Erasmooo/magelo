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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura os dados enviados pelo formulário
    $nome = $_POST['name'];
    $idade = $_POST['age'];
    $genero = $_POST['gender'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Lembre-se de hashear a senha
    $telefone = $_POST['phone'];
    $morada = $_POST['address'];
    $tipo_usuario = $_POST['userType'];

    // Prepara a consulta para inserir o funcionário no banco de dados
    $sql = "INSERT INTO funcionarios (nome, idade, genero, email, senha, telefone, morada, tipo_usuario)
            VALUES (:nome, :idade, :genero, :email, :senha, :telefone, :morada, :tipo_usuario)";
    $stmt = $pdo->prepare($sql);
    
    // Vincula os parâmetros
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':idade', $idade);
    $stmt->bindParam(':genero', $genero);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senha);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':morada', $morada);
    $stmt->bindParam(':tipo_usuario', $tipo_usuario);

    // Executa a consulta
    if ($stmt->execute()) {
        // Redireciona com uma mensagem de sucesso
        header("Location: funcionario.html?success=Funcionário+adicionado+com+sucesso");
        exit;
    } else {
        // Redireciona com uma mensagem de erro
        header("Location: funcionario.html?error=Erro+ao+adicionar+funcionário");
        exit;
    }
}
?>
