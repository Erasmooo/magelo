<?php
// Configurações de conexão com o banco de dados
$host = 'sql208.infinityfree.com'; // Servidor de banco de dados
$dbname = 'if0_37633251_magelo';  // Nome da base de dados
$user = 'if0_37633251';      // Usuário do banco de dados
$password = 'Pyros123456'; // Insira a senha correta

// $host = 'sql208.infinityfree.com'; // Servidor de banco de dados
// $dbname = 'if0_37633251_magelo';  // Nome da base de dados
// $user = 'root';      // Usuário do banco de dados
// $password = ''; // Insira a senha correta


try {
    // Cria a conexão PDO com o banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>
