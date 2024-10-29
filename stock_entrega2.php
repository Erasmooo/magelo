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

// Consulta para pegar as quantidades totais do estoque de gelo no caminhão
$sql_camiao_totals = "
  SELECT tipo_produto, SUM(quantidade_total) AS quantidade_total 
  FROM stock_carro
  GROUP BY tipo_produto
";
$stmt_camiao_totals = $pdo->query($sql_camiao_totals);
$camiao_totals = $stmt_camiao_totals->fetchAll(PDO::FETCH_ASSOC);

// Consulta para pegar as quantidades totais do estoque principal
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
    <title>Gestão de Estoque do Caminhão - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="stock.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f9f9f9;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            color: #333;
            min-height: 100vh;
        }

        
        .main-container {
            width: 100%;
            max-width: 800px;
            margin-top: 100px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            margin-bottom: 30px; /* Espaçamento extra para o footer */
        }

        h1 {
            font-size: 32px;
            color: #1e90ff;
            text-align: center;
            margin-bottom: 40px;
        }

        .form-container {
            margin-bottom: 30px;
            text-align: center;
        }

        .form-container h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 20px;
            width: 100%;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
            text-align: left;
        }

        label {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        select, input {
            padding: 10px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-actions {
            text-align: center;
        }

        .form-actions .btn {
            background-color: #1e90ff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 16px;
        }

        .form-actions .btn:hover {
            background-color: #0b68c1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            text-align: center;
        }

        table th, table td {
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        table td {
            font-size: 16px;
        }

       
        /* Mobile Adjustments */
        @media (max-width: 768px) {
            .admin-footer {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="logo">
            <img src="img/logo-magelo.PNG" alt="Logo Magelo Fábrica de Gelo">
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
        <h1>Gestão de Stock do Camião</h1>

        <!-- Formulário para Abastecer o Caminhão -->
        <div class="form-container">
            <form id="truckStockForm" action="processar_abastecimento.php" method="POST">
                <h2>Abastecer Camião</h2>

                <div class="form-group">
                    <label for="produto">Tipo de Produto:</label>
                    <select id="produto" name="produto" required>
                        <option value="Gelo em Cubo">Gelo em Cubo</option>
                        <option value="Gelo em Barra">Gelo em Barra</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantidade-abastecimento">Quantidade a Abastecer:</label>
                    <input type="number" id="quantidade-abastecimento" name="quantidade_abastecimento" required />
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">Abastecer Camião</button>
                </div>
            </form>
        </div>

        <!-- Formulário para Devolver Gelo ao Estoque Principal -->
        <div class="form-container">
            <form id="returnStockForm" action="processar_devolucao.php" method="POST">
                <h2>Devolver Gelo ao Stock Principal</h2>

                <div class="form-group">
                    <label for="produto-devolucao">Tipo de Gelo:</label>
                    <select id="produto-devolucao" name="produto" required>
                        <option value="Gelo em Cubo">Gelo em Cubo</option>
                        <option value="Gelo em Barra">Gelo em Barra</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantidade-devolucao">Quantidade a Devolver:</label>
                    <input type="number" id="quantidade-devolucao" name="quantidade_devolucao" required />
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">Devolver ao Stock</button>
                </div>
            </form>
        </div>

        <!-- Tabela de Resumo de Estoque no Caminhão -->
        <h2>Stock actual no camião</h2>
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($camiao_totals as $total): ?>
                <tr>
                    <td><?php echo $total['tipo_produto']; ?></td>
                    <td><?php echo $total['quantidade_total']; ?> Unidades</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Tabela de Resumo de Estoque Principal -->
        <h2>Stock actual no depósito principal</h2>
        <table>
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
      <div class="footer-logo">
        <img src="img/logo-magelo.PNG" alt="Logo Magelo Fábrica de Gelo" />
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
