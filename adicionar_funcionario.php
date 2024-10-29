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

// Se o formulário for enviado, adiciona o novo funcionário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['name'];
    $idade = $_POST['age'];
    $genero = $_POST['gender'];
    $username = $_POST['username'];
    $telefone = $_POST['phone'];
    $morada = $_POST['address'];
    $tipoUsuario = $_POST['userType'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Hash da senha

    $sqlInsert = "INSERT INTO funcionarios (nome, idade, genero, username, telefone, morada, tipo_usuario, senha) 
    VALUES (:nome, :idade, :genero, :username, :telefone, :morada, :tipo_usuario, :senha)";
    $stmtInsert = $pdo->prepare($sqlInsert);
    $stmtInsert->bindParam(':nome', $nome);
    $stmtInsert->bindParam(':idade', $idade);
    $stmtInsert->bindParam(':genero', $genero);
    $stmtInsert->bindParam(':username', $username);
    $stmtInsert->bindParam(':telefone', $telefone);
    $stmtInsert->bindParam(':morada', $morada);
    $stmtInsert->bindParam(':tipo_usuario', $tipoUsuario);
    $stmtInsert->bindParam(':senha', $senha);

    if ($stmtInsert->execute()) {
        header("Location: funcionario.php?mensagem=adicionado");
        exit;
    } else {
        echo "Erro ao adicionar funcionário.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Funcionário - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="css/funcionario.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

  <!-- Cabeçalho -->
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

    <div class="main-container">
        <h1>Adicionar Funcionário</h1>

        <!-- Formulário de Adicionar Funcionário -->
        <div class="form-container">
            <form action="" method="POST">
                <h2 id="formTitle">Adicionar Funcionário</h2>

                <label for="name">Nome:</label>
                <input type="text" id="name" name="name" required>

                <label for="age">Idade:</label>
                <input type="number" id="age" name="age" min="0" required>

                <label for="gender">Gênero:</label>
                <select id="gender" name="gender" required>
                    <option value="MASCULINO">Masculino</option>
                    <option value="FEMININO">Feminino</option>
                </select>

                <label for="email">Username:</label>
                <input type="username" id="username" name="username" required>

                <label for="phone">Telefone:</label>
                <input type="text" id="phone" name="phone" required>

                <label for="address">Morada:</label>
                <input type="text" id="address" name="address" required>

                <label for="userType">Tipo de Usuário:</label>
                <select id="userType" name="userType" required>
                    <option value="ADMIN">ADMIN</option>
                    <option value="NORMAL">Funcionário</option>
                </select>

                <label for="senha">Senha:</label>
                <input type="text" id="senha" name="senha" required>

                <button type="submit" class="btn save-btn">Salvar</button>
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
    <!-- Rodapé -->
    <div class="admin-footer">
        <div class="footer-logo">
            <img src="img/logo-magelo.PNG" alt="Logo Magelo Fábrica de Gelo">
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
            &copy; <?php echo date("Y"); ?> Magelo Fábrica de Gelo. Todos os direitos reservados.
        </div>
    </div>
</body>
</html>
