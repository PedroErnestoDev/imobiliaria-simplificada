# Guia do Desenvolvedor — DW2 Imobiliária

Documentação técnica para quem deseja entender, estender ou manter o código-fonte do sistema.

---

## 1. Arquitetura

O projeto adota uma **organização modular**, separando apresentação, processamento, configurações e integrações externas. **Não segue o padrão MVC.** O arquivo `public/index.php` é o ponto de entrada da aplicação.

### 1.1 Stack tecnológica

| Camada | Tecnologia |
|--------|------------|
| Backend | PHP 8.3 (organização modular, sem MVC) |
| Banco de dados | MySQL 8.0 |
| Servidor web | Apache (via Docker) |
| Frontend | HTML5, CSS3, Bootstrap 5.3, JavaScript |
| Containerização | Docker + Docker Compose |
| Diagramas | draw.io / PlantUML |

### 1.2 Estrutura de diretórios (atual)

```
imobiliaria-simplificada/
├── app/
│   ├── componentes/      # Componentes reutilizáveis (header, footer)
│   ├── db/               # Configuração de conexão (Database.php)
│   └── programacao/      # Lógica de programação (imoveis.php — vazio)
├── public/
│   ├── index.php         # Ponto de entrada da aplicação (landing page)
│   └── assets/           # Imagens, CSS e JS estáticos
├── docks/                # Diagramas e documentação técnica
│   ├── init.sql          # Script de inicialização do banco de dados
│   ├── dock.md           # Requisitos funcionais e estrutura do projeto
│   ├── dicionario_dados.md
│   ├── api_endpoints.md
│   └── *.puml / *.svg    # Fontes e renderizações dos diagramas
├── docker-compose.yml    # Orquestração dos containers (web, db, phpmyadmin)
├── Dockerfile            # Imagem PHP 8.3 + Apache
├── .gitignore            # Arquivos ignorados pelo Git
└── README.md             # Documentação principal
```

### 1.3 Estrutura planejada (docks/dock.md)

O documento `docks/dock.md` descreve uma estrutura ideal futura, ainda não refletida no código:

```
config/           # Configuração (database.php)
db/               # Scripts SQL (init.sql)
docks/            # Diagramas e documentos visuais
includes/         # Componentes reutilizáveis (header, footer)
pages/            # Páginas de apresentação
api/              # Integrações externas (viacep.php, geocoding.php)
actions/          # Processamento de operações (CRUD, autenticação)
storage/          # Arquivos enviados (uploads/)
public/           # Ponto de entrada e arquivos estáticos
```


---

## 2. Componentes de código

### 2.1 Conexão com banco — `app/db/config.php`

Classe `Database` que encapsula a conexão PDO com MySQL:

```php
class Database {
    private string $host = 'db';
    private string $database = 'imobiliaria';
    private string $username = 'root';
    private string $password = 'root123456';
    private string $charset = 'utf8mb4';

    public function connect(): PDO { ... }
}
```

- Usa `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`
- Usa `PDO::ATTR_EMULATE_PREPARES => false` (prepared statements reais)
- Retorna instância de `PDO`

### 2.2 Lógica de imóveis — `app/programacao/imoveis.php`

Arquivo atualmente **vazio**, destinado a concentrar a lógica de negócio dos imóveis (CRUD, filtros, upload de imagens).

### 2.3 Componentes de interface

- `app/componentes/header.php` — navbar com estilos e links de navegação
- `app/componentes/footer.php` — rodapé com links institucionais e área do cliente

### 2.4 Ponto de entrada — `public/index.php`

Página de destino (landing page) estática com:

- Seção hero com banner
- Busca rápida
- Cards de categorias de imóveis
- Seção "Sobre"
- Inclusão do rodapé via `require_once`

---

## 3. Banco de dados

O esquema é criado pelo script `docks/init.sql`. Tabelas:

| Tabela | Finalidade |
|--------|------------|
| `usuarios` | Usuários do sistema (corretores e administradores) |
| `imoveis` | Imóveis cadastrados para venda ou aluguel |
| `imagens_imoveis` | Imagens de cada imóvel |
| `caracteristicas` | Catálogo de características (interna/externa) |
| `imovel_caracteristicas` | Junção N:N entre imóveis e características |

O script também insere 5 características iniciais (3 internas, 2 externas).

### 3.1 Credenciais de banco (desenvolvimento)

| Usuário | Senha | Banco |
|---------|-------|-------|
| `root` | `root123456` | `imobiliaria` |
| `admin` | `admin123456` | `imobiliaria` |

> Nunca transforme credenciais de produção. Em produção, use variáveis de ambiente (`.env`).

---

## 4. Integrações externas

| Integração | Base URL | Finalidade |
|------------|----------|------------|
| ViaCEP | `https://viacep.com.br/ws/` | Preenchimento automático de endereço por CEP |
| Nominatim / OpenStreetMap | `https://nominatim.openstreetmap.org/` | Geocodificação (endereço → coordenadas) |

Detalhes em `docks/api_endpoints.md`.

---

## 5. Como executar localmente

```bash
# 1. Clone o repositório
git clone git@github.com:PedroErnestoDev/imobiliaria-simplificada.git
cd imobiliaria-simplificada

# 2. Suba os containers
docker compose up -d --build

# 3. Acesse
#   Aplicação: http://localhost:8083
#   phpMyAdmin: http://localhost:8889
```

O banco de dados é inicializado automaticamente pelo script `docks/init.sql`.

### 5.1 Parar e remover containers

```bash
docker compose down
```

### 5.2 Rebuild sem cache

```bash
docker compose up -d --build --no-cache
```

---

## 6. Como contribuir

1. Faça um fork do repositório
2. Crie uma branch para sua feature (`git checkout -b feature/minha-feature`)
3. Commit suas mudanças (`git commit -m "feat: descrição"`)
4. Envie para a branch (`git push origin feature/minha-feature`)
5. Abra um Pull Request

---

## 7. Diagramas

Todos os diagramas estão na pasta `docks/`. As fontes editáveis estão em `.puml` (PlantUML) e as renderizações em `.svg`.

| Diagrama | Arquivo fonte | Visualização |
|----------|--------------|--------------|
| Banco de dados (ER) | `Diagrama banco de dados.drawio.svg` | `docks/` |
| Casos de uso | `caso_de_uso.drawio.svg` | `docks/` |
| Fluxo de autenticação | `Fluxo de autenticação.drawio.svg` | `docks/` |
| Atividades — Visão geral | `Diagrama de atividades — Visão geral.drawio.svg` | `docks/` |
| Sequência — Cadastro de imóvel | `diagrama_sequencia_cadastro_imovel.puml` | `.svg` |
| Sequência — Login | — | [`Sequência — Login de Usuário.drawio.svg`](Sequência%20%E2%80%94%20Login%20de%20Usu%C3%A1rio.drawio.svg) |
| Sequência — Consulta de imóveis | `diagrama_sequencia_consulta_imovel.puml` | `.svg` |
| Componentes | `diagrama_componentes.puml` | `.svg` |
| Implantação (Docker) | `diagrama_implantacao_docker.puml` | `.svg` |
| Classes de domínio | `diagrama_classes_dominio.svg` | `.svg` |
| Estado do imóvel | `diagrama_estado_imovel.svg` | `.svg` |

### 7.1 Como editar

- Arquivos `.puml`: abra no [PlantUML Online Server](https://www.plantuml.com/plantuml/) ou editor com suporte a PlantUML (VS Code, IntelliJ).
- Arquivos `.svg` do draw.io: arraste o arquivo para o [draw.io](https://app.diagrams.net/).

---

## 8. Requisitos funcionais (resumo)

Documentados em `docks/dock.md` (RF01–RF17+):

| RF | Descrição |
|----|-----------|
| RF01 | Cadastro de imóveis |
| RF02 | Definição da finalidade (venda/aluguel) |
| RF03 | Definição do tipo de imóvel |
| RF04 | Listagem de imóveis |
| RF05 | Visualização de detalhes |
| RF06 | Filtro por finalidade |
| RF07 | Filtro por localização |
| RF08 | Filtro por quantidade de quartos |
| RF09 | Filtro por faixa de preço |
| RF10 | Filtro por tipo de imóvel |
| RF11 | Combinação de filtros |
| RF12 | Edição de imóveis |
| RF13 | Exclusão de imóveis |
| RF14 | Controle de disponibilidade |
| RF15 | Gerenciamento de imagens |
| RF16 | Validação de dados |
| RF17 | Consulta de CEP (ViaCEP) |
| RF18+ | Mapa interativo (Nominatim/OpenStreetMap) |

> **Nota:** parte desses requisitos está apenas especificada em `docks/dock.md` e ainda não foi implementada no código-fonte atual.
