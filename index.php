<?php
// Configuración general
$base = "https1.johneasy906.workers.dev/"; // o tu worker base
$validKey = "Proxy-day-9RTWVl1lGl4jtVEJ";

// 1. Obtener cabeceras y URI solicitada
$headers = getallheaders();
$clientKey = $headers['X-API-KEY'] ?? $headers['x-api-key'] ?? '';
$uri = $_SERVER['REQUEST_URI'] ?? '';

// Opcional: Validar la key si viene del cliente principal
// if ($clientKey !== $validKey) { ... }

// 2. Bloqueo de comportamiento de hacks / penalizaciones
if (
    strpos($uri, "/CheckHackBehavior") !== false ||
    strpos($uri, "/GetMatchmakingBlacklist") !== false ||
    strpos($uri, "/GetReportAndPenalty") !== false
) {
    http_response_code(403);
    echo "[c][b][00FF00]Easy Cheats Proxy On        ";
    exit;
}

// 3. Manejo de fileinfo
if (strpos($uri, "/1107/fileinfo") !== false || strpos($uri, "/fileinfo") !== false) {
    // Validar key contra el servicio base
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

    // Obtener el contenido original desde la base
    $ch = curl_init($base . "fileinfo");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-API-KEY: " . $validKey]);
    $raw = curl_exec($ch);
    curl_close($ch);

    // Reemplazos de cadenas según la ruta
    if (strpos($uri, "/1107/fileinfo") !== false) {
        $raw = str_replace(
            "cache_res,CfnFf59sr1SbsqQ6JqTKsEusjKs=,63056,0,yJzKtvtc4LBuiW/EnmIDajFP/EU=,13294,True,0",
            "cache_res,GkLlYqzsX4AtTdE55sDMR-h9sJOI~3D,63056,0,r2Mfay9FWf4SnCFDwNMLYvwtxJM=,10007,True,0",
            $raw
        );
    } else {
        $raw = str_replace(
            "cache_res,CfnFf59sr1SbsqQ6JqTKsEusjKs=,63056,0,yJzKtvtc4LBuiW/EnmIDajFP/EU=,13294,True,0",
            "cache_res,GkLlYqzsX4AtTdE55sDMR-h9sJOI~3D,63056,0,r2Mfay9FWf4SnCFDwNMLYvwtxJM=,10007,True,0",
            $raw
        );
    }

    http_response_code(200);
    echo $raw;
    exit;
}

// 4. Manejo de Shaders y Cache Res
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

// Petición al worker base para obtener el asset/shader
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

// Entregar la respuesta binaria/stream desde PHP
header("Content-Type: application/octet-stream");
http_response_code(200);
echo $rawRes;
exit;
?>
