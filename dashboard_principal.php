<?php
// Inicia a sessão
session_start();

// Verifica se o funcionário está logado
if (!isset($_SESSION['funcionario_nome'])) {
    header("Location: index.php");
    exit;
}

// Inclui o arquivo de configuração para conexão com o banco de dados
require 'config.php';

// Conecta ao banco de dados
$host = 'localhost';
$dbname = 'magelo';
$user = 'root';
$password = ''; 

$pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);

// Função para obter valores do banco de dados
function getDashboardData($pdo) {
    // Producao Diária de Gelo em Cubo
    $stmt = $pdo->query("SELECT SUM(quantidade_unit) AS producaoCuboDiaria FROM relatorio_estoque WHERE tipo_produto = 'Gelo em Cubo' AND DATE(data_registro) = CURDATE()");
    $producaoCuboDiaria = $stmt->fetchColumn() ?? 0;

    // Producao Diária de Gelo em Barra
    $stmt = $pdo->query("SELECT SUM(quantidade_unit) AS producaoBarraDiaria FROM relatorio_estoque WHERE tipo_produto = 'Gelo em Barra' AND DATE(data_registro) = CURDATE()");
    $producaoBarraDiaria = $stmt->fetchColumn() ?? 0;

    // Quebras Diárias
    $stmt = $pdo->query("SELECT SUM(quantidade) AS quebrasDiarias FROM relatorio_quebras WHERE DATE(data_registro) = CURDATE()");
    $quebrasDiarias = $stmt->fetchColumn() ?? 0;

    // Vendas Diárias
    $stmt = $pdo->query("SELECT SUM(quantidade) AS vendasDiarias FROM vendas WHERE DATE(hora_venda) = CURDATE()");
    $vendasDiarias = $stmt->fetchColumn() ?? 0;

    // Clientes Novos
    $stmt = $pdo->query("SELECT COUNT(*) AS clientesNovos FROM clientes WHERE DATE(data_cadastro) = CURDATE()");
    $clientesNovos = $stmt->fetchColumn() ?? 0;

    // Stock Total de Gelo em Cubo
    $stmt = $pdo->query("SELECT SUM(quantidade_total) AS stockCuboTotal FROM stock WHERE tipo_produto = 'Gelo em Cubo'");
    $stockCuboTotal = $stmt->fetchColumn() ?? 0;

    // Stock Total de Gelo em Barra
    $stmt = $pdo->query("SELECT SUM(quantidade_total) AS stockBarraTotal FROM stock WHERE tipo_produto = 'Gelo em Barra'");
    $stockBarraTotal = $stmt->fetchColumn() ?? 0;

    // Stock do Caminhão
    $stmt = $pdo->query("SELECT SUM(quantidade_total) AS stockCaminhao FROM stock_carro");
    $stockCaminhao = $stmt->fetchColumn() ?? 0;

    // Producao Mensal (Cubo e Barra)
    $producaoMensal = [];
    $stmt = $pdo->query("SELECT MONTH(data_registro) AS mes, tipo_produto, SUM(quantidade_unit) AS total FROM relatorio_estoque GROUP BY mes, tipo_produto");
    $producaoCuboMensal = array_fill(0, 12, 0);
    $producaoBarraMensal = array_fill(0, 12, 0);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['tipo_produto'] == 'Gelo em Cubo') {
            $producaoCuboMensal[$row['mes'] - 1] = $row['total'];
        } elseif ($row['tipo_produto'] == 'Gelo em Barra') {
            $producaoBarraMensal[$row['mes'] - 1] = $row['total'];
        }
    }

    // Vendas Mensais
    $stmt = $pdo->query("SELECT MONTH(hora_venda) AS mes, SUM(quantidade) AS total FROM vendas GROUP BY mes");
    $vendasMensais = array_fill(0, 12, 0);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $vendasMensais[$row['mes'] - 1] = $row['total'];
    }

    // Quebras Mensais
    $stmt = $pdo->query("SELECT MONTH(data_registro) AS mes, SUM(quantidade) AS total FROM relatorio_quebras GROUP BY mes");
    $quebrasMensais = array_fill(0, 12, 0);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $quebrasMensais[$row['mes'] - 1] = $row['total'];
    }

    return [
        'producaoCuboDiaria' => $producaoCuboDiaria,
        'producaoBarraDiaria' => $producaoBarraDiaria,
        'quebrasDiarias' => $quebrasDiarias,
        'vendasDiarias' => $vendasDiarias,
        'clientesNovos' => $clientesNovos,
        'stockCuboTotal' => $stockCuboTotal,
        'stockBarraTotal' => $stockBarraTotal,
        'stockCaminhao' => $stockCaminhao,
        'producaoCuboMensal' => $producaoCuboMensal,
        'producaoBarraMensal' => $producaoBarraMensal,
        'vendasMensais' => $vendasMensais,
        'quebrasMensais' => $quebrasMensais,
    ];
}

// Obtém os dados do dashboard
$dashboardData = getDashboardData($pdo);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="dash_principal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

<div class="dashboard-container">
    <h1> </h1>
    <h1> </h1>
    <h1> <center>DashBoard</center></h1>
    <div class="cards">
        <!-- Produção Diária e Estoque -->
        <div class="card"><h3>Produção Diária (Cubo)</h3><div class="stat"><?php echo $dashboardData['producaoCuboDiaria']; ?></div></div>
        <div class="card"><h3>Produção Diária (Barra)</h3><div class="stat"><?php echo $dashboardData['producaoBarraDiaria']; ?></div></div>
        <div class="card"><h3>Quebras Diárias</h3><div class="stat"><?php echo $dashboardData['quebrasDiarias']; ?></div></div>
        <div class="card"><h3>Vendas Diárias</h3><div class="stat"><?php echo $dashboardData['vendasDiarias']; ?></div></div>
        <div class="card"><h3>Clientes Novos</h3><div class="stat"><?php echo $dashboardData['clientesNovos']; ?></div></div>
        <div class="card"><h3>Stock do Caminhão</h3><div class="stat"><?php echo $dashboardData['stockCaminhao']; ?></div></div>
        <div class="card"><h3>Stock Total (Cubo)</h3><div class="stat"><?php echo $dashboardData['stockCuboTotal']; ?></div></div>
        <div class="card"><h3>Stock Total (Barra)</h3><div class="stat"><?php echo $dashboardData['stockBarraTotal']; ?></div></div>
    </div>

    <!-- Gráficos -->
    <div class="graphs">
        <div class="graph">
            <h3>Produção Mensal (Cubo e Barra)</h3>
            <canvas id="productionChart"></canvas>
        </div>
        <div class="graph">
            <h3>Vendas Mensais</h3>
            <canvas id="salesChart"></canvas>
        </div>
        <div class="graph">
            <h3>Quebras Mensais</h3>
            <canvas id="breakagesChart"></canvas>
        </div>
    </div>
</div>

<script>
    // Dados para os gráficos
    const meses = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const producaoCuboMensal = <?php echo json_encode($dashboardData['producaoCuboMensal']); ?>;
    const producaoBarraMensal = <?php echo json_encode($dashboardData['producaoBarraMensal']); ?>;
    const vendasMensais = <?php echo json_encode($dashboardData['vendasMensais']); ?>;
    const quebrasMensais = <?php echo json_encode($dashboardData['quebrasMensais']); ?>;

    // Gráfico de Produção Mensal
    new Chart(document.getElementById('productionChart'), {
        type: 'bar',
        data: {
            labels: meses,
            datasets: [
                { label: 'Gelo em Cubo', data: producaoCuboMensal, backgroundColor: 'rgba(54, 162, 235, 0.7)' },
                { label: 'Gelo em Barra', data: producaoBarraMensal, backgroundColor: 'rgba(153, 102, 255, 0.7)' }
            ]
        }
    });

    // Gráfico de Vendas Mensais
    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: meses,
            datasets: [{ label: 'Vendas Mensais', data: vendasMensais, borderColor: 'rgba(75, 192, 192, 1)', fill: false }]
        }
    });

    // Gráfico de Quebras Mensais
    new Chart(document.getElementById('breakagesChart'), {
        type: 'line',
        data: {
            labels: meses,
            datasets: [{ label: 'Quebras Mensais', data: quebrasMensais, borderColor: 'rgba(255, 99, 132, 1)', fill: false }]
        }
    });
</script>


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
