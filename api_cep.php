<?php
$endereco = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cep = preg_replace('/\D/', '', $_POST['cep']);
    $url = "https://viacep.com.br/ws/$cep/json/";
    $res = file_get_contents($url);
    $endereco = json_decode($res, true);
}
?>
<form method="post">
    <label>CEP:</label>
    <input type="text" name="cep" required>
    <button type="submit">Consultar</button>
</form>

<?php if($endereco): ?>
<p>Logradouro: <?= htmlspecialchars($endereco['logradouro'] ?? '') ?></p>
<p>Bairro: <?= htmlspecialchars($endereco['bairro'] ?? '') ?></p>
<p>Cidade: <?= htmlspecialchars($endereco['localidade'] ?? '') ?></p>
<p>Estado: <?= htmlspecialchars($endereco['uf'] ?? '') ?></p>
<?php endif; ?>
