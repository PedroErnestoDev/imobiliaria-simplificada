<?php
// api/geocoding.php
// Integração com Nominatim/OpenStreetMap para geocodificação.

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$response = ['sucesso' => false, 'dados' => null, 'mensagem' => ''];

$endereco = $_GET['q'] ?? '';
$limite = filter_var($_GET['limit'] ?? 1, FILTER_VALIDATE_INT);

if ($endereco === '') {
    http_response_code(400);
    $response['mensagem'] = 'Endereço não informado.';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($limite === false || $limite < 1) {
    $limite = 1;
}

$url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
    'q' => $endereco,
    'format' => 'json',
    'limit' => $limite,
]);

try {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER => [
            'User-Agent: DW2-Imobiliaria/1.0 (contato@imobiliaria.com)',
            'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8',
        ],
    ]);

    $resultado = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $erroCurl = curl_error($ch);
    curl_close($ch);

    if ($resultado === false) {
        http_response_code(502);
        $response['mensagem'] = 'Erro ao consultar Nominatim: ' . $erroCurl;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $dados = json_decode($resultado, true);

    if ($httpCode !== 200 || $dados === null || !is_array($dados)) {
        http_response_code(502);
        $response['mensagem'] = 'Resposta inválida do Nominatim.';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (empty($dados)) {
        http_response_code(404);
        $response['mensagem'] = 'Endereço não encontrado.';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resultados = [];
    foreach ($dados as $item) {
        $resultados[] = [
            'place_id' => $item['place_id'] ?? null,
            'latitude' => (float) ($item['lat'] ?? 0),
            'longitude' => (float) ($item['lon'] ?? 0),
            'display_name' => $item['display_name'] ?? '',
            'tipo' => $item['type'] ?? '',
            'categoria' => $item['category'] ?? '',
        ];
    }

    $response['sucesso'] = true;
    $response['dados'] = $resultados;
} catch (Throwable $e) {
    error_log('[Geocoding] ' . $e->getMessage());
    http_response_code(500);
    $response['mensagem'] = 'Erro interno ao geocodificar endereço.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);