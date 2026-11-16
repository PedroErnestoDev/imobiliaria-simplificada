# Documentação de Requisitos Funcionais
## Sistema de Gerenciamento Imobiliário — CRUD

### 1. Objetivo do sistema

O sistema tem como objetivo gerenciar imóveis disponíveis para venda e aluguel, permitindo o cadastro, a consulta, a edição e a exclusão de imóveis. Também deverá disponibilizar filtros de pesquisa para facilitar a localização de imóveis conforme as necessidades dos usuários, além de apresentar sua localização em um mapa interativo.

### 2. Requisitos funcionais

**RF01 — Cadastro de imóveis**

O sistema deverá permitir cadastrar imóveis, armazenando título, descrição, finalidade, tipo, localização, endereço, CEP, quantidade de quartos, banheiros, vagas, área, valor, imagens e status de disponibilidade.

**RF02 — Definição da finalidade**

O sistema deverá exigir a seleção de uma finalidade para cada imóvel, sendo obrigatoriamente uma das opções:

- Venda;
- Aluguel.

**RF03 — Definição do tipo de imóvel**

O sistema deverá permitir classificar os imóveis nas seguintes categorias:

- Casa;
- Apartamento;
- Condomínio;
- Terreno;
- Imóvel na planta.

**RF04 — Listagem de imóveis**

O sistema deverá apresentar os imóveis cadastrados, exibindo suas principais informações, como imagem, título, localização, quantidade de quartos, finalidade e valor.

**RF05 — Visualização de detalhes**

O sistema deverá permitir acessar uma página ou seção com os detalhes completos de cada imóvel cadastrado.

**RF06 — Filtro por finalidade**

O sistema deverá permitir filtrar os imóveis conforme a finalidade selecionada, entre venda e aluguel.

**RF07 — Filtro por localização**

O sistema deverá permitir pesquisar imóveis por cidade, bairro ou região.

**RF08 — Filtro por quantidade de quartos**

O sistema deverá permitir filtrar imóveis pela quantidade mínima de quartos desejada.

**RF09 — Filtro por faixa de preço**

O sistema deverá permitir definir um valor mínimo e um valor máximo para pesquisar imóveis dentro de determinada faixa de preço.

**RF10 — Filtro por tipo de imóvel**

O sistema deverá permitir filtrar os imóveis pelas seguintes categorias:

- Casa;
- Apartamento;
- Condomínio;
- Terreno;
- Imóvel na planta.

**RF11 — Combinação de filtros**

O sistema deverá permitir utilizar diferentes filtros simultaneamente, combinando finalidade, localização, quantidade de quartos, faixa de preço e tipo de imóvel.

**RF12 — Edição de imóveis**

O sistema deverá permitir atualizar as informações dos imóveis cadastrados, incluindo dados descritivos, finalidade, localização, características, valores, imagens e disponibilidade.

**RF13 — Exclusão de imóveis**

O sistema deverá permitir excluir imóveis cadastrados, solicitando uma confirmação antes de concluir a operação.

**RF14 — Controle de disponibilidade**

O sistema deverá permitir definir o status de cada imóvel como disponível ou indisponível.

**RF15 — Gerenciamento de imagens**

O sistema deverá permitir adicionar e atualizar imagens dos imóveis cadastrados, possibilitando sua apresentação na listagem e na visualização de detalhes.

**RF16 — Validação de dados**

O sistema deverá validar os dados informados durante o cadastro e a edição, impedindo o envio de informações inválidas ou a ausência de campos obrigatórios.

**RF17 — Consulta de CEP (ViaCEP)**

O sistema deverá integrar a API ViaCEP para consultar o CEP informado pelo usuário e preencher automaticamente os campos de endereço, incluindo rua, bairro, cidade e estado.

**RF18 — Geocodificação de endereços (Nominatim)**

O sistema deverá integrar o serviço Nominatim, do OpenStreetMap, para converter os endereços obtidos pelo ViaCEP em coordenadas geográficas, permitindo identificar a localização dos imóveis.

**RF19 — Visualização de localização (OpenLayers)**

O sistema deverá utilizar a biblioteca OpenLayers para apresentar um mapa interativo, permitindo visualizar a localização dos imóveis cadastrados por meio das coordenadas obtidas pelo Nominatim.

**RF20 — Integração entre os serviços de localização**

O sistema deverá integrar ViaCEP, Nominatim e OpenLayers, permitindo que a consulta do CEP resulte na identificação e apresentação da localização do imóvel no mapa, sem exigir que o usuário informe manualmente latitude e longitude.

### 3. Regras de negócio

- **RN01:** Todo imóvel deverá possuir uma finalidade obrigatória: venda ou aluguel.
- **RN02:** Todo imóvel deverá possuir um tipo obrigatório: casa, apartamento, condomínio, terreno ou imóvel na planta.
- **RN03:** O valor do imóvel deverá ser maior que zero.
- **RN04:** A quantidade de quartos deverá ser igual ou superior a zero.
- **RN05:** O valor mínimo informado na pesquisa não poderá ser superior ao valor máximo.
- **RN06:** Imóveis indisponíveis não deverão aparecer na listagem pública padrão.
- **RN07:** Os filtros deverão funcionar individualmente ou combinados.
- **RN08:** Os campos obrigatórios deverão ser preenchidos antes de cadastrar ou atualizar um imóvel.
- **RN09:** O CEP deverá possuir formato válido para realizar a consulta de endereço.
- **RN10:** Caso o CEP não seja encontrado, o sistema deverá informar o usuário e permitir a correção.
- **RN11:** Caso o Nominatim não encontre a localização, o sistema deverá informar que não foi possível identificar o endereço no mapa, sem impedir o cadastro do imóvel.
- **RN12:** As coordenadas geográficas deverão ser obtidas automaticamente, sem preenchimento manual pelo usuário.

### 4. Operações principais do sistema

| Operação | Funcionalidade |
|---|---|
| Create | Cadastrar imóveis |
| Read | Listar e visualizar imóveis |
| Update | Editar imóveis cadastrados |
| Delete | Excluir imóveis cadastrados |

## **5. Integrações utilizadas**

### **5.1. ViaCEP**

O **ViaCEP** é uma API gratuita utilizada para consultar informações de endereço a partir de um Código de Endereçamento Postal (CEP).

No sistema, será responsável por:

- Consultar o endereço informado pelo usuário a partir do CEP.
- Preencher automaticamente os campos de logradouro, bairro, cidade e estado.
- Reduzir a necessidade de digitação manual.
- Diminuir erros no cadastro dos endereços dos imóveis.

**Exemplo de utilização:** ao informar o CEP de um imóvel, o sistema consulta o ViaCEP e preenche automaticamente os dados de endereço correspondentes.

Documentação: [https://viacep.com.br/](https://viacep.com.br/?utm_source=chatgpt.com)

### **5.2. Nominatim — OpenStreetMap**

O **Nominatim** é um serviço de geocodificação do OpenStreetMap que permite pesquisar localizações por meio de endereços e convertê-las em coordenadas geográficas.

No sistema, será responsável por:

- Receber o endereço do imóvel.
- Pesquisar a localização geográfica correspondente.
- Retornar as coordenadas de latitude e longitude.
- Disponibilizar essas coordenadas para posicionar o imóvel no mapa.

**Exemplo de utilização:** após o preenchimento do endereço pelo ViaCEP, o sistema envia os dados de localização ao Nominatim, que retorna as coordenadas geográficas do imóvel.

Documentação: [https://nominatim.org/release-docs/latest/](https://nominatim.org/release-docs/latest/?utm_source=chatgpt.com)

### **5.3. OpenLayers**

O **OpenLayers** é uma biblioteca JavaScript de código aberto utilizada para incorporar mapas interativos em aplicações web.

No sistema, será responsável por:

- Exibir mapas interativos na interface.
- Posicionar marcadores nos locais dos imóveis cadastrados.
- Utilizar as coordenadas de latitude e longitude retornadas pelo Nominatim.
- Permitir que o usuário visualize a localização geográfica dos imóveis.

**Exemplo de utilização:** após obter as coordenadas do imóvel, o sistema utiliza o OpenLayers para exibir um marcador no mapa, indicando sua localização.

Documentação: [https://openlayers.org/](https://openlayers.org/?utm_source=chatgpt.com)

## **6. Fluxo de localização dos imóveis**

O processo de localização será realizado pela integração entre as três tecnologias:

1. **Informação do CEP:** o usuário informa o CEP do imóvel no formulário de cadastro.
2. **Consulta ao ViaCEP:** o sistema consulta a API e recebe os dados do endereço.
3. **Preenchimento automático:** os campos de logradouro, bairro, cidade e estado são preenchidos.
4. **Geocodificação pelo Nominatim:** o sistema utiliza o endereço obtido para consultar as coordenadas geográficas.
5. **Retorno das coordenadas:** o Nominatim retorna a latitude e a longitude correspondentes à localização encontrada.
6. **Exibição pelo OpenLayers:** a biblioteca utiliza as coordenadas para posicionar um marcador no mapa.
7. **Armazenamento:** as coordenadas são salvas no banco de dados junto às informações do imóvel, permitindo sua exibição posterior sem a necessidade de realizar uma nova consulta de geocodificação.

**Representação do fluxo:**

```
Usuário informa o CEP
        |
        v
     ViaCEP
        |
        v
Preenchimento automático
     do endereço
        |
        v
    Nominatim
        |
        v
Retorno da latitude
   e longitude
        |
        v
    OpenLayers
        |
        v
Exibição do imóvel
      no mapa
        |
        v
Armazenamento no banco
     de dados
```

**Observação técnica:** o ViaCEP e o Nominatim são serviços externos de consulta. Já o OpenLayers é uma biblioteca JavaScript utilizada na aplicação para exibir e manipular mapas. Caso o Nominatim não encontre o endereço, o sistema deverá informar o usuário e permitir que o cadastro do imóvel continue sem as coordenadas.

### 6. Fluxo de localização dos imóveis

1. O usuário informa o CEP durante o cadastro do imóvel.
2. O sistema consulta a API ViaCEP.
3. O ViaCEP retorna os dados do endereço.
4. O sistema preenche automaticamente os campos correspondentes.
5. O endereço é enviado ao Nominatim para geocodificação.
6. O Nominatim retorna as coordenadas geográficas, quando disponíveis.
7. O OpenLayers utiliza as coordenadas para apresentar a localização no mapa.
8. O sistema salva os dados do imóvel.

### 7. Critérios de conclusão

O sistema será considerado funcional quando permitir realizar todas as operações CRUD, aplicar corretamente os filtros de pesquisa, validar os dados cadastrados, controlar a disponibilidade dos imóveis e integrar os serviços de localização para apresentar os imóveis em um mapa interativo.

### 8. Observações finais

Esta documentação contempla os requisitos funcionais e as regras de negócio da primeira versão do sistema.

A estrutura do banco de dados, os requisitos não funcionais, os diagramas UML e os detalhes de implementação poderão ser documentados separadamente.