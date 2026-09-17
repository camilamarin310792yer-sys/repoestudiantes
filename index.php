<?php
require_once "config.php";

$mensaje = "";
$tipo = "success";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? "";

    try {
        if ($accion === "crear") {
            $nombre = trim($_POST["nombre"] ?? "");
            $identificacion = trim($_POST["identificacion"] ?? "");
            $telefono = trim($_POST["telefono"] ?? "");

            if ($nombre === "" || $identificacion === "" || $telefono === "") {
                throw new Exception("Completa todos los campos.");
            }

            $stmt = $conexion->prepare("INSERT INTO alumnos (nombre, identificacion, telefono) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nombre, $identificacion, $telefono);
            $stmt->execute();
            $stmt->close();

            $mensaje = "Alumno registrado correctamente.";
        }

        if ($accion === "editar") {
            $id = (int)($_POST["id"] ?? 0);
            $nombre = trim($_POST["nombre"] ?? "");
            $identificacion = trim($_POST["identificacion"] ?? "");
            $telefono = trim($_POST["telefono"] ?? "");

            if ($id <= 0 || $nombre === "" || $identificacion === "" || $telefono === "") {
                throw new Exception("Datos de edición inválidos.");
            }

            $stmt = $conexion->prepare("UPDATE alumnos SET nombre=?, identificacion=?, telefono=? WHERE id=?");
            $stmt->bind_param("sssi", $nombre, $identificacion, $telefono, $id);
            $stmt->execute();
            $stmt->close();

            $mensaje = "Alumno actualizado correctamente.";
        }

        if ($accion === "eliminar") {
            $id = (int)($_POST["id"] ?? 0);

            $stmt = $conexion->prepare("DELETE FROM alumnos WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            $mensaje = "Alumno eliminado correctamente.";
        }
    } catch (mysqli_sql_exception $e) {
        $mensaje = ($e->getCode() === 1062)
            ? "La identificación ya está registrada."
            : "No se pudo completar la operación: " . $e->getMessage();
        $tipo = "error";
    } catch (Exception $e) {
        $mensaje = $e->getMessage();
        $tipo = "error";
    }
}

$alumnos = [];
$resultado = $conexion->query("SELECT id, nombre, identificacion, telefono, creado_en FROM alumnos ORDER BY id DESC");
while ($fila = $resultado->fetch_assoc()) {
    $alumnos[] = $fila;
}
$resultado->free();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harvard Hall · Gestión de Alumnos</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
<header class="hero">
    <div class="hero-overlay"></div>
    <nav class="navbar">
        <div class="brand">
            <div class="crest">H</div>
            <div>
                <strong>HARVARD HALL</strong>
                <span>Academic Reception</span>
            </div>
        </div>
        <div class="nav-pill">Sistema de alumnos</div>
    </nav>

    <div class="hero-content">
        <p class="eyebrow">WELCOME TO THE ACADEMIC HALL</p>
        <h1>Gestión de<br><span>Alumnos</span></h1>
        <p class="intro">Un espacio digital elegante para registrar, consultar, editar y administrar la comunidad estudiantil.</p>
        <a href="#registro" class="gold-button">Registrar alumno <span>→</span></a>
    </div>
</header>

<main>
    <?php if ($mensaje !== ""): ?>
        <div class="alert <?= $tipo ?>">
            <span><?= $tipo === "success" ? "✓" : "!" ?></span>
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <section class="welcome-section">
        <div class="section-copy">
            <p class="eyebrow dark">RECEPTION · 01</p>
            <h2>Bienvenido al <em>Hall</em></h2>
            <p>Administra la información básica de los alumnos desde una interfaz inspirada en los grandes halls universitarios: sobria, cálida y moderna.</p>
            <div class="stats">
                <div><b><?= count($alumnos) ?></b><span>Alumnos registrados</span></div>
                <div><b>24/7</b><span>Acceso al sistema</span></div>
                <div><b>100%</b><span>Gestión digital</span></div>
            </div>
        </div>
        <div class="hall-card">
            <div class="hall-frame">
                <div class="arch"></div>
                <div class="lamp"></div>
                <div class="hall-sign">ACADEMIC<br><small>RECEPTION</small></div>
            </div>
        </div>
    </section>

    <section id="registro" class="panel-section">
        <div class="panel-heading">
            <div>
                <p class="eyebrow dark">STUDENT SERVICES · 02</p>
                <h2>Registrar un alumno</h2>
            </div>
            <span class="seal">HH</span>
        </div>

        <form method="POST" class="student-form">
            <input type="hidden" name="accion" value="crear">
            <label>
                <span>Nombre completo</span>
                <input type="text" name="nombre" placeholder="Ej. Ana María Torres" required>
            </label>
            <label>
                <span>Identificación</span>
                <input type="text" name="identificacion" placeholder="Ej. 1002456789" required>
            </label>
            <label>
                <span>Teléfono</span>
                <input type="tel" name="telefono" placeholder="Ej. 300 123 4567" required>
            </label>
            <button class="primary-button" type="submit">+ Registrar alumno</button>
        </form>
    </section>

    <section class="directory-section">
        <div class="directory-header">
            <div>
                <p class="eyebrow dark">DIRECTORY · 03</p>
                <h2>Directorio de alumnos</h2>
            </div>
            <div class="directory-count"><?= count($alumnos) ?> registros</div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Alumno</th>
                        <th>Identificación</th>
                        <th>Teléfono</th>
                        <th>Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!$alumnos): ?>
                    <tr><td colspan="5" class="empty">Aún no hay alumnos registrados. Usa el formulario superior para comenzar.</td></tr>
                <?php else: ?>
                    <?php foreach ($alumnos as $alumno): ?>
                        <tr>
                            <td>
                                <div class="person">
                                    <div class="avatar"><?= strtoupper(substr($alumno["nombre"], 0, 1)) ?></div>
                                    <strong><?= htmlspecialchars($alumno["nombre"]) ?></strong>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($alumno["identificacion"]) ?></td>
                            <td><?= htmlspecialchars($alumno["telefono"]) ?></td>
                            <td><?= date("d/m/Y", strtotime($alumno["creado_en"])) ?></td>
                            <td>
                                <div class="actions">
                                    <button type="button" class="edit-button" onclick='abrirEditar(<?= json_encode($alumno, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>)'>Editar</button>
                                    <form method="POST" onsubmit="return confirm('¿Eliminar este alumno?');">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?= (int)$alumno["id"] ?>">
                                        <button class="delete-button" type="submit">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<footer>
    <div class="footer-crest">H</div>
    <div><strong>HARVARD HALL</strong><br><span>Student Management System</span></div>
    <p>Diseñado para una experiencia académica elegante.</p>
</footer>

<div id="modal" class="modal">
    <div class="modal-box">
        <button class="close" onclick="cerrarEditar()">×</button>
        <p class="eyebrow dark">STUDENT SERVICES</p>
        <h2>Editar alumno</h2>
        <form method="POST">
            <input type="hidden" name="accion" value="editar">
            <input type="hidden" id="edit_id" name="id">
            <label><span>Nombre completo</span><input id="edit_nombre" name="nombre" required></label>
            <label><span>Identificación</span><input id="edit_identificacion" name="identificacion" required></label>
            <label><span>Teléfono</span><input id="edit_telefono" name="telefono" required></label>
            <button class="primary-button" type="submit">Guardar cambios</button>
        </form>
    </div>
</div>

<script>
function abrirEditar(alumno) {
    document.getElementById("edit_id").value = alumno.id;
    document.getElementById("edit_nombre").value = alumno.nombre;
    document.getElementById("edit_identificacion").value = alumno.identificacion;
    document.getElementById("edit_telefono").value = alumno.telefono;
    document.getElementById("modal").classList.add("show");
}
function cerrarEditar() {
    document.getElementById("modal").classList.remove("show");
}
window.addEventListener("click", e => {
    if (e.target.id === "modal") cerrarEditar();
});
</script>
</body>
</html>
