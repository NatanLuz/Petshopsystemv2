<?php
session_start();
require_once '../config/config.php';
checkLogin();

$page_title = 'Dashboard';

// Buscar estatisticas
$conn = getConnection();

// Total de clientes
$result = $conn->query("SELECT COUNT(*) as total FROM clientes WHERE ativo = 1");
$total_clientes = $result->fetch_assoc()['total'];

// Total de pets
$result = $conn->query("SELECT COUNT(*) as total FROM pets WHERE ativo = 1");
$total_pets = $result->fetch_assoc()['total'];

// Atendimentos de hoje
$result = $conn->query("SELECT COUNT(*) as total FROM atendimentos WHERE data_atendimento = CURDATE()");
$atendimentos_hoje = $result->fetch_assoc()['total'];

// Faturamento do mes
 $result = $conn->query("SELECT SUM(valor) as total FROM atendimentos 
                        WHERE MONTH(data_atendimento) = MONTH(CURDATE()) 
                        AND YEAR(data_atendimento) = YEAR(CURDATE())
                        AND status = 'Concluido'");
$faturamento_mes = $result->fetch_assoc()['total'] ?? 0;

// Atendimentos por especie (ultimos 30 dias)
 $result = $conn->query("SELECT p.especie, COUNT(*) as total 
                        FROM atendimentos a 
                        JOIN pets p ON a.pet_id = p.id 
                        WHERE a.data_atendimento >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                        GROUP BY p.especie
                        ORDER BY total DESC");
$atendimentos_especie = [];
while ($row = $result->fetch_assoc()) {
    $atendimentos_especie[] = $row;
}

// Servicos mais realizados
$result = $conn->query("SELECT s.nome, COUNT(*) as total 
                        FROM atendimentos a 
                        JOIN servicos s ON a.servico_id = s.id 
                        WHERE a.data_atendimento >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                        GROUP BY s.id
                        ORDER BY total DESC
                        LIMIT 5");
$servicos_populares = [];
while ($row = $result->fetch_assoc()) {
    $servicos_populares[] = $row;
}

// Faturamento dos ultimos 7 dias 
 $result = $conn->query("SELECT DATE(data_atendimento) as data, SUM(valor) as total 
                        FROM atendimentos 
                        WHERE data_atendimento >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                        AND status = 'Concluido'
                        GROUP BY DATE(data_atendimento)
                        ORDER BY data");
$faturamento_diario = [];
while ($row = $result->fetch_assoc()) {
    $faturamento_diario[] = $row;
}

// Proximos atendimentos
$result = $conn->query("SELECT a.*, p.nome as pet_nome, p.especie, c.nome as cliente_nome, s.nome as servico_nome
                        FROM atendimentos a
                        JOIN pets p ON a.pet_id = p.id
                        JOIN clientes c ON p.cliente_id = c.id
                        JOIN servicos s ON a.servico_id = s.id
                        WHERE a.data_atendimento >= CURDATE()
                        AND a.status = 'Agendado'
                        ORDER BY a.data_atendimento, a.hora_atendimento
                        LIMIT 5");
$proximos_atendimentos = [];
while ($row = $result->fetch_assoc()) {
    $proximos_atendimentos[] = $row;
}

$conn->close();

include '../includes/header.php';
?>

<!-- Cards de Estatisticas -->
<div class="cards-grid">
    <div class="card stat-card card-primary">
        <div class="stat-info">
            <h3><?php echo $total_clientes; ?></h3>
            <p>Total de Clientes</p>
        </div>
        <div class="stat-icon icon-primary">
            <i class="fas fa-users"></i>
        </div>
    </div>

    <div class="card stat-card card-success">
        <div class="stat-info">
            <h3><?php echo $total_pets; ?></h3>
            <p>Pets Cadastrados</p>
        </div>
        <div class="stat-icon icon-success">
            <i class="fas fa-dog"></i>
        </div>
    </div>

    <div class="card stat-card card-warning">
        <div class="stat-info">
            <h3><?php echo $atendimentos_hoje; ?></h3>
            <p>Atendimentos Hoje</p>
        </div>
        <div class="stat-icon icon-warning">
            <i class="fas fa-calendar-check"></i>
        </div>
    </div>

    <div class="card stat-card card-info">
        <div class="stat-info">
            <h3><?php echo formatMoney($faturamento_mes); ?></h3>
            <p>Faturamento do Mes</p>
        </div>
        <div class="stat-icon icon-info">
            <i class="fas fa-dollar-sign"></i>
        </div>
    </div>
</div>

<!-- Graficos -->
<div
    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Grafico de Atendimentos por Especie -->
    <div class="chart-card">
    <h2><i class="fas fa-chart-pie"></i> Atendimentos por Especie (30 dias)</h2>
        <canvas id="especieChart"></canvas>
    </div>

    <!-- Grafico de Faturamento Diario -->
    <div class="chart-card">
        <h2><i class="fas fa-chart-line"></i> Faturamento (7 dias)</h2>
        <canvas id="faturamentoChart"></canvas>
    </div>
</div>

<!-- Tabelas -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
    <!-- Servicos Mais Realizados -->
    <div class="table-card">
    <h2><i class="fas fa-star"></i> Servicos Mais Realizados</h2>
        <table>
            <thead>
                <tr>
                    <th>Servico</th>
                    <th>Quantidade</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servicos_populares as $servico): ?>
                <tr>
                    <td><?php echo htmlspecialchars($servico['nome']); ?></td>
                    <td><span class="badge badge-primary"><?php echo $servico['total']; ?></span></td>
                </tr>
                <?php endforeach; ?>
                    <?php if (empty($servicos_populares)): ?>
                <tr>
                    <td colspan="2" class="text-center">Nenhum servico realizado nos ultimos 30 dias</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Proximos Atendimentos -->
    <div class="table-card">
    <h2><i class="fas fa-clock"></i> Proximos Atendimentos</h2>
        <table>
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Cliente</th>
                    <th>Pet</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($proximos_atendimentos as $atendimento): ?>
                <tr>
                    <td><?php echo formatDateBR($atendimento['data_atendimento']) . ' ' . date('H:i', strtotime($atendimento['hora_atendimento'])); ?>
                    </td>
                    <td><?php echo htmlspecialchars($atendimento['cliente_nome']); ?></td>
                    <td><?php echo htmlspecialchars($atendimento['pet_nome']); ?>
                        (<?php echo $atendimento['especie']; ?>)</td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($proximos_atendimentos)): ?>
                <tr>
                    <td colspan="3" class="text-center">Nenhum atendimento agendado</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Grafico de Atendimentos por Especie
const especieCtx = document.getElementById('especieChart').getContext('2d');
const especieChart = new Chart(especieCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_column($atendimentos_especie, 'especie')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($atendimentos_especie, 'total')); ?>,
            backgroundColor: [
                '#4e73df',
                '#1cc88a',
                '#36b9cc',
                '#f6c23e',
                '#e74a3b',
                '#858796'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Grafico de Faturamento Diario
const faturamentoCtx = document.getElementById('faturamentoChart').getContext('2d');
const faturamentoChart = new Chart(faturamentoCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_map(function($item) { 
            return date('d/m', strtotime($item['data'])); 
        }, $faturamento_diario)); ?>,
        datasets: [{
            label: 'Faturamento (R$)',
            data: <?php echo json_encode(array_column($faturamento_diario, 'total')); ?>,
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'R$ ' + value.toFixed(2);
                    }
                }
            }
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>