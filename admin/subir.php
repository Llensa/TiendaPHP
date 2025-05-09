<?php
$carpeta = '../assets/images/';
$respuesta = ['status' => 'error'];

if (isset($_FILES['imagen'])) {
    $nombre = basename($_FILES['imagen']['name']);
    $nombre = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $nombre);
    $destino = $carpeta . $nombre;

    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
        $respuesta = ['status' => 'ok', 'nombre' => $nombre];
    }
}

header('Content-Type: application/json');
echo json_encode($respuesta);
