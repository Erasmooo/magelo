<?php
// Inicia a sessão
session_start();

// Verifica se o funcionário está logado
if (!isset($_SESSION['funcionario_nome'])) {
    header("Location: login.php");
    exit;
}

// Inclui a configuração de conexão ao banco de dados
require 'config.php';

// Verifica se o ID do pedido foi enviado
if (!isset($_GET['id'])) {
    header("Location: pedido.php");
    exit;
}

// Obtém o ID do pedido
$pedidoId = $_GET['id'];

// Consulta para obter os dados do pedido
$sql = "SELECT * FROM pedidos WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $pedidoId);
$stmt->execute();
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se o pedido existe
if (!$pedido) {
    header("Location: pedido.php?mensagem=naoencontrado");
    exit;
}

// Se o formulário for enviado, atualiza os dados do pedido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomeCliente = $_POST['nome_cliente'];
    $contato = $_POST['contato'];
    $quantidade = $_POST['quantidade'];
    $tipoProduto = $_POST['tipo_produto'];
    $enderecoEntrega = $_POST['endereco_entrega'];
    $status = $_POST['status'];

    // Atualiza o pedido
    $sqlUpdate = "UPDATE pedidos SET nome_cliente = :nome_cliente, contato = :contato, quantidade = :quantidade, tipo_produto = :tipo_produto, endereco_entrega = :endereco_entrega, status = :status WHERE id = :id";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->bindParam(':nome_cliente', $nomeCliente);
    $stmtUpdate->bindParam(':contato', $contato);
    $stmtUpdate->bindParam(':quantidade', $quantidade);
    $stmtUpdate->bindParam(':tipo_produto', $tipoProduto);
    $stmtUpdate->bindParam(':endereco_entrega', $enderecoEntrega);
    $stmtUpdate->bindParam(':status', $status);
    $stmtUpdate->bindParam(':id', $pedidoId);

    if ($stmtUpdate->execute()) {
        header("Location: pedido.php?mensagem=editado");
        exit;
    } else {
        echo "Erro ao atualizar pedido.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pedido - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="css/funcionario.css">
    <script>
        function verificarClienteAnonimo() {
            const clienteAnonimo = document.getElementById('nome_cliente').value === "Cliente Anônimo";
            const contatoInput = document.getElementById('contato');
            const enderecoInput = document.getElementById('endereco_entrega');

            if (clienteAnonimo) {
                contatoInput.value = "";
                enderecoInput.value = "";
                contatoInput.disabled = true;
                enderecoInput.disabled = true;
            } else {
                contatoInput.disabled = false;
                enderecoInput.disabled = false;
            }
        }

        // Chama a função ao carregar a página
        window.onload = function() {
            verificarClienteAnonimo();
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

    <div class="main-container">
        <h1>Editar Pedido</h1>

        <!-- Formulário de Edição de Pedido -->
        <div class="form-container" id="pedidoFormContainer">
            <form id="pedidoForm" action="" method="POST">
                <h2 id="formTitle">Editar Pedido</h2>

                <label for="nome_cliente">Nome do Cliente:</label>
                <input type="text" id="nome_cliente" name="nome_cliente" value="<?php echo $pedido['nome_cliente']; ?>" required oninput="verificarClienteAnonimo()">

                <label for="contato">Contato:</label>
                <input type="text" id="contato" name="contato" value="<?php echo $pedido['contato']; ?>" required>

                <label for="quantidade">Quantidade de Gelo (Unidades):</label>
                <input type="number" id="quantidade" name="quantidade" value="<?php echo $pedido['quantidade']; ?>" min="0" required>

                <label for="tipo_produto">Tipo de Gelo:</label>
                <select id="tipo_produto" name="tipo_produto" required>
                    <option value="Gelo em Cubo" <?php echo ($pedido['tipo_produto'] == 'Gelo em Cubo') ? 'selected' : ''; ?>>Gelo em Cubo</option>
                    <option value="Gelo em Barra" <?php echo ($pedido['tipo_produto'] == 'Gelo em Barra') ? 'selected' : ''; ?>>Gelo em Barra</option>
                </select>

                <label for="endereco_entrega">Endereço de Entrega:</label>
                <input type="text" id="endereco_entrega" name="endereco_entrega" value="<?php echo $pedido['endereco_entrega']; ?>" required>

                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="pendente" <?php echo ($pedido['status'] == 'pendente') ? 'selected' : ''; ?>>Pendente</option>
                    <option value="finalizado" <?php echo ($pedido['status'] == 'finalizado') ? 'selected' : ''; ?>>Finalizado</option>
                </select>

                <button type="submit" class="btn save-btn">Salvar Alterações</button>
                <a href="pedido.php" class="btn cancel-btn">Cancelar</a>
            </form>
        </div>
    </div>

    
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
