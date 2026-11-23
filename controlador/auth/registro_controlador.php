<?php
// ==============================================================
// CONTROLADOR: Registro Público de Clientes / Pasajeros - KEBA
// ==============================================================

require_once dirname(dirname(__DIR__)) . '/configuracion/constantes.php';
require_once dirname(dirname(__DIR__)) . '/configuracion/conexion.php';
require_once dirname(dirname(__DIR__)) . '/configuracion/session.php';
require_once dirname(dirname(__DIR__)) . '/ayudantes/funciones.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = trim($_POST['dni'] ?? '');
    $nombres = trim($_POST['nombres'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $passwordConfirm = trim($_POST['password_confirm'] ?? '');

    // Si el usuario no fue especificado, usar el DNI
    if (empty($usuario)) {
        $usuario = $dni;
    }

    // Validaciones
    if (empty($dni) || empty($nombres) || empty($apellidos) || empty($password)) {
        setFlash('danger', 'Por favor completa todos los campos obligatorios (*).');
        redireccionar('vista/auth/registro.php');
    }

    if (strlen($dni) !== 8 || !ctype_digit($dni)) {
        setFlash('danger', 'El DNI debe contener exactamente 8 dígitos numéricos.');
        redireccionar('vista/auth/registro.php');
    }

    if (strlen($password) < 6) {
        setFlash('danger', 'La contraseña debe tener al menos 6 caracteres.');
        redireccionar('vista/auth/registro.php');
    }

    if ($password !== $passwordConfirm) {
        setFlash('danger', 'Las contraseñas no coinciden. Por favor verifica.');
        redireccionar('vista/auth/registro.php');
    }

    $db = Database::conectar();

    // Comprobar si el DNI o el usuario ya existen
    $check = $db->prepare("SELECT id_usuario, dni, usuario FROM usuarios WHERE dni = :dni OR usuario = :usuario LIMIT 1");
    $check->execute([':dni' => $dni, ':usuario' => $usuario]);
    $existente = $check->fetch();

    if ($existente) {
        setFlash('warning', 'Ya existe una cuenta registrada con este DNI o Usuario. Puedes iniciar sesión directamente.');
        redireccionar('vista/auth/login.php');
    }

    // Insertar nuevo usuario con rol ROL_CLIENTES (4)
    $stmt = $db->prepare("INSERT INTO usuarios (id_rol, dni, nombres, apellidos, usuario, password, email, telefono, estado) 
                          VALUES (:id_rol, :dni, :nombres, :apellidos, :usuario, :password, :email, :telefono, 'Activo')");
    $exito = $stmt->execute([
        ':id_rol' => ROL_CLIENTES, // Rol 4 = Clientes
        ':dni' => $dni,
        ':nombres' => $nombres,
        ':apellidos' => $apellidos,
        ':usuario' => $usuario,
        ':password' => password_hash($password, PASSWORD_BCRYPT),
        ':email' => !empty($email) ? $email : null,
        ':telefono' => $telefono
    ]);

    if ($exito) {
        $idNuevo = $db->lastInsertId();
        
        // Iniciar sesión automáticamente para el nuevo cliente
        $usuarioValido = [
            'id_usuario' => $idNuevo,
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'usuario' => $usuario,
            'dni' => $dni,
            'telefono' => $telefono,
            'id_rol' => ROL_CLIENTES,
            'nombre_rol' => 'Clientes'
        ];
        iniciarSesionUsuario($usuarioValido);

        setFlash('success', '¡Bienvenido(a) ' . htmlspecialchars($nombres) . '! Tu cuenta de pasajero fue creada con éxito.');
        redireccionar('vista/cliente/index.php');
    } else {
        setFlash('danger', 'Ocurrió un error al crear tu cuenta. Por favor intenta nuevamente.');
        redireccionar('vista/auth/registro.php');
    }
} else {
    redireccionar('vista/auth/registro.php');
}
