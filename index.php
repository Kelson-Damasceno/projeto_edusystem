<?php
require_once 'includes/auth.php';
require_once 'includes/conexao.php';

$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$mensagem = isset($_GET['msg']) ? $_GET['msg'] : '';

if ($busca !== '') {
    $sql = "SELECT * FROM alunos WHERE nome LIKE ? OR ra LIKE ? OR curso LIKE ? ORDER BY nome ASC";
    $stmt = $conn->prepare($sql);
    $like = "%$busca%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $alunos = $stmt->get_result();
} else {
    $alunos = $conn->query("SELECT * FROM alunos ORDER BY nome ASC");
}
$total = $conn->query("SELECT COUNT(*) as total FROM alunos")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduSystem — Alunos</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --azul: #1a56ff;
            --azul-escuro: #0a34c7;
            --preto: #0d0d0d;
            --surface: #141414;
            --surface2: #1c1c1c;
            --borda: rgba(255,255,255,0.08);
            --branco: #ffffff;
            --texto: rgba(255,255,255,0.7);
            --sucesso: #2f9e44;
            --erro: #e03131;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--preto);
            color: var(--branco);
            min-height: 100vh;
        }

        /* NAVBAR */
        nav {
            background: var(--surface);
            border-bottom: 1px solid var(--borda);
            padding: 0 40px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky; top: 0; z-index: 100;
        }

        .nav-logo {
            display: flex; align-items: center; gap: 10px;
            font-family: 'Syne', sans-serif;
            font-weight: 700; font-size: 17px;
        }

        .nav-logo span {
            background: var(--azul);
            border-radius: 8px;
            padding: 4px 8px;
            font-size: 18px;
            line-height: 1;
        }

        .nav-info {
            display: flex; align-items: center; gap: 20px;
            font-size: 14px; color: var(--texto);
        }

        .nav-usuario { display: flex; align-items: center; gap: 8px; }
        .nav-avatar {
            width: 32px; height: 32px;
            background: var(--azul);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 13px;
        }

        .btn-logout {
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--borda);
            color: var(--texto);
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-logout:hover { background: rgba(255,255,255,0.1); color: var(--branco); }

        /* CONTEÚDO */
        main { max-width: 1100px; margin: 0 auto; padding: 40px 24px; }

        .page-header {
            display: flex; align-items: flex-end; justify-content: space-between;
            margin-bottom: 32px; flex-wrap: wrap; gap: 16px;
        }

        .page-header h1 {
            font-family: 'Syne', sans-serif;
            font-size: 32px; font-weight: 800;
        }

        .page-header p { color: var(--texto); font-size: 14px; margin-top: 4px; }

        .badge {
            background: rgba(26,86,255,0.15);
            border: 1px solid rgba(26,86,255,0.3);
            color: #7ea8ff;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 13px;
            font-weight: 500;
        }

        /* BARRA DE AÇÕES */
        .acoes {
            display: flex; gap: 12px; align-items: center;
            margin-bottom: 24px; flex-wrap: wrap;
        }

        .busca-form { display: flex; gap: 8px; flex: 1; min-width: 200px; }

        .busca-input {
            flex: 1;
            background: var(--surface);
            border: 1px solid var(--borda);
            border-radius: 10px;
            padding: 10px 16px;
            font-size: 14px;
            color: var(--branco);
            font-family: 'DM Sans', sans-serif;
            outline: none;
            transition: border-color 0.2s;
        }
        .busca-input:focus { border-color: var(--azul); }
        .busca-input::placeholder { color: rgba(255,255,255,0.25); }

        .btn {
            border: none; border-radius: 10px;
            padding: 10px 20px; font-size: 14px; font-weight: 600;
            font-family: 'Syne', sans-serif;
            cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 6px;
            transition: all 0.2s;
        }

        .btn-primary { background: var(--azul); color: var(--branco); }
        .btn-primary:hover { background: var(--azul-escuro); }

        .btn-secondary {
            background: var(--surface);
            border: 1px solid var(--borda);
            color: var(--texto);
        }
        .btn-secondary:hover { background: var(--surface2); color: var(--branco); }

        .btn-danger { background: rgba(224,49,49,0.15); color: #ff6b6b; border: 1px solid rgba(224,49,49,0.2); }
        .btn-danger:hover { background: rgba(224,49,49,0.25); }

        .btn-edit { background: rgba(26,86,255,0.12); color: #7ea8ff; border: 1px solid rgba(26,86,255,0.2); }
        .btn-edit:hover { background: rgba(26,86,255,0.2); }

        .btn-sm { padding: 6px 14px; font-size: 13px; }

        /* NOTIFICAÇÃO */
        .notif {
            border-radius: 10px;
            padding: 12px 18px;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
        }
        .notif-ok { background: rgba(47,158,68,0.12); border: 1px solid rgba(47,158,68,0.25); color: #8ce99a; }
        .notif-erro { background: rgba(224,49,49,0.12); border: 1px solid rgba(224,49,49,0.25); color: #ff8787; }

        /* TABELA */
        .tabela-wrapper {
            background: var(--surface);
            border: 1px solid var(--borda);
            border-radius: 16px;
            overflow: hidden;
        }

        table { width: 100%; border-collapse: collapse; }

        thead {
            background: rgba(255,255,255,0.03);
            border-bottom: 1px solid var(--borda);
        }

        th {
            padding: 14px 20px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(255,255,255,0.35);
        }

        td { padding: 16px 20px; font-size: 14px; color: var(--texto); border-bottom: 1px solid var(--borda); }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(255,255,255,0.02); }

        .td-nome { font-weight: 500; color: var(--branco); }
        .td-ra {
            font-family: monospace;
            font-size: 13px;
            background: rgba(26,86,255,0.1);
            color: #7ea8ff;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-block;
        }

        .acoes-td { display: flex; gap: 8px; }

        /* VAZIO */
        .vazio {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255,255,255,0.25);
        }
        .vazio-icone { font-size: 48px; margin-bottom: 12px; }
        .vazio p { font-size: 15px; }
    </style>
</head>
<body>

<nav>
    <div class="nav-logo">
        <span>🎓</span> EduSystem
    </div>
    <div class="nav-info">
        <div class="nav-usuario">
            <div class="nav-avatar"><?= strtoupper(substr($_SESSION['usuario'], 0, 1)) ?></div>
            <?= htmlspecialchars($_SESSION['usuario']) ?>
        </div>
        <a href="logout.php" class="btn-logout">Sair</a>
    </div>
</nav>

<main>
    <div class="page-header">
        <div>
            <h1>Alunos <span class="badge"><?= $total ?> cadastrados</span></h1>
            <p>Gerencie os alunos da instituição</p>
        </div>
        <a href="novo.php" class="btn btn-primary">+ Novo Aluno</a>
    </div>

    <?php if ($mensagem === 'incluido'): ?>
        <div class="notif notif-ok"> Aluno incluído com sucesso!</div>
    <?php elseif ($mensagem === 'alterado'): ?>
        <div class="notif notif-ok"> Aluno alterado com sucesso!</div>
    <?php elseif ($mensagem === 'excluido'): ?>
        <div class="notif notif-ok"> Aluno excluído com sucesso!</div>
    <?php elseif ($mensagem === 'ra_duplicado'): ?>
        <div class="notif notif-erro"> Erro: RA já cadastrado no sistema.</div>
    <?php endif; ?>

    <div class="acoes">
        <form class="busca-form" method="GET">
            <input class="busca-input" type="text" name="busca" placeholder="    Buscar por nome, RA ou curso..." value="<?= htmlspecialchars($busca) ?>">
            <button type="submit" class="btn btn-secondary">Buscar</button>
            <?php if ($busca): ?><a href="index.php" class="btn btn-secondary">✕</a><?php endif; ?>
        </form>
    </div>

    <div class="tabela-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>RA</th>
                    <th>Curso</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($alunos->num_rows > 0): ?>
                    <?php while ($a = $alunos->fetch_assoc()): ?>
                    <tr>
                        <td class="td-nome"><?= htmlspecialchars($a['nome']) ?></td>
                        <td><span class="td-ra"><?= htmlspecialchars($a['ra']) ?></span></td>
                        <td><?= htmlspecialchars($a['curso']) ?></td>
                        <td><?= htmlspecialchars($a['email']) ?></td>
                        <td><?= htmlspecialchars($a['telefone']) ?></td>
                        <td>
                            <div class="acoes-td">
                                <a href="editar.php?id=<?= $a['id'] ?>" class="btn btn-edit btn-sm"> Editar</a>
                                <a href="excluir.php?id=<?= $a['id'] ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Tem certeza que deseja excluir <?= htmlspecialchars($a['nome']) ?>?')"> Excluir</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">
                        <div class="vazio">
                            <div class="vazio-icone">🎓</div>
                            <p><?= $busca ? 'Nenhum aluno encontrado para essa busca.' : 'Nenhum aluno cadastrado ainda.' ?></p>
                        </div>
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

</body>
</html>
