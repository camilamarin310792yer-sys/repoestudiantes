<?php
declare(strict_types=1);
require __DIR__ . '/config.php';
$pdo = db();

$mensaje = '';
$error = '';
$accion = $_GET['accion'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $op = $_POST['op'] ?? '';
    try {
        if ($op === 'guardar') {
            $nombre = trim($_POST['nombre'] ?? '');
            $identificacion = trim($_POST['identificacion'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $id = (int)($_POST['id'] ?? 0);

            if ($nombre === '' || $identificacion === '' || $telefono === '') {
                throw new RuntimeException('Todos los campos son obligatorios.');
            }

            if ($id > 0) {
                $stmt = $pdo->prepare('UPDATE alumnos SET nombre=?, identificacion=?, telefono=? WHERE id=?');
                $stmt->execute([$nombre, $identificacion, $telefono, $id]);
                $mensaje = 'Alumno actualizado correctamente.';
            } else {
                $stmt = $pdo->prepare('INSERT INTO alumnos (nombre, identificacion, telefono) VALUES (?, ?, ?)');
                $stmt->execute([$nombre, $identificacion, $telefono]);
                $mensaje = 'Alumno registrado correctamente.';
            }
        } elseif ($op === 'eliminar') {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $pdo->prepare('DELETE FROM alumnos WHERE id=?');
            $stmt->execute([$id]);
            $mensaje = 'Alumno eliminado correctamente.';
        }
    } catch (PDOException $e) {
        $error = ($e->getCode() === '23000')
            ? 'La identificación ya está registrada.'
            : 'No fue posible completar la operación. Verifica la conexión y los datos.';
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$editar = null;
if ($accion === 'editar') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('SELECT * FROM alumnos WHERE id=?');
    $stmt->execute([$id]);
    $editar = $stmt->fetch();
    if (!$editar) $error = 'El alumno solicitado no existe.';
}

$buscar = trim($_GET['buscar'] ?? '');
if ($buscar !== '') {
    $stmt = $pdo->prepare('SELECT * FROM alumnos WHERE nombre LIKE ? OR identificacion LIKE ? OR telefono LIKE ? ORDER BY nombre');
    $like = "%$buscar%";
    $stmt->execute([$like, $like, $like]);
    $alumnos = $stmt->fetchAll();
} else {
    $alumnos = $pdo->query('SELECT * FROM alumnos ORDER BY nombre')->fetchAll();
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Harvard Hall · Gestión de Alumnos</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="hero">
  <div class="hero-overlay">
    <nav class="nav">
      <div class="brand"><span class="crest">H</span><div><strong>UNIVERSITY HALL</strong><small>Student Management</small></div></div>
      <a class="nav-link" href="index.php">Alumnos</a>
    </nav>
    <div class="hero-copy">
      <p class="eyebrow">REGISTRO ACADÉMICO</p>
      <h1>Bienvenido al<br><em>Hall Universitario</em></h1>
      <p>Administra de forma sencilla la información de tus alumnos.</p>
      <a href="#registro" class="button light">Registrar alumno</a>
    </div>
  </div>
</header>

<main class="container">
  <?php if ($mensaje): ?><div class="alert success"><?=htmlspecialchars($mensaje)?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert error"><?=htmlspecialchars($error)?></div><?php endif; ?>

  <section class="stats">
    <div><span>ALUMNOS</span><strong><?=count($alumnos)?></strong></div>
    <div><span>GESTIÓN</span><strong>CRUD</strong></div>
    <div><span>BASE DE DATOS</span><strong>MySQL</strong></div>
  </section>

  <section class="grid" id="registro">
    <div class="card form-card">
      <div class="card-head">
        <div><p class="eyebrow">ADMISIONES</p><h2><?=$editar ? 'Editar alumno' : 'Nuevo alumno'?></h2></div>
        <span class="seal">✦</span>
      </div>
      <form method="post">
        <input type="hidden" name="op" value="guardar">
        <input type="hidden" name="id" value="<?=htmlspecialchars((string)($editar['id'] ?? 0))?>">
        <label>Nombre completo
          <input name="nombre" required maxlength="150" value="<?=htmlspecialchars($editar['nombre'] ?? '')?>" placeholder="Ej. María Fernanda López">
        </label>
        <label>Identificación
          <input name="identificacion" required maxlength="50" value="<?=htmlspecialchars($editar['identificacion'] ?? '')?>" placeholder="Número de identificación">
        </label>
        <label>Teléfono
          <input name="telefono" required maxlength="30" value="<?=htmlspecialchars($editar['telefono'] ?? '')?>" placeholder="300 000 0000">
        </label>
        <div class="actions">
          <button class="button" type="submit"><?=$editar ? 'Guardar cambios' : 'Registrar alumno'?></button>
          <?php if ($editar): ?><a class="button ghost" href="index.php">Cancelar</a><?php endif; ?>
        </div>
      </form>
    </div>

    <div class="card list-card">
      <div class="card-head">
        <div><p class="eyebrow">DIRECTORIO</p><h2>Alumnos registrados</h2></div>
      </div>
      <form class="search" method="get">
        <input name="buscar" value="<?=htmlspecialchars($buscar)?>" placeholder="Buscar por nombre, identificación o teléfono">
        <button class="button" type="submit">Buscar</button>
        <?php if ($buscar): ?><a href="index.php" class="clear">Limpiar</a><?php endif; ?>
      </form>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Nombre</th><th>Identificación</th><th>Teléfono</th><th>Acciones</th></tr></thead>
          <tbody>
          <?php foreach ($alumnos as $alumno): ?>
            <tr>
              <td><strong><?=htmlspecialchars($alumno['nombre'])?></strong></td>
              <td><?=htmlspecialchars($alumno['identificacion'])?></td>
              <td><?=htmlspecialchars($alumno['telefono'])?></td>
              <td class="row-actions">
                <a href="?accion=editar&id=<?=$alumno['id']?>#registro">Editar</a>
                <form method="post" onsubmit="return confirm('¿Eliminar este alumno?');">
                  <input type="hidden" name="op" value="eliminar">
                  <input type="hidden" name="id" value="<?=$alumno['id']?>">
                  <button type="submit" class="delete">Eliminar</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$alumnos): ?><tr><td colspan="4" class="empty">No hay alumnos registrados.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</main>
<footer>University Hall · Sistema de Gestión de Alumnos · PHP + MySQL</footer>
</body>
</html>
