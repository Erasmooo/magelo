
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
$host = 'sql208.infinityfree.com'; // Servidor de banco de dados
$dbname = 'if0_37633251_magelo';  // Nome da base de dados
$user = 'if0_37633251';      // Usuário do banco de dados
$password = 'Pyros123456'; // Insira a senha correta

$pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);

// Função para obter valores do banco de dados
function getDashboardData($pdo) {
    // Stock Total de Gelo em Cubo e Barra
    $stmt = $pdo->query("SELECT tipo_produto, SUM(quantidade_total) AS quantidade FROM stock GROUP BY tipo_produto");
    $stockTotal = ['Gelo em Cubo' => 0, 'Gelo em Barra' => 0];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['tipo_produto'] == 'Gelo em Cubo') {
            $stockTotal['Gelo em Cubo'] = $row['quantidade'];
        } elseif ($row['tipo_produto'] == 'Gelo em Barra') {
            $stockTotal['Gelo em Barra'] = $row['quantidade'];
        }
    }

    // Stock no Caminhão
    $stmt = $pdo->query("SELECT tipo_produto, SUM(quantidade_total) AS quantidade FROM stock_carro GROUP BY tipo_produto");
    $stockCaminhao = ['Gelo em Cubo' => 0, 'Gelo em Barra' => 0];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['tipo_produto'] == 'Gelo em Cubo') {
            $stockCaminhao['Gelo em Cubo'] = $row['quantidade'];
        } elseif ($row['tipo_produto'] == 'Gelo em Barra') {
            $stockCaminhao['Gelo em Barra'] = $row['quantidade'];
        }
    }

    // Produção no Mês Corrente (Cubos e Barras)
    $stmt = $pdo->query("SELECT tipo_produto, SUM(quantidade_unit) AS producao FROM relatorio_estoque WHERE MONTH(data_registro) = MONTH(CURDATE()) GROUP BY tipo_produto");
    $producaoMes = ['Gelo em Cubo' => 0, 'Gelo em Barra' => 0];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['tipo_produto'] == 'Gelo em Cubo') {
            $producaoMes['Gelo em Cubo'] = $row['producao'];
        } elseif ($row['tipo_produto'] == 'Gelo em Barra') {
            $producaoMes['Gelo em Barra'] = $row['producao'];
        }
    }

    // Quebras no Mês Corrente
    $stmt = $pdo->query("SELECT SUM(quantidade) AS quebrasMes FROM relatorio_quebras WHERE MONTH(data_registro) = MONTH(CURDATE())");
    $quebrasMes = $stmt->fetchColumn() ?? 0;

    // Vendas no Mês Corrente e Receita Gerada
    $stmt = $pdo->query("SELECT COUNT(id) AS vendasMes, SUM(total_venda) AS receitaMes FROM vendas WHERE MONTH(hora_venda) = MONTH(CURDATE())");
    $vendasMesData = $stmt->fetch(PDO::FETCH_ASSOC);
    $vendasMes = $vendasMesData['vendasMes'] ?? 0;
    $receitaMes = $vendasMesData['receitaMes'] ?? 0;

    // Dados para gráficos
    // Produção Mensal
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
    $stmt = $pdo->query("SELECT MONTH(hora_venda) AS mes, COUNT(id) AS total FROM vendas GROUP BY mes");
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
        'stockTotal' => $stockTotal,
        'stockCaminhao' => $stockCaminhao,
        'producaoMes' => $producaoMes,
        'quebrasMes' => $quebrasMes,
        'vendasMes' => $vendasMes,
        'receitaMes' => $receitaMes,
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
    <link rel="shortcut icon" href="img/logo-magelo.PNG" type="">
    <link rel="stylesheet" href="dash_principal.css?v=<?php echo time(); ?>">
    <title>Painel Admin - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
      /* General Reset and Base Styling */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background-color: #f4f4f9;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Cards Container with Uniform Spacing */
.cards {
    align-items: center;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 70px; /* Uniform spacing between cards */
    margin-bottom: 20px;
    width: 90%; /* Full width with some margin */
    max-width: 1200px; /* Limits width on larger screens */
    
}

/* General Card Styling */
.card {
    background-color: #ffffff;
    padding: 30px;
    border-radius: 50px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s ease;
    max-width: 500px;
  
}

.card:hover {
    transform: translateY(-5px);
}

.card h3 {
    font-size: 16px;
    color: #555;
    margin-bottom: 10px;
}

.card .stat {
    font-size: 22px;
    font-weight: bold;
    color: #1e90ff;
}

.card i {
    font-size: 24px;
    color: #1e90ff;
    margin-bottom: 5px;
}

/* Specific Card Types */
.stock-total-card, .stock-caminhao-card, .producao-mes-card, .quebras-card {
    padding: 30px;
    border-radius: 50px;
    max-width: 500px;
}

.stock-total-card h3, .stock-caminhao-card h3, .producao-mes-card h3, .quebras-card h3 {
    font-size: 18px;
    color: #555;
    margin-bottom: 20px;
}

/* Stock Types Section (for Cubo and Barra) */
.stock-types {
    display: flex;
    align-items: center;
    justify-content: space-around;
    width: 100%;
}

.stock-type {
    text-align: center;
    flex: 1;
    min-width: 100px;
    padding: 0 15px;
}

.stock-type h4 {
    font-size: 16px;
    color: #555;
    margin-bottom: 5px;
}

.stock-type .stat {
    font-size: 18px;
    font-weight: bold;
    color: #1e90ff;
}

/* Separator Styling */
.separator {
    width: 1px;
    height: 60px;
    background-color: #ddd;
    margin: 0 15px;
}

/* Responsive Styling */
@media (max-width: 768px) {
    .cards {
        grid-template-columns: 1fr; /* Single column on smaller screens */
        gap: 15px; /* Reduced spacing on smaller screens */
        width: 100%; /* Full width on smaller screens */
    }
    .card {
        padding: 15px; /* Slightly reduced padding for smaller screens */
    }
    .stock-total-card, .stock-caminhao-card, .producao-mes-card, .quebras-card {
        padding: 20px;
    }
    .separator {
        height: 50px;
    }
}

    </style>
</head>
<body>

<!-- Cabeçalho -->
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

<div class="dashboard-container">
    <h1>  </h1>
    <div class="cards">
        <!-- Stock Total -->
        <div class="card stock-total-card">
            <h3>STOCK TOTAL</h3>
            <div class="stock-types">
                <div class="stock-type">
                    <h4>Cubo</h4>
                    <div class="stat"><?php echo $dashboardData['stockTotal']['Gelo em Cubo']; ?></div>
                    </div>
                    <div class="separator"></div>
                    <div class="stock-type">
                        <h4>Barra</h4>
                        <div class="stat"><?php echo $dashboardData['stockTotal']['Gelo em Barra']; ?></div>
                </div>
            </div>
        </div>

        <!-- Stock no Caminhão -->
       <!-- Stock no Caminhão Card with Cubo and Barra inside -->
        <div class="card stock-caminhao-card">
            <h3>Stock no Caminão</h3>
            <div class="stock-types">
                <div class="stock-type">
                    <i class="fas fa-truck"></i>
                    <h4>Cubo</h4>
                    <div class="stat"><?php echo $dashboardData['stockCaminhao']['Gelo em Cubo']; ?></div>
                </div>
                <div class="separator"></div>
                <div class="stock-type">
                    <i class="fas fa-truck"></i>
                    <h4>Barra</h4>
                    <div class="stat"><?php echo $dashboardData['stockCaminhao']['Gelo em Barra']; ?></div>
                </div>
            </div>
        </div>
        <!-- Produção no Mês Corrente -->
        <div class="card producao-mes-card">
            <h3>Produção no Mês</h3>
            <div class="stock-types">
                <div class="stock-type">
                    <i class="fas fa-chart-line"></i>
                    <h4>Cubo</h4>
                    <div class="stat"><?php echo $dashboardData['producaoMes']['Gelo em Cubo']; ?></div>
                </div>
                <div class="separator"></div>
                <div class="stock-type">
                    <i class="fas fa-chart-line"></i>
                    <h4>Barra</h4>
                    <div class="stat"><?php echo $dashboardData['producaoMes']['Gelo em Barra']; ?></div>
                </div>
            </div>
        </div>

        <!-- Quebras e Vendas no Mês Corrente -->
        <div class="card">
            <i class="fas fa-exclamation-triangle"></i>
            <h3>Quebras no Mês</h3>
            <div class="stat"><?php echo $dashboardData['quebrasMes']; ?></div>
        </div>
        <div class="card">
            <i class="fas fa-shopping-cart"></i>
            <h3>Vendas no Mês</h3>
            <div class="stat"><?php echo $dashboardData['vendasMes']; ?></div>
        </div>
        <div class="card">
            <i class="fas fa-dollar-sign"></i>
            <h3>Receita do Mês</h3>
            <div class="stat">MZN <?php echo number_format($dashboardData['receitaMes'], 2); ?></div>
        </div>
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

    <!-- Footer -->
    <footer class="admin-footer">
        <div class="footer-rights">
            <p>&copy; 2024 Magelo Fábrica de Gelo. Todos os direitos reservados.</p>
        </div>
    </footer>

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
