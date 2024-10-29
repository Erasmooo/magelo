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

// Se o formulário for enviado, adiciona o novo cliente
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome_cliente'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $nome_rota = $_POST['rota'];

    $sqlInsert = "INSERT INTO clientes (nome, endereco, telefone, email, nome_rota) 
                  VALUES (:nome, :endereco, :telefone, :email, :nome_rota)";
    $stmtInsert = $pdo->prepare($sqlInsert);
    $stmtInsert->bindParam(':nome', $nome);
    $stmtInsert->bindParam(':endereco', $endereco);
    $stmtInsert->bindParam(':telefone', $telefone);
    $stmtInsert->bindParam(':email', $email);
    $stmtInsert->bindParam(':nome_rota', $nome_rota);

    if ($stmtInsert->execute()) {
        header("Location: clientes.php?mensagem=adicionado");
        exit;
    } else {
        echo "Erro ao adicionar cliente.";
    }
}

// Busca as rotas para o dropdown
$sqlRotas = "SELECT nome_rota FROM rotas";
$stmtRotas = $pdo->prepare($sqlRotas);
$stmtRotas->execute();
$rotas = $stmtRotas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Cliente - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="css/funcionario.css">
</head>
<body>
    <div class="main-container">
        <h1>Adicionar Cliente</h1>

        <!-- Formulário de Adicionar Cliente -->
        <div class="form-container">
            <form action="" method="POST">
                <h2 id="formTitle">Adicionar Cliente</h2>

                <label for="nome_cliente">Nome:</label>
                <input type="text" id="nome_cliente" name="nome_cliente" required>

                <label for="endereco">Endereço:</label>
                <input type="text" id="endereco" name="endereco" required>

                <label for="telefone">Telefone:</label>
                <input type="text" id="telefone" name="telefone" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="rota">Rota:</label>
                <select id="rota" name="rota" required>
                    <?php foreach ($rotas as $rota): ?>
                        <option value="<?php echo $rota['nome_rota']; ?>"><?php echo $rota['nome_rota']; ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn save-btn">Salvar</button>
                <a href="clientes.php" class="btn cancel-btn">Cancelar</a>
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
</body>
</html>
