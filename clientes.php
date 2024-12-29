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

// Consulta para listar todos os clientes registrados
$sql_clientes = "SELECT * FROM clientes";
$stmt_clientes = $pdo->query($sql_clientes);
$clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/logo-magelo.PNG" type="">
    <title>Gestão de Clientes - Magelo Fábrica de Gelo</title>
  <link rel="stylesheet" href="clientes.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>

<!-- Cabeçalho -->
    <!-- Navbar -->
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
    <h1>Gestão de Clientes</h1>

    <!-- Botão Adicionar Cliente -->
    <button class="btn add-btn" onclick="window.location.href='adicionar_cliente.php'">
            <i class="fas fa-plus"></i> Adicionar Cliente
    </button>

    <!-- Tabela de Clientes -->
    <div class="table-container">
    <table class="client-table">
        <thead>
            <tr>
            <th>ID</th>
                <th>Nome</th>
                <th>Endereço</th>
                <th>Nuit</th>
                <th>Contacto</th>
                <th>Rota</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($clientes)): ?>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                    <td><?php echo $cliente['id']; ?></td>
                        <td><?php echo $cliente['nome']; ?></td>
                        <td><?php echo $cliente['endereco']; ?></td>
                        <td><?php echo $cliente['nuit']; ?></td>
                        <td><?php echo $cliente['telefone']; ?></td>
                        <td><?php echo $cliente['nome_rota']; ?></td>
                        <td class="table-actions">
                        <a href="editar_cliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-edit">
                                    <i class="fas fa-edit"></i> 
                                </a>
                            <form action="apagar_cliente.php" method="POST" style="display:inline-block;">
                                <input type="hidden" name="id_cliente" value="<?php echo $cliente['id']; ?>">
                                <button type="submit" name="remover" class="btn btn-delete">
                                    <i class="fas fa-trash"></i> 
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Nenhum cliente encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
        </table>
        </div>

    
    </div>
</div>

<!-- Footer -->
    <footer class="admin-footer">
      <div class="footer-rights">
        <p>&copy; 2024 Magelo Fábrica de Gelo. Todos os direitos reservados.</p>
      </div>
    </footer>

<script>
    const cancelFormBtn = document.getElementById('cancelFormBtn');
    cancelFormBtn.addEventListener('click', () => {
        document.getElementById('clientFormContainer').style.display = 'none';
    });
    
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

</body>
</html>
