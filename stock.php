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
$funcionario_id = $_SESSION['funcionario_id'];

// Inclui a configuração de conexão ao banco de dados
require 'config.php';

// Consulta para pegar as quantidades totais do estoque para cada tipo de produto
$sql_totals = "SELECT id, tipo_produto, quantidade_total FROM stock";
$stmt_totals = $pdo->query($sql_totals);
$totals = $stmt_totals->fetchAll(PDO::FETCH_ASSOC);

// Se o formulário for enviado, processa a atualização do estoque
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['estoque_id'])) {
    $estoque_id = $_POST['estoque_id'];
    $nova_quantidade = $_POST['quantidade_total'];

    // Atualiza o estoque
    $sql_update = "UPDATE stock SET quantidade_total = :quantidade_total WHERE id = :id";
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->bindParam(':quantidade_total', $nova_quantidade);
    $stmt_update->bindParam(':id', $estoque_id);
    $stmt_update->execute();

    header("Location: stock.php");
    exit;
}


?>

<!DOCTYPE html>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="img/logo-magelo.PNG" type="">
    <title>Gerenciar Estoque - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="stock.css?v=<?php echo time(); ?>" />
</head>
<body>
    <!-- Cabeçalho -->
    <div class="admin-header">
        <div class="logo">
            <a href="admin_dashboard.php">
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

    <!-- Espaço para o cabeçalho -->
    <div class="spacer"></div>

    <!-- Modal para mostrar mensagem de sucesso -->
    <?php if (isset($_GET['success'])): ?>
        <div id="successModal" class="modal" style="display: flex;">
            <div class="modal-content">
                <?php if ($_GET['success'] === 'remove'): ?>
                    <p>Quantidade removida com sucesso!</p>
                <?php else: ?>
                    <p>Quantidade adicionada ao stock com sucesso!</p>
                <?php endif; ?>
                <button class="close-btn" onclick="closeModal()">Fechar</button>
            </div>
        </div>
    <?php endif; ?>


    <!-- Container Principal -->
    <div class="main-container">
        <h1>Stock de Produtos</h1>

        <!-- Formulário para Adicionar Estoque -->
        <div class="form-container">
            <form id="stockForm" action="processar_stock.php" method="POST">
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
        <div class="table-container"> <!-- Contêiner com scroll horizontal -->
            <table class="employee-table" id="total-stock-table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade Total</th>
                        <th>Remover Quantidade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($totals as $total): ?>
                    <tr>
                        <td><?php echo $total['tipo_produto']; ?></td>
                        <td><?php echo $total['quantidade_total']; ?></td>
                        <td>
                            <form action="processar_stock_remover.php" method="POST" style="display: flex; align-items: center;">
                                <input type="hidden" name="estoque_id" value="<?php echo $total['id']; ?>">
                                <input type="number" name="quantidade_remover" min="0" placeholder="" required>
                                <button type="submit" class="btn-remover">Remover</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <script>
        // Função para fechar o modal
        function closeModal() {
            document.getElementById('successModal').style.display = 'none';
        }

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
    </div>
    <!-- Footer -->
    <footer class="admin-footer">
        <div class="footer-rights">
            <p>&copy; 2024 Magelo Fábrica de Gelo. Todos os direitos reservados.</p>
        </div>
    </footer>

    <!-- Estilos adicionais para ajuste de margem -->
    <style>
        .spacer {
            margin-top: 20px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            width: 300px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        .close-btn {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #1e90ff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</body>
</html>
