<?php
session_start();
require_once '../config/config.php';
checkLogin();

$page_title = 'Gerenciar Pets';
$success = '';
$error = '';

// Acoes do CRUD feito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create' || $action === 'update') {
        $cliente_id = intval($_POST['cliente_id']);
        $nome = cleanInput($_POST['nome']);
        $especie = cleanInput($_POST['especie']);
        $raca = cleanInput($_POST['raca']);
        $sexo = cleanInput($_POST['sexo']);
        $cor = cleanInput($_POST['cor']);
        $data_nascimento = cleanInput($_POST['data_nascimento']);
        $peso = floatval($_POST['peso']);
        $observacoes = cleanInput($_POST['observacoes']);
        
        if (empty($nome) || empty($cliente_id) || empty($especie) || empty($sexo)) {
            $error = 'Nome, cliente, especie e sexo sao obrigatorios.';
        } else {
            $conn = getConnection();
            
            if ($action === 'create') {
                $stmt = $conn->prepare("INSERT INTO pets (cliente_id, nome, especie, raca, sexo, cor, data_nascimento, peso, observacoes) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issssssds", $cliente_id, $nome, $especie, $raca, $sexo, $cor, $data_nascimento, $peso, $observacoes);
                
                if ($stmt->execute()) {
                    $success = 'Pet cadastrado com sucesso!';
                } else {
                    $error = 'Erro ao cadastrar pet.';
                }
            } else { // update
                $id = intval($_POST['id']);
                $stmt = $conn->prepare("UPDATE pets SET cliente_id=?, nome=?, especie=?, raca=?, sexo=?, cor=?, data_nascimento=?, peso=?, observacoes=? 
                                        WHERE id=?");
                $stmt->bind_param("issssssdsi", $cliente_id, $nome, $especie, $raca, $sexo, $cor, $data_nascimento, $peso, $observacoes, $id);
                
                if ($stmt->execute()) {
                    $success = 'Pet atualizado com sucesso!';
                } else {
                    $error = 'Erro ao atualizar pet.';
                }
            }
            
            $stmt->close();
            $conn->close();
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id']);
        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE pets SET ativo = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $success = 'Pet excluido com sucesso!';
        } else {
            $error = 'Erro ao excluir pet.';
        }
        
        $stmt->close();
        $conn->close();
    }
}

// Buscar todos os pets com seus donos
$search = isset($_GET['search']) ? cleanInput($_GET['search']) : '';
$especie_filter = isset($_GET['especie']) ? cleanInput($_GET['especie']) : '';

$conn = getConnection();

$query = "SELECT p.*, c.nome as cliente_nome FROM pets p 
          JOIN clientes c ON p.cliente_id = c.id 
          WHERE p.ativo = 1";

if (!empty($search)) {
    $query .= " AND (p.nome LIKE ? OR c.nome LIKE ?)";
}

if (!empty($especie_filter)) {
    $query .= " AND p.especie = ?";
}

$query .= " ORDER BY p.nome";

$stmt = $conn->prepare($query);

if (!empty($search) && !empty($especie_filter)) {
    $search_param = "%$search%";
    $stmt->bind_param("sss", $search_param, $search_param, $especie_filter);
} elseif (!empty($search)) {
    $search_param = "%$search%";
    $stmt->bind_param("ss", $search_param, $search_param);
} elseif (!empty($especie_filter)) {
    $stmt->bind_param("s", $especie_filter);
}

$stmt->execute();
$result = $stmt->get_result();

$pets = [];
while ($row = $result->fetch_assoc()) {
    $pets[] = $row;
}

// Buscar clientes para o select
$clientes_result = $conn->query("SELECT id, nome FROM clientes WHERE ativo = 1 ORDER BY nome");
$clientes = [];
while ($row = $clientes_result->fetch_assoc()) {
    $clientes[] = $row;
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
    <h1 class="page-title"><i class="fas fa-dog"></i> Pets</h1>
    <button class="btn btn-primary" onclick="openModal()">
        <i class="fas fa-plus"></i> Novo Pet
    </button>
</div>

<!-- Busca e Filtros -->
<div class="search-bar">
    <form method="GET" style="display: flex; gap: 1rem; flex: 1; flex-wrap: wrap;">
        <input type="text" name="search" placeholder="Buscar por nome do pet ou dono..."
            value="<?php echo htmlspecialchars($search); ?>">

        <select name="especie" style="padding: 0.75rem; border: 1px solid #d1d3e2; border-radius: 4px;">
            <option value="">Todas as Especies</option>
            <option value="Cachorro" <?php echo $especie_filter === 'Cachorro' ? 'selected' : ''; ?>>Cachorro</option>
            <option value="Gato" <?php echo $especie_filter === 'Gato' ? 'selected' : ''; ?>>Gato</option>
            <option value="Passaro" <?php echo $especie_filter === 'Passaro' ? 'selected' : ''; ?>>Passaro</option>
            <option value="Roedor" <?php echo $especie_filter === 'Roedor' ? 'selected' : ''; ?>>Roedor</option>
            <option value="Reptil" <?php echo $especie_filter === 'Reptil' ? 'selected' : ''; ?>>Reptil</option>
            <option value="Outro" <?php echo $especie_filter === 'Outro' ? 'selected' : ''; ?>>Outro</option>
        </select>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Buscar
        </button>

        <?php if (!empty($search) || !empty($especie_filter)): ?>
        <a href="pets.php" class="btn btn-secondary">
            <i class="fas fa-times"></i> Limpar
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- Tabela de Pets -->
<div class="table-card">
    <table>
        <thead>
            <tr>
                <th>Pet</th>
                <th>Dono</th>
                <th>Especie</th>
                <th>Raca</th>
                <th>Sexo</th>
                <th>Idade</th>
                <th>Acoes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pets as $pet): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($pet['nome']); ?></strong></td>
                <td><?php echo htmlspecialchars($pet['cliente_nome']); ?></td>
                <td><span class="badge badge-info"><?php echo $pet['especie']; ?></span></td>
                <td><?php echo htmlspecialchars($pet['raca']); ?></td>
                <td><?php echo $pet['sexo']; ?></td>
                <td>
                    <?php 
                    if (!empty($pet['data_nascimento'])) {
                        $nascimento = new DateTime($pet['data_nascimento']);
                        $hoje = new DateTime();
                        $idade = $hoje->diff($nascimento);
                        echo $idade->y . ' anos';
                    } else {
                        echo '-';
                    }
                    ?>
                </td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info" onclick='editPet(<?php echo json_encode($pet); ?>)'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <form method="POST" style="display: inline;"
                            onsubmit="return confirmDelete('Tem certeza que deseja excluir este pet?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $pet['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($pets)): ?>
            <tr>
                <td colspan="7" class="text-center">Nenhum pet encontrado</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal de Cadastro/Edicao -->
<div id="petModal"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; overflow-y: auto;">
    <div style="max-width: 800px; margin: 2rem auto; padding: 2rem;">
        <div class="form-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 id="modalTitle">Novo Pet</h2>
                <button onclick="closeModal()"
                    style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>

            <form method="POST" id="petForm">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="petId">

                <div class="form-row">
                    <div class="form-group">
                        <label for="cliente_id">Dono *</label>
                        <select id="cliente_id" name="cliente_id" required>
                            <option value="">Selecione o dono</option>
                            <?php foreach ($clientes as $cliente): ?>
                            <option value="<?php echo $cliente['id']; ?>">
                                <?php echo htmlspecialchars($cliente['nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nome">Nome do Pet *</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="especie">Especie *</label>
                        <select id="especie" name="especie" required>
                            <option value="">Selecione</option>
                            <option value="Cachorro">Cachorro</option>
                            <option value="Gato">Gato</option>
                            <option value="Passaro">Passaro</option>
                            <option value="Roedor">Roedor</option>
                            <option value="Reptil">Reptil</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="raca">Raca</label>
                        <input type="text" id="raca" name="raca">
                    </div>

                    <div class="form-group">
                        <label for="sexo">Sexo *</label>
                        <select id="sexo" name="sexo" required>
                            <option value="">Selecione</option>
                            <option value="Macho">Macho</option>
                            <option value="Femea">Femea</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="cor">Cor</label>
                        <input type="text" id="cor" name="cor">
                    </div>

                    <div class="form-group">
                        <label for="data_nascimento">Data de Nascimento</label>
                        <input type="date" id="data_nascimento" name="data_nascimento">
                    </div>

                    <div class="form-group">
                        <label for="peso">Peso (kg)</label>
                        <input type="number" id="peso" name="peso" step="0.01" min="0">
                    </div>
                </div>

                <div class="form-group">
                    <label for="observacoes">Observacoes</label>
                    <textarea id="observacoes" name="observacoes"></textarea>
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
    document.getElementById('petModal').style.display = 'block';
    document.getElementById('modalTitle').textContent = 'Novo Pet';
    document.getElementById('formAction').value = 'create';
    document.getElementById('petForm').reset();
}

function closeModal() {
    document.getElementById('petModal').style.display = 'none';
}

function editPet(pet) {
    document.getElementById('petModal').style.display = 'block';
    document.getElementById('modalTitle').textContent = 'Editar Pet';
    document.getElementById('formAction').value = 'update';
    document.getElementById('petId').value = pet.id;
    document.getElementById('cliente_id').value = pet.cliente_id;
    document.getElementById('nome').value = pet.nome;
    document.getElementById('especie').value = pet.especie;
    document.getElementById('raca').value = pet.raca || '';
    document.getElementById('sexo').value = pet.sexo;
    document.getElementById('cor').value = pet.cor || '';
    document.getElementById('data_nascimento').value = pet.data_nascimento || '';
    document.getElementById('peso').value = pet.peso || '';
    document.getElementById('observacoes').value = pet.observacoes || '';
}

document.getElementById('petModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

<?php include '../includes/footer.php'; ?>