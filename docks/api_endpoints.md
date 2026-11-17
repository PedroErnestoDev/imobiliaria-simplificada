# Integrações Externas — Endpoints de API

Documentação das integrações utilizadas pelo sistema (RF17 e mapa interativo).

---

## 1. ViaCEP — Consulta de CEP

Utilizada para preenchimento automático de endereço ao informar um CEP (cadastro/edição de imóvel e filtros).

| Item | Valor |
|------|-------|
| Base URL | `https://viacep.com.br/ws/` |
| Método | `GET` |
| Formato | JSON |
| Endpoint exemplo | `GET https://viacep.com.br/ws/01001000/json/` |

### Parâmetros
| Parâmetro | Tipo | Obrigatório? | Descrição |
|-----------|------|--------------|-----------|
| `{cep}` | string (8 dígitos) | SIM | CEP a ser consultado |

### Resposta de sucesso (exemplo)
```json
{
  "cep": "01001-000",
  "logradouro": "Praça da Sé",
  "complemento": "lado ímpar",
  "bairro": "Sé",
  "localidade": "São Paulo",
  "uf": "SP",
  "ibge": "3550308",
  "gia": "1004",
  "ddd": "11",
  "siafi": "7107"
}
```

### Casos de uso no sistema
- **RF17 — Consulta de CEP (ViaCEP):** ao cadastrar ou editar um imóvel, o CEP pode ser informado para preencher automaticamente logradouro, bairro, cidade e estado.
- Uso também nos filtros de pesquisa por localização.

---

## 2. Nominatim / OpenStreetMap — Geocodificação

Utilizada para converter endereços em coordenadas (latitude/longitude) e exibir a localização do imóvel em mapa interativo.

| Item | Valor |
|------|-------|
| Base URL | `https://nominatim.openstreetmap.org/` |
| Método | `GET` |
| Formato | JSON |
| Endpoint exemplo | `GET https://nominatim.openstreetmap.org/search?q=Pra%C3%A7a+da+S%C3%A9+S%C3%A3o+Paulo&format=json` |

### Parâmetros comuns
| Parâmetro | Tipo | Obrigatório? | Descrição |
|-----------|------|--------------|-----------|
| `q` | string | SIM | Endereço completo a ser geocodificado |
| `format` | string | SIM | Formato de resposta (`json`) |
| `limit` | int | NÃO | Quantidade máxima de resultados |

### Resposta de sucesso (exemplo resumido)
```json
[
  {
    "place_id": "12345678",
    "lat": "-23.5505",
    "lon": "-46.6333",
    "display_name": "Praça da Sé, Sé, São Paulo, SP, Brasil",
    "type": "square"
  }
]
```

### Casos de uso no sistema
- Exibição da localização do imóvel em mapa interativo na página de detalhes.
- Armazenamento de `latitude` e `longitude` na tabela `imoveis`.

---

## Recomendações de uso

1. **Tratamento de erros:** ambas as APIs podem retornar erro ou dados vazios. O sistema deve validar a resposta e permitir preenchimento manual do endereço caso a consulta falhe.
2. **Rate limit (Nominatim):** respeite a política de uso do OpenStreetMap (máximo de 1 requisição por segundo, user-agent identificável).
3. **Cache:** considere armazenar em cache o resultado da consulta por CEP e as coordenadas geográficas para evitar chamadas repetidas.

---

## Diagramas relacionados

- [`diagrama_sequencia_consulta_imovel.puml`](./diagrama_sequencia_consulta_imovel.puml) — fluxo de consulta com ViaCEP e Nominatim
- [`dock.md`](./dock.md) — requisitos funcionais RF17 e mapa interativo
