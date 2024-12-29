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
?>

<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="img/logo-magelo.PNG" type="">
    <title>Painel Admin - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="geral.css?v=<?php echo time(); ?>" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
  </head>
  <body>
  <div class="admin-header">
      <div class="logo" >
        <a href="admin_dashboard.php">
          <img src="img/logo-magelo.PNG" alt="Logo Magelo Fábrica de Gelo" />
        </a>
      </div>
      <div class="user-info">
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

    <div class="main-container">
      <h1>Administrador</h1>
      <div class="menu-options">

        <!-- Card de Dashboard -->
        <a href="dashboard_principal.php" class="menu-link">
          <div class="menu-card">
            <i class="fas fa-chart-line"></i>
            <h2>Dashboard</h2>
          </div>
        </a>

        <!-- Card de Relatório -->
        <a href="relatorio.php" class="menu-link">
          <div class="menu-card">
            <i class="fas fa-file-alt"></i>
            <h2>Relatório</h2>
          </div>
        </a>

        <!-- Card de Pedido e Entrega -->
        <a href="vendas.php" class="menu-link">
          <div class="menu-card">
            <i class="fas fa-shopping-cart"></i>
            <h2>Vendas</h2>
          </div>
        </a>

        <!-- Card de Stock -->
        <a href="stock.php" class="menu-link">
          <div class="menu-card">
            <i class="fas fa-box"></i>
            <h2>Stock</h2>
          </div>
        </a>

        <!-- Card de Funcionários -->
        <a href="funcionario.php" class="menu-link">
          <div class="menu-card">
            <i class="fas fa-users"></i>
            <h2>Funcionários</h2>
          </div>
        </a>

        <!-- Card de Rota -->
        <a href="rotaErasmo.php" class="menu-link">
          <div class="menu-card">
            <i class="fas fa-route"></i>
            <h2>Rota</h2>
          </div>
        </a>

        <!-- Card de Clientes -->
        <a href="clientes.php" class="menu-link">
          <div class="menu-card">
            <i class="fas fa-user-friends"></i>
            <h2>Clientes</h2>
          </div>
        </a>

        <!-- Card de Quebras -->
        <a href="quebras.php" class="menu-link">
          <div class="menu-card">
            <i class="fas fa-exclamation-triangle"></i>
            <h2>Quebras</h2>
          </div>
        </a>

         <!-- Card de Despesas -->
         <a href="despesas.php" class="menu-link">
          <div class="menu-card">
            <i class="fas fa-wallet"></i>
            <h2>Despesas</h2>
          </div>
        </a>

      </div>
    </div>

    <script>
        // Dropdown Menu Script
        const userInfo = document.querySelector(".user-info");
        const dropdownMenu = document.querySelector(".dropdown-menu");
        const arrowIcon = document.querySelector(".arrow");

        userInfo.addEventListener("click", () => {
            dropdownMenu.classList.toggle("show");
            arrowIcon.classList.toggle("rotate");
        });

        // Fecha o dropdown se o usuário clicar fora dele
        window.onclick = function (event) {
            if (!event.target.matches(".user-info, .user-info *")) {
                if (dropdownMenu.classList.contains("show")) {
                    dropdownMenu.classList.remove("show");
                    arrowIcon.classList.remove("rotate");
                }
            }
        };
    </script>

    <footer class="admin-footer">
      <div class="footer-rights">
        <p>&copy; 2024 Magelo Fábrica de Gelo. Todos os direitos reservados.</p>
      </div>
    </footer>
  </body>
</html>
