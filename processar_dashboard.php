<?php
header('Content-Type: application/json');
require 'config.php';

$host = 'localhost'; // Servidor de banco de dados
$dbname = 'magelo';  // Nome da base de dados
$user = 'root';      // Usuário do banco de dados
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    echo "Conexão com o banco de dados bem-sucedida!";

    // Producao Diaria
    $stmt = $pdo->query("SELECT SUM(quantidade_total) AS producaoDiaria FROM relatorio_estoque WHERE DATE(data_registro) = CURDATE()");
    $producaoDiaria = $stmt->fetchColumn();

    // Quebras Diarias
    $stmt = $pdo->query("SELECT SUM(quantidade) AS total_quebras FROM relatorio_quebras WHERE DATE(data_registro) = CURDATE();");
    $quebrasDiarias = $stmt->fetchColumn();

    // Vendas Diarias
    $stmt = $pdo->query("SELECT COUNT(id) AS vendasDiarias FROM vendas WHERE DATE(hora_venda) = CURDATE()");
    $vendasDiarias = $stmt->fetchColumn();

    // Clientes Novos
    $stmt = $pdo->query("SELECT COUNT(*) AS clientesNovos FROM clientes WHERE DATE(data_cadastro) = CURDATE()");
    $clientesNovos = $stmt->fetchColumn();

    // Stock do Caminhão
    $stmt = $pdo->query("SELECT SUM(quantidade_total) AS stockCaminhao FROM stock_carro");
    $stockCaminhao = $stmt->fetchColumn();

    // Total de Produtos
    $stmt = $pdo->query("SELECT COUNT(id) AS totalProdutos FROM stock");
    $totalProdutos = $stmt->fetchColumn();

    // Dados Mensais
    $meses = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    // Novos Clientes Mensais
    $stmt = $pdo->query("SELECT MONTH(data_cadastro) AS mes, COUNT(*) AS total FROM clientes GROUP BY mes");
    $novosClientesMensais = array_fill(0, 12, 0);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $novosClientesMensais[$row['mes'] - 1] = $row['total'];
    }

    // Quebras Mensais
    $stmt = $pdo->query("SELECT MONTH(data_registro) AS mes, SUM(quantidade) AS total FROM relatorio_quebras GROUP BY mes");
    $quebrasMensais = array_fill(0, 12, 0);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $quebrasMensais[$row['mes'] - 1] = $row['total'];
    }

    // Produção de Gelo Mensal (Cubo e Barra)
    $stmt = $pdo->query("SELECT MONTH(data_pedido) AS mes, tipo_produto, SUM(quantidade) AS total FROM pedidos GROUP BY mes, tipo_produto");
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

    echo json_encode([
        'producaoDiaria' => $producaoDiaria,
        'quebrasDiarias' => $quebrasDiarias,
        'vendasDiarias' => $vendasDiarias,
        'clientesNovos' => $clientesNovos,
        'stockCaminhao' => $stockCaminhao,
        'totalProdutos' => $totalProdutos,
        'meses' => $meses,
        'novosClientesMensais' => $novosClientesMensais,
        'quebrasMensais' => $quebrasMensais,
        'producaoCuboMensal' => $producaoCuboMensal,
        'producaoBarraMensal' => $producaoBarraMensal,
        'vendasMensais' => $vendasMensais,
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao buscar dados: ' . $e->getMessage()]);
}
