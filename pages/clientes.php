<?php
session_start();
require_once '../config/config.php';
checkLogin();

$page_title = 'Gerenciar Clientes';
$success = '';
$error = '';

// Acoes de CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create' || $action === 'update') {
        $nome = cleanInput($_POST['nome']);
        $email = cleanInput($_POST['email']);
        $telefone = cleanInput($_POST['telefone']);
        $celular = cleanInput($_POST['celular']);
        $cpf = cleanInput($_POST['cpf']);
        $endereco = cleanInput($_POST['endereco']);
        $bairro = cleanInput($_POST['bairro']);
        $cidade = cleanInput($_POST['cidade']);
        $estado = cleanInput($_POST['estado']);
        $cep = cleanInput($_POST['cep']);
        $observacoes = cleanInput($_POST['observacoes']);
        
        if (empty($nome) || empty($telefone)) {
            $error = 'Nome e telefone sao obrigatorios.';
        } else {
            $conn = getConnection();
            
            if ($action === 'create') {
                $stmt = $conn->prepare("INSERT INTO clientes (nome, email, telefone, celular, cpf, endereco, bairro, cidade, estado, cep, observacoes) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssssssss", $nome, $email, $telefone, $celular, $cpf, $endereco, $bairro, $cidade, $estado, $cep, $observacoes);
                
                if ($stmt->execute()) {
                    $success = 'Cliente cadastrado com sucesso!';
                } else {
                    $error = 'Erro ao cadastrar cliente.';
                }
            } else { // update
                $id = intval($_POST['id']);
                $stmt = $conn->prepare("UPDATE clientes SET nome=?, email=?, telefone=?, celular=?, cpf=?, endereco=?, bairro=?, cidade=?, estado=?, cep=?, observacoes=? 
                                        WHERE id=?");
                $stmt->bind_param("sssssssssssi", $nome, $email, $telefone, $celular, $cpf, $endereco, $bairro, $cidade, $estado, $cep, $observacoes, $id);
                
                if ($stmt->execute()) {
                    $success = 'Cliente atualizado com sucesso!';
                } else {
                    $error = 'Erro ao atualizar cliente.';
                }
            }
            
            $stmt->close();
            $conn->close();
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id']);
        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE clientes SET ativo = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $success = 'Cliente excluido com sucesso!';
        } else {
            $error = 'Erro ao excluir cliente.';
        }
        
        $stmt->close();
        $conn->close();
    }
}

// Buscar todos os clientes
$search = isset($_GET['search']) ? cleanInput($_GET['search']) : '';
$conn = getConnection();

if (!empty($search)) {
    $search_param = "%$search%";
    $stmt = $conn->prepare("SELECT * FROM clientes WHERE ativo = 1 AND (nome LIKE ? OR email LIKE ? OR telefone LIKE ? OR cpf LIKE ?) ORDER BY nome");
    $stmt->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM clientes WHERE ativo = 1 ORDER BY nome");
}

$clientes = [];
while ($row = $result->fetch_assoc()) {
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
    <h1 class="page-title"><i class="fas fa-users"></i> Clientes</h1>
    <button class="btn btn-primary" onclick="openModal()">
        <i class="fas fa-plus"></i> Novo Cliente
    </button>
</div>

<!-- Busca -->
<div class="search-bar">
    <form method="GET" style="display: flex; gap: 1rem; flex: 1;">
        <input type="text" name="search" placeholder="Buscar por nome, email, telefone ou CPF..."
            value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Buscar
        </button>
        <?php if (!empty($search)): ?>
        <a href="clientes.php" class="btn btn-secondary">
            <i class="fas fa-times"></i> Limpar
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- Tabela de Clientes -->
<div class="table-card">
    <table id="clientesTable">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Celular</th>
                <th>Cidade</th>
                <th>Acoes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente): ?>
            <tr>
                <td><?php echo htmlspecialchars($cliente['nome']); ?></td>
                <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                <td><?php echo htmlspecialchars($cliente['telefone']); ?></td>
                <td><?php echo htmlspecialchars($cliente['celular']); ?></td>
                <td><?php echo htmlspecialchars($cliente['cidade'] . '/' . $cliente['estado']); ?></td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info" onclick='editCliente(<?php echo json_encode($cliente); ?>)'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <form method="POST" style="display: inline;"
                            onsubmit="return confirmDelete('Tem certeza que deseja excluir este cliente?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($clientes)): ?>
            <tr>
                <td colspan="6" class="text-center">Nenhum cliente encontrado</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal de Cadastro/Edicao -->
<div id="clienteModal"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; overflow-y: auto;">
    <div style="max-width: 800px; margin: 2rem auto; padding: 2rem;">
        <div class="form-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 id="modalTitle">Novo Cliente</h2>
                <button onclick="closeModal()"
                    style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>

            <form method="POST" id="clienteForm">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="clienteId">

                <div class="form-row">
                    <div class="form-group">
                        <label for="nome">Nome Completo *</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>

                    <div class="form-group">
                        <label for="cpf">CPF</label>
                        <input type="text" id="cpf" name="cpf" maxlength="14">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email">
                    </div>

                    <div class="form-group">
                        <label for="telefone">Telefone *</label>
                        <input type="text" id="telefone" name="telefone" required>
                    </div>

                    <div class="form-group">
                        <label for="celular">Celular</label>
                        <input type="text" id="celular" name="celular">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="cep">CEP</label>
                        <input type="text" id="cep" name="cep" maxlength="9">
                    </div>

                    <div class="form-group">
                        <label for="endereco">Endereco</label>
                        <input type="text" id="endereco" name="endereco">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="bairro">Bairro</label>
                        <input type="text" id="bairro" name="bairro">
                    </div>

                    <div class="form-group">
                        <label for="cidade">Cidade</label>
                        <input type="text" id="cidade" name="cidade">
                    </div>

                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado">
                            <option value="">Selecione</option>
                            <option value="SP">SP</option>
                            <option value="RJ">RJ</option>
                            <option value="MG">MG</option>
                            <option value="ES">ES</option>
                            <option value="PR">PR</option>
                            <option value="SC">SC</option>
                            <option value="RS">RS</option>
                            <option value="BA">BA</option>
                            <option value="PE">PE</option>
                            <option value="CE">CE</option>
                            <option value="RN">RN</option>
                            <option value="PB">PB</option>
                            <option value="AL">AL</option>
                            <option value="SE">SE</option>
                            <option value="PA">PA</option>
                            <option value="AM">AM</option>
                            <option value="RR">RR</option>
                            <option value="AP">AP</option>
                            <option value="TO">TO</option>
                            <option value="DF">DF</option>
                            <option value="AC">AC</option>
                            <option value="RO">RO</option>
                            <option value="MT">MT</option>
                            <option value="MS">MS</option>
                            </option>

                        </select>
                    </div>
                </div>

                <div class=" form-group">
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
    document.getElementById('clienteModal').style.display = 'block';
    document.getElementById('modalTitle').textContent = 'Novo Cliente';
    document.getElementById('formAction').value = 'create';
    document.getElementById('clienteForm').reset();
}

function closeModal() {
    document.getElementById('clienteModal').style.display = 'none';
}

function editCliente(cliente) {
    document.getElementById('clienteModal').style.display = 'block';
    document.getElementById('modalTitle').textContent = 'Editar Cliente';
    document.getElementById('formAction').value = 'update';
    document.getElementById('clienteId').value = cliente.id;
    document.getElementById('nome').value = cliente.nome;
    document.getElementById('email').value = cliente.email || '';
    document.getElementById('telefone').value = cliente.telefone;
    document.getElementById('celular').value = cliente.celular || '';
    document.getElementById('cpf').value = cliente.cpf || '';
    document.getElementById('endereco').value = cliente.endereco || '';
    document.getElementById('bairro').value = cliente.bairro || '';
    document.getElementById('cidade').value = cliente.cidade || '';
    document.getElementById('estado').value = cliente.estado || '';
    document.getElementById('cep').value = cliente.cep || '';
    document.getElementById('observacoes').value = cliente.observacoes || '';
}

// Fechar modal ao clicar fora
document.getElementById('clienteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

<?php include '../includes/footer.php'; ?>