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

// Processa o formulário de inserção de despesas
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['adicionar_despesa'])) {
    $data = date('Y-m-d'); // Define a data automaticamente
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];

    try {
        // Insere a nova despesa no banco de dados
        $sql_inserir = "INSERT INTO despesas (data, descricao, valor) VALUES (:data, :descricao, :valor)";
        $stmt = $pdo->prepare($sql_inserir);
        $stmt->execute(['data' => $data, 'descricao' => $descricao, 'valor' => $valor]);

        // Redireciona para evitar reenvio do formulário e exibir mensagem de sucesso
        header("Location: despesas.php?success=1");
        exit;
    } catch (PDOException $e) {
        echo "Erro ao inserir despesa: " . $e->getMessage();
    }
}

// Consulta para obter todas as despesas, ordenadas por ID em ordem decrescente (despesa mais recente primeiro)
$sql_despesas = "SELECT * FROM despesas ORDER BY id DESC";
$stmt_despesas = $pdo->query($sql_despesas);
$despesas = $stmt_despesas->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Despesas - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="rotas.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

    <!-- Container Principal -->
    <div class="main-container">
        <h1>Gestão de Despesas</h1>

        <!-- Exibe mensagem de sucesso, se existir -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success">Despesa adicionada com sucesso!</div>
        <?php endif; ?>

        <!-- Formulário para Adicionar Nova Despesa -->
        <div class="add-route-container">
            <h2>Adicionar Nova Despesa</h2>
            <form action="despesas.php" method="POST">
                <div class="form-group">
                    <label for="descricao">Tipo de despesa:</label>
                    <input type="text" id="descricao" name="descricao" placeholder="Descrição da despesa" required>
                </div>
                <div class="form-group">
                    <label for="valor">Valor:</label>
                    <input type="number" step="0.01" id="valor" name="valor" placeholder="Valor da despesa" required>
                </div>
                <button type="submit" name="adicionar_despesa" class="btn"><i class="fas fa-plus"></i> Adicionar Despesa</button>
            </form>
        </div>

        <!-- Tabela de Todas as Despesas -->
        <div class="table-container">
            <h2>Lista de Despesas</h2>
            <div class="table-wrapper">
                <table class="route-list-container">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($despesas as $despesa): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($despesa['id']); ?></td>
                                <td><?php echo htmlspecialchars($despesa['data']); ?></td>
                                <td><?php echo htmlspecialchars($despesa['descricao']); ?></td>
                                <td><?php echo 'MZN ' . number_format($despesa['valor'], 2, ',', '.'); ?></td>
                                <td>
                                    <a href="remover_despesa.php?id=<?php echo $despesa['id']; ?>" class="btn-delete"><i class="fas fa-trash"></i> Remover</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Script para o Dropdown -->
    <script>
        const userInfo = document.querySelector(".user-info");
        const dropdownMenu = document.querySelector(".dropdown-menu");
        const arrowIcon = document.querySelector(".arrow");

        userInfo.addEventListener("click", () => {
            dropdownMenu.classList.toggle("show");
            arrowIcon.classList.toggle("rotate");
        });

        window.onclick = function (event) {
            if (!event.target.matches(".user-info, .user-info *")) {
                if (dropdownMenu.classList.contains("show")) {
                    dropdownMenu.classList.remove("show");
                    arrowIcon.classList.remove("rotate");
                }
            }
        };
    </script>

    <!-- Rodapé -->
    <footer class="admin-footer">
        <div class="footer-rights">
            <p>&copy; <?php echo date("Y"); ?> Magelo Fábrica de Gelo. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>
