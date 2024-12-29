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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/logo-magelo.PNG" type="">
    <title>Painel Armazém - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="dash.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  
</head>
<body>
    <div class="admin-header">
      <div class="logo">
        <img src="img/logo-magelo.PNG" alt="Logo Magelo Fábrica de Gelo" />
      </div>
      <div class="user-info">
        <i class="fas fa-user"></i>
        <!-- Exibe o nome do funcionário dinamicamente -->
        <span id="user-name"><?php echo $nomeFuncionario; ?></span>
        <i class="fas fa-chevron-down arrow"></i>
        <ul class="dropdown-menu">
          <li>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout </a>
          </li>
        </ul>
      </div>
    </div>

    <div class="main-container">
        <h1>Menu Principal - Gestor Fabrica</h1>
        <div class="menu-options">
            <a href="adicionar_stockNormal.php" class="menu-card">
                <i class="fas fa-box"></i>
                <h2>Adicionar Stock</h2>
            </a>

            <a href="vendaEntregador.php" class="menu-link">
                <div class="menu-card">
                    <i class="fas fa-shopping-cart"></i>
                    <h2>Vendas</h2>
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

    <script>
      // Toggle dropdown visibility and rotate arrow
      const userInfo = document.querySelector(".user-info");
      const dropdownMenu = document.querySelector(".dropdown-menu");
      const arrowIcon = document.querySelector(".arrow");

      userInfo.addEventListener("click", () => {
        dropdownMenu.classList.toggle("show");
        arrowIcon.classList.toggle("rotate"); // Adiciona classe de rotação para a seta
      });

      // Close dropdown if clicked outside
      window.onclick = function (event) {
        if (!event.target.matches(".user-info, .user-info *")) {
          if (dropdownMenu.classList.contains("show")) {
            dropdownMenu.classList.remove("show");
            arrowIcon.classList.remove("rotate"); // Volta seta ao normal
          }
        }
      };
    </script>

    <footer class="admin-footer">
      <div class="footer-rights">
        <p>&copy; 2024 Magelo Fábrica de Gelo. Todos os direitos reservados.</p>
      </div>
    </footer>  </body>
</html>
