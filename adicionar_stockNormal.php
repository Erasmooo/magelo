<?php
// Inicia a sessão
session_start();

// Verifica se o funcionário está logado
if (!isset($_SESSION['funcionario_nome'])) {
    header("Location: index.php");
    exit;
}

// Captura os dados do funcionário logado
$funcionario_nome = $_SESSION['funcionario_nome'];
$funcionario_id = $_SESSION['funcionario_id']; // Use este ID para registrar as operações no banco de dados

// Inclui a configuração de conexão ao banco de dados
require 'config.php';

// Consulta para pegar as quantidades totais do estoque para cada tipo de produto
$sql_totals = "
  SELECT tipo_produto, quantidade_total 
  FROM stock
";

$stmt_totals = $pdo->query($sql_totals);
$totals = $stmt_totals->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="img/logo-magelo.PNG" type="">
    <title>Adicionar Estoque - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="stock.css" />
</head>
<body>
    <!-- Cabeçalho -->
    <div class="admin-header">
        <div class="logo">
            <a href="stock_dashboard.php">
                 <img src="img/logo-magelo.PNG" alt="Logo Magelo Fábrica de Gelo">
            </a>
        </div>
        <div class="user-info">
            <i class="fas fa-user"></i>
            <span id="user-name"><?php echo $_SESSION['funcionario_nome']; ?></span>
            <i class="fas fa-chevron-down arrow"></i>
            <ul class="dropdown-menu">
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
    <!-- Container Principal -->
    <div class="main-container">
        <h1>Adicionar Stock de Produtos</h1>

        <!-- Formulário para Adicionar Estoque -->
        <div class="form-container">
            <form id="stockForm" action="processar_stock.php" method="POST">
                <h2>Registrar Produção de Gelo</h2>

                <div class="form-group">
                    <label for="produto">Tipo de Produto:</label>
                    <select id="produto" name="produto" required>
                        <option value="Gelo em Cubo">Gelo em Cubo</option>
                        <option value="Gelo em Barra">Gelo em Barra</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantidade-producao">Quantidade Produzida:</label>
                    <input type="number" id="quantidade-producao" name="quantidade-producao" min="0" required />
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">Adicionar ao Stock</button>
                </div>
            </form>
        </div>

        <!-- Tabela de Resumo Total de Produtos -->
        <h2>Total de Produtos no Stock</h2>
        <table class="employee-table" id="total-stock-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($totals as $total): ?>
                <tr>
                    <td><?php echo $total['tipo_produto']; ?></td>
                    <td><?php echo $total['quantidade_total']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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

    <!-- Footer -->
    <footer class="admin-footer">
      <div class="footer-rights">
        <p>&copy; 2024 Magelo Fábrica de Gelo. Todos os direitos reservados.</p>
      </div>
    </footer>
</body>
</html>
