<?php

declare(strict_types=1);

require_once __DIR__ . '/../../db/config.php';

if (!isset($pdo) || !($pdo instanceof PDO)) {
    $pdo = (new Database())->connect();
}

/*
|--------------------------------------------------------------------------
| LISTAR IMÓVEIS DISPONÍVEIS
|--------------------------------------------------------------------------
*/

function listarImoveis(
    PDO $pdo,
    array $filtros = [],
    int $pagina = 1,
    int $limite = 9
): array {
    $sql = "
        SELECT
            i.id,
            i.codigo,
            i.titulo,
            i.descricao,
            i.finalidade,
            i.tipo,
            i.bairro,
            i.cidade,
            i.estado,
            i.quartos,
            i.banheiros,
            i.vagas,
            i.area,
            i.valor,
            i.condominio,
            i.iptu,
            (
                SELECT img.caminho_imagem
                FROM imagens_imoveis img
                WHERE img.imovel_id = i.id
                ORDER BY img.imagem_principal DESC, img.id ASC
                LIMIT 1
            ) AS imagem
        FROM imoveis i
        WHERE i.status = 'disponivel'
    ";

    $parametros = [];

    if (!empty($filtros['tipo'])) {
        $sql .= " AND i.tipo = :tipo";
        $parametros[':tipo'] = $filtros['tipo'];
    }

    if (!empty($filtros['finalidade'])) {
        $sql .= " AND i.finalidade = :finalidade";
        $parametros[':finalidade'] = $filtros['finalidade'];
    }

    if (!empty($filtros['busca'])) {
        $sql .= "
            AND (
                i.titulo LIKE :busca_titulo
                OR i.codigo LIKE :busca_codigo
                OR i.bairro LIKE :busca_bairro
                OR i.cidade LIKE :busca_cidade
            )
        ";

        $busca = '%' . $filtros['busca'] . '%';

        $parametros[':busca_titulo'] = $busca;
        $parametros[':busca_codigo'] = $busca;
        $parametros[':busca_bairro'] = $busca;
        $parametros[':busca_cidade'] = $busca;
    }

    if (isset($filtros['valor_min']) && $filtros['valor_min'] !== '') {
        $sql .= " AND i.valor >= :valor_min";
        $parametros[':valor_min'] = $filtros['valor_min'];
    }

    if (isset($filtros['valor_max']) && $filtros['valor_max'] !== '') {
        $sql .= " AND i.valor <= :valor_max";
        $parametros[':valor_max'] = $filtros['valor_max'];
    }

    $sql .= "
        ORDER BY i.criado_em DESC, i.id DESC
        LIMIT :limite OFFSET :offset
    ";

    $pagina = max(1, $pagina);
    $limite = max(1, min(48, $limite));
    $offset = ($pagina - 1) * $limite;

    $stmt = $pdo->prepare($sql);

    foreach ($parametros as $chave => $valor) {
        $stmt->bindValue($chave, $valor);
    }

    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/*
|--------------------------------------------------------------------------
| CONTAR IMÓVEIS DISPONÍVEIS
|--------------------------------------------------------------------------
*/

function contarImoveis(PDO $pdo, array $filtros = []): int
{
    $sql = "
        SELECT COUNT(*)
        FROM imoveis i
        WHERE i.status = 'disponivel'
    ";

    $parametros = [];

    if (!empty($filtros['tipo'])) {
        $sql .= " AND i.tipo = :tipo";
        $parametros[':tipo'] = $filtros['tipo'];
    }

    if (!empty($filtros['finalidade'])) {
        $sql .= " AND i.finalidade = :finalidade";
        $parametros[':finalidade'] = $filtros['finalidade'];
    }

    if (!empty($filtros['busca'])) {
        $sql .= "
            AND (
                i.titulo LIKE :busca_titulo
                OR i.codigo LIKE :busca_codigo
                OR i.bairro LIKE :busca_bairro
                OR i.cidade LIKE :busca_cidade
            )
        ";

        $busca = '%' . $filtros['busca'] . '%';

        $parametros[':busca_titulo'] = $busca;
        $parametros[':busca_codigo'] = $busca;
        $parametros[':busca_bairro'] = $busca;
        $parametros[':busca_cidade'] = $busca;
    }

    if (isset($filtros['valor_min']) && $filtros['valor_min'] !== '') {
        $sql .= " AND i.valor >= :valor_min";
        $parametros[':valor_min'] = $filtros['valor_min'];
    }

    if (isset($filtros['valor_max']) && $filtros['valor_max'] !== '') {
        $sql .= " AND i.valor <= :valor_max";
        $parametros[':valor_max'] = $filtros['valor_max'];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($parametros);

    return (int) $stmt->fetchColumn();
}

/*
|--------------------------------------------------------------------------
| BUSCAR IMÓVEL POR ID
|--------------------------------------------------------------------------
*/

function buscarImovelPorId(PDO $pdo, int $id): ?array
{
    $sql = "
        SELECT i.*,
        (
            SELECT img.caminho_imagem
            FROM imagens_imoveis img
            WHERE img.imovel_id = i.id
            ORDER BY img.imagem_principal DESC, img.id ASC
            LIMIT 1
        ) AS imagem
        FROM imoveis i
        WHERE i.id = :id
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $imovel = $stmt->fetch(PDO::FETCH_ASSOC);

    return $imovel ?: null;
}

/*
|--------------------------------------------------------------------------
| LISTAR TODOS OS IMÓVEIS (ADMIN / DASHBOARD)
|--------------------------------------------------------------------------
*/

function listarTodosImoveis(
    PDO $pdo,
    array $filtros = [],
    int $pagina = 1,
    int $limite = 9
): array {
    $sql = "
        SELECT
            i.id,
            i.codigo,
            i.titulo,
            i.descricao,
            i.finalidade,
            i.tipo,
            i.bairro,
            i.cidade,
            i.estado,
            i.quartos,
            i.banheiros,
            i.vagas,
            i.area,
            i.valor,
            i.status,
            i.criado_em,
            u.nome AS usuario_nome
        FROM imoveis i
        LEFT JOIN usuarios u ON i.usuario_id = u.id
        WHERE 1=1
    ";

    $parametros = [];

    if (!empty($filtros['tipo'])) {
        $sql .= " AND i.tipo = :tipo";
        $parametros[':tipo'] = $filtros['tipo'];
    }

    if (!empty($filtros['finalidade'])) {
        $sql .= " AND i.finalidade = :finalidade";
        $parametros[':finalidade'] = $filtros['finalidade'];
    }

    if (!empty($filtros['status'])) {
        $sql .= " AND i.status = :status";
        $parametros[':status'] = $filtros['status'];
    }

    if (!empty($filtros['busca'])) {
        $sql .= "
            AND (
                i.titulo LIKE :busca_titulo
                OR i.codigo LIKE :busca_codigo
                OR i.bairro LIKE :busca_bairro
                OR i.cidade LIKE :busca_cidade
            )
        ";

        $busca = '%' . $filtros['busca'] . '%';

        $parametros[':busca_titulo'] = $busca;
        $parametros[':busca_codigo'] = $busca;
        $parametros[':busca_bairro'] = $busca;
        $parametros[':busca_cidade'] = $busca;
    }

    $sql .= "
        ORDER BY i.criado_em DESC, i.id DESC
        LIMIT :limite OFFSET :offset
    ";

    $pagina = max(1, $pagina);
    $limite = max(1, min(48, $limite));
    $offset = ($pagina - 1) * $limite;

    $stmt = $pdo->prepare($sql);

    foreach ($parametros as $chave => $valor) {
        $stmt->bindValue($chave, $valor);
    }

    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/*
|--------------------------------------------------------------------------
| CONTAR TODOS OS IMÓVEIS (ADMIN / DASHBOARD)
|--------------------------------------------------------------------------
*/

function contarTodosImoveis(PDO $pdo, array $filtros = []): int
{
    $sql = "
        SELECT COUNT(*)
        FROM imoveis i
        WHERE 1=1
    ";

    $parametros = [];

    if (!empty($filtros['tipo'])) {
        $sql .= " AND i.tipo = :tipo";
        $parametros[':tipo'] = $filtros['tipo'];
    }

    if (!empty($filtros['finalidade'])) {
        $sql .= " AND i.finalidade = :finalidade";
        $parametros[':finalidade'] = $filtros['finalidade'];
    }

    if (!empty($filtros['status'])) {
        $sql .= " AND i.status = :status";
        $parametros[':status'] = $filtros['status'];
    }

    if (!empty($filtros['busca'])) {
        $sql .= "
            AND (
                i.titulo LIKE :busca_titulo
                OR i.codigo LIKE :busca_codigo
                OR i.bairro LIKE :busca_bairro
                OR i.cidade LIKE :busca_cidade
            )
        ";

        $busca = '%' . $filtros['busca'] . '%';

        $parametros[':busca_titulo'] = $busca;
        $parametros[':busca_codigo'] = $busca;
        $parametros[':busca_bairro'] = $busca;
        $parametros[':busca_cidade'] = $busca;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($parametros);

    return (int) $stmt->fetchColumn();
}

/*
|--------------------------------------------------------------------------
| BUSCAR IMÓVEL COMPLETO COM IMAGENS
|--------------------------------------------------------------------------
*/

function buscarImovelCompleto(PDO $pdo, int $id): ?array
{
    $sql = "
        SELECT *
        FROM imoveis
        WHERE id = :id
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $imovel = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$imovel) {
        return null;
    }

    $sqlImg = "
        SELECT
            id,
            caminho_imagem,
            imagem_principal
        FROM imagens_imoveis
        WHERE imovel_id = :imovel_id
        ORDER BY imagem_principal DESC, id ASC
    ";

    $stmtImg = $pdo->prepare($sqlImg);
    $stmtImg->bindValue(':imovel_id', $id, PDO::PARAM_INT);
    $stmtImg->execute();

    $imovel['imagens'] = $stmtImg->fetchAll(PDO::FETCH_ASSOC);

    return $imovel;
}

/*
|--------------------------------------------------------------------------
| CADASTRAR IMÓVEL
|--------------------------------------------------------------------------
*/

function cadastrarImovel(
    PDO $pdo,
    array $dados,
    ?int $usuarioId = null
): int {
    // Normalizar código: remover espaços e converter para maiúsculo
    $codigo = trim($dados["codigo"] ?? "");
    $codigo = strtoupper(preg_replace("/\s+/", "", $codigo));
    // Verificar se código já existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM imoveis WHERE codigo = :codigo");
    $stmt->bindValue(":codigo", $codigo);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Já existe um imóvel com este código: " . $codigo);
    }
    $sql = "
        INSERT INTO imoveis (
            codigo,
            usuario_id,
            titulo,
            descricao,
            finalidade,
            tipo,
            cep,
            endereco,
            numero,
            complemento,
            bairro,
            cidade,
            estado,
            quartos,
            banheiros,
            vagas,
            area,
            valor,
            condominio,
            iptu,
            telefone_contato,
            whatsapp,
            email_contato,
            latitude,
            longitude,
            status
        ) VALUES (
            :codigo,
            :usuario_id,
            :titulo,
            :descricao,
            :finalidade,
            :tipo,
            :cep,
            :endereco,
            :numero,
            :complemento,
            :bairro,
            :cidade,
            :estado,
            :quartos,
            :banheiros,
            :vagas,
            :area,
            :valor,
            :condominio,
            :iptu,
            :telefone_contato,
            :whatsapp,
            :email_contato,
            :latitude,
            :longitude,
            :status
        )
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':codigo', $dados['codigo'] ?? '');
    
    if ($usuarioId === null) {
        $stmt->bindValue(':usuario_id', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
    }

    $stmt->bindValue(':titulo', $dados['titulo'] ?? '');
    $stmt->bindValue(':descricao', $dados['descricao'] ?? null);
    $stmt->bindValue(':finalidade', $dados['finalidade'] ?? 'venda');
    $stmt->bindValue(':tipo', $dados['tipo'] ?? 'casa');
    $stmt->bindValue(':cep', $dados['cep'] ?? '');
    $stmt->bindValue(':endereco', $dados['endereco'] ?? '');
    $stmt->bindValue(':numero', $dados['numero'] ?? null);
    $stmt->bindValue(':complemento', $dados['complemento'] ?? null);
    $stmt->bindValue(':bairro', $dados['bairro'] ?? '');
    $stmt->bindValue(':cidade', $dados['cidade'] ?? '');
    $stmt->bindValue(':estado', $dados['estado'] ?? '');
    $stmt->bindValue(':quartos', (int) ($dados['quartos'] ?? 0), PDO::PARAM_INT);
    $stmt->bindValue(':banheiros', (int) ($dados['banheiros'] ?? 0), PDO::PARAM_INT);
    $stmt->bindValue(':vagas', (int) ($dados['vagas'] ?? 0), PDO::PARAM_INT);
    $stmt->bindValue(':area', $dados['area'] ?? null);
    $stmt->bindValue(':valor', (float) ($dados['valor'] ?? 0));
    $stmt->bindValue(':condominio', $dados['condominio'] ?? null);
    $stmt->bindValue(':iptu', $dados['iptu'] ?? null);
    $stmt->bindValue(':telefone_contato', $dados['telefone_contato'] ?? null);
    $stmt->bindValue(':whatsapp', $dados['whatsapp'] ?? null);
    $stmt->bindValue(':email_contato', $dados['email_contato'] ?? null);
    $stmt->bindValue(':latitude', $dados['latitude'] ?? null);
    $stmt->bindValue(':longitude', $dados['longitude'] ?? null);
    $stmt->bindValue(':status', $dados['status'] ?? 'disponivel');

    $stmt->execute();

    return (int) $pdo->lastInsertId();
}

/*
|--------------------------------------------------------------------------
| ATUALIZAR IMÓVEL
|--------------------------------------------------------------------------
*/

function atualizarImovel(PDO $pdo, int $id, array $dados): bool
{
    $sql = "
        UPDATE imoveis SET
            codigo = :codigo,
            titulo = :titulo,
            descricao = :descricao,
            finalidade = :finalidade,
            tipo = :tipo,
            cep = :cep,
            endereco = :endereco,
            numero = :numero,
            complemento = :complemento,
            bairro = :bairro,
            cidade = :cidade,
            estado = :estado,
            quartos = :quartos,
            banheiros = :banheiros,
            vagas = :vagas,
            area = :area,
            valor = :valor,
            condominio = :condominio,
            iptu = :iptu,
            telefone_contato = :telefone_contato,
            whatsapp = :whatsapp,
            email_contato = :email_contato,
            status = :status
        WHERE id = :id
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':codigo', $dados['codigo'] ?? '');
    $stmt->bindValue(':titulo', $dados['titulo'] ?? '');
    $stmt->bindValue(':descricao', $dados['descricao'] ?? null);
    $stmt->bindValue(':finalidade', $dados['finalidade'] ?? 'venda');
    $stmt->bindValue(':tipo', $dados['tipo'] ?? 'casa');
    $stmt->bindValue(':cep', $dados['cep'] ?? '');
    $stmt->bindValue(':endereco', $dados['endereco'] ?? '');
    $stmt->bindValue(':numero', $dados['numero'] ?? null);
    $stmt->bindValue(':complemento', $dados['complemento'] ?? null);
    $stmt->bindValue(':bairro', $dados['bairro'] ?? '');
    $stmt->bindValue(':cidade', $dados['cidade'] ?? '');
    $stmt->bindValue(':estado', $dados['estado'] ?? '');
    $stmt->bindValue(':quartos', (int) ($dados['quartos'] ?? 0), PDO::PARAM_INT);
    $stmt->bindValue(':banheiros', (int) ($dados['banheiros'] ?? 0), PDO::PARAM_INT);
    $stmt->bindValue(':vagas', (int) ($dados['vagas'] ?? 0), PDO::PARAM_INT);
    $stmt->bindValue(':area', $dados['area'] ?? null);
    $stmt->bindValue(':valor', (float) ($dados['valor'] ?? 0));
    $stmt->bindValue(':condominio', $dados['condominio'] ?? null);
    $stmt->bindValue(':iptu', $dados['iptu'] ?? null);
    $stmt->bindValue(':telefone_contato', $dados['telefone_contato'] ?? null);
    $stmt->bindValue(':whatsapp', $dados['whatsapp'] ?? null);
    $stmt->bindValue(':email_contato', $dados['email_contato'] ?? null);
    $stmt->bindValue(':status', $dados['status'] ?? 'disponivel');

    return $stmt->execute();
}

/*
|--------------------------------------------------------------------------
| EXCLUIR IMÓVEL (EXCLUSÃO LÓGICA)
|--------------------------------------------------------------------------
*/

function excluirImovel(PDO $pdo, int $id): bool
{
    $stmtImagens = $pdo->prepare(
        'SELECT caminho_imagem FROM imagens_imoveis WHERE imovel_id = :id'
    );
    $stmtImagens->bindValue(':id', $id, PDO::PARAM_INT);
    $stmtImagens->execute();
    $caminhosImagens = $stmtImagens->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->prepare('DELETE FROM imoveis WHERE id = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    if (!$stmt->execute() || $stmt->rowCount() === 0) {
        return false;
    }

    $diretorioUploads = realpath(dirname(__DIR__, 3) . '/public/uploads/imoveis');

    if ($diretorioUploads === false) {
        return true;
    }

    foreach ($caminhosImagens as $caminhoImagem) {
        if (
            !is_string($caminhoImagem)
            || preg_match('~\Auploads/imoveis/([^/\\\\]+)\z~', $caminhoImagem, $partes) !== 1
        ) {
            continue;
        }

        $arquivo = realpath($diretorioUploads . DIRECTORY_SEPARATOR . $partes[1]);
        $prefixoUploads = $diretorioUploads . DIRECTORY_SEPARATOR;

        if (
            $arquivo !== false
            && str_starts_with($arquivo, $prefixoUploads)
            && is_file($arquivo)
            && !unlink($arquivo)
        ) {
            error_log('[Imoveis] Não foi possível remover a imagem: ' . $arquivo);
        }
    }

    return true;
}

function alterarStatusImovel(PDO $pdo, int $id, string $status): bool
{
    if (!in_array($status, ['disponivel', 'indisponivel'], true)) {
        return false;
    }

    $stmtAtual = $pdo->prepare('SELECT status FROM imoveis WHERE id = :id');
    $stmtAtual->bindValue(':id', $id, PDO::PARAM_INT);
    $stmtAtual->execute();
    $statusAtual = $stmtAtual->fetchColumn();

    if ($statusAtual === false) {
        return false;
    }

    if ($statusAtual === $status) {
        return true;
    }

    $stmt = $pdo->prepare('UPDATE imoveis SET status = :status WHERE id = :id');
    $stmt->bindValue(':status', $status);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    return $stmt->execute();
}


/*
|--------------------------------------------------------------------------
| SALVAR IMAGEM DO IMÓVEL
|--------------------------------------------------------------------------
*/

function salvarImagemImovel(
    PDO $pdo,
    int $imovelId,
    string $caminho,
    bool $principal = false
): void {
    if ($principal) {
        $stmtAtualizar = $pdo->prepare("
            UPDATE imagens_imoveis
            SET imagem_principal = 0
            WHERE imovel_id = :imovel_id
        ");

        $stmtAtualizar->execute([
            ':imovel_id' => $imovelId
        ]);
    }

    $sql = "
        INSERT INTO imagens_imoveis (
            imovel_id,
            caminho_imagem,
            imagem_principal
        ) VALUES (
            :imovel_id,
            :caminho,
            :principal
        )
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':imovel_id' => $imovelId,
        ':caminho' => $caminho,
        ':principal' => $principal ? 1 : 0
    ]);
}

function salvarUploadsImagensImovel(PDO $pdo, int $imovelId): int
{
    if (
        !isset($_FILES['imagens']['name']) ||
        !is_array($_FILES['imagens']['name'])
    ) {
        return 0;
    }

    $uploadDirectory = dirname(__DIR__, 3) . '/public/uploads/imoveis';
    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
        throw new RuntimeException('Não foi possível criar a pasta de imagens.');
    }

    $extensionsByMime = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    $images = listarImagensImovel($pdo, $imovelId);
    $principal = $images === [];
    $savedCount = 0;

    foreach ($_FILES['imagens']['name'] as $index => $name) {
        $error = $_FILES['imagens']['error'][$index] ?? UPLOAD_ERR_NO_FILE;
        if ($error === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if ($error !== UPLOAD_ERR_OK) {
            $uploadError = match ($error) {
                UPLOAD_ERR_INI_SIZE => 'Uma imagem excede o limite de tamanho configurado no servidor.',
                UPLOAD_ERR_FORM_SIZE => 'Uma imagem excede o limite de tamanho do formulário.',
                UPLOAD_ERR_PARTIAL => 'Uma imagem foi enviada parcialmente. Tente novamente.',
                UPLOAD_ERR_NO_TMP_DIR => 'A pasta temporária para uploads não está disponível.',
                UPLOAD_ERR_CANT_WRITE => 'O servidor não conseguiu gravar a imagem enviada.',
                UPLOAD_ERR_EXTENSION => 'Uma extensão do PHP interrompeu o envio da imagem.',
                default => 'O envio de uma das imagens falhou.',
            };
            throw new RuntimeException($uploadError);
        }

        $temporaryPath = $_FILES['imagens']['tmp_name'][$index] ?? '';
        $fileSize = (int) ($_FILES['imagens']['size'][$index] ?? 0);
        if ($fileSize <= 0 || $fileSize > 10 * 1024 * 1024) {
            throw new RuntimeException('Cada imagem deve ter no máximo 10 MB.');
        }

        $imageInfo = is_string($temporaryPath) ? getimagesize($temporaryPath) : false;
        $mimeType = is_array($imageInfo) ? ($imageInfo['mime'] ?? '') : '';
        if (!isset($extensionsByMime[$mimeType])) {
            throw new RuntimeException('Formato de imagem inválido. Use JPG, PNG ou WebP.');
        }

        $fileName = bin2hex(random_bytes(16)) . '.' . $extensionsByMime[$mimeType];
        $destination = $uploadDirectory . '/' . $fileName;
        if (!move_uploaded_file($temporaryPath, $destination)) {
            throw new RuntimeException('Não foi possível salvar uma das imagens enviadas.');
        }

        try {
            salvarImagemImovel(
                $pdo,
                $imovelId,
                'uploads/imoveis/' . $fileName,
                $principal
            );
        } catch (Throwable $e) {
            unlink($destination);
            throw $e;
        }

        $principal = false;
        $savedCount++;
    }

    return $savedCount;
}

/*
|--------------------------------------------------------------------------
| LISTAR IMAGENS DO IMÓVEL
|--------------------------------------------------------------------------
*/

function listarImagensImovel(PDO $pdo, int $imovelId): array
{
    $sql = "
        SELECT
            id,
            caminho_imagem,
            imagem_principal
        FROM imagens_imoveis
        WHERE imovel_id = :imovel_id
        ORDER BY imagem_principal DESC, id ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':imovel_id', $imovelId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/*
|--------------------------------------------------------------------------
| EXCLUIR IMAGEM DO IMÓVEL
|--------------------------------------------------------------------------
*/

function excluirImagemImovel(PDO $pdo, int $imagemId): bool
{
    // Buscar caminho da imagem antes de excluir
    $stmt = $pdo->prepare(
        'SELECT caminho_imagem FROM imagens_imoveis WHERE id = :id LIMIT 1'
    );
    $stmt->bindValue(':id', $imagemId, PDO::PARAM_INT);
    $stmt->execute();
    $imagem = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($imagem === false) {
        return false;
    }

    // Excluir do banco de dados
    $stmtDelete = $pdo->prepare(
        'DELETE FROM imagens_imoveis WHERE id = :id'
    );
    $stmtDelete->bindValue(':id', $imagemId, PDO::PARAM_INT);
    $stmtDelete->execute();

    // Remover arquivo físico se existir
    $caminhoArquivo = __DIR__ . '/../../../../public/' . $imagem['caminho_imagem'];
    if (is_file($caminhoArquivo)) {
        unlink($caminhoArquivo);
    }

    return true;
}
