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
    <link rel="shortcut icon" href="img/logo-magelo.PNG" type="">
    <title>Gestão de Pedidos - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="css/funcionario.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
            flex-grow: 1;
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

        /* Contêiner com rolagem horizontal para a tabela */
        .table-container {
            width: 100%;
            overflow-x: auto;
            margin-bottom: 30px;
        }

        .employee-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px; /* Define um tamanho mínimo para a tabela */
        }

        .employee-table th, .employee-table td {
            padding: 15px;
            border-bottom: 1px solid #ddd;
            text-align: center;
            white-space: nowrap;
        }

        .employee-table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .table-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .table-actions .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            width: 35px;
            height: 35px;
            font-size: 14px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .table-actions .btn-edit {
            background-color: #1e90ff;
        }

        .table-actions .btn-delete {
            background-color: #f44336;
        }

        .table-actions .btn-finish {
            background-color: #28a745;
        }

        .table-actions .btn:hover {
            background-color: #0b68c1;
        }

        .table-actions .btn-delete:hover {
            background-color: #c0392b;
        }

        .table-actions .btn-finish:hover {
            background-color: #218838;
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

        /* Footer */
        .admin-footer {
            background-color: #ffffff; 
            color: #333; 
            display: flex; 
            justify-content: space-around;
            align-items: center; 
            padding: 20px 0; 
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
            justify-content: center;
            align-items: center;
            text-align: center;
            font-size: 1em;
            gap: 10px;
        }

        .footer-rights {
            text-align: center;
            width: 100%; 
            margin-top: 10px;
            font-size: 1em;
            font-weight: 300;
        }

        .admin-footer i {
            font-size: 1em; 
        }

        /* Ajustes para dispositivos móveis */
        @media (max-width: 768px) {
            .admin-footer {
                flex-direction: column;
                text-align: center;
            }
        }

    </style>
</head>
<body>

    <!-- Cabeçalho -->
    <div class="admin-header">
    <div class="logo">
            <a href="stock_dashboard.php">
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
        <h1>Gestão de Pedidos - Entregador</h1>

        <!-- Botão Adicionar Pedido -->
        <button class="btn add-btn" onclick="window.location.href='adicionar_pedidoEntrega.php'">
            <i class="fas fa-plus"></i> Adicionar Pedido
        </button>

        <!-- Contêiner para rolagem horizontal -->
        <div class="table-container">
            <table class="employee-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Contato</th>
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
                                <td><?php echo $pedido['quantidade']; ?> Unidades</td>
                                <td><?php echo $pedido['tipo_produto']; ?></td>
                                <td><?php echo $pedido['endereco_entrega']; ?></td>
                                <td><?php echo $pedido['status']; ?></td>
                                <td class="table-actions">
                                    <a href="editar_pedidoEntrega.php?id=<?php echo $pedido['id']; ?>" class="btn btn-edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="apagar_pedidoEntrega.php?id=<?php echo $pedido['id']; ?>" class="btn btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <a href="finalizar_pedidoEntrega.php?id=<?php echo $pedido['id']; ?>&quantidade=<?php echo $pedido['quantidade']; ?>&tipo_produto=<?php echo $pedido['tipo_produto']; ?>" class="btn btn-finish">
                                        <i class="fas fa-check"></i>
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
    </div>

    <!-- Rodapé -->
    <footer class="admin-footer">
      <div class="footer-rights">
        <p>&copy; 2024 Magelo Fábrica de Gelo. Todos os direitos reservados.</p>
      </div>
    </footer>
</body>
</html>
