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

// Variável para armazenar mensagem de sucesso ou erro
$successMessage = "";
$errorMessage = "";

// Se o formulário for enviado, processa os dados do pedido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quantidade_cubo = !empty($_POST['quantidade_cubo']) ? $_POST['quantidade_cubo'] : 0;
    $preco_cubo = !empty($_POST['preco_cubo']) ? $_POST['preco_cubo'] : 0;
    $quantidade_barra = !empty($_POST['quantidade_barra']) ? $_POST['quantidade_barra'] : 0;
    $preco_barra = !empty($_POST['preco_barra']) ? $_POST['preco_barra'] : 0;
    $clienteSelecionado = $_POST['nome_cliente'];
    $funcionario_nome = $_SESSION['funcionario_nome'];
    $funcionario_id = $_SESSION['funcionario_id'];

    // Verifica o estoque do carro
    $sql_estoque_carro = "SELECT tipo_produto, quantidade_total FROM stock_carro";
    $stmt_estoque_carro = $pdo->query($sql_estoque_carro);
    $estoque_carro = $stmt_estoque_carro->fetchAll(PDO::FETCH_ASSOC);

    // Armazena o estoque atual do carro em variáveis para cubo e barra
    $estoque_cubo_carro = 0;
    $estoque_barra_carro = 0;
    foreach ($estoque_carro as $item) {
        if ($item['tipo_produto'] == 'Gelo em Cubo') {
            $estoque_cubo_carro = $item['quantidade_total'];
        } elseif ($item['tipo_produto'] == 'Gelo em Barra') {
            $estoque_barra_carro = $item['quantidade_total'];
        }
    }

    // Verifica se há estoque suficiente no carro
    if ($quantidade_cubo > $estoque_cubo_carro || $quantidade_barra > $estoque_barra_carro) {
        $errorMessage = "Estoque insuficiente no carro para atender à venda.";
    } else {
        // Inicializa variáveis de cliente
        $nome_cliente = ($clienteSelecionado === 'anonimo') ? "Cliente Anônimo" : "";
        foreach ($clientes as $cliente) {
            if ($cliente['id'] == $clienteSelecionado) {
                $nome_cliente = $cliente['nome'];
                break;
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

        // Atualiza o estoque do carro
        $sql_update_cubo = "UPDATE stock_carro SET quantidade_total = quantidade_total - :quantidade WHERE tipo_produto = 'Gelo em Cubo'";
        $sql_update_barra = "UPDATE stock_carro SET quantidade_total = quantidade_total - :quantidade WHERE tipo_produto = 'Gelo em Barra'";

        if ($quantidade_cubo > 0) {
            $stmt_update_cubo = $pdo->prepare($sql_update_cubo);
            $stmt_update_cubo->bindParam(':quantidade', $quantidade_cubo);
            $stmt_update_cubo->execute();
        }

        if ($quantidade_barra > 0) {
            $stmt_update_barra = $pdo->prepare($sql_update_barra);
            $stmt_update_barra->bindParam(':quantidade', $quantidade_barra);
            $stmt_update_barra->execute();
        }

        // Define a mensagem de sucesso
        $successMessage = "Venda registrada com sucesso!";
    }
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
    /* Estilos principais e modais */
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
        margin-top: 80px;
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
        margin-bottom: 10px;
    }
    .input-group {
        display: flex;
        gap: 10px;
    }
    .input-group input {
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

    .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            width: 300px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        .close-btn {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #1e90ff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
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
</style></head>
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
        <h1>Venda</h1>

        <!-- Modal para mostrar erro de estoque -->
        <?php if (!empty($errorMessage)): ?>
            <div id="errorModal" class="modal" style="display: flex;">
                <div class="modal-content">
                    <p><?php echo $errorMessage; ?></p>
                    <button class="close-btn" onclick="closeModal()">Fechar</button>
                </div>
            </div>
        <?php endif; ?>

        <!-- Modal para mostrar sucesso de venda -->
        <?php if (!empty($successMessage)): ?>
            <div id="successModal" class="modal" style="display: flex;">
                <div class="modal-content">
                    <p><?php echo $successMessage; ?></p>
                    <button class="close-btn" onclick="closeModal()">Fechar</button>
                </div>
            </div>
        <?php endif; ?>

 <!-- Formulário de Adicionar Pedido -->
        <div class="form-container">
            <form action="" method="POST">
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
                    <label for="quantidade_cubo">Cubo:</label>
                    <input type="number" id="quantidade_cubo" name="quantidade_cubo" placeholder="Quantidade">
                    <input type="number" id="preco_cubo" name="preco_cubo" placeholder="Preço">
                </div>

                <div class="input-group">
                    <label for="quantidade_barra">Barra:</label>
                    <input type="number" id="quantidade_barra" name="quantidade_barra" placeholder="Quantidade">
                    <input type="number" id="preco_barra" name="preco_barra" placeholder="Preço">
                </div>

                <button type="submit" class="btn">Vender</button>
                <a href="admin_dashboard.php" class="cancel-btn">Cancelar</a>
            </form>
        </div>
    </div>

    <script>
        function closeModal() {
            document.querySelectorAll('.modal').forEach(modal => {
                modal.style.display = 'none';
            });
        }
    </script>

    <!-- Rodapé -->
    <footer class="admin-footer">
        <p>&copy; <?php echo date("Y"); ?> Magelo Fábrica de Gelo. Todos os direitos reservados.</p>
    </footer>

</body>
</html>
