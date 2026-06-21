<?php
require_once 'auth.php';

if (isAuthenticated()) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = isset($_POST['username']) ? trim($_POST['username']) : '';
    $pass = isset($_POST['password']) ? $_POST['password'] : '';

    if ($user === AUTH_USER && $pass === AUTH_PASS) {
        session_regenerate_id(true);
        $_SESSION['allpneu_auth'] = true;
        header('Location: index.php');
        exit();
    } else {
        $error = 'Identifiant ou mot de passe incorrect.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ALLPNEU 84 — Connexion</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/dist/tabler-icons.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',system-ui,sans-serif;background:#0F172A;min-height:100vh;display:flex;align-items:center;justify-content:center}
.card{background:#fff;border-radius:16px;padding:2.5rem;width:420px;max-width:94vw;box-shadow:0 25px 60px rgba(0,0,0,.4)}
.logo{display:flex;align-items:center;gap:12px;margin-bottom:2rem;justify-content:center}
.logo-icon{width:48px;height:48px;background:#1A56DB;border-radius:12px;display:flex;align-items:center;justify-content:center}
.logo-icon i{color:#fff;font-size:22px}
.logo h1{font-size:22px;font-weight:800;color:#0F172A}
.logo p{font-size:12px;color:#64748b}
h2{font-size:18px;font-weight:700;color:#0F172A;margin-bottom:.4rem}
.subtitle{font-size:13px;color:#64748b;margin-bottom:1.5rem}
.form-group{margin-bottom:1rem}
label{font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:5px}
.input-wrap{position:relative}
.input-wrap i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:16px}
input{width:100%;padding:10px 12px 10px 38px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;color:#111}
input:focus{outline:none;border-color:#1A56DB}
.error{background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;border-radius:8px;padding:.65rem 1rem;font-size:13px;margin-bottom:1rem}
.btn{width:100%;padding:11px;background:#1A56DB;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;margin-top:.5rem}
.btn:hover{background:#1043A5}
.footer{text-align:center;font-size:11px;color:#9ca3af;margin-top:1.5rem}
</style>
</head>
<body>
<div class="card">
  <div class="logo">
    <div class="logo-icon"><i class="ti ti-steering-wheel"></i></div>
    <div><h1>ALLPNEU</h1><p>Gestion atelier & facturation</p></div>
  </div>
  <h2>Connexion</h2>
  <p class="subtitle">Accès réservé au personnel autorisé</p>
  <?php if ($error): ?>
  <div class="error"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>
  <form method="POST" action="login.php">
    <div class="form-group">
      <label for="username">Identifiant</label>
      <div class="input-wrap">
        <i class="ti ti-user"></i>
        <input type="text" id="username" name="username" placeholder="admin" required value="<?php echo htmlspecialchars(isset($_POST['username']) ? $_POST['username'] : ''); ?>"/>
      </div>
    </div>
    <div class="form-group">
      <label for="password">Mot de passe</label>
      <div class="input-wrap">
        <i class="ti ti-lock"></i>
        <input type="password" id="password" name="password" placeholder="••••••••" required/>
      </div>
    </div>
    <button type="submit" class="btn">Se connecter</button>
  </form>
  <p class="footer">ALLO PNEU 84 — Avignon</p>
</div>
</body>
</html>
