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
    <title>Editar Rota - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="rota.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        h1 {
            color: #1e90ff;
            text-align: center;
            margin-top: 20px;
        }

        /* Container Principal */
        .main-container {
            max-width: 600px;
            width: 90%;
            margin: 150px auto;
            background-color: #ffffff;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 100px;
            
        }

        /* Formulário */
        .form-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-container .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        .form-container .form-group input[type="text"] {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
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
        }

        .btn:hover {
            background-color: #0b68c1;
        }

        .cancel-btn {
            background-color: #1e90ff;
            margin-top: 10px;
            text-align: center;
            text-decoration: none;
            color: white;
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .cancel-btn:hover {
            background-color: #c0392b;
        }

    </style> 
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

    <!-- Conteúdo Principal -->
    <div class="main-container">
        <h1>Editar Rota</h1>

        <div class="form-container">
            <form action="" method="POST">
                <div class="form-group">
                    <label for="nomeRota">Nome da Rota:</label>
                    <input type="text" id="nomeRota"  placeholder="Nome da Rota" name="nome_rota"  value="<?php echo htmlspecialchars($rota['nome_rota']); ?>" required>
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
            <p>&copy; 2024 Magelo Fábrica de Gelo. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>
