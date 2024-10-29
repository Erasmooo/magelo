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
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registro de Quebras - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="stock.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />


</head>
<body>
    <!-- Navbar (Cabeçalho) -->
    <div class="admin-header">
        <div class="logo">
            <img src="img/logo-magelo.PNG" alt="Logo Magelo Fábrica de Gelo" />
        </div>
        <div class="user-info">
            <i class="fas fa-user"></i>
            <span id="user-name"><?php echo $nomeFuncionario; ?></span>
            <i class="fas fa-chevron-down arrow"></i>
            <ul class="dropdown-menu">
                <li>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Conteúdo Principal -->
    <div class="main-container">
        <div class="form-container">
            <h2>Registro de Quebras</h2>
            <form action="processar_quebra.php" method="POST">
                <!-- Tipo de Gelo -->
                <div class="form-group">
                    <label for="tipoGelo">Tipo de Gelo</label>
                    <select name="tipoGelo" id="tipoGelo" required>
                        <option value="" disabled selected>Selecione o tipo de gelo</option>
                        <option value="cubos">Cubos</option>
                        <option value="barras">Barras</option>
                    </select>
                </div>

                <!-- Tipo de Quebra -->
                <div class="form-group">
                    <label for="tipoQuebra">Tipo de Quebra</label>
                    <select name="tipoQuebra" id="tipoQuebra" required>
                        <option value="" disabled selected>Selecione o tipo de quebra</option>
                        <option value="gelo">Quebra de Gelo</option>
                        <option value="plastico">Quebra de Plástico</option>
                    </select>
                </div>

                <!-- Quantidade de Quebra -->
                <div class="form-group">
                    <label for="quantidade">Quantidade de Quebra</label>
                    <input type="number" name="quantidade" id="quantidade" min="1" required>
                </div>

                <!-- Botão de Enviar -->
                <div class="form-actions">
                    <button type="submit" class="btn">Registrar Quebra</button>
                </div>
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
        <div class="footer-rights">
            <p>&copy; 2024 Magelo Fábrica de Gelo. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
        // Mostra/oculta o campo "Tipo de Gelo" conforme a seleção do "Tipo de Quebra"
        const tipoQuebra = document.getElementById('tipoQuebra');
        const tipoGeloGroup = document.getElementById('tipoGeloGroup');

        tipoQuebra.addEventListener('change', function() {
            if (this.value === 'gelo') {
                tipoGeloGroup.style.display = 'block'; // Mostra o campo "Tipo de Gelo"
                document.getElementById('tipoGelo').setAttribute('required', 'required'); // Torna o campo obrigatório
            } else {
                tipoGeloGroup.style.display = 'none'; // Oculta o campo "Tipo de Gelo"
                document.getElementById('tipoGelo').removeAttribute('required'); // Remove a obrigatoriedade se não for gelo
            }
        });

        // Toggle dropdown visibility and rotate arrow
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
