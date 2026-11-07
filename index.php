<?php
// Configuración general
$base = "https://easy1.johneasy906.workers.dev/";
$validKey = "Proxy-day-9RTWVl1lGl4jtVEJ";

$headers = getallheaders();
$clientKey = $headers['X-API-KEY'] ?? $headers['x-api-key'] ?? '';
$uri = $_SERVER['REQUEST_URI'] ?? '';

// 1. Bloqueo de hacks (Sin cambios)
if (
    strpos($uri, "/CheckHackBehavior") !== false ||
    strpos($uri, "/GetMatchmakingBlacklist") !== false ||
    strpos($uri, "/GetReportAndPenalty") !== false
) {
    http_response_code(403);
    echo "[c][b][00FF00]Easy Cheats Proxy On        ";
    exit;
}

// 2. RUTA DE IMAGEN AISLADA (Solo actúa si buscas /foto o /image, NO interfiere con el juego)
if (strpos($uri, "/foto") !== false || strpos($uri, "/image") !== false) {
    // Sube una imagen llamada "imagen.png" en la misma carpeta de este script PHP
    $rutaImagen = "imagen.png"; 
    
    if (file_exists($rutaImagen)) {
        header("Content-Type: image/png"); // Muestra la foto sin descargar
        header("Content-Disposition: inline; filename=\"imagen.png\"");
        readfile($rutaImagen);
        exit;
    } else {
        http_response_code(404);
        echo "Sube una imagen llamada 'imagen.png' al servidor.";
        exit;
    }
}

// 3. Manejo de fileinfo (Funcionalidad original intacta)
if (strpos($uri, "/1107/fileinfo") !== false || strpos($uri, "/fileinfo") !== false) {
    $ch = curl_init($base . "fileinfo");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-API-KEY: " . $validKey]);
    $authCheckResponse = curl_exec($ch);
    $authCheckStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($authCheckStatus !== 200) {
        http_response_code(403);
        echo "Invalid key";
        exit;
    }

    $ch = curl_init($base . "fileinfo");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-API-KEY: " . $validKey]);
    $raw = curl_exec($ch);
    curl_close($ch);

    $raw = str_replace(
        "cache_res,CfnFf59sr1SbsqQ6JqTKsEusjKs=,63056,0,yJzKtvtc4LBuiW/EnmIDajFP/EU=,13294,True,0",
        "cache_res,GkLlYqzsX4AtTdE55sDMR-h9sJOI~3D,63056,0,r2Mfay9FWf4SnCFDwNMLYvwtxJM=,10007,True,0",
        $raw
    );

    http_response_code(200);
    echo $raw;
    exit;
}

// 4. Shaders y Assets del juego (Funcionalidad original intacta)
$route = "";
if (strpos($uri, "/1107/gameassetbundles/shaders") !== false) {
    $route = "x-shader";
} else if (strpos($uri, "/cache_res") !== false) {
    $route = "assetindexer-v3";
} else {
    http_response_code(404);
    echo "Route not found";
    exit;
}

$ch = curl_init($base . $route);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-API-KEY: " . $validKey]);
$rawRes = curl_exec($ch);
$rawStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($rawStatus !== 200) {
    http_response_code(403);
    echo "Invalid key";
    exit;
}

header("Content-Type: application/octet-stream");
http_response_code(200);
echo $rawRes;
exit;
?>

