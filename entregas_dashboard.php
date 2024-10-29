<?php
// Inicia a sessão
session_start();

// Verifica se o funcionário está logado
if (!isset($_SESSION['funcionario_nome'])) {
    header("Location: index.php"); // Redireciona para o login se não estiver logado
    exit;
}

// Armazena o nome do funcionário na variável para ser usada no HTML
$nomeFuncionario = $_SESSION['funcionario_nome'];

// Conexão com o banco de dados
require 'config.php';

// Consulta as rotas
$sql_rotas = "SELECT id, nome_rota FROM rotas";
$stmt_rotas = $pdo->query($sql_rotas);
$rotas = $stmt_rotas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Entregas - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="css/menuprincipal.css"> <!-- Certifique-se de usar o CSS correto -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
</head>
<body>
    <!-- Navbar -->
    <div class="admin-header">
      <div class="logo">
        <img src="img/logo-magelo.PNG" alt="Logo Magelo Fábrica de Gelo" />
      </div>
      <div class="user-info">
        <!-- Carregar as rotas dinamicamente -->
        <select id="rota" name="rota">
            <?php foreach ($rotas as $rota): ?>
                <option value="<?php echo $rota['id']; ?>"><?php echo $rota['nome_rota']; ?></option>
            <?php endforeach; ?>
        </select>
        <i class="fas fa-user"></i>
        <!-- Exibe o nome do funcionário dinamicamente -->
        <span id="user-name"><?php echo $nomeFuncionario; ?></span>
        <i class="fas fa-chevron-down arrow"></i>
        <ul class="dropdown-menu">
          <li>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Container Principal -->
    <div class="main-container">
        <h1>Menu Principal </h1>
        <div class="menu-options">
        <a href="stock.php" class="menu-card">
                <i class="fas fa-box"></i>
                <h2>Adicionar Stock</h2>
            </a>
            <a href="pedidoEntrega.php" class="menu-link">
                <div class="menu-card">
                    <i class="fas fa-shopping-cart"></i>
                    <h2>produção e Vendas</h2>
                </div>
            </a>
            <a href="stock_entrega2.php" class="menu-link">
                <div class="menu-card">
                    <i class="fas fa-box"></i>
                    <h2>Stock do Camião</h2>
                </div>
            </a>
            
        </div>
    </div>

    <!-- Footer -->
    <footer class="admin-footer">
        <div class="footer-logo">
            <img src="img/logo-magelo.PNG" alt="Logo Magelo Fábrica de Gelo">
        </div>
        <div class="footer-info">
            <i class="fas fa-map-marker-alt"></i>
            <span>Av. Eduardo Mondlane 1527, Maputo, Moçambique</span>
        </div>
        <div class="footer-info">
            <i class="fas fa-envelope"></i>
            <span>magelo.moz@gmail.com</span>
        </div>
        <div class="footer-info">
            <i class="fas fa-phone"></i>
            <span>+258 82 306 1764</span>
        </div>
        <div class="footer-rights">
            <p>&copy; 2024 Magelo Fábrica de Gelo. Todos os direitos reservados.</p>
        </div>
    </footer>

</body>
</html>
