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

// Consulta para listar todos os pedidos pendentes
$sql_pedidos = "SELECT * FROM pedidos WHERE status = 'pendente'";
$stmt_pedidos = $pdo->query($sql_pedidos);
$pedidos = $stmt_pedidos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Pedidos - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="css/pedido.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f0f2f5;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        min-height: 100vh; /* Garante que o conteúdo ocupe toda a tela */
        color: #333;
        }

        .admin-header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 30px;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            height: 65px;
        }

        .admin-header .logo img {
            width: 120px;
        }

        .admin-header .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .admin-header .user-info i {
            font-size: 20px;
        }

        .main-container {
            margin-top: 100px;
            width: 100%;
            max-width: 1200px;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            flex-grow: 1; /* Permite que o conteúdo cresça */
        }

        h1 {
            text-align: center;
            color: #1e90ff;
            margin-bottom: 30px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #1e90ff;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0b68c1;
        }

        .employee-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .employee-table th, .employee-table td {
            padding: 15px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        /* Estilo para os botões de ações */
        .table-actions {
            display: flex;
            justify-content: center;
            gap: 10px; /* Espaçamento entre os botões */
        }

        .table-actions .btn {
            display: inline-block;
            padding: 8px 12px;
            background-color: #1e90ff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
            width: auto;
            height: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .table-actions .btn i {
            margin-right: 5px;
        }

        /* Cores diferenciadas para os botões de ação */
        .btn-finish {
            background-color: #28a745;
        }

        .btn-delete {
            background-color: #dc3545;
        }

        .btn-edit {
            background-color: #ffc107;
        }

        /* Ajustes no hover dos botões */
        .btn-finish:hover {
            background-color: #218838;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .btn-edit:hover {
            background-color: #e0a800;
        }

        .btn.add-btn {
            float: right;
            margin-bottom: 15px;
            display: inline-block;
        }

        .btn.save-btn {
            margin-top: 20px;
        }

        .btn.cancel-btn {
            background-color: #f44336;
        }

        .btn.cancel-btn:hover {
            background-color: #c0392b;
        }

        .form-container {
            display: none;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        .form-container input, .form-container select {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-container h2 {
            text-align: center;
            color: #1e90ff;
            margin-bottom: 20px;
        }

        .btn-voltar {
            display: block;
            margin: 20px auto;
            background-color: #0b68c1;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            text-align: center;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .main-container {
                padding: 20px;
            }

            .employee-table th, .employee-table td {
                padding: 10px;
            }
        }
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

    <!-- Container Principal -->
    <div class="main-container">
        <h1>Gestão de Pedidos</h1>

        <!-- Botão Adicionar Pedido -->
        <button class="btn add-btn" onclick="window.location.href='adicionar_pedido.php'">
            <i class="fas fa-plus"></i> Adicionar Pedido
        </button>

        <!-- Tabela de Pedidos -->
        <table class="employee-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Contacto</th>
                    <th>Quantidade</th>
                    <th>Tipo de Gelo</th>
                    <th>Endereço</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
    <?php if (!empty($pedidos)): ?>
        <?php foreach ($pedidos as $pedido): ?>
            <tr>
                <td><?php echo $pedido['id']; ?></td>
                <td><?php echo $pedido['nome_cliente']; ?></td>
                <td><?php echo $pedido['contato']; ?></td>
                <td><?php echo $pedido['quantidade']; ?></td>
                <td><?php echo $pedido['tipo_produto']; ?></td>
                <td><?php echo $pedido['endereco_entrega']; ?></td>
                <td><?php echo $pedido['status']; ?></td>
                <td class="table-actions">
    <a href="editar_pedido.php?id=<?php echo $pedido['id']; ?>" class="btn btn-edit">
        <i class="fas fa-edit"></i>
    </a>
    <a href="apagar_pedido.php?id=<?php echo $pedido['id']; ?>" class="btn btn-delete">
        <i class="fas fa-trash"></i>
    </a>
    <a href="finalizar_pedido.php?id=<?php echo $pedido['id']; ?>&quantidade=<?php echo $pedido['quantidade']; ?>&tipo_produto=<?php echo $pedido['tipo_produto']; ?>" class="btn btn-finish">
        <i class="fas fa-check"></i> Finalizar
    </a>
</td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="8">Nenhum pedido encontrado.</td>
        </tr>
    <?php endif; ?>
            </tbody>
        </table>
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
            <span>magelo.moz@gmail.com.com</span>
        </div>
        <div class="footer-info">
            <i class="fas fa-phone"></i>
            <span>+258 82 306 1764</span>
        </div>
        <div class="footer-rights">
            &copy; <?php echo date("Y"); ?> Magelo Fábrica de Gelo. Todos os direitos reservados.
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
</body>
</html>
