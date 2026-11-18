
<?php

require_once __DIR__ . '/../../db/config.php';

/*
|--------------------------------------------------------------------------
| VALIDAR CONEXÃO
|--------------------------------------------------------------------------
*/

if (!isset($pdo) || !$pdo instanceof PDO) {
    throw new RuntimeException(
        'A conexão PDO não foi inicializada em app/db/config.php.'
    );
}

/*
|--------------------------------------------------------------------------
| LISTAR IMÓVEIS
|--------------------------------------------------------------------------
*/

function listarImoveis(PDO $pdo, array $filtros = [], int $pagina = 1, int $limite = 9): array
{
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

    /*
    |--------------------------------------------------------------------------
    | FILTRO POR TIPO
    |--------------------------------------------------------------------------
    */

    if (!empty($filtros['tipo'])) {
        $sql .= " AND i.tipo = :tipo";
        $parametros[':tipo'] = $filtros['tipo'];
    }

    /*
    |--------------------------------------------------------------------------
    | FILTRO POR FINALIDADE
    |--------------------------------------------------------------------------
    */

    if (!empty($filtros['finalidade'])) {
        $sql .= " AND i.finalidade = :finalidade";
        $parametros[':finalidade'] = $filtros['finalidade'];
    }

    /*
    |--------------------------------------------------------------------------
    | PESQUISA
    |--------------------------------------------------------------------------
    */

    if (!empty($filtros['busca'])) {
        $sql .= "
            AND (
                i.titulo LIKE :busca
                OR i.codigo LIKE :busca
                OR i.bairro LIKE :busca
                OR i.cidade LIKE :busca
            )
        ";

        $parametros[':busca'] = '%' . $filtros['busca'] . '%';
    }

    /*
    |--------------------------------------------------------------------------
    | PREÇO MÍNIMO
    |--------------------------------------------------------------------------
    */

    if (isset($filtros['valor_min']) && $filtros['valor_min'] !== '') {
        $sql .= " AND i.valor >= :valor_min";
        $parametros[':valor_min'] = $filtros['valor_min'];
    }

    /*
    |--------------------------------------------------------------------------
    | PREÇO MÁXIMO
    |--------------------------------------------------------------------------
    */

    if (isset($filtros['valor_max']) && $filtros['valor_max'] !== '') {
        $sql .= " AND i.valor <= :valor_max";
        $parametros[':valor_max'] = $filtros['valor_max'];
    }

    /*
    |--------------------------------------------------------------------------
    | ORDENAÇÃO
    |--------------------------------------------------------------------------
    */

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
| CONTAR IMÓVEIS
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
                i.titulo LIKE :busca
                OR i.codigo LIKE :busca
                OR i.bairro LIKE :busca
                OR i.cidade LIKE :busca
            )
        ";

        $parametros[':busca'] = '%' . $filtros['busca'] . '%';
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

function buscarImovelPorId(PDO $pdo, int $id): ?array
{
    $sql = "SELECT * FROM imoveis WHERE id = :id LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $imovel = $stmt->fetch(PDO::FETCH_ASSOC);

    return $imovel ?: null;
}