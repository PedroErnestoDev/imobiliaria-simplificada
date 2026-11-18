# Dicionário de Dados — Banco `imobiliaria`

Documentação das tabelas e colunas criadas pelo script `docks/init.sql`.

---

## 1. usuarios

Armazena os usuários do sistema (corretores e administradores).

| Coluna | Tipo | Nulo? | Chave | Descrição |
|--------|------|-------|-------|-----------|
| id | INT | NÃO | PK | Identificador automático |
| nome | VARCHAR(150) | NÃO | | Nome completo do usuário |
| email | VARCHAR(150) | NÃO | UNIQUE | E-mail de login (único) |
| senha_hash | VARCHAR(255) | NÃO | | Hash da senha (bcrypt/argon2) |
| telefone | VARCHAR(20) | SIM | | Telefone de contato |
| tipo | ENUM('admin','corretor') | NÃO | DEFAULT 'corretor' | Perfil de acesso |
| ativo | BOOLEAN | NÃO | DEFAULT TRUE | Usuário ativo ou inativo |
| criado_em | TIMESTAMP | NÃO | DEFAULT CURRENT_TIMESTAMP | Data de criação |

**Relacionamentos:** um usuário pode cadastrar vários imóveis (`usuarios 1 — * imoveis` via `usuario_id`).

---

## 2. imoveis

Armazena os imóveis cadastrados para venda ou aluguel.

| Coluna | Tipo | Nulo? | Chave | Descrição |
|--------|------|-------|-------|-----------|
| id | INT | NÃO | PK | Identificador automático |
| codigo | VARCHAR(20) | NÃO | UNIQUE | Código interno do imóvel |
| usuario_id | INT | SIM | FK | Usuário responsável (cadastrou) |
| titulo | VARCHAR(150) | NÃO | | Título/anúncio do imóvel |
| descricao | TEXT | SIM | | Descrição detalhada |
| finalidade | ENUM('venda','aluguel') | NÃO | | Venda ou aluguel (RF02) |
| tipo | ENUM('casa','apartamento','condominio','terreno','na_planta') | NÃO | | Tipo do imóvel (RF03) |
| cep | VARCHAR(9) | NÃO | | CEP do endereço |
| endereco | VARCHAR(255) | NÃO | | Logradouro |
| numero | VARCHAR(20) | SIM | | Número |
| complemento | VARCHAR(100) | SIM | | Complemento |
| bairro | VARCHAR(100) | NÃO | | Bairro |
| cidade | VARCHAR(100) | NÃO | | Cidade |
| estado | CHAR(2) | NÃO | | Sigla do estado (UF) |
| quartos | INT | NÃO | DEFAULT 0 | Quantidade de quartos |
| banheiros | INT | NÃO | DEFAULT 0 | Quantidade de banheiros |
| vagas | INT | NÃO | DEFAULT 0 | Quantidade de vagas de garagem |
| area | DECIMAL(10,2) | SIM | | Área em m² |
| valor | DECIMAL(12,2) | NÃO | | Valor de venda/aluguel (> 0) |
| condominio | DECIMAL(10,2) | SIM | DEFAULT 0 | Valor do condomínio |
| iptu | DECIMAL(10,2) | SIM | DEFAULT 0 | Valor do IPTU |
| telefone_contato | VARCHAR(20) | SIM | | Telefone de contato do imóvel |
| whatsapp | VARCHAR(20) | SIM | | WhatsApp de contato |
| email_contato | VARCHAR(150) | SIM | | E-mail de contato |
| latitude | DECIMAL(10,7) | SIM | | Latitude para mapa |
| longitude | DECIMAL(10,7) | SIM | | Longitude para mapa |
| status | ENUM('disponivel','indisponivel') | NÃO | DEFAULT 'disponivel' | Disponibilidade (RF14) |
| criado_em | TIMESTAMP | NÃO | DEFAULT CURRENT_TIMESTAMP | Data de criação |
| atualizado_em | TIMESTAMP | NÃO | DEFAULT ... ON UPDATE | Data da última atualização |

**Restrições (CHECK):** `valor > 0`, `quartos >= 0`, `banheiros >= 0`, `vagas >= 0`, `area IS NULL OR area > 0`.

**Relacionamentos:**
- `usuarios 1 — * imoveis` (um usuário cadastra vários imóveis; `ON DELETE SET NULL`)
- `imoveis 1 — * imagens_imoveis` (`ON DELETE CASCADE`)
- `imoveis * — * caracteristicas` (via `imovel_caracteristicas`)

---

## 3. imagens_imoveis

Armazena as imagens de cada imóvel.

| Coluna | Tipo | Nulo? | Chave | Descrição |
|--------|------|-------|-------|-----------|
| id | INT | NÃO | PK | Identificador automático |
| imovel_id | INT | NÃO | FK | Imóvel ao qual a imagem pertence |
| caminho_imagem | VARCHAR(255) | NÃO | | Caminho/arquivo da imagem |
| imagem_principal | BOOLEAN | NÃO | DEFAULT FALSE | Indica se é a imagem principal |
| criado_em | TIMESTAMP | NÃO | DEFAULT CURRENT_TIMESTAMP | Data do upload |

**Relacionamento:** `imoveis 1 — * imagens_imoveis` (`ON DELETE CASCADE`).

---

## 4. caracteristicas

Catálogo de características dos imóveis (interna/externa).

| Coluna | Tipo | Nulo? | Chave | Descrição |
|--------|------|-------|-------|-----------|
| id | INT | NÃO | PK | Identificador automático |
| nome | VARCHAR(100) | NÃO | | Nome da característica |
| categoria | ENUM('interna','externa') | NÃO | | Categoria da característica |

**Restrição:** `UNIQUE (nome, categoria)`.

**Dados iniciais:** Box banheiro, Armário cozinha, Armário quarto (internas); Portão eletrônico, Interfone (externas).

---

## 5. imovel_caracteristicas

Tabela de junção entre imóveis e características (relacionamento N para N).

| Coluna | Tipo | Nulo? | Chave | Descrição |
|--------|------|-------|-------|-----------|
| imovel_id | INT | NÃO | PK, FK | Imóvel |
| caracteristica_id | INT | NÃO | PK, FK | Característica |

**Relacionamentos:** `imoveis * — * caracteristicas` (`ON DELETE CASCADE` em ambos).

---

## Diagramas relacionados

- [`Diagrama banco de dados.drawio.svg`](./Diagrama%20banco%20de%20dados.drawio.svg) — modelo entidade-relacionamento visual
- [`diagrama_classes_dominio.puml`](./diagrama_classes_dominio.puml) — classes de domínio
- [`dock.md`](./dock.md) — requisitos funcionais (RF01–RF17+)
