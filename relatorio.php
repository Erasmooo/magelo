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

    // Converte o mês em inglês para português se necessário
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
    $mesSelecionado = strtolower(date('F', mktime(0, 0, 0, date('n'), 10)));
}

$mesNumero = getMonthNumber($mesSelecionado);

// Consulta para pegar os dados de Produção do mês selecionado
$sql_producao = "
    SELECT funcionario_nome, tipo_produto, quantidade_unit, quantidade_total, data_registro
    FROM relatorio_estoque
    WHERE MONTH(data_registro) = $mesNumero
    ORDER BY data_registro DESC
";
$stmt_producao = $pdo->query($sql_producao);
$relatorioProducao = $stmt_producao->fetchAll(PDO::FETCH_ASSOC);

// Consulta para pegar os dados de Vendas do mês selecionado
$sql_vendas = "
    SELECT funcionario_nome, nome_cliente, tipo_produto, quantidade, hora_venda
    FROM vendas
    WHERE MONTH(hora_venda) = $mesNumero
    ORDER BY hora_venda DESC
";
$stmt_vendas = $pdo->query($sql_vendas);
$relatorioVendas = $stmt_vendas->fetchAll(PDO::FETCH_ASSOC);

// Consulta para pegar os dados de Quebras do mês selecionado
$sql_quebras = "
    SELECT funcionario_nome, tipo_gelo, tipo_quebra, quantidade, data_registro
    FROM relatorio_quebras
    WHERE MONTH(data_registro) = $mesNumero
    ORDER BY data_registro DESC
";
$stmt_quebras = $pdo->query($sql_quebras);
$relatorioQuebras = $stmt_quebras->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório - Magelo Fábrica de Gelo</title>
    <link rel="stylesheet" href="relatorio.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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

    <!-- Barra de navegação dos meses -->
    <div class="months-navigation">
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
        <a href="?resumo=anual">Resumo Anual</a>
    </div>

    <!-- Botões para selecionar o tipo de relatório -->
    <div class="report-buttons">
        <button class="active" onclick="showReport('producao')">Relatório de Produção</button>
        <button onclick="showReport('vendas')">Relatório de Vendas</button>
        <button onclick="showReport('quebras')">Relatório de Quebras</button>
    </div>

    <!-- Conteúdo principal -->
    <div class="main-container">
        <!-- Conteúdo de cada relatório -->
        <div id="producao" class="report-content">
            <h2>Produção de Gelo - <?php echo ucfirst($mesSelecionado); ?></h2>
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

        <div id="vendas" class="report-content" style="display:none;">
            <h2>Relatório de Vendas - <?php echo ucfirst($mesSelecionado); ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Funcionário</th>
                        <th>Cliente</th>
                        <th>Tipo de Gelo</th>
                        <th>Quantidade Vendida</th>
                        <th>Data da Venda</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($relatorioVendas as $venda): ?>
                    <tr>
                        <td><?php echo $venda['funcionario_nome']; ?></td>
                        <td><?php echo $venda['nome_cliente']; ?></td>
                        <td><?php echo $venda['tipo_produto']; ?></td>
                        <td><?php echo $venda['quantidade']; ?></td>
                        <td><?php echo $venda['hora_venda']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="quebras" class="report-content" style="display:none;">
            <h2>Relatório de Quebras - <?php echo ucfirst($mesSelecionado); ?></h2>
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

    <script>
        function showReport(report) {
            const contents = document.querySelectorAll('.report-content');
            contents.forEach(content => content.style.display = 'none');
            document.getElementById(report).style.display = 'block';

            const buttons = document.querySelectorAll('.report-buttons button');
            buttons.forEach(button => button.classList.remove('active'));
            event.currentTarget.classList.add('active');
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

</body>
</html>
