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
    $quantidade_cubo = $_POST['quantidade_cubo'];
    $quantidade_barra = $_POST['quantidade_barra'];
    $clienteSelecionado = $_POST['cliente'];

    // Inicializa variáveis de cliente
    $nomeCliente = "";
    $contato = "";
    $enderecoEntrega = "";

    // Verifica se um cliente anônimo ou um cliente registrado foi selecionado
    if ($clienteSelecionado === 'anonimo') {
        $nomeCliente = "Cliente Anônimo";
        $contato = "";  // Não precisa de contato para cliente anônimo
        $enderecoEntrega = "";  // Não precisa de endereço para cliente anônimo
    } else {
        // Encontra o cliente selecionado
        foreach ($clientes as $cliente) {
            if ($cliente['id'] == $clienteSelecionado) {
                $nomeCliente = $cliente['nome'];
                $contato = $cliente['telefone'];
                $enderecoEntrega = $cliente['endereco']; // Preenche o endereço do cliente
                break;
            }
        }
    }

    // Verifica se as quantidades foram preenchidas e se ao menos um pedido é válido
    if ((!empty($quantidade_cubo) || !empty($quantidade_barra)) && 
        (!empty($enderecoEntrega) || $clienteSelecionado === 'anonimo')) {
        
        // Insere o pedido no banco de dados (separado por tipo de gelo)
        if (!empty($quantidade_cubo)) {
            $sql_cubo = "INSERT INTO pedidos (nome_cliente, contato, quantidade, tipo_produto, endereco_entrega, status) 
                         VALUES (:nome_cliente, :contato, :quantidade, 'Gelo em Cubo', :endereco_entrega, 'pendente')";
            $stmt_cubo = $pdo->prepare($sql_cubo);
            $stmt_cubo->bindParam(':nome_cliente', $nomeCliente);
            $stmt_cubo->bindParam(':contato', $contato);
            $stmt_cubo->bindParam(':quantidade', $quantidade_cubo);
            $stmt_cubo->bindParam(':endereco_entrega', $enderecoEntrega);
            $stmt_cubo->execute();
        }

        if (!empty($quantidade_barra)) {
            $sql_barra = "INSERT INTO pedidos (nome_cliente, contato, quantidade, tipo_produto, endereco_entrega, status) 
                          VALUES (:nome_cliente, :contato, :quantidade, 'Gelo em Barra', :endereco_entrega, 'pendente')";
            $stmt_barra = $pdo->prepare($sql_barra);
            $stmt_barra->bindParam(':nome_cliente', $nomeCliente);
            $stmt_barra->bindParam(':contato', $contato);
            $stmt_barra->bindParam(':quantidade', $quantidade_barra);
            $stmt_barra->bindParam(':endereco_entrega', $enderecoEntrega);
            $stmt_barra->execute();
        }

        header("Location: pedido.php?success=Pedido adicionado com sucesso!");
        exit;
    } else {
        $error = "Por favor, preencha todos os campos necessários.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Pedido - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="css/funcionario.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        function preencherDados(cliente) {
            const clienteAnonimo = cliente.value === "anonimo";

            document.getElementById('nome_cliente').value = clienteAnonimo ? "Cliente Anônimo" : cliente.options[cliente.selectedIndex].dataset.nome;
            document.getElementById('contato').value = clienteAnonimo ? "" : cliente.options[cliente.selectedIndex].dataset.contato;
            document.getElementById('endereco_entrega').value = clienteAnonimo ? "" : cliente.options[cliente.selectedIndex].dataset.endereco;

            // Habilitar ou desabilitar campos para cliente anônimo
            document.getElementById('nome_cliente').disabled = clienteAnonimo;
            document.getElementById('contato').disabled = clienteAnonimo;
            document.getElementById('endereco_entrega').disabled = clienteAnonimo;
        }


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
        <h1>Adicionar Pedido</h1>

        <!-- Exibe mensagens de erro, se houver -->
        <?php if (isset($error)): ?>
            <p class="error-msg"><?php echo $error; ?></p>
        <?php endif; ?>

        <!-- Formulário de Adicionar Pedido -->
        <div class="form-container">
            <form action="" method="POST">
                <h2>Informações do Pedido</h2>

                <label for="cliente">Selecionar Cliente:</label>
                <select id="cliente" name="cliente" onchange="preencherDados(this)" required>
                    <option value="" disabled selected>Selecione um cliente</option>
                    <option value="anonimo">Novo Cliente</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?php echo $cliente['id']; ?>" 
                                data-nome="<?php echo $cliente['nome']; ?>" 
                                data-contato="<?php echo $cliente['telefone']; ?>" 
                                data-endereco="<?php echo $cliente['endereco']; ?>">
                            <?php echo $cliente['nome']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="nome_cliente">Nome do Cliente:</label>
                <input type="text" id="nome_cliente" name="nome_cliente" required>

                <label for="contato">Contacto:</label>
                <input type="text" id="contato" name="contato" required>

                <label for="quantidade_cubo">Quantidade de Gelo em Cubo:</label>
                <input type="number" id="quantidade_cubo" name="quantidade_cubo">

                <label for="quantidade_barra">Quantidade de Gelo em Barra:</label>
                <input type="number" id="quantidade_barra" name="quantidade_barra">

                <label for="endereco_entrega">Endereço de Entrega:</label>
                <input type="text" id="endereco_entrega" name="endereco_entrega" required>

                <button type="submit" class="btn">Adicionar Pedido</button>
                <a href="pedido.php" class="btn cancel-btn">Cancelar</a>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="admin-footer">
        <div class="footer-container">
            <div class="footer-logo">
                <img src="img/logo-magelo.PNG" alt="Logo Magelo Fábrica de Gelo" />
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
        </div>
        <div class="footer-rights">
            &copy; <?php echo date("Y"); ?> Magelo Fábrica de Gelo. Todos os direitos reservados.
        </div>
    </footer>

    <style>
        .admin-footer {
            background-color: #fff;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.1);
            border-top: 1px solid #e0e0e0;
            width: 100%;
        }

        .footer-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 80%;
            max-width: 1200px;
        }

        .footer-logo img {
            width: 150px;
        }

        .footer-info {
            display: flex;
            align-items: center;
            font-size: 1em;
            color: #333;
        }

        .footer-info i {
            margin-right: 10px;
            font-size: 1.2em;
        }

        .footer-rights {
            margin-top: 15px;
            font-size: 0.9em;
            color: #333;
            text-align: center;
        }

        @media (max-width: 768px) {
            .footer-container {
                flex-direction: column;
                align-items: center;
            }

            .footer-info {
                margin-bottom: 10px;
            }
        }
    </style>

</body>
</html>
