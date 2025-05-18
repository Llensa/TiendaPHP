<?php
require_once '../db/db.php'; // Asegúrate que esta ruta a tu conexión PDO es correcta

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$usuario_id = $_SESSION['usuario_id'] ?? null; // CORREGIDO: Usar 'usuario_id'

if (!$usuario_id) {
    echo json_encode(['status' => 'unauthorized', 'message' => 'Usuario no autenticado.']);
    exit;
}

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) { // Para cargar una dirección para editar
            $direccion_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if (!$direccion_id) {
                 echo json_encode(['status' => 'error', 'message' => 'ID de dirección inválido.']);
                 exit;
            }
            $stmt = $pdo->prepare("SELECT id, calle, numero, apartamento, ciudad, provincia, codigo_postal, pais, es_predeterminada FROM direcciones WHERE id = ? AND usuario_id = ?");
            $stmt->execute([$direccion_id, $usuario_id]);
            $direccion = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($direccion) {
                echo json_encode(['status' => 'ok', 'direccion' => $direccion]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Dirección no encontrada o no pertenece al usuario.']);
            }
        } else { // Para obtener todas las direcciones
            $stmt = $pdo->prepare("SELECT id, calle, numero, apartamento, ciudad, provincia, codigo_postal, pais, es_predeterminada FROM direcciones WHERE usuario_id = ? ORDER BY es_predeterminada DESC, id DESC");
            $stmt->execute([$usuario_id]);
            echo json_encode(['status' => 'ok', 'direcciones' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        }
        break;

    case 'POST': // Usado para crear Y para actualizar si se envía un ID
        $data = json_decode(file_get_contents("php://input"), true);

        // Sanitizar y validar datos (¡MUY IMPORTANTE!)
        $id = isset($data['id']) ? filter_var($data['id'], FILTER_VALIDATE_INT) : null;
        $calle = trim(htmlspecialchars($data['calle'] ?? ''));
        $numero = trim(htmlspecialchars($data['numero'] ?? ''));
        $apartamento = trim(htmlspecialchars($data['apartamento'] ?? ''));
        $ciudad = trim(htmlspecialchars($data['ciudad'] ?? ''));
        $provincia = trim(htmlspecialchars($data['provincia'] ?? ''));
        $codigo_postal = trim(htmlspecialchars($data['codigo_postal'] ?? ''));
        $pais = trim(htmlspecialchars($data['pais'] ?? 'Argentina'));
        $es_predeterminada = !empty($data['es_predeterminada_check']); // Viene del checkbox

        // Validaciones básicas (ejemplo, expandir según necesidad)
        if (empty($calle) || empty($numero) || empty($ciudad) || empty($provincia) || empty($codigo_postal) || empty($pais)) {
            echo json_encode(['status' => 'error', 'message' => 'Todos los campos excepto apartamento son requeridos.']);
            exit;
        }
        // Aquí puedes añadir más validaciones: longitud, formato de CP, etc.

        $pdo->beginTransaction();
        try {
            if ($es_predeterminada) {
                // Quitar predeterminada de otras direcciones del mismo usuario
                $stmt_clear_predet = $pdo->prepare("UPDATE direcciones SET es_predeterminada = 0 WHERE usuario_id = ?");
                $stmt_clear_predet->execute([$usuario_id]);
            }

            if ($id) { // Actualizar dirección existente
                $stmt = $pdo->prepare("UPDATE direcciones SET 
                    calle = ?, numero = ?, apartamento = ?, ciudad = ?, provincia = ?, codigo_postal = ?, pais = ?, es_predeterminada = ? 
                    WHERE id = ? AND usuario_id = ?");
                $stmt->execute([$calle, $numero, $apartamento, $ciudad, $provincia, $codigo_postal, $pais, $es_predeterminada ? 1 : 0, $id, $usuario_id]);
                $message = 'Dirección actualizada correctamente.';
            } else { // Insertar nueva dirección
                $stmt = $pdo->prepare("INSERT INTO direcciones 
                    (usuario_id, calle, numero, apartamento, ciudad, provincia, codigo_postal, pais, es_predeterminada) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$usuario_id, $calle, $numero, $apartamento, $ciudad, $provincia, $codigo_postal, $pais, $es_predeterminada ? 1 : 0]);
                $message = 'Dirección agregada correctamente.';
            }
            $pdo->commit();
            echo json_encode(['status' => 'ok', 'message' => $message]); // 'ok' para que coincida con JS
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Error al guardar dirección: " . $e->getMessage()); // Loguear el error real
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar la dirección. Intente más tarde.']);
        }
        break;

    case 'PATCH': // Para establecer como predeterminada
        $data = json_decode(file_get_contents("php://input"), true);
        $direccion_id = isset($data['id']) ? filter_var($data['id'], FILTER_VALIDATE_INT) : 0;

        if ($direccion_id > 0) {
            $pdo->beginTransaction();
            try {
                $stmt_clear = $pdo->prepare("UPDATE direcciones SET es_predeterminada = 0 WHERE usuario_id = ?");
                $stmt_clear->execute([$usuario_id]);

                $stmt_set = $pdo->prepare("UPDATE direcciones SET es_predeterminada = 1 WHERE id = ? AND usuario_id = ?");
                $stmt_set->execute([$direccion_id, $usuario_id]);
                $pdo->commit();
                echo json_encode(['status' => 'ok', 'message' => 'Dirección establecida como predeterminada.']);
            } catch (Exception $e) {
                $pdo->rollBack();
                error_log("Error al establecer predeterminada: " . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => 'Error al actualizar la dirección.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID de dirección inválido.']);
        }
        break;

    case 'DELETE':
        // DELETE espera el ID en el body como 'id=X'
        // pero es más estándar enviarlo como query param o parte de la URL.
        // Si se envía como x-www-form-urlencoded en el body:
        $id_param = null;
        if ($_SERVER['CONTENT_TYPE'] === 'application/x-www-form-urlencoded') {
            parse_str(file_get_contents("php://input"), $delete_data);
            $id_param = isset($delete_data['id']) ? filter_var($delete_data['id'], FILTER_VALIDATE_INT) : null;
        }
        // Alternativamente, si JS lo envía como JSON:
        // $delete_data_json = json_decode(file_get_contents("php://input"), true);
        // $id_param = isset($delete_data_json['id']) ? filter_var($delete_data_json['id'], FILTER_VALIDATE_INT) : null;


        if (!$id_param) {
            echo json_encode(['status' => 'error', 'message' => 'ID de dirección no proporcionado o inválido.']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM direcciones WHERE id = ? AND usuario_id = ?");
        if ($stmt->execute([$id_param, $usuario_id])) {
            if ($stmt->rowCount() > 0) {
                echo json_encode(['status' => 'ok', 'message' => 'Dirección eliminada.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se pudo eliminar la dirección o no se encontró.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al ejecutar la eliminación.']);
        }
        break;

    default:
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
        break;
}
?>