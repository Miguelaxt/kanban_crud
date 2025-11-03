<?php
session_start();
require 'db.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nome'];
        header("Location: gerenciar_tarefas.php");
        exit;
    } else {
        $msg = "E-mail ou senha incorretos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Login - Kanban</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h2>Login</h2>
        </header>
        <form method="post">
            <label>E-mail</label>
            <input type="email" name="email" required>
            <label>Senha</label>
            <input type="password" name="senha" required>
            <button type="submit">Entrar</button>
        </form>
        <?php if($msg): ?>
            <div class="msg error"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>