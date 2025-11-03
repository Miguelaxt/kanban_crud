<?php
require 'db.php';

$msg = "";
$msgType = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? "");
    $email = trim($_POST['email'] ?? "");

    if ($nome === "" || $email === "") {
        $msg = "Todos os campos são obrigatórios.";
        $msgType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "E-mail inválido.";
        $msgType = "error";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $msg = "E-mail já cadastrado.";
            $msgType = "error";
        } else {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email) VALUES (?, ?)");
            if ($stmt->execute([$nome, $email])) {
                header("Location: cadastro_usuario.php?msg=ok");
                exit;
            } else {
                $msg = "Erro ao cadastrar.";
                $msgType = "error";
            }
        }
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'ok') {
    $msg = "Cadastro concluído com sucesso.";
    $msgType = "success";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Cadastro de Usuários</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <header>
      <h2>Cadastro de Usuários</h2>
      <nav><a href="index.php">Voltar ao Menu</a></nav>
    </header>

    <?php if($msg): ?>
      <div class="msg <?php echo $msgType==='success'?'success':'error'; ?>"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <form method="post" action="cadastro_usuario.php" novalidate>
      <label>Nome</label>
      <input type="text" name="nome" required>

      <label>E-mail</label>
      <input type="email" name="email" required>

      <button type="submit">Cadastrar</button>
    </form>
  </div>
</body>
</html>
