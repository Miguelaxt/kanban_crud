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

<form method="post">
    <label>E-mail</label>
    <input type="email" name="email" required>
    <label>Senha</label>
    <input type="password" name="senha" required>
    <button type="submit">Entrar</button>
</form>

<?php if($msg): ?>
    <p><?= htmlspecialchars($msg) ?></p>
<?php endif; ?>
