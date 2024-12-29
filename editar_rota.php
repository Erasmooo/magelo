<?php
// Inicia a sessão
session_start();

// Verifica se o funcionário está logado
if (!isset($_SESSION['funcionario_nome'])) {
    header("Location: index.php"); // Redireciona para o login se não estiver logado
    exit;
}

// Inclui a configuração de conexão ao banco de dados
require 'config.php';

// Verifica se o ID da rota foi enviado
if (!isset($_GET['id'])) {
    header("Location: gestao_rotas.php"); // Redireciona se o ID não estiver definido
    exit;
}

// Obtém o ID da rota
$rotaId = $_GET['id'];

// Consulta para obter os dados da rota com o ID especificado
$sql = "SELECT * FROM rotas WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $rotaId, PDO::PARAM_INT);
$stmt->execute();
$rota = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se a rota existe
if (!$rota) {
    header("Location: rotaErasmo.php?mensagem=naoencontrado");
    exit;
}

// Atualiza os dados da rota ao enviar o formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomeRota = $_POST['nome_rota'];
    $descricao = $_POST['descricao'];

    // Consulta para atualizar a rota
    $sqlUpdate = "UPDATE rotas SET nome_rota = :nome_rota, descricao = :descricao WHERE id = :id";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->bindParam(':nome_rota', $nomeRota);
    $stmtUpdate->bindParam(':descricao', $descricao);
    $stmtUpdate->bindParam(':id', $rotaId, PDO::PARAM_INT);

    if ($stmtUpdate->execute()) {
        header("Location: rotaErasmo.php?mensagem=editado");
        exit;
    } else {
        echo "Erro ao atualizar a rota.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/logo-magelo.PNG" type="">
    <title>Editar Rota - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background-color: #f0f2f5;
        color: #333;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    /* Cabeçalho */
    .admin-header {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        background-color: #ffffff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
        height: 60px;
    }

    .logo img {
        width: 100px;
    }

    .user-info {
        font-size: 14px;
        color: #333;
        display: flex;
        align-items: center;
        cursor: pointer;
        gap: 8px;
    }

    .user-info i {
        font-size: 16px;
    }

    .user-info .arrow {
        transition: transform 0.3s ease;
    }

    .user-info .arrow.rotate {
        transform: rotate(180deg);
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        top: 60px;
        right: 20px;
        background-color: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        list-style: none;
        padding: 10px;
        border-radius: 8px;
        width: 150px;
        border: 1px solid #e0e0e0;
    }

    .dropdown-menu li {
        padding: 10px;
        display: flex;
        align-items: center;
    }

    .dropdown-menu li a {
        text-decoration: none;
        color: #333;
        font-size: 14px;
        transition: color 0.3s;
        display: flex;
        align-items: center;
    }

    .dropdown-menu li a i {
        margin-right: 10px;
    }

    .dropdown-menu li a:hover {
        color: #1e90ff;
    }

    .dropdown-menu.show {
        display: block;
    }

    /* Container Principal */
    .main-container {
        max-width: 500px;
        width: 90%;
        margin: 100px auto;
        background-color: #ffffff;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    h1 {
        color: #1e90ff;
        font-size: 1.5em;
        text-align: center;
        font-weight: bold;
        margin-bottom: 20px;
    }

    /* Formulário */
    .form-container form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .form-container .form-group label {
        font-weight: bold;
        color: #333;
        display: block; /* Certifica-se de que o label está acima do input */
        margin-bottom: 5px;
    }

    .form-container .form-group input[type="text"] {
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 5px;
        width: 100%; /* Faz o input ocupar toda a largura do contêiner */
    }

    .btn {
        background-color: #1e90ff;
        color: white;
        border: none;
        padding: 10px;
        cursor: pointer;
        border-radius: 5px;
        font-size: 16px;
        transition: background-color 0.3s ease;
        text-align: center;
    }

    .btn:hover {
        background-color: #0b68c1;
    }

    .cancel-btn {
        background-color: #1e90ff;
        color: white;
        text-decoration: none;
        display: inline-block;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
    }

    .cancel-btn:hover {
        background-color: #c9302c;
    }

    /* Rodapé */
    .admin-footer {
        background-color: #ffffff;
        color: #333;
        text-align: center;
        padding: 15px 0;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        margin-top: auto;
        font-size: 0.9em;
    }
</style>

<!-- Cabeçalho -->
<div class="admin-header">
    <div class="logo">
        <a href="admin_dashboard.php">
            <img src="img/logo-magelo.PNG" alt="Logo Magelo Fábrica de Gelo">
        </a>
    </div>
    <div class="user-info">
        <i class="fas fa-user"></i>
        <span id="user-name"><?php echo htmlspecialchars($_SESSION['funcionario_nome']); ?></span>
        <i class="fas fa-chevron-down arrow"></i>
        <ul class="dropdown-menu">
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
</div>

<!-- Conteúdo Principal -->
<div class="main-container">
    <h1>Editar Rota</h1>

    <div class="form-container">
        <form action="" method="POST">
            <div class="form-group">
                <label for="nomeRota">Nome da Rota:</label>
                <input type="text" id="nomeRota" name="nome_rota" placeholder="Nome da Rota" value="<?php echo htmlspecialchars($rota['nome_rota']); ?>" required>
            </div>
            <div class="form-group">
                <label for="descricaoRota">Descrição da Rota:</label>
                <input type="text" id="descricaoRota" name="descricao" placeholder="Descrição da Rota (Opcional)" value="<?php echo htmlspecialchars($rota['descricao']); ?>">
            </div>
            <button type="submit" class="btn">Salvar Alterações</button>
            <a href="rotaErasmo.php" class="cancel-btn">Cancelar</a>
        </form>
    </div>
</div>

<!-- Rodapé -->
<footer class="admin-footer">
    <p>&copy; 2024 Magelo Fábrica de Gelo. Todos os direitos reservados.</p>
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
