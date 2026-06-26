# Documentação

## Introdução
Esta API foi desenvolvida com o framework Lumen para gerenciar informações sobre CPUs e GPUs. Abaixo estará descrito como executar a API, as rotas disponíveis, os parâmetros esperados, e as respostas fornecidas pela API.

## Requisitos

- Composer
- PHP
- MySQL

## Execução

Antes de executar, é necessário ir aos arquivos da API e modificar o arquivo .env.example, para adequa-lo ao seu próprio banco de dados.
Para executar, abra o servidor local, e então abra um cmd diretamente no diretório em que estiver a API. Execute os comandos:
```
composer install
php artisan migrate
php -S localhost:8000 -t public
```
Para instalar as dependencias do projeto, rodar as migrações do banco de dados e iniciar o servidor apontando para a pasta public.

Caso não vá utilizar a porta 8000, substitua no comando de iniciar o servidor.

## Autenticação
Algumas rotas da API exigem uma chave de autenticação. Para utilizar essas rotas, inclua o cabeçalho `X-API-KEY` com a chave de autenticação fornecida abaixo:

```
X-API-KEY: keySecreta
```


## Endpoints

### 1. CPUs

#### Listar todas as CPUs
- **Rota:** `GET /cpus`
- **Resposta:**
    ```json
    [
      {
        "id": 1,
        "model": "Ryzen 5 3600",
        "fabricante": "AMD",
        "arquitetura": "Zen 2",
        "cores": 6,
        "threads": 12,
        "clock": 3.6,
        "boost": 4.2,
        "integrated_graphics": null,
        "created_at": "2024-08-14T13:25:02.000000Z",
        "updated_at": "2024-08-14T13:25:02.000000Z"
      }
    ]
    ```

#### Consultar uma CPU específica
- **Rota:** `GET /cpus/{model}`
- **Parâmetros de Rota:**
  - `{model}`: Modelo da CPU (ex.: `Ryzen%205%203600`).
- **Resposta:**
    ```json
    {
      "id": 1,
      "model": "Ryzen 5 3600",
      "fabricante": "AMD",
      "arquitetura": "Zen 2",
      "cores": 6,
      "threads": 12,
      "clock": 3.6,
      "boost": 4.2,
      "integrated_graphics": null,
      "created_at": "2024-08-14T13:25:02.000000Z",
      "updated_at": "2024-08-14T13:25:02.000000Z"
    }
    ```

#### Adicionar uma nova CPU
- **Rota:** `POST /cpus`
- **Cabeçalhos:**
  - `Content-Type: application/json`
  - `X-API-KEY: keySecreta`
- **Exemplo de requisição:**
  ```json
  {
      "model": "Ryzen 7 5800X",
      "fabricante": "AMD",
      "arquitetura": "Zen 3",
      "cores": 8,
      "threads": 16,
      "clock": 3800,
      "boost": 4700,
      "integrated_graphics": false
  }
  ```

#### Atualizar uma CPU
- **Rota:** `PUT /cpus/{id}`
- **Parâmetros de Rota:**
  - `{id}`: ID da CPU (ex.: `1`).
- **Cabeçalhos:**
  - `Content-Type: application/json`
  - `X-API-KEY: keySecreta`
- **Exemplo de requisição:**
  ```json
  {
  "threads": 12,
  "boost": 4600
  }
  ```
  
#### Deletar uma CPU
- **Rota:** `DELETE /cpus/{id}`
- **Parâmetros de Rota:**
  - `{id}`: ID da CPU (ex.: `1`).
- **Cabeçalhos:**
  - `X-API-KEY: keySecreta`

### 2. GPUs

#### Listar todas as GPUs
- **Rota:** `GET /gpus`
- **Resposta:**
    ```json
    [
      {
        "id": 1,
        "model": "RTX 3080",
        "fabricante": "NVIDIA",
        "arquitetura": "Ampere",
        "cuda_cores": 8704,
        "base_clock": 1440,
        "boost_clock": 1710,
        "memory": 10240,
        "created_at": "2024-08-14T13:25:02.000000Z",
        "updated_at": "2024-08-14T13:25:02.000000Z"
      },
      {
        "id": 2,
        "model": "RX 570",
        "fabricante": "AMD",
        "arquitetura": "Polaris",
        "cuda_cores": 2048,
        "base_clock": 1168,
        "boost_clock": 1244,
        "memory": 8192,
        "created_at": "2024-08-14T13:25:02.000000Z",
        "updated_at": "2024-08-14T13:25:02.000000Z"
      }
    ]
    ```

#### Consultar uma GPU específica
- **Rota:** `GET /gpus/{model}`
- **Parâmetros de Rota:**
  - `{model}`: Modelo da GPU (ex.: `RTX%203080`).
- **Resposta:**
    ```json
    {
      "id": 1,
      "model": "RTX 3080",
      "fabricante": "NVIDIA",
      "arquitetura": "Ampere",
      "cuda_cores": 8704,
      "base_clock": 1440,
      "boost_clock": 1710,
      "memory": 10240,
      "created_at": "2024-08-14T13:25:02.000000Z",
      "updated_at": "2024-08-14T13:25:02.000000Z"
    }
    ```

#### Adicionar uma nova GPU
- **Rota:** `POST /gpus`
- **Cabeçalhos:**
  - `Content-Type: application/json`
  - `X-API-KEY: keySecreta`
- **Exemplo de requisição:**
  ```json
  {
      "model": "RTX 3090",
      "fabricante": "NVIDIA",
      "arquitetura": "Ampere",
      "cuda_cores": 10496,
      "base_clock": 1395,
      "boost_clock": 1695,
      "memory": 24576
  }
  ```

#### Atualizar uma GPU
- **Rota:** `PUT /gpus/{id}`
- **Parâmetros de Rota:**
  - `{id}`: ID da GPU (ex.: `3`).
- **Cabeçalhos:**
  - `Content-Type: application/json`
  - `X-API-KEY: keySecreta`
- **Exemplo de requisição:**
  ```json
  {
  "model": "RX 570",
  "memory": 8192
  }
  ```

#### Deletar uma GPU
- **Rota:** `DELETE /gpus/{id}`
- **Parâmetros de Rota:**
  - `{id}`: ID da GPU (ex.: `3`).
- **Cabeçalhos:**
  - `X-API-KEY: keySecreta`

### 3. Comparação de Hardware

#### Comparar dois hardwares
- **Rota:** `POST /compare`
- **Cabeçalhos:**
  - `Content-Type: application/json`
- **Exemplo de requisição (CPU):**
  ```json
  {
    "type": "cpu",
    "hardware1": "Ryzen 5 3600",
    "hardware2": "Intel Core i5-10600K"
  }
  ```
- **Exemplo de requisição (GPU):**
  ```json
  {
    "type": "gpu",
    "hardware1": "RTX 3080",
    "hardware2": "RX 6800 XT"
  }
  ```
- **Resposta de sucesso (exemplo para CPU):**
  ```json
  {
    "success": true,
    "type": "cpu",
    "hardware1": {
      "id": 1,
      "model": "Ryzen 5 3600",
      "fabricante": "AMD",
      "arquitetura": "Zen 2",
      "cores": 6,
      "threads": 12,
      "clock": 3.6,
      "boost": 4.2,
      "integrated_graphics": null,
      "created_at": "2024-08-14T13:25:02.000000Z",
      "updated_at": "2024-08-14T13:25:02.000000Z"
    },
    "hardware2": {
      "id": 2,
      "model": "Intel Core i5-10600K",
      "fabricante": "Intel",
      "arquitetura": "Comet Lake",
      "cores": 6,
      "threads": 12,
      "clock": 4.1,
      "boost": 4.8,
      "integrated_graphics": "UHD Graphics 630",
      "created_at": "2024-08-14T13:25:02.000000Z",
      "updated_at": "2024-08-14T13:25:02.000000Z"
    },
    "comparison": {
      "winner": "hardware2",
      "is_technical_tie": false,
      "winner_confidence": 12.5,
      "score1": 42.5,
      "score2": 55.0,
      "differences": [
        {
          "characteristic": "Clock Base (GHz)",
          "hardware1_value": 3.6,
          "hardware2_value": 4.1,
          "advantage": "hardware2",
          "significance": 12
        },
        {
          "characteristic": "Clock de Boost (GHz)",
          "hardware1_value": 4.2,
          "hardware2_value": 4.8,
          "advantage": "hardware2",
          "significance": 13
        }
      ],
      "specs": [
        {"name": "Modelo", "value1": "Ryzen 5 3600", "value2": "Intel Core i5-10600K", "better": "hardware2"},
        {"name": "Clock Base (GHz)", "value1": 3.6, "value2": 4.1, "better": "hardware2"},
        {"name": "Clock de Boost (GHz)", "value1": 4.2, "value2": 4.8, "better": "hardware2"},
        {"name": "Núcleos", "value1": 6, "value2": 6, "better": "equal"},
        {"name": "Threads", "value1": 12, "value2": 12, "better": "equal"}
      ],
      "message": "O hardware2 tem um desempenho ligeiramente melhor que o hardware1."
    }
  }
  ```
- **Possíveis códigos de erro:**
  - `400`: Requisição inválida (parâmetros ausentes ou inválidos)
  - `404`: Hardware não encontrado
  - `422`: Erro de validação

### 4. Jogos

Permite gerenciar o catálogo de jogos e seus respectivos requisitos mínimos e recomendados.

#### Listar todos os jogos
- **Rota:** `GET /games`
- **Resposta:**
    ```json
    [
      {
        "id": 1,
        "nome": "Cyberpunk 2077",
        "cpu_recomendada": "Core i7-4790",
        "ram_recomendada": "12 GB",
        "gpu_recomendada": "GTX 1060 6GB",
        "cpu_minima": "Core i5-3570K",
        "ram_minima": "8 GB",
        "gpu_minima": "GTX 780 3GB",
        "ano_lancamento": 2020,
        "desenvolvedora": "CD Projekt Red",
        "genero": "RPG"
      }
    ]
    ```

#### Consultar um jogo específico
- **Rota:** `GET /games/{id}`
- **Parâmetros de Rota:**
  - `{id}`: ID do jogo (ex.: `1`).
- **Resposta:**
    ```json
    {
      "id": 1,
      "nome": "Cyberpunk 2077",
      "cpu_recomendada": "Core i7-4790",
      "ram_recomendada": "12 GB",
      "gpu_recomendada": "GTX 1060 6GB",
      "cpu_minima": "Core i5-3570K",
      "ram_minima": "8 GB",
      "gpu_minima": "GTX 780 3GB",
      "ano_lancamento": 2020,
      "desenvolvedora": "CD Projekt Red",
      "genero": "RPG"
    }
    ```

#### Adicionar um novo jogo
- **Rota:** `POST /games`
- **Cabeçalhos:**
  - `Content-Type: application/json`
  - `X-API-KEY: keySecreta`
- **Exemplo de requisição:**
  ```json
  {
      "nome": "Cyberpunk 2077",
      "ano_lancamento": 2020,
      "desenvolvedora": "CD Projekt Red",
      "genero": "RPG",
      "min_cpu": "Core i5-3570K",
      "min_ram": "8 GB",
      "min_gpu": "GTX 780 3GB",
      "rec_cpu": "Core i7-4790",
      "rec_ram": "12 GB",
      "rec_gpu": "GTX 1060 6GB"
  }
  ```

#### Atualizar um jogo
- **Rota:** `PUT /games/{id}`
- **Parâmetros de Rota:**
  - `{id}`: ID do jogo (ex.: `1`).
- **Cabeçalhos:**
  - `Content-Type: application/json`
  - `X-API-KEY: keySecreta`
- **Exemplo de requisição:**
  ```json
  {
      "ano_lancamento": 2021,
      "rec_ram": "16 GB"
  }
  ```

#### Deletar um jogo
- **Rota:** `DELETE /games/{id}`
- **Parâmetros de Rota:**
  - `{id}`: ID do jogo (ex.: `1`).
- **Cabeçalhos:**
  - `X-API-KEY: keySecreta`

### 5. Benchmarks

Permite registrar e consultar benchmarks reais (FPS médio obtido com hardwares específicos rodando jogos específicos).

#### Listar todos os benchmarks
- **Rota:** `GET /benchmarks`
- **Resposta:**
    ```json
    [
      {
        "id": 1,
        "jogo": "Cyberpunk 2077",
        "cpu": "Ryzen 5 3600",
        "gpu": "RTX 3080",
        "ram": "16GB DDR4",
        "configuracao": "Ultra 1080p",
        "fps_medio": 85,
        "created_at": "2024-08-14T13:25:02.000000Z",
        "updated_at": "2024-08-14T13:25:02.000000Z"
      }
    ]
    ```

#### Consultar um benchmark específico
- **Rota:** `GET /benchmarks/{id}`
- **Parâmetros de Rota:**
  - `{id}`: ID do benchmark (ex.: `1`).
- **Resposta:**
    ```json
    {
      "id": 1,
      "jogo": "Cyberpunk 2077",
      "cpu": "Ryzen 5 3600",
      "gpu": "RTX 3080",
      "ram": "16GB DDR4",
      "configuracao": "Ultra 1080p",
      "fps_medio": 85,
      "created_at": "2024-08-14T13:25:02.000000Z",
      "updated_at": "2024-08-14T13:25:02.000000Z"
    }
    ```

#### Registrar um novo benchmark
- **Rota:** `POST /benchmarks`
- **Cabeçalhos:**
  - `Content-Type: application/json`
  - `X-API-KEY: keySecreta`
- **Exemplo de requisição:**
  ```json
  {
      "jogo": "Cyberpunk 2077",
      "cpu": "Ryzen 5 3600",
      "gpu": "RTX 3080",
      "ram": "16GB DDR4",
      "configuracao": "Ultra 1080p",
      "fps_medio": 85
  }
  ```

#### Atualizar um benchmark
- **Rota:** `PUT /benchmarks/{id}`
- **Parâmetros de Rota:**
  - `{id}`: ID do benchmark (ex.: `1`).
- **Cabeçalhos:**
  - `Content-Type: application/json`
  - `X-API-KEY: keySecreta`
- **Exemplo de requisição:**
  ```json
  {
      "fps_medio": 90,
      "configuracao": "Ultra 1080p (DLSS Quality)"
  }
  ```

#### Deletar um benchmark
- **Rota:** `DELETE /benchmarks/{id}`
- **Parâmetros de Rota:**
  - `{id}`: ID do benchmark (ex.: `1`).
- **Cabeçalhos:**
  - `X-API-KEY: keySecreta`


## Algoritmo de Comparação e Funcionamento do Benchmark

O funcionamento central do comparador/benchmark baseia-se em um algoritmo de pontuação técnica por pesos dinâmicos que avalia se o hardware selecionado pelo usuário atende ou supera as especificações exigidas pelos jogos.

### 1. Pesos de CPU
Na comparação de processadores (CPU do usuário vs CPU do jogo), os seguintes atributos são avaliados com seus respectivos pesos originais:
- **Núcleos (Cores):** Peso `4.0` (Maior impacto multi-tarefa e arquiteturas modernas)
- **Threads:** Peso `3.5` (Impacto no paralelismo de execução)
- **Clock de Boost (GHz):** Peso `3.0` (Desempenho máximo em pico de uso)
- **Clock Base (GHz):** Peso `2.5` (Desempenho sustentado mínimo)

### 2. Pesos de GPU
Na comparação de placas de vídeo (GPU do usuário vs GPU do jogo), os seguintes atributos são avaliados:
- **Cores de Processamento (CUDA Cores / Stream Processors):** Peso `3.5` (Poder de processamento bruto)
- **Memória VRAM (MB):** Peso `3.0` (Capacidade de armazenamento de texturas e buffers)
- **Clock de Boost (MHz):** Peso `2.5` (Clock dinâmico em carga de trabalho)
- **Clock Base (MHz):** Peso `2.0` (Frequência mínima de operação)

### 3. Ajuste Dinâmico de Peso (Dynamic Weighting)
Para que diferenças significativas de especificação tenham maior relevância do que pequenas variações, o algoritmo ajusta o peso de cada característica dinamicamente com base na diferença percentual entre as duas peças de hardware:

$$ \text{Peso Dinâmico} = \text{Peso Original} \times \left(1 + \min\left(\frac{\text{Diferença Percentual}}{20}, 1\right)\right) $$

*Exemplo:* Se a CPU do usuário tem $10\%$ a mais de clock de boost que a CPU requerida pelo jogo, o peso desse clock sobe de $3.0$ para $3.0 \times (1 + 0.5) = 4.5$. O acréscimo máximo de peso é de $+100\%$ (quando a diferença é de $20\%$ ou mais).

### 4. Confiança do Vencedor (Winner Confidence)
Ao final do acúmulo de pontuações de cada especificação, a diferença de pontuação final determina a margem de vitória em termos percentuais:

$$ \text{Confiança} = \left(\frac{|\text{Score}_1 - \text{Score}_2|}{\text{Pontuação Máxima Possível}}\right) \times 100 $$

- **Empate Técnico (Technical Tie):** Se a confiança calculada for **menor que 5%**, o resultado é um empate técnico (`is_technical_tie = true`). Significa que a diferença de desempenho prático em jogos é insignificante, indicando que o hardware do usuário atenderá perfeitamente aos requisitos mesmo que tenha alguma especificação ligeiramente inferior.
- **Vitória de Hardware:** Se a diferença for de **5% ou mais**, a peça com maior pontuação é declarada vencedora (`hardware1` para o hardware do usuário, `hardware2` para o requisito do jogo).

### 5. Comparação de Memória RAM
A memória RAM é comparada localmente de forma binária com base em sua capacidade em Gigabytes:
- Se $\text{Capacidade do Usuário} \geq \text{Capacidade Requerida}$, o requisito é atendido (`true`).
- Caso contrário, o requisito não é atendido (`false`).

### 6. Fluxo de Execução do Benchmark no Sistema
1. **Seleção:** O usuário escolhe no frontend um Jogo, sua CPU, GPU e RAM.
2. **Requisitos:** O frontend busca as especificações de hardware correspondentes à CPU e GPU requeridas pelo jogo (Mínimas e Recomendadas).
3. **Chamada Paralela à API:** O frontend envia requisições assíncronas para `/compare` enviando a CPU e GPU do usuário contra os requisitos correspondentes do jogo.
4. **Decisão:** Se o hardware do usuário vencer ou empatar tecnicamente em todas as três categorias (CPU, GPU e RAM), o sistema sinaliza que o PC do usuário **atende** aos requisitos (seja mínimos ou recomendados). Caso contrário, avisa que não atende.


## Códigos de Resposta

A API utiliza os seguintes códigos de status HTTP:

| Código | Descrição |
|--------|-----------|
| 200 | OK - Requisição bem-sucedida |
| 201 | Criado - Recurso criado com sucesso |
| 400 | Requisição inválida - Verifique os parâmetros fornecidos |
| 401 | Não autorizado - Chave de API inválida ou ausente |
| 404 | Não encontrado - O recurso solicitado não existe |
| 405 | Método não permitido - O método HTTP não é suportado para este endpoint |
| 422 | Entidade não processável - Erro de validação nos dados fornecidos |
| 500 | Erro interno do servidor - Ocorreu um erro inesperado |

## Postman

Para facilitar os testes, você pode importar a coleção do Postman com todos os endpoints através do link abaixo:

[![Executar no Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/SEU_ID_DA_COLECAO?action=collection%2Fimport)

Ou acesse o workspace diretamente:
[Postman Workspace](https://www.postman.com/nicolas018/workspace/api-benchmark/overview)
