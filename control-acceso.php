<?php
// control-acceso.php - Panel para administrar el bloqueo
// PROTEGE ESTE ARCHIVO CON CONTRASEÑA

session_start();

// Contraseña del administrador
$ADMIN_PASSWORD = 'DIEGO FERNANDO RANGEL CALDERON 2026 - 1995';

// Verificar autenticación
if (!isset($_SESSION['autenticado']) && (!isset($_POST['password']) || $_POST['password'] !== $ADMIN_PASSWORD)) {
    if (isset($_POST['password'])) {
        $error = 'Contraseña incorrecta';
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Panel de Control</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .login-container {
                background: white;
                padding: 40px;
                border-radius: 10px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                text-align: center;
            }
            input {
                padding: 10px;
                margin: 10px 0;
                width: 100%;
                border: 1px solid #ddd;
                border-radius: 5px;
            }
            button {
                background: #2c3e50;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
            }
            .error {
                color: red;
                margin-top: 10px;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h2>🔐 Panel de Control</h2>
            <form method="POST">
                <input type="password" name="password" placeholder="Contraseña de administrador" required>
                <button type="submit">Ingresar</button>
                <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$_SESSION['autenticado'] = true;

// Procesar acciones
$archivo_config = __DIR__ . '/../config/estado.json';
$mensaje_config = __DIR__ . '/../config/mensaje.json';

// Crear directorio config si no existe
if (!file_exists(__DIR__ . '/../config')) {
    mkdir(__DIR__ . '/../config', 0777, true);
}

// Cargar estado actual
$estado_actual = file_exists($archivo_config) ? json_decode(file_get_contents($archivo_config), true) : ['estado' => 'activo'];
$mensaje_actual = file_exists($mensaje_config) ? json_decode(file_get_contents($mensaje_config), true) : ['mensaje' => 'El sistema está temporalmente bloqueado'];

// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        if ($_POST['accion'] === 'activar') {
            $estado_actual['estado'] = 'activo';
            $estado_actual['fecha'] = date('Y-m-d H:i:s');
            file_put_contents($archivo_config, json_encode($estado_actual, JSON_PRETTY_PRINT));
            $mensaje_exito = "✅ Sistema ACTIVADO correctamente";
        } elseif ($_POST['accion'] === 'desactivar') {
            $estado_actual['estado'] = 'bloqueado';
            $estado_actual['fecha'] = date('Y-m-d H:i:s');
            file_put_contents($archivo_config, json_encode($estado_actual, JSON_PRETTY_PRINT));
            $mensaje_exito = "🔒 Sistema DESACTIVADO correctamente";
        } elseif ($_POST['accion'] === 'guardar_mensaje') {
            $mensaje_actual['mensaje'] = $_POST['mensaje'];
            $mensaje_actual['fecha'] = date('Y-m-d H:i:s');
            file_put_contents($mensaje_config, json_encode($mensaje_actual, JSON_PRETTY_PRINT));
            $mensaje_exito = "📝 Mensaje guardado correctamente";
        }
    }
}

// Verificar estado actual para el .htaccess (archivo simple)
$archivo_estado_txt = __DIR__ . '/../config/estado.txt';
file_put_contents($archivo_estado_txt, $estado_actual['estado']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control - Administración</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .subtitulo {
            color: #666;
            margin-bottom: 30px;
        }

        .estado-actual {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }

        .estado-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: bold;
            margin-top: 10px;
        }

        .estado-activo {
            background: #d4edda;
            color: #155724;
        }

        .estado-bloqueado {
            background: #f8d7da;
            color: #721c24;
        }

        .botones {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 30px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-activar {
            background: #27ae60;
            color: white;
        }

        .btn-activar:hover {
            background: #1e8449;
            transform: scale(1.05);
        }

        .btn-desactivar {
            background: #e74c3c;
            color: white;
        }

        .btn-desactivar:hover {
            background: #c0392b;
            transform: scale(1.05);
        }

        .btn-guardar {
            background: #3498db;
            color: white;
            width: 100%;
        }

        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            resize: vertical;
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #2c3e50;
        }

        .exito {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .info {
            background: #e8f0fe;
            color: #2c3e50;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 14px;
        }

        hr {
            margin: 20px 0;
            border: none;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>🎮 Panel de Control</h1>
            <div class="subtitulo">Administra el acceso a la aplicación</div>

            <?php if (isset($mensaje_exito)): ?>
                <div class="exito"><?php echo $mensaje_exito; ?></div>
            <?php endif; ?>

            <div class="estado-actual">
                <strong>Estado actual del sistema:</strong><br>
                <span class="estado-badge <?php echo $estado_actual['estado'] === 'activo' ? 'estado-activo' : 'estado-bloqueado'; ?>">
                    <?php echo $estado_actual['estado'] === 'activo' ? '🟢 ACTIVO' : '🔴 BLOQUEADO'; ?>
                </span>
                <?php if (isset($estado_actual['fecha'])): ?>
                    <div style="margin-top: 10px; font-size: 12px;">Último cambio: <?php echo $estado_actual['fecha']; ?></div>
                <?php endif; ?>
            </div>

            <div class="botones">
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="accion" value="activar">
                    <button type="submit" class="btn btn-activar">🔓 ACTIVAR SISTEMA</button>
                </form>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="accion" value="desactivar">
                    <button type="submit" class="btn btn-desactivar">🔒 DESACTIVAR SISTEMA</button>
                </form>
            </div>

            <hr>

            <form method="POST">
                <input type="hidden" name="accion" value="guardar_mensaje">
                <label>📝 Mensaje personalizado para usuarios bloqueados:</label>
                <textarea name="mensaje" rows="4" placeholder="Escribe el mensaje que verán los usuarios cuando el sistema esté bloqueado..."><?php echo htmlspecialchars($mensaje_actual['mensaje'] ?? ''); ?></textarea>
                <button type="submit" class="btn btn-guardar">💾 Guardar Mensaje</button>
            </form>

            <div class="info">
                <strong>ℹ️ Información importante:</strong><br>
                • Cuando el sistema está BLOQUEADO, NADIE puede acceder a la página principal.<br>
                • Los datos de los usuarios NO se pierden, solo se oculta el acceso.<br>
                • Para reactivar, simplemente haz clic en "ACTIVAR SISTEMA".<br>
                • El mensaje personalizado se mostrará a todos los usuarios bloqueados.
            </div>
        </div>
    </div>
</body>
</html>