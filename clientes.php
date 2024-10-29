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
    <title>Gestão de Clientes - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
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

        .client-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .client-table th, .client-table td {
            padding: 15px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        .client-table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .btn.add-btn {
            float: right;
            margin-bottom: 15px;
            display: inline-block;
        }

        .form-container {
            display: none;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
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

        .table-actions {
            display: flex;
            justify-content: center;
            gap: 10px; /* Espaçamento entre os botões */
        }

        .table-actions .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px; /* Reduzindo o padding para ajustar o tamanho */
            width: 35px; /* Definindo largura fixa */
            height: 35px; /* Definindo altura fixa */
            font-size: 14px;
            transition: background-color 0.3s ease;
            background-color: #1e90ff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .table-actions .btn-delete {
            background-color: #f44336; /* Cor do botão de exclusão */
        }

        .table-actions .btn:hover {
            background-color: #0b68c1; /* Cor de hover */
        }

        .table-actions .btn-delete:hover {
            background-color: #c0392b; /* Cor de hover para exclusão */
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .main-container {
                padding: 20px;
            }

            .client-table th, .client-table td {
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
    <h1>Gestão de Clientes</h1>

    <!-- Botão Adicionar Cliente -->
    <button class="btn add-btn" onclick="window.location.href='adicionar_cliente.php'">
            <i class="fas fa-plus"></i> Adicionar Cliente
    </button>

    <!-- Tabela de Clientes -->
    <table class="client-table">
        <thead>
            <tr>
            <th>ID</th>
                <th>Nome</th>
                <th>Endereço</th>
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

    <!-- Formulário de Adicionar Cliente -->
    <div class="form-container" id="clientFormContainer">
        <form id="clientForm" action="processar_cliente.php" method="POST">
            <h2 id="formTitle">Adicionar Cliente</h2>

            <label for="nome_cliente">Nome do Cliente:</label>
            <input type="text" id="nome_cliente" name="nome_cliente" required>

            <label for="contato">Contacto:</label>
            <input type="text" id="contato" name="contato" required>

            <label for="rota">Rota:</label>
            <select id="rota" name="rota" required>
                <!-- Preencha com as rotas disponíveis do banco de dados -->
                <?php
                // Busca as rotas para o dropdown
                $sqlRotas = "SELECT nome_rota FROM rota";
                $stmtRotas = $pdo->prepare($sqlRotas);
                $stmtRotas->execute();
                $rotas = $stmtRotas->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rotas as $rota):
                ?>
                    <option value="<?php echo $rota['nome_rota']; ?>"><?php echo $rota['nome_rota']; ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn save-btn">Salvar</button>
            <button type="button" class="btn cancel-btn" onclick="document.getElementById('clientFormContainer').style.display='none'">Cancelar</button>
        </form>
    </div>
</div>

<!-- Footer -->
<div class="admin-footer">
    <div class="footer-logo">
        <img src="img/logo-magelo.PNG" alt="Logo Magelo Fábrica de Gelo">
    </div>
    <div class="footer-info">
        <i class="fas fa-map-marker-alt"></i>
        <span>Av. Eduardo Mondlane, Maputo, Moçambique</span>
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
    const cancelFormBtn = document.getElementById('cancelFormBtn');
    cancelFormBtn.addEventListener('click', () => {
        document.getElementById('clientFormContainer').style.display = 'none';
    });
    <
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
