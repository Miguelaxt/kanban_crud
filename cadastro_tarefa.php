<?php
require 'db.php';

$msg = "";
$msgType = "";

$usuarios = $pdo->query("SELECT id, nome FROM usuarios ORDER BY nome ASC")->fetchAll();

$editMode = false;
$task = [
    'id' => '',
    'usuario_id' => '',
    'descricao' => '',
    'setor' => '',
    'prioridade' => 'Baixa',
    'status' => 'A Fazer'
];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $editMode = true;
    $stmt = $pdo->prepare("SELECT * FROM tarefas WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $row = $stmt->fetch();
    if ($row) {
        $task = $row;
    } else {
        $msg = "Tarefa não encontrada.";
        $msgType = "error";
        $editMode = false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? "";
    $usuario_id = $_POST['usuario_id'] ?? "";
    $descricao = trim($_POST['descricao'] ?? "");
    $setor = trim($_POST['setor'] ?? "");
    $prioridade = $_POST['prioridade'] ?? "";
    $status = $_POST['status'] ?? "A Fazer";

    if ($usuario_id === "" || $descricao === "" || $setor === "" || $prioridade === "" ) {
        $msg = "Todos os campos são obrigatórios.";
        $msgType = "error";
    } else {
        if ($id !== "") {
            $stmt = $pdo->prepare("UPDATE tarefas SET usuario_id = ?, descricao = ?, setor = ?, prioridade = ?, status = ? WHERE id = ?");
            if ($stmt->execute([$usuario_id, $descricao, $setor, $prioridade, $status, $id])) {
                header("Location: cadastro_tarefa.php?msg=ok&action=update");
                exit;
            } else {
                $msg = "Erro ao atualizar tarefa.";
                $msgType = "error";
            }
        } else {
            $stmt = $pdo->prepare("INSERT INTO tarefas (usuario_id, descricao, setor, prioridade, status) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$usuario_id, $descricao, $setor, $prioridade, $status])) {
                header("Location: cadastro_tarefa.php?msg=ok&action=create");
                exit;
            } else {
                $msg = "Erro ao cadastrar tarefa.";
                $msgType = "error";
            }
        }
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'ok') {
    $action = $_GET['action'] ?? 'create';
    $msg = ($action === 'create') ? "Cadastro concluído com sucesso." : "Atualização concluída com sucesso.";
    $msgType = "success";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title><?php echo $editMode?'Editar Tarefa':'Cadastro de Tarefas'; ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <header>
      <h2><?php echo $editMode?'Editar Tarefa':'Cadastro de Tarefas'; ?></h2>
      <nav><a href="index.php">Voltar ao Menu</a> | <a href="gerenciar_tarefas.php">Gerenciar Tarefas</a></nav>
    </header>

    <?php if($msg): ?>
      <div class="msg <?php echo $msgType==='success'?'success':'error'; ?>"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <form method="post" action="cadastro_tarefa.php">
      <input type="hidden" name="id" value="<?php echo htmlspecialchars($task['id']); ?>">

      <label>Usuário</label>
      <select name="usuario_id" required>
        <option value="">Selecione</option>
        <?php foreach($usuarios as $u): ?>
          <option value="<?php echo $u['id']; ?>" <?php echo ($u['id']==$task['usuario_id'])?'selected':''; ?>>
            <?php echo htmlspecialchars($u['nome']); ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>Descrição</label>
      <textarea name="descricao" rows="4" required><?php echo htmlspecialchars($task['descricao']); ?></textarea>

      <label>Setor</label>
      <input type="text" name="setor" value="<?php echo htmlspecialchars($task['setor']); ?>" required>

      <label>Prioridade</label>
      <select name="prioridade" required>
        <option value="Baixa" <?php echo ($task['prioridade']=='Baixa')?'selected':''; ?>>Baixa</option>
        <option value="Média" <?php echo ($task['prioridade']=='Média')?'selected':''; ?>>Média</option>
        <option value="Alta" <?php echo ($task['prioridade']=='Alta')?'selected':''; ?>>Alta</option>
      </select>

      <label>Status</label>
      <select name="status" required>
        <option value="A Fazer" <?php echo ($task['status']=='A Fazer')?'selected':''; ?>>A Fazer</option>
        <option value="Fazendo" <?php echo ($task['status']=='Fazendo')?'selected':''; ?>>Fazendo</option>
        <option value="Pronto" <?php echo ($task['status']=='Pronto')?'selected':''; ?>>Pronto</option>
      </select>

      <button type="submit"><?php echo $editMode?'Salvar Alterações':'Cadastrar Tarefa'; ?></button>
    </form>
  </div>
</body>
</html>
