<?php
require_once __DIR__ . '/../app/programacao/imoveis/imoveis.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$sucesso = '';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'codigo' => trim($_POST['codigo'] ?? ''),
        'titulo' => trim($_POST['titulo'] ?? ''),
        'descricao' => trim($_POST['descricao'] ?? ''),
        'finalidade' => $_POST['finalidade'] ?? 'venda',
        'tipo' => $_POST['tipo'] ?? 'casa',
        'cep' => trim($_POST['cep'] ?? ''),
        'endereco' => trim($_POST['endereco'] ?? ''),
        'numero' => trim($_POST['numero'] ?? ''),
        'complemento' => trim($_POST['complemento'] ?? ''),
        'bairro' => trim($_POST['bairro'] ?? ''),
        'cidade' => trim($_POST['cidade'] ?? ''),
        'estado' => trim($_POST['estado'] ?? ''),
        'latitude' => $_POST['latitude'] ?? null,
        'longitude' => $_POST['longitude'] ?? null,
        'quartos' => (int) ($_POST['quartos'] ?? 0),
        'banheiros' => (int) ($_POST['banheiros'] ?? 0),
        'vagas' => (int) ($_POST['vagas'] ?? 0),
        'area' => $_POST['area'] !== '' && $_POST['area'] > 0 ? (float) $_POST['area'] : null,
        'valor' => (float) ($_POST['valor'] ?? 0),
        'condominio' => $_POST['condominio'] ?? null,
        'iptu' => $_POST['iptu'] ?? null,
        'telefone_contato' => trim($_POST['telefone_contato'] ?? ''),
        'whatsapp' => trim($_POST['whatsapp'] ?? ''),
        'email_contato' => trim($_POST['email_contato'] ?? ''),
        'status' => $_POST['status'] ?? 'disponivel'
    ];
    try {
        $id = cadastrarImovel($pdo, $dados, (int) $_SESSION['usuario_id']);
        try {
            $contador = salvarUploadsImagensImovel($pdo, $id);
            $sucesso = "Imóvel cadastrado com sucesso! ID: $id. $contador imagem(ns) adicionada(s).";
        } catch (Throwable $e) {
            error_log('Erro ao salvar imagens do imóvel ' . $id . ': ' . $e->getMessage());
            $erro = "Imóvel cadastrado com ID $id, mas não foi possível salvar as imagens: " . $e->getMessage();
        }
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Imóvel | DW2 Imobiliária</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
    <style>
        :root { --gold: #c99a3b; --dark: #151515; }
        body { font-family: 'DM Sans', sans-serif; background: #f8f9fa; }
        .btn-gold { background: var(--gold); border: 1px solid var(--gold); color: #151515; font-weight: 700; }
        .map-container { height: 320px; border-radius: 8px; overflow: hidden; border: 1px solid #ddd; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background: var(--dark);">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">DW2 Dashboard</a>
        <div class="navbar-nav ms-auto"><a class="nav-link" href="dashboard.php">Voltar ao Dashboard</a></div>
    </div>
</nav>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header bg-dark text-white"><h5>Cadastrar Novo Imóvel</h5></div>
                <div class="card-body">
                    <?php if ($sucesso): ?><div class="alert alert-success"><?= $sucesso ?></div><?php endif; ?>
                    <?php if ($erro): ?><div class="alert alert-danger"><?= $erro ?></div><?php endif; ?>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Código *</label>
                                <input type="text" name="codigo" class="form-control" required placeholder="Ex: CASA001">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Finalidade *</label>
                                <select name="finalidade" class="form-select">
                                    <option value="venda">Venda</option>
                                    <option value="aluguel">Aluguel</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tipo *</label>
                                <select name="tipo" class="form-select">
                                    <option value="casa">Casa</option>
                                    <option value="apartamento">Apartamento</option>
                                    <option value="condominio">Condomínio</option>
                                    <option value="terreno">Terreno</option>
                                    <option value="na_planta">Imóvel na planta</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Título *</label>
                                <input type="text" name="titulo" class="form-control" required placeholder="Apartamento 3 quartos no centro">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Valor (R$) *</label>
                                <input type="number" name="valor" class="form-control" required step="0.01" min="0">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Descrição</label>
                                <textarea name="descricao" class="form-control" rows="3" placeholder="Descreva o imóvel..."></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Imagens do Imóvel</label>
                                <div class="border rounded p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                            <path d="M11 8a2 2 0 012-2h3a2 2 0 012 2v6a2 2 0 01-2 2h-3a2 2 0 01-2-2v-6c0-1.1-.9-2-2-2m0-10l6 6m0 0l-6 6m6.618-6.618L18.1 7.9a2.9 2.9 0 010 4.1H9l.9 8.7a2.9 2.9 0 01-2 2.5H5a2 2 0 01-2-2V5a2 2 0 012-2h3.69l1.43-1.43a2.9 2.9 0 014.06 0zM11 3.99v12l5-5"/>
                                        </svg>
                                        <span>Upload de Fotos</span>
                                    </div>
                                    <input class="form-control mt-2" type="file" name="imagens[]" multiple accept="image/jpeg,image/png,image/webp">
                                    <small class="text-muted mt-2">Selecione uma ou mais imagens (JPEG, PNG ou WebP), com até 10 MB por arquivo.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">CEP</label>
                                <input type="text" name="cep" class="form-control cep-mask" placeholder="00000-000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Endereço</label>
                                <input type="text" name="endereco" class="form-control" placeholder="Rua, Avenida..." readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Número</label>
                                <input type="text" name="numero" class="form-control" placeholder="123">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Complemento</label>
                                <input type="text" name="complemento" class="form-control" placeholder="Apto 101">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Bairro *</label>
                                <input type="text" name="bairro" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Cidade *</label>
                                <input type="text" name="cidade" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Estado (UF) *</label>
                                <input type="text" name="estado" class="form-control" required maxlength="2" placeholder="SP">
                            </div>
                            <div class="col-md-2"><label class="form-label fw-bold">Quartos</label><input type="number" name="quartos" class="form-control" value="0" min="0"></div>
                            <div class="col-md-2"><label class="form-label fw-bold">Banheiros</label><input type="number" name="banheiros" class="form-control" value="0" min="0"></div>
                            <div class="col-md-2"><label class="form-label fw-bold">Vagas</label><input type="number" name="vagas" class="form-control" value="0" min="0"></div>
                            <div class="col-md-2"><label class="form-label fw-bold">Área (m²)</label><input type="number" name="area" class="form-control" step="0.01" min="0"></div>
                            <div class="col-md-2"><label class="form-label fw-bold">Condomínio (R$)</label><input type="number" name="condominio" class="form-control" step="0.01" min="0"></div>
                            <div class="col-md-2"><label class="form-label fw-bold">IPTU (R$)</label><input type="number" name="iptu" class="form-control" step="0.01" min="0"></div>
                            <div class="col-md-4"><label class="form-label fw-bold">Telefone Contato</label><input type="text" name="telefone_contato" class="form-control" placeholder="(00) 0000-0000"></div>
                            <div class="col-md-4"><label class="form-label fw-bold">WhatsApp</label><input type="text" name="whatsapp" class="form-control" placeholder="(00) 00000-0000"></div>
                            <div class="col-md-4"><label class="form-label fw-bold">E-mail Contato</label><input type="email" name="email_contato" class="form-control" placeholder="contato@imobiliaria.com"></div>
                            <div class="col-md-12"><label class="form-label fw-bold">Localização no Mapa</label><div class="map-container" id="mapa"></div></div>
                            <div class="col-12"><div class="d-grid gap-2 d-flex align-items-center">
                                <button type="submit" class="btn btn-gold py-2">Salvar Imóvel</button>
                                <a href="dashboard.php" class="btn btn-outline-secondary py-2">Cancelar</a>
                            </div></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
(function() {
    'use strict';
    let map, marker, lat = -23.550520, lng = -46.633309;
    let cepInput, enderecoInput, bairroInput, cidadeInput, estadoInput, numeroInput;
    let latLongInput, lonInput;

    function initMap() {
        map = L.map('mapa').setView([lat, lng], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 18
        }).addTo(map);
        marker = L.marker([lat, lng], { draggable: true }).addTo(map);

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            lat = e.latlng.lat;
            lng = e.latlng.lng;
            latLongInput.value = lat;
            lonInput.value = lng;
        });

        marker.on('dragend', function() {
            var latlng = marker.getLatLng();
            lat = latlng.lat;
            lng = latlng.lng;
            latLongInput.value = lat;
            lonInput.value = lng;
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        cepInput = document.querySelector('input[name="cep"]');
        enderecoInput = document.querySelector('input[name="endereco"]');
        bairroInput = document.querySelector('input[name="bairro"]');
        cidadeInput = document.querySelector('input[name="cidade"]');
        estadoInput = document.querySelector('input[name="estado"]');
        numeroInput = document.querySelector('input[name="numero"]');

        latLongInput = document.createElement('input');
        latLongInput.type = 'hidden';
        latLongInput.name = 'latitude';
        document.querySelector('form').appendChild(latLongInput);

        lonInput = document.createElement('input');
        lonInput.type = 'hidden';
        lonInput.name = 'longitude';
        document.querySelector('form').appendChild(lonInput);

        cepInput.addEventListener('blur', function() {
            var cep = this.value.replace(/\D/g, '');
            if (cep.length !== 8) {
                limparCampos();
                return;
            }
            fetch('/api/viacep.php?cep=' + cep)
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.sucesso && data.dados) {
                        enderecoInput.value = data.dados.logradouro || '';
                        bairroInput.value = data.dados.bairro || '';
                        cidadeInput.value = data.dados.cidade || '';
                        estadoInput.value = data.dados.estado || '';
                        numeroInput.value = data.dados.ddd || '';
                        buscaCoordenadas(enderecoInput.value);
                    } else {
                        limparCampos();
                        alert(data.mensagem || 'CEP não encontrado.');
                    }
                })
                .catch(function() {
                    limparCampos();
                    alert('Erro ao conectar ao servidor.');
                });
        });

        cepInput.addEventListener('input', function(e) {
            var v = e.target.value.replace(/\D/g, '');
            if (v.length > 5) v = v.substring(0, 5) + '-' + v.substring(5);
            e.target.value = v;
        });

        if (typeof L !== 'undefined') initMap();
    });

    function limparCampos() {
        enderecoInput.value = '';
        bairroInput.value = '';
        cidadeInput.value = '';
        estadoInput.value = '';
        numeroInput.value = '';
        latLongInput.value = '';
        lonInput.value = '';
        limparMapa();
    }

    function limparMapa() {
        if (map) {
            map.setView([-23.550520, -46.633309], 16);
            if (marker) marker.setLatLng([-23.550520, -46.633309]);
            latLongInput.value = '';
            lonInput.value = '';
        }
    }

    function buscaCoordenadas(endereco) {
        fetch('/api/geocoding.php?q=' + encodeURIComponent(endereco) + '&limit=1')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.sucesso && data.dados && data.dados.length > 0) {
                    var c = data.dados[0];
                    latLongInput.value = c.latitude;
                    lonInput.value = c.longitude;
                    if (map) {
                        map.setView([c.latitude, c.longitude], 16);
                        if (marker) marker.setLatLng([c.latitude, c.longitude]);
                    }
                } else {
                    alert('Endereço não encontrado no mapa.');
                }
            })
            .catch(function() {
                alert('Erro ao consultar coordenadas.');
            });
    }
})();
</script>
</body>
</html>
