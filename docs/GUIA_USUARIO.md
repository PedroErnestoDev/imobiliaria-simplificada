# Guia do Usuário — DW2 Imobiliária

Sistema de gerenciamento imobiliário para cadastro, consulta, edição e exclusão de imóveis disponíveis para venda e aluguel.

> Este guia descreve como utilizar o sistema a partir das funcionalidades planejadas e já implementadas. Algumas telas (cadastro, login, gerenciamento de usuários) estão previstas nos requisitos funcionais (`docks/dock.md`) e ainda em fase de implementação no código-fonte.

---

## 1. Primeiros passos

### 1.1 Acessando o sistema

Após subir os containers com Docker:

| Serviço | URL | Usuário | Senha |
|---------|-----|---------|-------|
| Aplicação | `http://localhost:8083` | — | — |
| phpMyAdmin | `http://localhost:8889` | `root` | `root123456>` |

> As credenciais acima são apenas para **ambiente de desenvolvimento**. Nunca as utilize em produção.

### 1.2 Navegação principal

O cabeçalho (navbar) disponibiliza:

- **Início** — página principal com banner, busca rápida e cards de categorias (casa, apartamento, terreno, etc.)
- **Imóveis** — listagem de imóveis disponíveis
- **Quem Somos** — institucional da DW2 Imobiliária
- **Contato** — canal de atendimento
- **Entrar** — área de autenticação do corretor/administrador

---

## 2. Consulta pública de imóveis

Qualquer visitante pode consultar os imóveis sem necessidade de login.

### 2.1 Listagem

Na página de imóveis (`imoveis.php`) são exibidos os cards com:

- Imagem principal do imóvel
- Tipo (Casa, Apartamento, Condomínio, Terreno, Imóvel na planta)
- Finalidade (Venda ou Aluguel)
- Título e descrição resumida
- Localização (cidade / bairro)
- Valor
- Quantidade de quartos, banheiros e vagas

### 2.2 Filtros de pesquisa (RF06–RF11)

É possível combinar os seguintes filtros simultaneamente:

| Filtro | Descrição |
|--------|-----------|
| Finalidade | Venda ou Aluguel |
| Tipo de imóvel | Casa, Apartamento, Condomínio, Terreno, Imóvel na planta |
| Localização | Cidade, bairro ou região |
| Quartos | Quantidade mínima de quartos |
| Faixa de preço | Valor mínimo e máximo |

Os resultados são atualizados de acordo com a combinação selecionada.

### 2.3 Detalhes do imóvel (RF05)

Ao clicar em um imóvel, a página de detalhes exibe:

- Todas as imagens cadastradas (com destaque para a imagem principal)
- Descrição completa
- Endereço completo (CEP, logradouro, número, complemento, bairro, cidade, estado)
- Características internas e externas
- Valor de venda/aluguel, condomínio e IPTU
- Contatos (telefone, WhatsApp, e-mail)
- Localização no mapa interativo (OpenStreetMap / Nominatim)

---

## 3. Área autenticada (corretor / administrador)

O acesso restrito permite gerenciar os imóveis.

> **Aviso:** o módulo de autenticação (login, cadastro de usuários, controle de acesso) está **planejado** nos requisitos funcionais (`docks/dock.md`), mas **ainda não está implementado** no código-fonte atual. As telas de cadastro, edição e exclusão de imóveis também estão em fase de implementação.

### 3.1 Cadastro de imóveis (RF01, RF02, RF03, RF15, RF16)

O corretor pode cadastrar um novo imóvel informando:

- Código interno (único)
- Título e descrição
- Finalidade (venda ou aluguel) — obrigatório
- Tipo (casa, apartamento, condomínio, terreno, na planta) — obrigatório
- CEP (consulta automática via ViaCEP para preencher endereço)
- Endereço, número, complemento, bairro, cidade, estado
- Quartos, banheiros, vagas, área (m²)
- Valor, condomínio, IPTU
- Contatos (telefone, WhatsApp, e-mail)
- Imagens (upload de uma ou mais; uma pode ser definida como principal)
- Status (disponível / indisponível)
- Características (seleção do catálogo interno/externo)

Todos os campos obrigatórios são validados antes do envio.

### 3.2 Edição de imóveis (RF12)

É possível atualizar qualquer imóvel cadastrado, incluindo:

- Dados descritivos, finalidade e tipo
- Localização e características
- Valores e contatos
- Imagens (adicionar, substituir ou remover)
- Status de disponibilidade

### 3.3 Exclusão de imóveis (RF13)

Ao solicitar a exclusão, o sistema exibe uma confirmação. Após confirmar:

- O imóvel é removido
- Suas imagens associadas são excluídas (`ON DELETE CASCADE`)
- O cadastro não pode ser desfeito

### 3.4 Controle de disponibilidade (RF14)

Defina cada imóvel como:

- **Disponível** — aparece nas buscas públicas e listagens
- **Indisponível** — oculto da consulta pública (alugado/vendido ou fora de mercado)

---

## 4. Integrações externas utilizadas

| Integração | Finalidade |
|------------|------------|
| **ViaCEP** | Preenchimento automático de endereço ao informar o CEP |
| **Nominatim / OpenStreetMap** | Conversão de endereço em coordenadas para o mapa interativo |

Mais detalhes em `docks/api_endpoints.md`.

---

## 5. Boas práticas de uso

- Preencha sempre os campos obrigatórios marcados com asterisco (*).
- Use CEP válido para aproveitar o preenchimento automático de endereço.
- Faça upload de imagens apenas nos formatos permitidos (JPG, PNG) e respeite o limite de tamanho.
- Mantenha o status do imóvel atualizado (disponível / indisponível) para refletir a realidade do mercado.
- Não compartilhe suas credenciais de acesso.

---

## 6. Suporte

Em caso de dúvidas ou problemas, acesse a seção **Contato** no sistema ou entre em contato com a equipe de suporte da DW2 Imobiliária.
