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

// Verifica se o ID do funcionário foi enviado
if (!isset($_GET['id'])) {
    header("Location: funcionario.php");
    exit;
}

// Obtém o ID do funcionário
$funcionarioId = $_GET['id'];

// Consulta para obter os dados do funcionário
$sql = "SELECT * FROM funcionarios WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $funcionarioId);
$stmt->execute();
$funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se o funcionário existe
if (!$funcionario) {
    header("Location: funcionario.php?mensagem=naoencontrado");
    exit;
}

// Se o formulário for enviado, atualiza os dados do funcionário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['name'];
    $idade = $_POST['age'];
    $genero = $_POST['gender'];
    $username = $_POST['username'];
    $telefone = $_POST['phone'];
    $morada = $_POST['address'];
    $tipoUsuario = $_POST['userType'];
    $novaSenha = $_POST['password'];

    // Monta a consulta SQL para atualização
    $sqlUpdate = "UPDATE funcionarios SET nome = :nome, idade = :idade, genero = :genero, username = :username, telefone = :telefone, morada = :morada, tipo_usuario = :tipo_usuario";

    // Apenas atualiza a senha se um novo valor foi fornecido
    if (!empty($novaSenha)) {
        $sqlUpdate .= ", senha = :senha";
    }

    $sqlUpdate .= " WHERE id = :id";

    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->bindParam(':nome', $nome);
    $stmtUpdate->bindParam(':idade', $idade);
    $stmtUpdate->bindParam(':genero', $genero);
    $stmtUpdate->bindParam(':username', $username);
    $stmtUpdate->bindParam(':telefone', $telefone);
    $stmtUpdate->bindParam(':morada', $morada);
    $stmtUpdate->bindParam(':tipo_usuario', $tipoUsuario);
    $stmtUpdate->bindParam(':id', $funcionarioId);

    if (!empty($novaSenha)) {
        $stmtUpdate->bindParam(':senha', $novaSenha);
    }

    if ($stmtUpdate->execute()) {
        header("Location: funcionario.php?mensagem=editado");
        exit;
    } else {
        echo "Erro ao atualizar funcionário.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/logo-magelo.PNG" type="">
    <title>Editar Funcionário - Magelo Fábrica de Gelo</title>
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
        <h1>Editar Funcionário</h1>

        <!-- Formulário de Edição de Funcionário -->
        <div class="form-container" id="employeeFormContainer">
            <form id="employeeForm" action="" method="POST">
                <h2 id="formTitle">Editar Funcionário</h2>

                <label for="name">Nome:</label>
                <input type="text" id="name" name="name" value="<?php echo $funcionario['nome']; ?>" required>

                <label for="age">Idade:</label>
                <input type="number" id="age" name="age" value="<?php echo $funcionario['idade']; ?>" min="0" required>

                <label for="gender">Gênero:</label>
                <select id="gender" name="gender" required>
                    <option value="MASCULINO" <?php echo ($funcionario['genero'] == 'MASCULINO') ? 'selected' : ''; ?>>Masculino</option>
                    <option value="FEMININO" <?php echo ($funcionario['genero'] == 'FEMININO') ? 'selected' : ''; ?>>Feminino</option>
                </select>

                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo $funcionario['username']; ?>" required>

                <label for="password">Senha:</label>
                <input type="text" id="senha" name="password" value="<?php echo $funcionario['senha']; ?>" placeholder="Digite uma nova senha">

                <label for="phone">Telefone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo $funcionario['telefone']; ?>" required>

                <label for="address">Morada:</label>
                <input type="text" id="address" name="address" value="<?php echo $funcionario['morada']; ?>" required>

                <label for="userType">Tipo de Usuário:</label>
                <select id="userType" name="userType" required>
                    <option value="ADMIN" <?php echo ($funcionario['tipo_usuario'] == 'ADMIN') ? 'selected' : ''; ?>>ADMIN</option>
                    <option value="NORMAL" <?php echo ($funcionario['tipo_usuario'] == 'NORMAL') ? 'selected' : ''; ?>>NORMAL</option>
                </select>

                <button type="submit" class="btn save-btn">Salvar Alterações</button>
                <a href="funcionario.php" class="btn cancel-btn">Cancelar</a>
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
