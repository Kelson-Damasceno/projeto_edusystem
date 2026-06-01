<?php
require_once 'includes/auth.php';
require_once 'includes/conexao.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$erro = '';

// Busca aluno
$stmt = $conn->prepare("SELECT * FROM alunos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$aluno = $stmt->get_result()->fetch_assoc();

if (!$aluno) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome']);
    $ra       = trim($_POST['ra']);
    $curso    = trim($_POST['curso']);
    $email    = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);

    // Verifica RA duplicado (exceto o próprio aluno)
    $check = $conn->prepare("SELECT id FROM alunos WHERE ra = ? AND id != ?");
    $check->bind_param("si", $ra, $id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $erro = "Este RA já está cadastrado para outro aluno!";
    } else {
        $sql = "UPDATE alunos SET nome=?, ra=?, curso=?, email=?, telefone=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $nome, $ra, $curso, $email, $telefone, $id);
        if ($stmt->execute()) {
            header("Location: index.php?msg=alterado");
            exit;
        } else {
            $erro = "Erro ao alterar aluno.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Aluno — EduSystem</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --azul: #1a56ff; --azul-escuro: #0a34c7;
            --preto: #0d0d0d; --surface: #141414; --surface2: #1c1c1c;
            --borda: rgba(255,255,255,0.08); --branco: #ffffff; --texto: rgba(255,255,255,0.7);
        }
        body { font-family: 'DM Sans', sans-serif; background: var(--preto); color: var(--branco); min-height: 100vh; }
        nav { background: var(--surface); border-bottom: 1px solid var(--borda); padding: 0 40px; height: 64px; display: flex; align-items: center; justify-content: space-between; }
        .nav-logo { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 17px; display: flex; align-items: center; gap: 10px; }
        .nav-logo span { background: var(--azul); border-radius: 8px; padding: 4px 8px; font-size: 18px; }
        .btn-voltar { background: rgba(255,255,255,0.06); border: 1px solid var(--borda); color: var(--texto); padding: 6px 14px; border-radius: 8px; font-size: 13px; cursor: pointer; text-decoration: none; transition: all 0.2s; }
        .btn-voltar:hover { background: rgba(255,255,255,0.1); color: var(--branco); }
        main { max-width: 640px; margin: 0 auto; padding: 48px 24px; }
        .page-header { margin-bottom: 36px; }
        .page-header h1 { font-family: 'Syne', sans-serif; font-size: 28px; font-weight: 800; }
        .page-header p { color: var(--texto); font-size: 14px; margin-top: 6px; }
        .form-card { background: var(--surface); border: 1px solid var(--borda); border-radius: 16px; padding: 36px; }
        .campo { margin-bottom: 20px; }
        .campo-duplo { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        label { display: block; font-size: 12px; font-weight: 500; color: rgba(255,255,255,0.45); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 8px; }
        input, select { width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--borda); border-radius: 10px; padding: 13px 16px; font-size: 14px; color: var(--branco); font-family: 'DM Sans', sans-serif; outline: none; transition: border-color 0.2s; }
        input:focus, select:focus { border-color: var(--azul); background: rgba(26,86,255,0.07); }
        select option { background: #1c1c1c; }
        .erro { background: rgba(224,49,49,0.12); border: 1px solid rgba(224,49,49,0.3); color: #ff8787; border-radius: 10px; padding: 12px 16px; font-size: 14px; margin-bottom: 20px; }
        .tag-editando { background: rgba(255,165,0,0.1); border: 1px solid rgba(255,165,0,0.2); color: #ffa94d; border-radius: 8px; padding: 8px 14px; font-size: 13px; margin-bottom: 24px; display: inline-flex; align-items: center; gap: 6px; }
        .form-footer { display: flex; gap: 12px; margin-top: 28px; justify-content: flex-end; }
        .btn { border: none; border-radius: 10px; padding: 12px 24px; font-size: 14px; font-weight: 600; font-family: 'Syne', sans-serif; cursor: pointer; text-decoration: none; transition: all 0.2s; }
        .btn-primary { background: var(--azul); color: var(--branco); }
        .btn-primary:hover { background: var(--azul-escuro); }
        .btn-ghost { background: transparent; border: 1px solid var(--borda); color: var(--texto); }
        .btn-ghost:hover { background: var(--surface2); color: var(--branco); }
    </style>
</head>
<body>
<nav>
    <div class="nav-logo"><span>🎓</span> EduSystem</div>
    <a href="index.php" class="btn-voltar">← Voltar</a>
</nav>

<main>
    <div class="page-header">
        <h1>Editar Aluno</h1>
        <p>Altere os dados do aluno abaixo</p>
    </div>

    <div class="form-card">
        <div class="tag-editando"> Editando: <?= htmlspecialchars($aluno['nome']) ?></div>

        <?php if ($erro): ?>
            <div class="erro">⚠️ <?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="campo">
                <label>Nome completo *</label>
                <input type="text" name="nome" required value="<?= htmlspecialchars($_POST['nome'] ?? $aluno['nome']) ?>">
            </div>

            <div class="campo-duplo">
                <div class="campo">
                    <label>RA *</label>
                    <input type="text" name="ra" required value="<?= htmlspecialchars($_POST['ra'] ?? $aluno['ra']) ?>">
                </div>
                <div class="campo">
                    <label>Telefone</label>
                    <input type="text" name="telefone" value="<?= htmlspecialchars($_POST['telefone'] ?? $aluno['telefone']) ?>">
                </div>
            </div>

            <div class="campo">
                <label>Curso *</label>
                <select name="curso" required>
                    <?php
                    $cursos = ['Análise e Desenvolvimento de Sistemas', 'Ciência da Computação', 'Sistemas de Informação', 'Engenharia de Software', 'Redes de Computadores', 'Banco de Dados', 'Inteligência Artificial'];
                    $cursoAtual = $_POST['curso'] ?? $aluno['curso'];
                    foreach ($cursos as $c) {
                        $sel = ($cursoAtual === $c) ? 'selected' : '';
                        echo "<option value=\"$c\" $sel>$c</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="campo">
                <label>E-mail *</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? $aluno['email']) ?>">
            </div>

            <div class="form-footer">
                <a href="index.php" class="btn btn-ghost">Cancelar</a>
                <button type="submit" class="btn btn-primary">Salvar Alterações →</button>
            </div>
        </form>
    </div>
</main>
</body>
</html>
