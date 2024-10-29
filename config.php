<?php
// Configurações de conexão com o banco de dados
$host = 'localhost'; // Servidor de banco de dados
$dbname = 'magelo';  // Nome da base de dados
$user = 'root';      // Usuário do banco de dados
$password = '';      // Senha do banco de dados (altere conforme sua configuração)

try {
    // Cria a conexão PDO com o banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>
