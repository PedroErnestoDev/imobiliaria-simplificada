<?php
// api/viacep.php
// Integração com ViaCEP para consulta de endereço por CEP.

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$response = ['sucesso' => false, 'dados' => null, 'mensagem' => ''];

$cep = $_GET['cep'] ?? '';

if ($cep === '') {
    http_response_code(400);
    $response['mensagem'] = 'CEP não informado.';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// Remove máscara (apenas dígitos)
$cepLimpo = preg_replace('/\D/', '', $cep);

if (!preg_match('/^\d{8}$/', $cepLimpo)) {
    http_response_code(400);
    $response['mensagem'] = 'CEP inválido. Informe 8 dígitos.';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

$url = "https://viacep.com.br/ws/{$cepLimpo}/json/";

try {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);

    $resultado = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $erroCurl = curl_error($ch);
    curl_close($ch);

    if ($resultado === false) {
        http_response_code(502);
        $response['mensagem'] = 'Erro ao consultar ViaCEP: ' . $erroCurl;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $dados = json_decode($resultado, true);

    if ($httpCode !== 200 || $dados === null) {
        http_response_code(502);
        $response['mensagem'] = 'Resposta inválida do ViaCEP.';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ViaCEP retorna {"erro": true} quando o CEP não existe
    if (isset($dados['erro']) && $dados['erro'] === true) {
        http_response_code(404);
        $response['mensagem'] = 'CEP não encontrado.';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $response['sucesso'] = true;
    $response['dados'] = [
        'cep' => $dados['cep'] ?? '',
        'logradouro' => $dados['logradouro'] ?? '',
        'complemento' => $dados['complemento'] ?? '',
        'bairro' => $dados['bairro'] ?? '',
        'cidade' => $dados['localidade'] ?? '',
        'estado' => $dados['uf'] ?? '',
        'ddd' => $dados['ddd'] ?? '',
        'ibge' => $dados['ibge'] ?? '',
    ];
} catch (Throwable $e) {
    error_log('[ViaCEP] ' . $e->getMessage());
    http_response_code(500);
    $response['mensagem'] = 'Erro interno ao consultar CEP.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);