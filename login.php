<?php
session_start();
require_once 'includes/conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $senha = md5(trim($_POST['senha']));

    $sql = "SELECT * FROM usuarios WHERE usuario = ? AND senha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usuario, $senha);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['logado'] = true;
        $_SESSION['usuario'] = $usuario;
        header("Location: index.php");
        exit;
    } else {
        $erro = "Usuário ou senha incorretos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistema de Alunos</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --azul: #1a56ff;
            --azul-escuro: #0a34c7;
            --preto: #0d0d0d;
            --cinza: #f2f2f2;
            --branco: #ffffff;
            --erro: #e03131;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--preto);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Grade de fundo animada */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(26,86,255,0.07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(26,86,255,0.07) 1px, transparent 1px);
            background-size: 48px 48px;
            animation: grade 20s linear infinite;
        }

        @keyframes grade {
            0% { background-position: 0 0; }
            100% { background-position: 48px 48px; }
        }

        .card {
            position: relative;
            background: #141414;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            padding: 52px 48px;
            width: 420px;
            box-shadow: 0 40px 80px rgba(0,0,0,0.6), 0 0 0 1px rgba(26,86,255,0.15);
            animation: surgir 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes surgir {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 36px;
        }

        .logo-icone {
            width: 44px; height: 44px;
            background: var(--azul);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
        }

        .logo h1 {
            font-family: 'Syne', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--branco);
            line-height: 1.2;
        }

        .logo span {
            display: block;
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            font-weight: 400;
            color: rgba(255,255,255,0.4);
        }

        h2 {
            font-family: 'Syne', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: var(--branco);
            margin-bottom: 6px;
        }

        .subtitulo {
            font-size: 14px;
            color: rgba(255,255,255,0.4);
            margin-bottom: 32px;
        }

        .campo {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 12px;
            font-weight: 500;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 8px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 15px;
            color: var(--branco);
            font-family: 'DM Sans', sans-serif;
            transition: border-color 0.2s, background 0.2s;
            outline: none;
        }

        input:focus {
            border-color: var(--azul);
            background: rgba(26,86,255,0.08);
        }

        .erro {
            background: rgba(224,49,49,0.12);
            border: 1px solid rgba(224,49,49,0.3);
            color: #ff8787;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 14px;
            margin-bottom: 18px;
        }

        .btn-entrar {
            width: 100%;
            background: var(--azul);
            color: var(--branco);
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Syne', sans-serif;
            cursor: pointer;
            margin-top: 8px;
            transition: background 0.2s, transform 0.1s;
        }

        .btn-entrar:hover { background: var(--azul-escuro); }
        .btn-entrar:active { transform: scale(0.98); }

        .dica {
            text-align: center;
            font-size: 12px;
            color: rgba(255,255,255,0.2);
            margin-top: 24px;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">
        <div class="logo-icone">🎓</div>
        <h1>EduSystem <span>UNINOVE</span></h1>
    </div>

    <h2>Bem-vindo</h2>
    <p class="subtitulo">Faça login para acessar o sistema</p>

    <?php if ($erro): ?>
        <div class="erro">⚠️ <?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="campo">
            <label>Usuário</label>
            <input type="text" name="usuario" placeholder="Digite seu usuário" required autofocus>
        </div>
        <div class="campo">
            <label>Senha</label>
            <input type="password" name="senha" placeholder="Digite sua senha" required>
        </div>
        <button type="submit" class="btn-entrar">Entrar →</button>
    </form>

    <p class="dica">Login padrão: admin / 1234</p>
</div>
</body>
</html>
