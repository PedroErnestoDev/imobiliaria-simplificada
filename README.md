# Imobiliária Simplificada

Sistema de gerenciamento imobiliário para cadastro, consulta, edição e exclusão de imóveis disponíveis para venda e aluguel, com autenticação de usuários, controle de acesso (2FA), filtros de pesquisa e localização em mapa interativo.

## 📋 Visão geral

A **DW2 Imobiliária** conecta pessoas a oportunidades imobiliárias. O sistema permite:

- Consulta pública de imóveis com filtros (finalidade, tipo, localização, quartos, faixa de preço)
- Cadastro, edição, exclusão e controle de disponibilidade de imóveis
- Gerenciamento de imagens por imóvel
- Autenticação de corretores e administradores com verificação em duas etapas (2FA/TOTP)
- Consulta de endereço por CEP (ViaCEP) e geolocalização (Nominatim/OpenStreetMap)

## 🛠️ Stack tecnológica

| Camada | Tecnologia |
|--------|------------|
| Backend | PHP 8.3 (organização modular, sem padrão MVC) |
| Banco de dados | MySQL 8.0 |
| Servidor web | Apache (via Docker) |
| Frontend | HTML5, CSS3, Bootstrap 5.3, JavaScript |
| Containerização | Docker + Docker Compose |
| Diagramas | draw.io / PlantUML |

## 🚀 Como rodar o projeto

### Pré-requisitos
- [Docker](https://www.docker.com/) e [Docker Compose](https://docs.docker.com/compose/)

### Passos

```bash
# 1. Acesse a pasta do projeto
cd imobiliaria-simplificada

# 2. Suba os containers
docker compose up -d --build

# 3. Acesse a aplicação
#   Aplicação: http://localhost:8083
#   phpMyAdmin: http://localhost:8889 (root / root123456)
```

O banco de dados é inicializado automaticamente pelo script `docks/init.sql` (tabelas e características iniciais).

### Credenciais padrão (banco)
- Usuário: `root` / Senha: `root123456`
- Usuário app: `admin` / Senha: `admin123456`
- Banco: `imobiliaria`

> ⚠️ As credenciais estão definidas no `docker-compose.yml` e `app/db/config.php` apenas para ambiente de desenvolvimento. Em produção, use variáveis de ambiente (`.env`) e nunca versione segredos.

## 📁 Estrutura de diretórios

```
imobiliaria-simplificada/
├── app/
│   ├── componentes/      # Componentes reutilizáveis (header, footer)
│   ├── db/               # Configuração de conexão (Database.php)
│   └── programacao/      # Lógica de programação (imoveis.php)
├── public/
│   ├── index.php         # Ponto de entrada da aplicação
│   └── assets/           # Imagens, CSS e JS estáticos
├── docks/                # Diagramas e documentação técnica
│   ├── init.sql          # Script de inicialização do banco de dados
│   ├── dock.md           # Requisitos funcionais e estrutura do projeto
│   └── *.puml / *.svg    # Fontes e renderizações dos diagramas
├── docker-compose.yml    # Orquestração dos containers (web, db, phpmyadmin)
├── Dockerfile            # Imagem PHP 8.3 + Apache
├── .gitignore            # Arquivos ignorados pelo Git
└── README.md             # Este arquivo
```

> O projeto adota uma **organização modular**, separando apresentação, processamento, configurações e integrações externas. **Não segue o padrão MVC.** O arquivo `public/index.php` é o ponto de entrada da aplicação.

## 📐 Diagramas

Todos os diagramas estão na pasta `docks/`. As fontes editáveis estão em `.puml` (PlantUML) e as renderizações em `.svg`.

| Diagrama | Arquivo fonte | Visualização |
|----------|--------------|--------------|
| Banco de dados (ER) | `Diagrama banco de dados.drawio.svg` | `docks/` |
| Casos de uso | `caso_de _uso.drawio.svg` | `docks/` |
| Fluxo de autenticação | `Fluxo de autenticação.drawio.svg` | `docks/` |
| Atividades — Visão geral | `Diagrama de atividades — Visão geral.drawio.svg` | `docks/` |
| Sequência — Cadastro de imóvel | `diagrama_sequencia_cadastro_imovel.puml` | `.svg` |
| Sequência — Login com 2FA | `diagrama_sequencia_login_2fa.puml` | `.svg` |
| Sequência — Consulta de imóveis | `diagrama_sequencia_consulta_imovel.puml` | `.svg` |
| Componentes | `diagrama_componentes.puml` | `.svg` |
| Implantação (Docker) | `diagrama_implantacao_docker.puml` | `.svg` |
| Classes de domínio | `diagrama_classes_dominio.puml` | `.svg` |
| Estado do imóvel | `diagrama_estado_imovel.puml` | `.svg` |

### Como editar os diagramas
- Abra os arquivos `.puml` no [PlantUML Online Server](https://www.plantuml.com/plantuml/) ou em qualquer editor com suporte a PlantUML (VS Code, IntelliJ, etc.).
- Os arquivos `.svg` existentes são do [draw.io](https://app.diagrams.net/) e podem ser editados arrastando o arquivo para o editor.

## 📖 Documentação relacionada

- [`docks/dock.md`](docks/dock.md) — Requisitos funcionais (RF01–RF17+) e estrutura do projeto
- [`docks/dicionario_dados.md`](docks/dicionario_dados.md) — Dicionário de dados do banco de dados
- [`docks/api_endpoints.md`](docks/api_endpoints.md) — Integrações externas (ViaCEP, Nominatim)

## 🧑‍💻 Contribuição

1. Faça um fork do repositório
2. Crie uma branch para sua feature (`git checkout -b feature/minha-feature`)
3. Commit suas mudanças (`git commit -m "feat: descrição"`)
4. Envie para a branch (`git push origin feature/minha-feature`)
5. Abra um Pull Request

## 📄 Licença

Projeto acadêmico — curso DW2.