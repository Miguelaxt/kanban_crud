<?php
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
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <title>Kanban - Menu</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Industria - Sistema de Tarefas</h1>
        </header>
    </div>
    <nav>
        <div class="usuario">
            <a href="cadastro_usuario.php">Cadastro de Usuários</a>
        </div>

        <div class="tarefa">
            <a href="cadastro_tarefa.php">Cadastro de Tarefas</a>
        </div>

        <div class="gerenciar">
            <a href="gerenciar_tarefas.php">Gerenciar Tarefas</a>
        </div>
    </nav>
</body>

</html>