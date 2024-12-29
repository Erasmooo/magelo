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

// Verifica se o ID do cliente foi enviado
if (!isset($_GET['id'])) {
    header("Location: clientes.php");
    exit;
}

// Obtém o ID do cliente
$clienteId = $_GET['id'];

// Consulta para obter os dados do cliente
$sql = "SELECT * FROM clientes WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $clienteId);
$stmt->execute();
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se o cliente existe
if (!$cliente) {
    header("Location: clientes.php?mensagem=naoencontrado");
    exit;
}

// Se o formulário for enviado, atualiza os dados do cliente
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome_cliente'];
    $endereco = $_POST['endereco'];
    $nuit = $_POST['nuit'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $nome_rota = $_POST['rota'];

    $sqlUpdate = "UPDATE clientes SET nome = :nome, endereco = :endereco, nuit = :nuit, telefone = :telefone, email = :email, nome_rota = :nome_rota WHERE id = :id";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->bindParam(':nome', $nome);
    $stmtUpdate->bindParam(':endereco', $endereco);
    $stmtUpdate->bindParam(':nuit', $nuit);
    $stmtUpdate->bindParam(':telefone', $telefone);
    $stmtUpdate->bindParam(':email', $email);
    $stmtUpdate->bindParam(':nome_rota', $nome_rota);
    $stmtUpdate->bindParam(':id', $clienteId);

    if ($stmtUpdate->execute()) {
        header("Location: clientes.php?mensagem=editado");
        exit;
    } else {
        echo "Erro ao atualizar cliente.";
    }
}

// Busca as rotas para o dropdown
$sqlRotas = "SELECT nome_rota FROM rotas";
$stmtRotas = $pdo->prepare($sqlRotas);
$stmtRotas->execute();
$rotas = $stmtRotas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/logo-magelo.PNG" type="">
    <title>Editar Cliente - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="css/funcionario.css">
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
    <div class="main-container">
        <h1>Editar Cliente</h1>

        <!-- Formulário de Edição de Cliente -->
        <div class="form-container">
            <form action="" method="POST">
                <h2 id="formTitle">Editar Cliente</h2>

                <label for="nome_cliente">Nome:</label>
                <input type="text" id="nome_cliente" name="nome_cliente" value="<?php echo $cliente['nome']; ?>" required>

                <label for="endereco">Endereço:</label>
                <input type="text" id="endereco" name="endereco" value="<?php echo $cliente['endereco']; ?>" required>

                <label for="nuit">Nuit:</label>
                <input type="text" id="nuit" name="nuit" value="<?php echo $cliente['nuit']; ?>" required>

                <label for="telefone">Telefone:</label>
                <input type="text" id="telefone" name="telefone" value="<?php echo $cliente['telefone']; ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $cliente['email']; ?>" required>

                <label for="rota">Rota:</label>
                <select id="rota" name="rota" required>
                    <?php foreach ($rotas as $rota): ?>
                        <option value="<?php echo $rota['nome_rota']; ?>" <?php echo ($cliente['nome_rota'] == $rota['nome_rota']) ? 'selected' : ''; ?>>
                            <?php echo $rota['nome_rota']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn save-btn">Salvar Alterações</button>
                <a href="clientes.php" class="btn cancel-btn">Cancelar</a>
            </form>
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

    
    <!-- Footer -->
    <footer class="admin-footer">
      <div class="footer-rights">
        <p>&copy; 2024 Magelo Fábrica de Gelo. Todos os direitos reservados.</p>
      </div>
    </footer>
</body>
</html>
