<?php
session_start();
require_once '../config/config.php';
checkLogin();

$page_title = 'Gerenciar Atendimentos';
$success = '';
$error = '';

// Acoes do CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create' || $action === 'update') {
        $pet_id = intval($_POST['pet_id']);
        $servico_id = intval($_POST['servico_id']);
        $data_atendimento = cleanInput($_POST['data_atendimento']);
        $hora_atendimento = cleanInput($_POST['hora_atendimento']);
        $status = cleanInput($_POST['status']);
        $valor = floatval($_POST['valor']);
        $observacoes = cleanInput($_POST['observacoes']);
        $usuario_id = $_SESSION['user_id'];
        
        if (empty($pet_id) || empty($servico_id) || empty($data_atendimento) || empty($hora_atendimento)) {
            $error = 'Pet, servico, data e hora sao obrigatorios.';
        } else {
            $conn = getConnection();
            
            if ($action === 'create') {
                $stmt = $conn->prepare("INSERT INTO atendimentos (pet_id, servico_id, usuario_id, data_atendimento, hora_atendimento, status, valor, observacoes) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("iiiissds", $pet_id, $servico_id, $usuario_id, $data_atendimento, $hora_atendimento, $status, $valor, $observacoes);
                
                if ($stmt->execute()) {
                    $success = 'Atendimento agendado com sucesso!';
                } else {
                    $error = 'Erro ao agendar atendimento.';
                }
            } else { // update
                $id = intval($_POST['id']);
                $stmt = $conn->prepare("UPDATE atendimentos SET pet_id=?, servico_id=?, data_atendimento=?, hora_atendimento=?, status=?, valor=?, observacoes=? 
                                        WHERE id=?");
                $stmt->bind_param("iisssdsi", $pet_id, $servico_id, $data_atendimento, $hora_atendimento, $status, $valor, $observacoes, $id);
                
                if ($stmt->execute()) {
                    $success = 'Atendimento atualizado com sucesso!';
                } else {
                    $error = 'Erro ao atualizar atendimento.';
                }
            }
            
            $stmt->close();
            $conn->close();
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id']);
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM atendimentos WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $success = 'Atendimento excluido com sucesso!';
        } else {
            $error = 'Erro ao excluir atendimento.';
        }
        
        $stmt->close();
        $conn->close();
    }
}

// Buscando atendimentos
$status_filter = isset($_GET['status']) ? cleanInput($_GET['status']) : '';
$data_filter = isset($_GET['data']) ? cleanInput($_GET['data']) : '';

$conn = getConnection();

$query = "SELECT a.*, p.nome as pet_nome, p.especie, c.nome as cliente_nome, c.telefone, 
          s.nome as servico_nome, s.categoria, u.nome as usuario_nome
          FROM atendimentos a
          JOIN pets p ON a.pet_id = p.id
          JOIN clientes c ON p.cliente_id = c.id
          JOIN servicos s ON a.servico_id = s.id
          LEFT JOIN usuarios u ON a.usuario_id = u.id
          WHERE 1=1";

if (!empty($status_filter)) {
    $query .= " AND a.status = ?";
}

if (!empty($data_filter)) {
    $query .= " AND a.data_atendimento = ?";
}

$query .= " ORDER BY a.data_atendimento DESC, a.hora_atendimento DESC";

$stmt = $conn->prepare($query);

if (!empty($status_filter) && !empty($data_filter)) {
    $stmt->bind_param("ss", $status_filter, $data_filter);
} elseif (!empty($status_filter)) {
    $stmt->bind_param("s", $status_filter);
} elseif (!empty($data_filter)) {
    $stmt->bind_param("s", $data_filter);
}

$stmt->execute();
$result = $stmt->get_result();

$atendimentos = [];
while ($row = $result->fetch_assoc()) {
    $atendimentos[] = $row;
}

// Buscar pets para o select
$pets_result = $conn->query("SELECT p.id, p.nome, c.nome as cliente_nome 
                              FROM pets p 
                              JOIN clientes c ON p.cliente_id = c.id 
                              WHERE p.ativo = 1 
                              ORDER BY c.nome, p.nome");
$pets = [];
while ($row = $pets_result->fetch_assoc()) {
    $pets[] = $row;
}

// Buscar servicos para o select
$servicos_result = $conn->query("SELECT id, nome, preco FROM servicos WHERE ativo = 1 ORDER BY nome");
$servicos = [];
while ($row = $servicos_result->fetch_assoc()) {
    $servicos[] = $row;
}

$conn->close();

include '../includes/header.php';
?>

<?php if ($success): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
</div>
<?php endif; ?>

<div class="d-flex justify-between align-center mb-3">
    <h1 class="page-title"><i class="fas fa-calendar-check"></i> Atendimentos</h1>
    <button class="btn btn-primary" onclick="openModal()">
        <i class="fas fa-plus"></i> Novo Atendimento
    </button>
</div>

<!-- parte dos filtros -->
<div class="search-bar">
    <form method="GET" style="display: flex; gap: 1rem; flex: 1; flex-wrap: wrap;">
        <input type="date" name="data" value="<?php echo htmlspecialchars($data_filter); ?>"
            style="padding: 0.75rem; border: 1px solid #d1d3e2; border-radius: 4px;">

        <select name="status" style="padding: 0.75rem; border: 1px solid #d1d3e2; border-radius: 4px;">
            <option value="">Todos os Status</option>
                <option value="Agendado" <?php echo $status_filter === 'Agendado' ? 'selected' : ''; ?>>Agendado</option>
                <option value="Em Atendimento" <?php echo $status_filter === 'Em Atendimento' ? 'selected' : ''; ?>>Em Atendimento</option>
                <option value="Concluido" <?php echo $status_filter === 'Concluido' ? 'selected' : ''; ?>>Concluido</option>
                <option value="Cancelado" <?php echo $status_filter === 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
        </select>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter"></i> Filtrar
        </button>

        <?php if (!empty($status_filter) || !empty($data_filter)): ?>
        <a href="atendimentos.php" class="btn btn-secondary">
            <i class="fas fa-times"></i> Limpar
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- Tabela de Atendimentos -->
<div class="table-card">
    <table>
        <thead>
            <tr>
                <th>Data/Hora</th>
                <th>Cliente</th>
                <th>Pet</th>
                <th>Servico</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Acoes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($atendimentos as $atendimento): ?>
            <tr>
                <td>
                    <?php echo formatDateBR($atendimento['data_atendimento']); ?><br>
                    <small><?php echo date('H:i', strtotime($atendimento['hora_atendimento'])); ?></small>
                </td>
                <td>
                    <?php echo htmlspecialchars($atendimento['cliente_nome']); ?><br>
                    <small style="color: #858796;"><?php echo htmlspecialchars($atendimento['telefone']); ?></small>
                </td>
                <td>
                    <strong><?php echo htmlspecialchars($atendimento['pet_nome']); ?></strong><br>
                    <small style="color: #858796;"><?php echo $atendimento['especie']; ?></small>
                </td>
                <td>
                    <?php echo htmlspecialchars($atendimento['servico_nome']); ?><br>
                    <small style="color: #858796;"><?php echo $atendimento['categoria']; ?></small>
                </td>
                <td><strong><?php echo formatMoney($atendimento['valor']); ?></strong></td>
                <td>
                    <?php 
                    $badge_class = '';
                    switch($atendimento['status']) {
                        case 'Agendado': $badge_class = 'badge-primary'; break;
                        case 'Em Atendimento': $badge_class = 'badge-warning'; break;
                        case 'Concluido': $badge_class = 'badge-success'; break;
                        case 'Cancelado': $badge_class = 'badge-danger'; break;
                    }
                    ?>
                    <span class="badge <?php echo $badge_class; ?>"><?php echo $atendimento['status']; ?></span>
                </td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info"
                            onclick='editAtendimento(<?php echo json_encode($atendimento); ?>)'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <form method="POST" style="display: inline;"
                            onsubmit="return confirmDelete('Tem certeza que deseja excluir este atendimento?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $atendimento['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($atendimentos)): ?>
            <tr>
                <td colspan="7" class="text-center">Nenhum atendimento encontrado</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal de Cadastro/Edicao -->

<div id="atendimentoModal"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; overflow-y: auto;">
    <div style="max-width: 700px; margin: 2rem auto; padding: 2rem;">
        <div class="form-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 id="modalTitle">Novo Atendimento</h2>
                <button onclick="closeModal()"
                    style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>

            <form method="POST" id="atendimentoForm">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="atendimentoId">

                <div class="form-row">
                    <div class="form-group">
                        <label for="pet_id">Pet *</label>
                        <select id="pet_id" name="pet_id" required>
                            <option value="">Selecione o pet</option>
                            <?php foreach ($pets as $pet): ?>
                            <option value="<?php echo $pet['id']; ?>">
                                <?php echo htmlspecialchars($pet['nome'] . ' - ' . $pet['cliente_nome']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="servico_id">Servico *</label>
                        <select id="servico_id" name="servico_id" required onchange="updateValor()">
                            <option value="">Selecione o servico</option>
                            <?php foreach ($servicos as $servico): ?>
                            <option value="<?php echo $servico['id']; ?>" data-preco="<?php echo $servico['preco']; ?>">
                                <?php echo htmlspecialchars($servico['nome']) . ' - ' . formatMoney($servico['preco']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="data_atendimento">Data *</label>
                        <input type="date" id="data_atendimento" name="data_atendimento" required>
                    </div>

                    <div class="form-group">
                        <label for="hora_atendimento">Hora *</label>
                        <input type="time" id="hora_atendimento" name="hora_atendimento" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Status *</label>
                        <select id="status" name="status" required>
                            <option value="Agendado">Agendado</option>
                            <option value="Em Atendimento">Em Atendimento</option>
                            <option value="Concluido">Concluido</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="valor">Valor (R$) *</label>
                    <input type="number" id="valor" name="valor" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label for="observacoes">Observacoes</label>
                    <textarea id="observacoes" name="observacoes" rows="3"></textarea>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('atendimentoModal').style.display = 'block';
    document.getElementById('modalTitle').textContent = 'Novo Atendimento';
    document.getElementById('formAction').value = 'create';
    document.getElementById('atendimentoForm').reset();

    // Definir data e hora atual

    const now = new Date();
    document.getElementById('data_atendimento').value = now.toISOString().split('T')[0];
    document.getElementById('hora_atendimento').value = now.toTimeString().slice(0, 5);
}

function closeModal() {
    document.getElementById('atendimentoModal').style.display = 'none';
}

function updateValor() {
    const servicoSelect = document.getElementById('servico_id');
    const selectedOption = servicoSelect.options[servicoSelect.selectedIndex];
    const preco = selectedOption.getAttribute('data-preco');

    if (preco) {
        document.getElementById('valor').value = preco;
    }
}

function editAtendimento(atendimento) {
    document.getElementById('atendimentoModal').style.display = 'block';
    document.getElementById('modalTitle').textContent = 'Editar Atendimento';
    document.getElementById('formAction').value = 'update';
    document.getElementById('atendimentoId').value = atendimento.id;
    document.getElementById('pet_id').value = atendimento.pet_id;
    document.getElementById('servico_id').value = atendimento.servico_id;
    document.getElementById('data_atendimento').value = atendimento.data_atendimento;
    document.getElementById('hora_atendimento').value = atendimento.hora_atendimento;
    document.getElementById('status').value = atendimento.status;
    document.getElementById('valor').value = atendimento.valor;
    document.getElementById('observacoes').value = atendimento.observacoes || '';
}

document.getElementById('atendimentoModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

<?php include '../includes/footer.php'; ?>