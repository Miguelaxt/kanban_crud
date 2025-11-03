<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$msg = "";
$msgType = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_inline') {
    $id = $_POST['id'] ?? "";
    $status = $_POST['status'] ?? "";
    $prioridade = $_POST['prioridade'] ?? "";

    if ($id !== "" && $status !== "" && $prioridade !== "") {
        $stmt = $pdo->prepare("UPDATE tarefas SET status = ?, prioridade = ? WHERE id = ?");
        if ($stmt->execute([$status, $prioridade, $id])) {
            $msg = "Tarefa atualizada.";
            $msgType = "success";
        } else {
            $msg = "Erro ao atualizar tarefa.";
            $msgType = "error";
        }
    } else {
        $msg = "Dados incompletos para atualização.";
        $msgType = "error";
    }
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM tarefas WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: gerenciar_tarefas.php?msg=deleted");
        exit;
    } else {
        $msg = "Erro ao excluir.";
        $msgType = "error";
    }
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'deleted') { $msg = "Tarefa excluída."; $msgType = "success"; }
}

$user_id = $_SESSION['user_id'];

$tarefas = $pdo->prepare("
    SELECT t.*, u.nome AS usuario_nome
    FROM tarefas t
    JOIN usuarios u ON t.usuario_id = u.id
    WHERE t.usuario_id = ?
    ORDER BY t.data_cadastro ASC
");
$tarefas->execute([$user_id]);
$tasks = $tarefas->fetchAll();

$kanban = ['A Fazer'=>[], 'Fazendo'=>[], 'Pronto'=>[]];
foreach ($tasks as $t) {
    $s = $t['status'] ?? 'A Fazer';
    if (!isset($kanban[$s])) $s = 'A Fazer';
    $kanban[$s][] = $t;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Gerenciar Tarefas</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <header>
      <h2>Gerenciamento de Tarefas</h2>
      <nav><a href="index.php">Menu</a> | <a href="cadastro_tarefa.php">Nova Tarefa</a> | <a href="cadastro_usuario.php">Novo Usuário</a></nav>
    </header>

    <?php if($msg): ?>
      <div class="msg <?php echo $msgType==='success'?'success':'error'; ?>"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <div class="kanban">
      <?php foreach ($kanban as $status => $cards): ?>
        <div class="column">
          <h3><?php echo $status; ?></h3>

          <?php if(empty($cards)): ?>
            <p style="color:#666">Nenhuma tarefa.</p>
          <?php endif; ?>

          <?php foreach ($cards as $c): ?>
            <div class="card">
              <div><strong>Descrição:</strong> <?php echo nl2br(htmlspecialchars($c['descricao'])); ?></div>
              <div class="meta"><strong>Setor:</strong> <?php echo htmlspecialchars($c['setor']); ?></div>
              <div class="meta"><strong>Prioridade:</strong> <?php echo htmlspecialchars($c['prioridade']); ?></div>
              <div class="meta"><strong>Usuário:</strong> <?php echo htmlspecialchars($c['usuario_nome']); ?></div>
              <div class="meta"><strong>Cadastrada em:</strong> <?php echo htmlspecialchars($c['data_cadastro']); ?></div>

              <div class="card-actions">
                <form method="post" style="display:inline;">
                  <input type="hidden" name="action" value="update_inline">
                  <input type="hidden" name="id" value="<?php echo $c['id']; ?>">

                  <label>Status</label>
                  <select name="status" class="small">
                    <option value="A Fazer" <?php echo ($c['status']=='A Fazer')?'selected':''; ?>>A Fazer</option>
                    <option value="Fazendo" <?php echo ($c['status']=='Fazendo')?'selected':''; ?>>Fazendo</option>
                    <option value="Pronto" <?php echo ($c['status']=='Pronto')?'selected':''; ?>>Pronto</option>
                  </select>

                  <label>Prioridade</label>
                  <select name="prioridade" class="small">
                    <option value="Baixa" <?php echo ($c['prioridade']=='Baixa')?'selected':''; ?>>Baixa</option>
                    <option value="Média" <?php echo ($c['prioridade']=='Média')?'selected':''; ?>>Média</option>
                    <option value="Alta" <?php echo ($c['prioridade']=='Alta')?'selected':''; ?>>Alta</option>
                  </select>

                  <button type="submit" class="small">Atualizar</button>
                </form>

                <a class="small btn-edit" href="cadastro_tarefa.php?id=<?php echo $c['id']; ?>" style="text-decoration:none; color:white;">Editar</a>

                <a class="small btn-danger" href="gerenciar_tarefas.php?delete=<?php echo $c['id']; ?>" onclick="return confirm('Deseja realmente excluir essa tarefa?');" style="text-decoration:none; color:white;">Excluir</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
