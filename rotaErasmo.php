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

// Inclui a configuração de conexão ao banco de dados
require 'config.php';

// Consulta para buscar as rotas da base de dados
$sql_rotas = "SELECT id, nome_rota, descricao FROM rotas";
$stmt_rotas = $pdo->query($sql_rotas);
$rotas = $stmt_rotas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/logo-magelo.PNG" type="">
    <title>Gestão de Rotas - Magelo Fábrica de Gelo</title>
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
        <span id="user-name"><?php echo htmlspecialchars($nomeFuncionario); ?></span>
        <i class="fas fa-chevron-down arrow"></i>
        <ul class="dropdown-menu">
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
</div>

<!-- Container Principal -->
<div class="main-container">
    <h1>Gestão de Rotas</h1>

    <!-- Formulário para Adicionar Nova Rota -->
    <div class="add-route-container">
        <h2>Adicionar Nova Rota</h2>
        <form id="stockForm" action="processar_rotas.php" method="POST">
            <div class="form-group">
                <label for="nomeRota">Nome da Rota:</label>
                <input type="text" id="nomeRota" name="nome_rota" placeholder="Nome da Rota" required>
            </div>
            <div class="form-group">
                <label for="descricaoRota">Descrição da Rota:</label>
                <input type="text" id="descricaoRota" name="descricao" placeholder="Descrição da Rota (Opcional)">
            </div>
            <button type="submit" class="btn">Adicionar Rota</button>
        </form>
    </div>

    <!-- Lista de Rotas -->
    <div class="table-container">
        <h2>Lista de Rotas</h2>
        <div class="table-wrapper">
            <table class="route-list-container">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome da Rota</th>
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rotas)): ?>
                        <?php foreach ($rotas as $rota): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($rota['id']); ?></td>
                            <td><?php echo htmlspecialchars($rota['nome_rota']); ?></td>
                            <td class="descricao"><?php echo !empty($rota['descricao']) ? htmlspecialchars($rota['descricao']) : 'Sem descrição'; ?></td>
                            <td>
                                <a href="editar_rota.php?id=<?php echo htmlspecialchars($rota['id']); ?>" class="btn-edit"><i class="fas fa-edit"></i> Editar</a>
                                <a href="apagar_rota.php?id=<?php echo htmlspecialchars($rota['id']); ?>" class="btn-delete" onclick="return confirm('Tem certeza que deseja excluir esta rota?');">
                                    <i class="fas fa-trash"></i> Excluir
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">Nenhuma rota encontrada.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Rodapé -->
    <footer class="admin-footer">
      <div class="footer-rights">
        <p>&copy; 2024 Magelo Fábrica de Gelo. Todos os direitos reservados.</p>
      </div>
    </footer>
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

</body>
</html>
