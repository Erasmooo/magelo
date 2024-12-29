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

// Consulta para obter todos os clientes
$sql_clientes = "SELECT * FROM clientes";
$stmt_clientes = $pdo->query($sql_clientes);
$clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);

// Se o formulário for enviado, processa os dados do pedido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quantidade_cubo = !empty($_POST['quantidade_cubo']) ? $_POST['quantidade_cubo'] : 0;
    $preco_cubo = !empty($_POST['preco_cubo']) ? $_POST['preco_cubo'] : 0;
    $quantidade_barra = !empty($_POST['quantidade_barra']) ? $_POST['quantidade_barra'] : 0;
    $preco_barra = !empty($_POST['preco_barra']) ? $_POST['preco_barra'] : 0;
    $clienteSelecionado = $_POST['nome_cliente'];
    $funcionario_nome = $_SESSION['funcionario_nome'];
    $funcionario_id = $_SESSION['funcionario_id'];

    // Inicializa variáveis de cliente
    $nome_cliente = "";
    if ($clienteSelecionado === 'anonimo') {
        $nome_cliente = "Cliente Anônimo";
    } else {
        foreach ($clientes as $cliente) {
            if ($cliente['id'] == $clienteSelecionado) {
                $nome_cliente = $cliente['nome']; // Captura o nome do cliente selecionado
                break;
            }
        }
    }

    // Calcula o total de venda
    $total_cubo = $quantidade_cubo * $preco_cubo;
    $total_barra = $quantidade_barra * $preco_barra;
    $total_venda = $total_cubo + $total_barra;

    // Insere os dados na tabela de vendas
    $sql_venda = "INSERT INTO vendas 
                  (funcionario_id, funcionario_nome, nome_cliente, quantidade_cubo, preco_unitario_cubo, quantidade_barra, preco_unitario_barra, total_venda) 
                  VALUES (:funcionario_id, :funcionario_nome, :nome_cliente, :quantidade_cubo, :preco_unitario_cubo, :quantidade_barra, :preco_unitario_barra, :total_venda)";
    
    $stmt_venda = $pdo->prepare($sql_venda);
    $stmt_venda->bindParam(':funcionario_id', $funcionario_id);
    $stmt_venda->bindParam(':funcionario_nome', $funcionario_nome);
    $stmt_venda->bindParam(':nome_cliente', $nome_cliente);
    $stmt_venda->bindParam(':quantidade_cubo', $quantidade_cubo);
    $stmt_venda->bindParam(':preco_unitario_cubo', $preco_cubo);
    $stmt_venda->bindParam(':quantidade_barra', $quantidade_barra);
    $stmt_venda->bindParam(':preco_unitario_barra', $preco_barra);
    $stmt_venda->bindParam(':total_venda', $total_venda);
    $stmt_venda->execute();

    // Redireciona para a página de confirmação com uma mensagem de sucesso
    header("Location: pedido.php?success=Venda registrada com sucesso!");
    exit;
}
?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/logo-magelo.PNG" type="">
    <title>Adicionar Pedido - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="css/funcionario.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .main-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 500px;
            text-align: center;
            margin-top: 80px; /* Adiciona uma margem superior para afastar do navbar */
        }
        h1 {
            color: #1e90ff;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .form-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-weight: bold;
            text-align: left;
        }
        input, select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
        }
        .btn {
            background-color: #1e90ff;
            color: #fff;
            padding: 10px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
        .cancel-btn {
            background-color: #ccc;
            color: #333;
            text-decoration: none;
            padding: 10px;
            font-size: 18px;
            border-radius: 5px;
            display: inline-block;
            width: 100%;
            text-align: center;
        }
        .input-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .input-group input {
            flex: 1;
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
        }
        .admin-footer {
            background-color: #ffffff;
            padding: 20px 0;
            text-align: center;
            color: #333;
            font-size: 0.9em;
            box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.1);
            border-top: 1px solid #e0e0e0;
            width: 100%;
            margin-top: auto;
        }
    </style>
</head>
<body>

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
        <h1>Venda</h1>

        <?php if (isset($error)): ?>
            <p class="error-msg" style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <!-- Formulário de Adicionar Pedido -->
        <div class="form-container">
            <form action="processar_vendas.php" method="POST">
                <label for="cliente">Selecionar Cliente:</label>
                <select id="nome_cliente" name="nome_cliente" required>
                    <option value="" disabled selected>Selecione um cliente</option>
                    <option value="anonimo">Novo Cliente</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?php echo $cliente['id']; ?>">
                            <?php echo $cliente['nome']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="input-group">
                    <label for="quantidade_cubo">Quantidade de Gelo em Cubo:</label>
                    <input type="number" id="quantidade_cubo" name="quantidade_cubo" placeholder="Digite a quantidade">
                    <input type="number" id="preco_cubo" name="preco_cubo" placeholder="Preço unitário (Cubo)">
                </div>

                <div class="input-group">
                    <label for="quantidade_barra">Quantidade de Gelo em Barra:</label>
                    <input type="number" id="quantidade_barra" name="quantidade_barra" placeholder="Digite a quantidade">
                    <input type="number" id="preco_barra" name="preco_barra" placeholder="Preço unitário (Barra)">
                </div>

                <button type="submit" class="btn">Vender</button>
                <a href="pedido.php" class="cancel-btn">Cancelar</a>
            </form>
        </div>
    </div>

    <!-- Rodapé -->
    <footer class="admin-footer">
        &copy; <?php echo date("Y"); ?> Magelo Fábrica de Gelo. Todos os direitos reservados.
    </footer>

</body>
</html>
