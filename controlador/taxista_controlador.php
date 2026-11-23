<?php
// ==============================================================
// CONTROLADOR: Taxistas - KEBA MOTOS
// Asistencia, Carga de Documentos y Fotos (Persona y Moto)
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/constantes.php';
require_once dirname(__DIR__) . '/configuracion/session.php';
require_once dirname(__DIR__) . '/ayudantes/funciones.php';
require_once dirname(__DIR__) . '/modelos/Asistencia.php';
require_once dirname(__DIR__) . '/modelos/Taxista.php';
require_once dirname(__DIR__) . '/modelos/ReporteVehiculo.php';

$usuario = obtenerUsuarioActual();
if (!$usuario) {
    redireccionar('vista/auth/login.php');
}

$idUsuario = (int)$usuario['id'];
$rolId = (int)($usuario['rol_id'] ?? 0);
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

$asistenciaMod = new Asistencia();
$taxistaMod = new Taxista();
$fichaTaxista = $taxistaMod->obtenerFichaTaxista($idUsuario);

// -------------------------------------------------------------
// 1. REGISTRAR ASISTENCIA: ENTRADA / INICIO DE TURNO (1 CLIC)
// -------------------------------------------------------------
if ($accion === 'marcar_entrada' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $idConductor = !empty($_POST['id_conductor']) ? (int)$_POST['id_conductor'] : ($fichaTaxista['conductor']['id_conductor'] ?? null);
    $idVehiculo = !empty($_POST['id_vehiculo']) ? (int)$_POST['id_vehiculo'] : ($fichaTaxista['vehiculo']['id_vehiculo'] ?? null);
    $turno = !empty($_POST['turno']) ? trim($_POST['turno']) : Asistencia::detectarTurnoActual();
    $kmInicial = !empty($_POST['kilometraje_inicial']) ? (int)$_POST['kilometraje_inicial'] : null;
    $obs = trim($_POST['observaciones'] ?? '');

    $ok = $asistenciaMod->registrarEntrada($idUsuario, $idConductor, $idVehiculo, $turno, $kmInicial, $obs);
    if ($ok) {
        setFlash('success', '¡Entrada registrada con éxito! Tu turno ha iniciado hoy a las ' . date('h:i A') . '.');
    } else {
        setFlash('warning', 'Ya tienes un turno activo registrado el día de hoy.');
    }
    redireccionar('vista/taxista/asistencia.php');
}

// -------------------------------------------------------------
// 2. REGISTRAR ASISTENCIA: SALIDA / FIN DE TURNO (1 CLIC)
// -------------------------------------------------------------
elseif ($accion === 'marcar_salida' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $idAsistencia = (int)$_POST['id_asistencia'];
    $kmFinal = !empty($_POST['kilometraje_final']) ? (int)$_POST['kilometraje_final'] : null;
    $obs = trim($_POST['observaciones'] ?? '');

    $ok = $asistenciaMod->registrarSalida($idAsistencia, $kmFinal, $obs);
    if ($ok) {
        setFlash('success', '¡Turno finalizado con éxito a las ' . date('h:i A') . '! Salida y entrega de mototaxi registradas.');
    } else {
        setFlash('danger', 'No se pudo finalizar el turno o ya estaba cerrado.');
    }
    redireccionar('vista/taxista/asistencia.php');
}

// -------------------------------------------------------------
// 3. SUBIR FOTO DE LA PERSONA (CONDUCTOR)
// -------------------------------------------------------------
elseif ($accion === 'subir_foto_persona' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $idConductor = (int)($_POST['id_conductor'] ?? ($fichaTaxista['conductor']['id_conductor'] ?? 0));
    if (!$idConductor) {
        setFlash('danger', 'Primero debes vincular tus datos de conductor.');
        redireccionar('vista/taxista/fotos.php');
    }

    if (isset($_FILES['foto_persona']) && $_FILES['foto_persona']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['foto_persona'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $permitidos = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $permitidos)) {
            $dirDestino = ROOT_PATH . 'subidas' . DIRECTORY_SEPARATOR . 'fotos_conductores';
            if (!is_dir($dirDestino)) {
                mkdir($dirDestino, 0777, true);
            }

            $nuevoNombre = 'conductor_' . $idConductor . '_' . time() . '.' . $ext;
            $destino = $dirDestino . DIRECTORY_SEPARATOR . $nuevoNombre;

            if (move_uploaded_file($file['tmp_name'], $destino)) {
                $taxistaMod->actualizarFotoPersona($idConductor, $nuevoNombre);
                setFlash('success', '¡Foto personal actualizada satisfactoriamente!');
            } else {
                setFlash('danger', 'Hubo un error al guardar la imagen en el servidor.');
            }
        } else {
            setFlash('danger', 'Formato de imagen inválido. Solo se admiten JPG, PNG o WEBP.');
        }
    } else {
        setFlash('danger', 'Por favor selecciona una imagen válida.');
    }
    redireccionar('vista/taxista/fotos.php');
}

// -------------------------------------------------------------
// 4. SUBIR FOTO DE LA MOTO (VEHÍCULO)
// -------------------------------------------------------------
elseif ($accion === 'subir_foto_moto' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $idVehiculo = (int)($_POST['id_vehiculo'] ?? ($fichaTaxista['vehiculo']['id_vehiculo'] ?? 0));
    if (!$idVehiculo) {
        setFlash('danger', 'No tienes una mototaxi asignada para actualizar su foto.');
        redireccionar('vista/taxista/fotos.php');
    }

    if (isset($_FILES['foto_moto']) && $_FILES['foto_moto']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['foto_moto'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $permitidos = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $permitidos)) {
            $dirDestino = ROOT_PATH . 'subidas' . DIRECTORY_SEPARATOR . 'fotos_vehiculos';
            if (!is_dir($dirDestino)) {
                mkdir($dirDestino, 0777, true);
            }

            $nuevoNombre = 'moto_' . $idVehiculo . '_' . time() . '.' . $ext;
            $destino = $dirDestino . DIRECTORY_SEPARATOR . $nuevoNombre;

            if (move_uploaded_file($file['tmp_name'], $destino)) {
                $taxistaMod->actualizarFotoMoto($idVehiculo, $nuevoNombre);
                setFlash('success', '¡Foto de la mototaxi actualizada con éxito!');
            } else {
                setFlash('danger', 'No se pudo guardar la imagen de la mototaxi.');
            }
        } else {
            setFlash('danger', 'Formato no permitido. Solo se aceptan JPG, PNG o WEBP.');
        }
    } else {
        setFlash('danger', 'Selecciona una foto válida para la mototaxi.');
    }
    redireccionar('vista/taxista/fotos.php');
}

// -------------------------------------------------------------
// 5. SUBIR DOCUMENTO (SOLO ARCHIVO IMAGEN O PDF - SIN FORMULARIO EXTENSO)
// -------------------------------------------------------------
elseif ($accion === 'subir_documento' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $idVehiculo = (int)($_POST['id_vehiculo'] ?? ($fichaTaxista['vehiculo']['id_vehiculo'] ?? 0));
    $remitenteNombre = trim($_POST['remitente_nombre'] ?? '');
    if (empty($remitenteNombre)) {
        $remitenteNombre = trim(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellidos'] ?? ''));
        if (empty($remitenteNombre)) $remitenteNombre = $usuario['usuario'] ?? 'Taxista';
    }

    $tipo = trim($_POST['tipo_documento'] ?? 'Licencia de Conducir');
    if (empty($tipo)) {
        $tipo = 'Licencia de Conducir';
    }

    // Datos opcionales o con valores por defecto automáticos (sin exigir campos de texto)
    $numero = trim($_POST['numero_documento'] ?? '');
    if (empty($numero)) {
        $cleanPrefijo = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $tipo), 0, 4));
        $numero = 'DOC-' . ($cleanPrefijo ?: 'AUTO') . '-' . date('ymd-His');
    }
    $entidad = trim($_POST['entidad_emisora'] ?? 'Por verificar');
    $emision = !empty($_POST['fecha_emision']) ? $_POST['fecha_emision'] : date('Y-m-d');
    $vencimiento = !empty($_POST['fecha_vencimiento']) ? $_POST['fecha_vencimiento'] : date('Y-m-d', strtotime('+1 year'));
    $obs = trim($_POST['observaciones'] ?? 'Documento adjunto por el taxista (Foto / PDF)');

    // Validación obligatoria: Debe subir una foto o archivo PDF
    if (!isset($_FILES['archivo_documento']) || $_FILES['archivo_documento']['error'] !== UPLOAD_ERR_OK) {
        setFlash('danger', 'Por favor selecciona o toma una foto (JPG, PNG) o archivo PDF de tu documento.');
        redireccionar('vista/taxista/documentos.php');
    }

    $file = $_FILES['archivo_documento'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $permitidos = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $permitidos)) {
        setFlash('danger', 'Formato no permitido. Solo se aceptan imágenes (JPG, PNG, WEBP) o documentos PDF.');
        redireccionar('vista/taxista/documentos.php');
    }

    if (!$idVehiculo) {
        setFlash('danger', 'Debes seleccionar o tener una mototaxi asignada para registrar el documento.');
        redireccionar('vista/taxista/documentos.php');
    }

    $dirDestino = ROOT_PATH . 'subidas' . DIRECTORY_SEPARATOR . 'documentos';
    if (!is_dir($dirDestino)) {
        mkdir($dirDestino, 0777, true);
    }

    $cleanTipo = preg_replace('/[^a-zA-Z0-9]/', '', $tipo);
    $nombreArchivo = 'doc_' . $idVehiculo . '_' . $cleanTipo . '_' . time() . '.' . $ext;
    $destino = $dirDestino . DIRECTORY_SEPARATOR . $nombreArchivo;

    if (!move_uploaded_file($file['tmp_name'], $destino)) {
        setFlash('danger', 'No se pudo guardar el archivo en el servidor. Intenta de nuevo.');
        redireccionar('vista/taxista/documentos.php');
    }

    $ok = $taxistaMod->registrarDocumento($idVehiculo, $tipo, $numero, $entidad, $emision, $vencimiento, $nombreArchivo, $obs, $idUsuario, $remitenteNombre);
    if ($ok) {
        // Si el documento subido es Licencia de Conducir, sincronizar también la ficha del conductor
        if (stripos($tipo, 'Licencia') !== false) {
            $taxistaMod->asegurarFichaConductor($idUsuario, $numero, 'B-IIc', $vencimiento);
        }

        $tipoPropiedad = trim($_POST['tipo_propiedad'] ?? 'empresa');
        if ($tipoPropiedad === 'empresa') {
            setFlash('success', '¡Licencia de Conducir subida exitosamente (' . strtoupper($ext) . ') para la unidad oficial de KEBA MOTOS!');
        } else {
            setFlash('success', '¡Documento (' . htmlspecialchars($tipo) . ') subido exitosamente en formato ' . strtoupper($ext) . '!');
        }
    } else {
        setFlash('danger', 'Hubo un error al registrar el documento en la base de datos.');
    }
    redireccionar('vista/taxista/documentos.php');
}

// -------------------------------------------------------------
// 6. ASOCIAR / ACTUALIZAR LICENCIA DE CONDUCIR
// -------------------------------------------------------------
elseif ($accion === 'actualizar_licencia' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $lic = trim($_POST['numero_licencia'] ?? '');
    $cat = trim($_POST['categoria_licencia'] ?? 'B-IIc');
    $venc = $_POST['fecha_vencimiento_licencia'] ?? '';
    $dir = trim($_POST['direccion'] ?? '');

    if (empty($lic) || empty($venc)) {
        setFlash('danger', 'El número de licencia y fecha de vencimiento son obligatorios.');
    } else {
        $ok = $taxistaMod->asegurarFichaConductor($idUsuario, $lic, $cat, $venc, $dir);
        if ($ok) {
            setFlash('success', 'Datos de licencia guardados correctamente.');
        } else {
            setFlash('danger', 'Error al guardar datos de la licencia.');
        }
    }
    redireccionar('vista/taxista/fotos.php');
}

// -------------------------------------------------------------
// 7. ENVIAR REPORTE / MENSAJE DE ESTADO, DAÑOS O REVISIÓN DE LA MOTO
// -------------------------------------------------------------
elseif ($accion === 'enviar_reporte_moto' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $idVehiculo = (int)($_POST['id_vehiculo'] ?? ($fichaTaxista['vehiculo']['id_vehiculo'] ?? 0));
    $idConductor = (int)($fichaTaxista['conductor']['id_conductor'] ?? 0);
    $tipoAsunto = trim($_POST['tipo_asunto'] ?? 'Daños y Desperfectos');
    $descripcionDanos = trim($_POST['descripcion_danos'] ?? '');
    $estadoLimpieza = trim($_POST['estado_limpieza'] ?? 'Regular / Aceptable');
    $fechaRevision = !empty($_POST['fecha_revision_sugerida']) ? $_POST['fecha_revision_sugerida'] : null;
    $prioridad = $_POST['prioridad'] ?? 'Normal';

    if (!$idVehiculo || empty($descripcionDanos)) {
        setFlash('danger', 'Por favor describe los daños o la novedad de la mototaxi.');
        redireccionar('vista/taxista/reportar_estado.php');
    }

    $fotoEvidencia = null;
    if (isset($_FILES['foto_evidencia']) && $_FILES['foto_evidencia']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['foto_evidencia'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $permitidos = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $permitidos)) {
            $dirDestino = ROOT_PATH . 'subidas' . DIRECTORY_SEPARATOR . 'fotos_danos';
            if (!is_dir($dirDestino)) {
                mkdir($dirDestino, 0777, true);
            }
            $fotoEvidencia = 'dano_' . $idVehiculo . '_' . time() . '.' . $ext;
            $destino = $dirDestino . DIRECTORY_SEPARATOR . $fotoEvidencia;
            if (!move_uploaded_file($file['tmp_name'], $destino)) {
                $fotoEvidencia = null;
            }
        }
    }

    $repMod = new ReporteVehiculo();
    $ok = $repMod->crearReporte($idVehiculo, $idUsuario, $idConductor, $tipoAsunto, $descripcionDanos, $estadoLimpieza, $fechaRevision, $prioridad, $fotoEvidencia);

    if ($ok) {
        setFlash('success', '¡Mensaje enviado al Administrador! Tu reporte sobre el estado de la mototaxi ha sido recibido para su atención.');
    } else {
        setFlash('danger', 'Hubo un inconveniente al enviar el reporte.');
    }
    redireccionar('vista/taxista/reportar_estado.php');
}

// -------------------------------------------------------------
// 8. SUBIR CUOTA MENSUAL (EXCLUSIVO YAPE - SOLO IMAGEN/CAPTURA)
// -------------------------------------------------------------
elseif ($accion === 'subir_cuota_yape' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once dirname(__DIR__) . '/modelos/CuotaMensual.php';

    $idConductor = (int)($fichaTaxista['conductor']['id_conductor'] ?? 0);
    $idVehiculo = (int)($_POST['id_vehiculo'] ?? ($fichaTaxista['vehiculo']['id_vehiculo'] ?? 0));
    $remitenteNombre = trim($_POST['remitente_nombre'] ?? '');
    if (empty($remitenteNombre)) {
        $remitenteNombre = trim(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellidos'] ?? ''));
        if (empty($remitenteNombre)) $remitenteNombre = $usuario['usuario'] ?? 'Taxista';
    }

    $mesPeriodo = trim($_POST['mes_periodo'] ?? (date('m/Y')));
    $monto = (float)($_POST['monto'] ?? 30.00);
    $nroOperacion = trim($_POST['numero_operacion'] ?? '');
    $observaciones = trim($_POST['observaciones'] ?? '');

    // Validación de imagen/captura de Yape obligatoria
    if (!isset($_FILES['comprobante_yape']) || $_FILES['comprobante_yape']['error'] !== UPLOAD_ERR_OK) {
        setFlash('danger', 'Debes adjuntar la imagen o captura de tu comprobante de Yape.');
        redireccionar('vista/taxista/cuotas.php');
    }

    $file = $_FILES['comprobante_yape'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $permitidos)) {
        setFlash('danger', 'Formato no admitido. Solo se aceptan imágenes o capturas de Yape (JPG, PNG o WEBP).');
        redireccionar('vista/taxista/cuotas.php');
    }

    // Obtener los bytes de la imagen para almacenarla directamente en la base de datos (LONGBLOB)
    $blobData = file_get_contents($file['tmp_name']);
    $mimeType = !empty($file['type']) ? $file['type'] : ('image/' . ($ext === 'jpg' ? 'jpeg' : $ext));

    $dirDestino = ROOT_PATH . 'subidas' . DIRECTORY_SEPARATOR . 'cuotas_yape';
    if (!is_dir($dirDestino)) {
        mkdir($dirDestino, 0777, true);
    }

    $nombreArchivo = 'yape_' . $idUsuario . '_' . date('Ymd_His') . '_' . rand(100, 999) . '.' . $ext;
    $destino = $dirDestino . DIRECTORY_SEPARATOR . $nombreArchivo;

    // Guardar también copia física en disco
    @move_uploaded_file($file['tmp_name'], $destino);

    $cuotaMod = new CuotaMensual();
    $ok = $cuotaMod->registrarCuota($idUsuario, $idConductor, $idVehiculo, $remitenteNombre, $mesPeriodo, $monto, $nroOperacion, $nombreArchivo, $blobData, $mimeType, $observaciones);

    if ($ok) {
        setFlash('success', '¡Captura de Yape enviada exitosamente por ' . htmlspecialchars($remitenteNombre) . '! El Administrador revisará y validará tu cuota de ' . htmlspecialchars($mesPeriodo) . '.');
    } else {
        setFlash('danger', 'Hubo un error al registrar tu pago en el sistema.');
    }
    redireccionar('vista/taxista/cuotas.php');
}

// -------------------------------------------------------------
// 9. VALIDACIÓN DE CUOTA MENSUAL POR ADMINISTRADOR
// -------------------------------------------------------------
elseif ($accion === 'validar_cuota_yape' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once dirname(__DIR__) . '/modelos/CuotaMensual.php';

    // Verificar rol de admin o analista
    if ($rolId != ROL_ADMIN && $rolId != ROL_ANALISTA) {
        setFlash('danger', 'No tienes permisos para validar cuotas.');
        redireccionar('index.php');
    }

    $idCuota = (int)($_POST['id_cuota'] ?? 0);
    $nuevoEstado = trim($_POST['estado'] ?? '');
    $respuestaAdmin = trim($_POST['respuesta_admin'] ?? '');

    if (!$idCuota || !in_array($nuevoEstado, ['Aprobado', 'Rechazado'])) {
        setFlash('danger', 'Acción o estado de cuota inválido.');
        redireccionar('vista/vehiculos/cuotas_taxistas.php');
    }

    $cuotaMod = new CuotaMensual();
    $ok = $cuotaMod->responderCuota($idCuota, $nuevoEstado, $respuestaAdmin, $idUsuario);

    if ($ok) {
        $txt = $nuevoEstado === 'Aprobado' ? 'aprobada y validada' : 'rechazada';
        setFlash('success', 'La cuota mensual ha sido ' . $txt . ' satisfactoriamente.');
    } else {
        setFlash('danger', 'No se pudo actualizar el estado de la cuota.');
    }
    redireccionar('vista/vehiculos/cuotas_taxistas.php');
}

// -------------------------------------------------------------
// 10. GESTIÓN DE VIAJES KEBA RIDE (TAXISTA)
// -------------------------------------------------------------
elseif ($accion === 'aceptar_viaje' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once dirname(__DIR__) . '/modelos/Viaje.php';
    $viajeMod = new Viaje();
    $idViaje = (int)($_POST['id_viaje'] ?? 0);
    $idConductor = (int)($fichaTaxista['conductor']['id_conductor'] ?? 0);
    $idVehiculo = (int)($fichaTaxista['vehiculo']['id_vehiculo'] ?? 0);

    $ok = $viajeMod->aceptarViajePorTaxista($idViaje, $idConductor, $idVehiculo);
    if ($ok) {
        setFlash('success', '¡Has aceptado el viaje! Dirígete al punto de recogida para contactar al cliente.');
    } else {
        setFlash('warning', 'Este viaje ya fue asignado o no se encuentra disponible.');
    }
    redireccionar('vista/taxista/viajes.php');
}
elseif ($accion === 'iniciar_viaje' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once dirname(__DIR__) . '/modelos/Viaje.php';
    $viajeMod = new Viaje();
    $idViaje = (int)($_POST['id_viaje'] ?? 0);
    $ok = $viajeMod->cambiarEstado($idViaje, 'En Curso');
    if ($ok) {
        setFlash('success', 'Viaje iniciado con el cliente a bordo. Conduce con precaución hacia el destino.');
    }
    redireccionar('vista/taxista/viajes.php');
}
elseif ($accion === 'finalizar_viaje' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once dirname(__DIR__) . '/modelos/Viaje.php';
    $viajeMod = new Viaje();
    $idViaje = (int)($_POST['id_viaje'] ?? 0);
    $precioFinal = !empty($_POST['precio_final']) ? floatval($_POST['precio_final']) : null;
    $ok = $viajeMod->cambiarEstado($idViaje, 'Completado', $precioFinal);
    if ($ok) {
        setFlash('success', '¡Viaje finalizado con éxito! Carrera completada.');
    }
    redireccionar('vista/taxista/viajes.php');
}
elseif ($accion === 'cancelar_viaje' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once dirname(__DIR__) . '/modelos/Viaje.php';
    $viajeMod = new Viaje();
    $idViaje = (int)($_POST['id_viaje'] ?? 0);
    $ok = $viajeMod->cancelarViaje($idViaje);
    if ($ok) {
        setFlash('info', 'El viaje ha sido cancelado.');
    }
    redireccionar('vista/taxista/viajes.php');
}

// Redirección por defecto
redireccionar('vista/taxista/dashboard.php');
