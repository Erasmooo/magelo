<?php
// Inicia a sessão
session_start();

// Verifica se o funcionário está logado
if (!isset($_SESSION['funcionario_nome'])) {
    header("Location: index.php");
    exit;
}

$nomeFuncionario = $_SESSION['funcionario_nome'];

// Inclui a configuração de conexão ao banco de dados
require 'config.php';

// Função para obter o número do mês a partir do nome em português
function getMonthNumber($mes) {
    $meses = [
        'janeiro' => 1,
        'fevereiro' => 2,
        'marco' => 3,
        'abril' => 4,
        'maio' => 5,
        'junho' => 6,
        'julho' => 7,
        'agosto' => 8,
        'setembro' => 9,
        'outubro' => 10,
        'novembro' => 11,
        'dezembro' => 12
    ];

    $mesInglesParaPortugues = [
        'january' => 'janeiro',
        'february' => 'fevereiro',
        'march' => 'marco',
        'april' => 'abril',
        'may' => 'maio',
        'june' => 'junho',
        'july' => 'julho',
        'august' => 'agosto',
        'september' => 'setembro',
        'october' => 'outubro',
        'november' => 'novembro',
        'december' => 'dezembro'
    ];

    if (isset($mesInglesParaPortugues[$mes])) {
        $mes = $mesInglesParaPortugues[$mes];
    }

    return $meses[$mes] ?? null;
}

// Define o mês atual caso nenhum tenha sido selecionado
if (isset($_GET['mes'])) {
    $mesSelecionado = $_GET['mes'];
} else {
    $mesSelecionado = strtolower(date('F'));
}

$mesNumero = getMonthNumber($mesSelecionado);

// Consulta para pegar os dados de Produção do mês selecionado
$sql_producao = "
    SELECT funcionario_nome, tipo_produto, quantidade_unit, quantidade_total, data_registro
    FROM relatorio_estoque
    WHERE MONTH(data_registro) = :mes
    ORDER BY data_registro DESC
";
$stmt_producao = $pdo->prepare($sql_producao);
$stmt_producao->bindParam(':mes', $mesNumero, PDO::PARAM_INT);
$stmt_producao->execute();
$relatorioProducao = $stmt_producao->fetchAll(PDO::FETCH_ASSOC);

// Consulta para pegar os dados de Vendas do mês selecionado
$sql_vendas = "
    SELECT funcionario_nome, nome_cliente, hora_venda, quantidade_cubo, quantidade_barra, preco_unitario_barra, preco_unitario_cubo, total_venda
    FROM vendas
    WHERE MONTH(hora_venda) = :mes
    ORDER BY hora_venda DESC
";
$stmt_vendas = $pdo->prepare($sql_vendas);
$stmt_vendas->bindParam(':mes', $mesNumero, PDO::PARAM_INT);
$stmt_vendas->execute();
$relatorioVendas = $stmt_vendas->fetchAll(PDO::FETCH_ASSOC);

// Consulta para pegar os dados de Despesas do mês selecionado
$sql_despesas = "
    SELECT id, data, descricao, valor
    FROM despesas
    WHERE MONTH(data) = :mes
    ORDER BY data DESC
";
$stmt_despesas = $pdo->prepare($sql_despesas);
$stmt_despesas->bindParam(':mes', $mesNumero, PDO::PARAM_INT);
$stmt_despesas->execute();
$relatorioDespesas = $stmt_despesas->fetchAll(PDO::FETCH_ASSOC);


// Consulta para pegar os dados de Quebras do mês selecionado
$sql_quebras = "
    SELECT funcionario_nome, tipo_gelo, tipo_quebra, quantidade, data_registro
    FROM relatorio_quebras
    WHERE MONTH(data_registro) = :mes
    ORDER BY data_registro DESC
";
$stmt_quebras = $pdo->prepare($sql_quebras);
$stmt_quebras->bindParam(':mes', $mesNumero, PDO::PARAM_INT);
$stmt_quebras->execute();
$relatorioQuebras = $stmt_quebras->fetchAll(PDO::FETCH_ASSOC);

// Inicializa um array para armazenar o resumo mensal
$resumoMensal = [];

// Consulta para obter totais mensais de produção, vendas, quebras e despesas
for ($mes = 1; $mes <= 12; $mes++) {
    // Produção mensal
    $sql_producao_mes = "SELECT SUM(quantidade_total) AS total_producao FROM relatorio_estoque WHERE MONTH(data_registro) = :mes";
    $stmt_producao_mes = $pdo->prepare($sql_producao_mes);
    $stmt_producao_mes->execute(['mes' => $mes]);
    $totalProducaoMes = $stmt_producao_mes->fetchColumn() ?: 0;

    // Vendas mensal
    $sql_vendas_mes = "SELECT SUM(total_venda) AS total_vendas FROM vendas WHERE MONTH(hora_venda) = :mes";
    $stmt_vendas_mes = $pdo->prepare($sql_vendas_mes);
    $stmt_vendas_mes->execute(['mes' => $mes]);
    $totalVendasMes = $stmt_vendas_mes->fetchColumn() ?: 0;

    // Quebras mensal
    $sql_quebras_mes = "SELECT SUM(quantidade) AS total_quebras FROM relatorio_quebras WHERE MONTH(data_registro) = :mes";
    $stmt_quebras_mes = $pdo->prepare($sql_quebras_mes);
    $stmt_quebras_mes->execute(['mes' => $mes]);
    $totalQuebrasMes = $stmt_quebras_mes->fetchColumn() ?: 0;

    // Despesas mensal
    $sql_despesas_mes = "SELECT SUM(valor) AS total_despesas FROM despesas WHERE MONTH(data) = :mes";
    $stmt_despesas_mes = $pdo->prepare($sql_despesas_mes);
    $stmt_despesas_mes->execute(['mes' => $mes]);
    $totalDespesasMes = $stmt_despesas_mes->fetchColumn() ?: 0;

    // Armazena os dados no array resumoMensal
    $resumoMensal[] = [
        'mes' => $mes,
        'totalProducao' => $totalProducaoMes,
        'totalVendas' => $totalVendasMes,
        'totalQuebras' => $totalQuebrasMes,
        'totalDespesas' => $totalDespesasMes
    ];
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/logo-magelo.PNG" type="">
    <title>Relatório - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="relatorio.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            <span id="user-name"><?php echo $nomeFuncionario; ?></span>
            <i class="fas fa-chevron-down arrow"></i>
            <ul class="dropdown-menu">
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>

    <!-- Botão de Toggle para Navegação dos Meses -->
    <div class="months-toggle">
        <button onclick="toggleMonthsNavigation()">Escolher Mês <i class="fas fa-chevron-down"></i></button>
    </div>

    <!-- Barra de navegação dos meses -->
    <div class="months-navigation" id="months-navigation">
        <?php 
        $meses = [
            'janeiro', 'fevereiro', 'marco', 'abril', 'maio', 'junho', 
            'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'
        ];
        foreach ($meses as $mes) {
            $activeClass = ($mes == $mesSelecionado) ? 'active' : '';
            echo "<a href='?mes=$mes' class='$activeClass'>".ucfirst($mes)."</a>";
        }
        ?>
        <a href="javascript:void(0);" onclick="showReport('resumo-anual')">Resumo Anual</a>
    </div>

    <!-- Botões para selecionar o tipo de relatório -->
    <div class="report-buttons">
        <button class="active" onclick="showReport('producao')">Relatório de Produção</button>
        <button onclick="showReport('vendas')">Relatório de Vendas</button>
        <button onclick="showReport('quebras')">Relatório de Quebras</button>
        <button onclick="showReport('despesas')">Relatório de Despesas</button>

    </div>

    <!-- Conteúdo principal -->
    <div class="main-container">
        <!-- Conteúdo de cada relatório -->
        <div id="producao" class="report-content">
            <h2>Produção de Gelo - <?php echo ucfirst($mesSelecionado); ?></h2>
            <button onclick="printReport('producao')" class="print-button"><i class="fas fa-print"></i> Imprimir Produção</button>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Funcionário</th>
                            <th>Tipo de Gelo</th>
                            <th>Quantidade</th>
                            <th>Quantidade Total</th>
                            <th>Data de Produção</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($relatorioProducao as $registro): ?>
                        <tr>
                            <td><?php echo $registro['funcionario_nome']; ?></td>
                            <td><?php echo $registro['tipo_produto']; ?></td>
                            <td><?php echo $registro['quantidade_unit']; ?></td>
                            <td><?php echo $registro['quantidade_total']; ?></td>
                            <td><?php echo $registro['data_registro']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="vendas" class="report-content" style="display:none;">
            <h2>Relatório de Vendas - <?php echo ucfirst($mesSelecionado); ?></h2>
            <button onclick="printReport('vendas')" class="print-button"><i class="fas fa-print"></i> Imprimir Vendas</button>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Funcionário</th>
                            <th>Cliente</th>
                            <th>Quantidade de Cubos</th>
                            <th>Preço Unitário de Cubos</th>
                            <th>Quantidade de Barras</th>
                            <th>Preço Unitário de Barras</th>
                            <th>Total da venda</th>
                            <th>Data da Venda</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($relatorioVendas as $venda): ?>
                        <tr>
                            <td><?php echo $venda['funcionario_nome']; ?></td>
                            <td><?php echo $venda['nome_cliente']; ?></td>
                            <td><?php echo $venda['quantidade_cubo']; ?></td>
                            <td><?php echo $venda['preco_unitario_cubo']; ?></td>
                            <td><?php echo $venda['quantidade_barra']; ?></td>
                            <td><?php echo $venda['preco_unitario_barra']; ?></td>
                            <td><?php echo $venda['total_venda']; ?></td>
                            <td><?php echo $venda['hora_venda']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="quebras" class="report-content" style="display:none;">
            <h2>Relatório de Quebras - <?php echo ucfirst($mesSelecionado); ?></h2>
            <button onclick="printReport('quebras')" class="print-button"><i class="fas fa-print"></i> Imprimir Quebras</button>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Funcionário</th>
                            <th>Tipo de Gelo</th>
                            <th>Tipo de Quebra</th>
                            <th>Quantidade</th>
                            <th>Data de Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($relatorioQuebras as $quebra): ?>
                        <tr>
                            <td><?php echo $quebra['funcionario_nome']; ?></td>
                            <td><?php echo $quebra['tipo_gelo']; ?></td>
                            <td><?php echo $quebra['tipo_quebra']; ?></td>
                            <td><?php echo $quebra['quantidade']; ?> Unidades</td>
                            <td><?php echo $quebra['data_registro']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="despesas" class="report-content" style="display:none;">
            <h2>Relatório de Despesas - <?php echo ucfirst($mesSelecionado); ?></h2>
            <button onclick="printReport('despesas')" class="print-button"><i class="fas fa-print"></i> Imprimir Despesas</button>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($relatorioDespesas as $despesa): ?>
                        <tr>
                            <td><?php echo $despesa['id']; ?></td>
                            <td><?php echo $despesa['data']; ?></td>
                            <td><?php echo $despesa['descricao']; ?></td>
                            <td><?php echo number_format($despesa['valor'], 2, ',', '.'); ?> MZN</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>


            <!-- Nova seção para o Resumo Anual -->
            <div id="resumo-anual" class="report-content" style="display:none;">
            <h2>Resumo Anual</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Mês</th>
                            <th>Produção Anual</th>
                            <th>Vendas Anuais</th>
                            <th>Quebras Anuais</th>
                            <th>Despesas Anuais</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resumoMensal as $mesResumo): ?>
                        <tr>
                            <td><?php echo ucfirst(date("F", mktime(0, 0, 0, $mesResumo['mes'], 10))); ?></td>
                            <td><?php echo number_format($mesResumo['totalProducao'], 2, ',', '.'); ?></td>
                            <td><?php echo number_format($mesResumo['totalVendas'], 2, ',', '.'); ?> MZN</td>
                            <td><?php echo number_format($mesResumo['totalQuebras'], 2, ',', '.'); ?></td>
                            <td><?php echo number_format($mesResumo['totalDespesas'], 2, ',', '.'); ?> MZN</td>
                        </tr>
                        <?php endforeach; ?>
                        <!-- Linha de Totais Anuais -->
                        <tr>
                            <td><strong>Total</strong></td>
                            <td><?php echo number_format($totalProducao, 2, ',', '.'); ?></td>
                            <td><?php echo number_format($totalVendas, 2, ',', '.'); ?> MZN</td>
                            <td><?php echo number_format($totalQuebras, 2, ',', '.'); ?></td>
                            <td><?php echo number_format($totalDespesas, 2, ',', '.'); ?> MZN</td>
                        </tr>
                        <!-- Linha de Percentuais -->
                        <tr>
                            <td><strong>% Total</strong></td>
                            <td><?php echo $percentProducao; ?>%</td>
                            <td><?php echo $percentVendas; ?>%</td>
                            <td><?php echo $percentQuebras; ?>%</td>
                            <td><?php echo $percentDespesas; ?>%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
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

        function toggleMonthsNavigation() {
            const monthsNav = document.getElementById("months-navigation");
            monthsNav.classList.toggle("show");
        }

        function showReport(report) {
            const contents = document.querySelectorAll('.report-content');
            contents.forEach(content => content.style.display = 'none');
            document.getElementById(report).style.display = 'block';

            const buttons = document.querySelectorAll('.report-buttons button');
            buttons.forEach(button => button.classList.remove('active'));
            event.currentTarget.classList.add('active'); 
        }

        function showReport(report) {
            const contents = document.querySelectorAll('.report-content');
            contents.forEach(content => content.style.display = 'none');
            document.getElementById(report).style.display = 'block';

            const buttons = document.querySelectorAll('.report-buttons button');
            buttons.forEach(button => button.classList.remove('active'));
            event.currentTarget.classList.add('active');
        }

        function showReport(report) {
            // Oculta todos os conteúdos de relatório
            const contents = document.querySelectorAll('.report-content');
            contents.forEach(content => content.style.display = 'none');
            
            // Exibe o conteúdo do relatório selecionado
            document.getElementById(report).style.display = 'block';

            // Remove a classe 'active' de todos os botões de relatório
            const buttons = document.querySelectorAll('.report-buttons button');
            buttons.forEach(button => button.classList.remove('active'));

            // Atualiza a navegação de meses para destacar o mês ou "Resumo Anual" selecionado
            const monthLinks = document.querySelectorAll('.months-navigation a');
            monthLinks.forEach(link => link.classList.remove('active'));
            if (report === 'resumo-anual') {
                document.querySelector('.months-navigation a[href="javascript:void(0);"]').classList.add('active');
            }

            // Fecha a janela de navegação dos meses
            const monthsNav = document.getElementById("months-navigation");
            monthsNav.classList.remove("show");
    }


        function printReport(reportId) {
            const reportContent = document.getElementById(reportId).innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.open();
            printWindow.document.write(`
                <html>
                <head>
                    <title>Imprimir Relatório</title>
                    <link rel="stylesheet" href="relatorio.css?v=<?php echo time(); ?>" />
                    <style>
                        .print-button { display: none; }
                    </style>
                </head>
                <body onload="window.print(); window.close();">
                    ${reportContent}
                </body>
                </html>
            `);
            printWindow.document.close();
        }

    </script>

    <!-- Rodapé -->
    <footer class="admin-footer">
        <p>&copy; 2024 Magelo Fábrica de Gelo. Todos os direitos reservados.</p>
    </footer>

</body>
</html>
