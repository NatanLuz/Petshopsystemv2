<?php
session_start();
require_once '../config/config.php';
checkLogin();

$page_title = 'Gerenciar Servicos';
$success = '';
$error = '';

// Acoes de CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create' || $action === 'update') {
        $nome = cleanInput($_POST['nome']);
        $descricao = cleanInput($_POST['descricao']);
        $preco = floatval($_POST['preco']);
        $duracao_minutos = intval($_POST['duracao_minutos']);
        $categoria = cleanInput($_POST['categoria']);
        
        if (empty($nome) || $preco <= 0) {
            $error = 'Nome e preco sao obrigatorios.';
        } else {
            $conn = getConnection();
            
            if ($action === 'create') {
                $stmt = $conn->prepare("INSERT INTO servicos (nome, descricao, preco, duracao_minutos, categoria) 
                                        VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssdis", $nome, $descricao, $preco, $duracao_minutos, $categoria);
                
                if ($stmt->execute()) {
                    $success = 'Servico cadastrado com sucesso!';
                } else {
                    $error = 'Erro ao cadastrar servico.';
                }
            } else { // update
                $id = intval($_POST['id']);
                $stmt = $conn->prepare("UPDATE servicos SET nome=?, descricao=?, preco=?, duracao_minutos=?, categoria=? 
                                        WHERE id=?");
                $stmt->bind_param("ssdisi", $nome, $descricao, $preco, $duracao_minutos, $categoria, $id);
                
                if ($stmt->execute()) {
                    $success = 'Servico atualizado com sucesso!';
                } else {
                    $error = 'Erro ao atualizar servico.';
                }
            }
            
            $stmt->close();
            $conn->close();
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id']);
        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE servicos SET ativo = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $success = 'Servico excluido com sucesso!';
        } else {
            $error = 'Erro ao excluir servico.';
        }
        
        $stmt->close();
        $conn->close();
    }
}

// Buscar todos os servicos
$search = isset($_GET['search']) ? cleanInput($_GET['search']) : '';
$categoria_filter = isset($_GET['categoria']) ? cleanInput($_GET['categoria']) : '';

$conn = getConnection();

$query = "SELECT * FROM servicos WHERE ativo = 1";

if (!empty($search)) {
    $query .= " AND (nome LIKE ? OR descricao LIKE ?)";
}

if (!empty($categoria_filter)) {
    $query .= " AND categoria = ?";
}

$query .= " ORDER BY nome";

$stmt = $conn->prepare($query);

if (!empty($search) && !empty($categoria_filter)) {
    $search_param = "%$search%";
    $stmt->bind_param("sss", $search_param, $search_param, $categoria_filter);
} elseif (!empty($search)) {
    $search_param = "%$search%";
    $stmt->bind_param("ss", $search_param, $search_param);
} elseif (!empty($categoria_filter)) {
    $stmt->bind_param("s", $categoria_filter);
}

$stmt->execute();
$result = $stmt->get_result();

$servicos = [];
while ($row = $result->fetch_assoc()) {
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
    <h1 class="page-title"><i class="fas fa-briefcase"></i> Servicos</h1>
    <button class="btn btn-primary" onclick="openModal()">
        <i class="fas fa-plus"></i> Novo Servico
    </button>
</div>

<!-- Busca e Filtros -->
<div class="search-bar">
    <form method="GET" style="display: flex; gap: 1rem; flex: 1; flex-wrap: wrap;">
    <input type="text" name="search" placeholder="Buscar por nome ou descricao..." value="<?php echo htmlspecialchars($search); ?>">
        
        <select name="categoria" style="padding: 0.75rem; border: 1px solid #d1d3e2; border-radius: 4px;">
            <option value="">Todas as Categorias</option>
            <option value="Banho e Tosa" <?php echo $categoria_filter === 'Banho e Tosa' ? 'selected' : ''; ?>>Banho e Tosa</option>
            <option value="Veterinario" <?php echo $categoria_filter === 'Veterinario' ? 'selected' : ''; ?>>Veterinario</option>
            <option value="Vacina" <?php echo $categoria_filter === 'Vacina' ? 'selected' : ''; ?>>Vacina</option>
            <option value="Consulta" <?php echo $categoria_filter === 'Consulta' ? 'selected' : ''; ?>>Consulta</option>
            <option value="Cirurgia" <?php echo $categoria_filter === 'Cirurgia' ? 'selected' : ''; ?>>Cirurgia</option>
            <option value="Hospedagem" <?php echo $categoria_filter === 'Hospedagem' ? 'selected' : ''; ?>>Hospedagem</option>
            <option value="Outro" <?php echo $categoria_filter === 'Outro' ? 'selected' : ''; ?>>Outro</option>
        </select>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Buscar
        </button>
        
        <?php if (!empty($search) || !empty($categoria_filter)): ?>
            <a href="servicos.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Limpar
            </a>
        <?php endif; ?>
    </form>
</div>

<!-- Tabela de Servicos -->
<div class="table-card">
    <table>
        <thead>
            <tr>
                <th>Servico</th>
                <th>Categoria</th>
                <th>Duracao</th>
                <th>Preco</th>
                <th>Acoes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($servicos as $servico): ?>
            <tr>
                <td>
                    <strong><?php echo htmlspecialchars($servico['nome']); ?></strong>
                    <?php if (!empty($servico['descricao'])): ?>
                        <br><small style="color: #858796;"><?php echo htmlspecialchars($servico['descricao']); ?></small>
                    <?php endif; ?>
                </td>
                <td><span class="badge badge-info"><?php echo $servico['categoria']; ?></span></td>
                <td><?php echo $servico['duracao_minutos']; ?> min</td>
                <td><strong><?php echo formatMoney($servico['preco']); ?></strong></td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info" onclick='editServico(<?php echo json_encode($servico); ?>)'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <form method="POST" style="display: inline;" onsubmit="return confirmDelete('Tem certeza que deseja excluir este servico?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $servico['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($servicos)): ?>
            <tr>
                <td colspan="5" class="text-center">Nenhum servico encontrado</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal de Cadastro/Edicao -->
<div id="servicoModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; overflow-y: auto;">
    <div style="max-width: 700px; margin: 2rem auto; padding: 2rem;">
        <div class="form-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 id="modalTitle">Novo Servico</h2>
                <button onclick="closeModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            
            <form method="POST" id="servicoForm">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="servicoId">
                
                <div class="form-group">
                    <label for="nome">Nome do Servico *</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                
                <div class="form-group">
                    <label for="descricao">Descricao</label>
                    <textarea id="descricao" name="descricao" rows="3"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="categoria">Categoria *</label>
                        <select id="categoria" name="categoria" required>
                            <option value="">Selecione</option>
                            <option value="Banho e Tosa">Banho e Tosa</option>
                            <option value="Veterinario">Veterinario</option>
                            <option value="Vacina">Vacina</option>
                            <option value="Consulta">Consulta</option>
                            <option value="Cirurgia">Cirurgia</option>
                            <option value="Hospedagem">Hospedagem</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="preco">Preco (R$) *</label>
                        <input type="number" id="preco" name="preco" step="0.01" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="duracao_minutos">Duracao (min)</label>
                        <input type="number" id="duracao_minutos" name="duracao_minutos" min="0" value="30">
                    </div>
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
    document.getElementById('servicoModal').style.display = 'block';
    document.getElementById('modalTitle').textContent = 'Novo Servico';
    document.getElementById('formAction').value = 'create';
    document.getElementById('servicoForm').reset();
}

function closeModal() {
    document.getElementById('servicoModal').style.display = 'none';
}

function editServico(servico) {
    document.getElementById('servicoModal').style.display = 'block';
    document.getElementById('modalTitle').textContent = 'Editar Servico';
    document.getElementById('formAction').value = 'update';
    document.getElementById('servicoId').value = servico.id;
    document.getElementById('nome').value = servico.nome;
    document.getElementById('descricao').value = servico.descricao || '';
    document.getElementById('categoria').value = servico.categoria;
    document.getElementById('preco').value = servico.preco;
    document.getElementById('duracao_minutos').value = servico.duracao_minutos;
}

document.getElementById('servicoModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

<?php include '../includes/footer.php'; ?>
