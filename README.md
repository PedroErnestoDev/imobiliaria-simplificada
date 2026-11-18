# Imobiliária Simplificada

Sistema de gerenciamento imobiliário para cadastro, consulta, edição e exclusão de imóveis disponíveis para venda e aluguel, com filtros de pesquisa e localização em mapa interativo.

## 📋 Visão geral

A **DW2 Imobiliária** conecta pessoas a oportunidades imobiliárias. O sistema permite:

- Consulta pública de imóveis com filtros (finalidade, tipo, localização, quartos, faixa de preço)
- Cadastro, edição, exclusão e controle de disponibilidade de imóveis
- Gerenciamento de imagens por imóvel
- Consulta de endereço por CEP (ViaCEP) e geolocalização (Nominatim/OpenStreetMap)

> ⚠️ O módulo de autenticação (login, cadastro de usuários, controle de acesso) está especificado em `docs/dock.md`, mas **ainda não está implementado** no código-fonte atual.

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

O banco de dados é inicializado automaticamente pelo script `db/init.sql` (tabelas e características iniciais).

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
├── docs/                 # Diagramas e documentação técnica
│   ├── dock.md           # Requisitos funcionais
│   └── *.drawio.svg      # Diagramas do sistema
├── db/
│   └── init.sql          # Script de inicialização do banco de dados
├── docker-compose.yml    # Orquestração dos containers (web, db, phpmyadmin)
├── Dockerfile            # Imagem PHP 8.3 + Apache
├── .gitignore            # Arquivos ignorados pelo Git
└── README.md             # Este arquivo
```

> O projeto adota uma **organização modular**, separando apresentação, processamento, configurações e integrações externas. **Não segue o padrão MVC.** O arquivo `public/index.php` é o ponto de entrada da aplicação.

## 📐 Diagramas

Os diagramas estão na pasta `docs/`.

| Diagramas | Arquivo fonte | Visualização |
|----------|--------------|--------------|
| Banco de dados (ER) | [`Diagrama Entidade-Relacionamento — Sistema Imobiliário.drawio.svg`](docs/Diagrama%20Entidade-Relacionamento%20—%20Sistema%20Imobiliário.drawio.svg) |
| Casos de uso | [`Diagrama de Casos de Uso — DW2 Imobiliária.drawio.svg`](docs/Diagrama%20de%20Casos%20de%20Uso%20—%20DW2%20Imobiliária.drawio.svg) |
| Sequência — Cadastro de imóvel | [`Diagrama de Sequência — Cadastro de Imóvel.drawio.svg`](docs/Diagrama%20de%20Sequência%20—%20Cadastro%20de%20Imóvel.drawio.svg) |
| Sequência — Consulta de imóveis | [`Diagrama de Sequência — Consulta de Imóveis.drawio.svg`](docs/Diagrama%20de%20Sequência%20—%20Consulta%20de%20Imóveis.drawio.svg) |
| Sequência — Login | [`Sequência — Login de Usuário.drawio.svg`](docs/Sequência%20—%20Login%20de%20Usuário.drawio.svg) |
| Componentes | [`diagrama_componentes.svg`](docs/diagrama_componentes.svg) |
| Estado do imóvel | [`diagrama_estado_imovel.svg`](docs/diagrama_estado_imovel.svg) |

## 📚 Documentação completa

Toda a documentação do projeto está na pasta `docs/`:

| Documento | Descrição |
|-----------|-----------|
| [`GUIA_USUARIO.md`](docs/GUIA_USUARIO.md) | Guia para uso do sistema (consulta, filtros, cadastro de imóveis) |
| [`GUIA_DESENVOLVEDOR.md`](docs/GUIA_DESENVOLVEDOR.md) | Arquitetura, stack, estrutura, banco de dados e como contribuir |
| [`dock.md`](docs/dock.md) | Requisitos funcionais e estrutura do projeto |
| [`dicionario_dados.md`](docs/dicionario_dados.md) | Dicionário de dados do banco de dados |
| [`api_endpoints.md`](docs/api_endpoints.md) | Integrações externas (ViaCEP, Nominatim) |

## 🧑‍💻 Contribuição

1. Faça um fork do repositório
2. Crie uma branch para sua feature (`git checkout -b feature/minha-feature`)
3. Commit suas mudanças (`git commit -m "feat: descrição"`)
4. Envie para a branch (`git push origin feature/minha-feature`)
5. Abra um Pull Request

## 📄 Licença

Projeto acadêmico — curso DW2.
