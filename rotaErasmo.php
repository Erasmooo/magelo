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
    <title>Gestão de Rotas - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="rota.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- <style>
        /* Estilos gerais */



        button, input[type="submit"] {
            background-color: #1e90ff;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        button:hover, input[type="submit"]:hover {
            background-color: #0b68c1;
        }

        a {
            color: #1e90ff;
            text-decoration: none;
        }

        a:hover {
            color: #0b68c1;
        }




        /* Tabela de Rotas */
        .route-list-container {
            margin-bottom: 40px;
        }

        .route-list-container table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 16px;
        }

        .route-list-container th, .route-list-container td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .route-list-container th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .route-list-container td .btn-edit, .route-list-container td .btn-delete {
            font-size: 14px;
            padding: 8px 12px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
        }

        .route-list-container td .btn-edit {
            background-color: #1e90ff;
        }

        .route-list-container td .btn-edit:hover {
            background-color: #0b68c1;
        }

        .route-list-container td .btn-delete {
            background-color: #f44336;
        }

        .route-list-container td .btn-delete:hover {
            background-color: #c0392b;
        }

        /* Rodapé */
        .admin-footer {
            background-color: #ffffff;
            color: #333;
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 10px 0;
            flex-wrap: wrap;
            width: 100%;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-top: 1px solid #e0e0e0;
            margin-top: auto;
        }

        .footer-logo img {
            width: 100px;
        }

        .admin-footer .footer-info {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9em;
        }

        .footer-rights {
            text-align: center;
            width: 100%;
            margin-top: 5px;
            font-size: 0.8em;
            font-weight: 300;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 20px;
            }

            .employee-table th, .employee-table td {
                padding: 10px;
            }
        }
    </style> -->
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
    <div class="route-list-container">
        <h2>Lista de Rotas</h2>
        <table class="route-table">
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
