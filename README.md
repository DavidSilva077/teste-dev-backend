# Estech API

API RESTful desenvolvida em Laravel para gerenciamento de usuários, candidatos e vagas. Inclui funcionalidades como autenticação com Sanctum, inscrição de candidatos em vagas, importação de dados via CSV com análise estatística e muito mais.

---

## 🔧 Tecnologias Utilizadas

- **PHP 8.2+**
- **Laravel 10+**
- **MySQL 8**
- **Redis**
- **Docker & Docker Compose**
- **Sanctum (Autenticação)**
- **Queue com Supervisor + Jobs**
- **League CSV (Importação de arquivos)**

---

## 🚀 Como Rodar o Projeto

### 1. Clone o repositório:

```bash
git clone https://github.com/seu-usuario/estech-api-app.git
cd estech-api-app
```

### 2. Copie o arquivo `.env.example`:

```bash
cp .env.example .env
```

Ajuste as variáveis se necessário.

### 3. Suba os containers Docker:

```bash
docker-compose up -d
```

### 4. Acesse o container da aplicação:

```bash
docker-compose exec app bash
```

### 5. Instale as dependências:

```bash
composer install
```

### 6. Gere a chave da aplicação:

```bash
php artisan key:generate
```

### 7. Execute as migrations e seeds:

```bash
php artisan migrate --seed
```

### 8. Rode as filas (Job Import):

```bash
php artisan queue:work
```

---

## 📦 Comandos Úteis

- Gerar migrations:

```bash
php artisan migrate
```

- Rodar testes:

```bash
php artisan test
```

- Limpar cache:

```bash
php artisan optimize:clear
```

---

## 🔐 Autenticação

- Utiliza **Laravel Sanctum**.
- Após realizar login ou registro, você receberá um token.
- Utilize o token nos headers:

```http
Authorization: Bearer {token}
Content-Type: application/json
```

---

## 🛠️ Funcionalidades da API

### ✔️ Usuários (Recruiter ou Candidate)

- **CRUD completo**
- **Deleção em massa** `/users/bulk-delete`
- **Validações robustas**
- **SoftDeletes ativo**

### ✔️ Vagas

- **CRUD completo**
- **Pausar vaga** (`paused: true`)
- **Inscrição de candidatos** `/jobs/{job}/subscribe/{candidate}`
- **Deleção em massa** `/jobs/bulk-delete`
- **Filtros dinâmicos e ordenação**

### ✔️ Candidatos

- **CRUD completo**
- **Inscrição em múltiplas vagas**
- **Deleção em massa** `/candidates/bulk-delete`
- **Relacionamento many-to-many com jobs**

### ✔️ Importação CSV

- **Endpoint para upload CSV:** `/import`
- Arquivo CSV no formato:

```csv
date,value
2025-05-01,5
2025-05-01,15
```

- O processo é **assíncrono** via queue.
- Endpoint para análise dos dados importados: `/import/analysis`

Inclui:

- Média
- Mediana
- Valor mínimo
- Valor máximo
- % acima de 10
- % abaixo de -10
- % entre -10 e 10

---

## ⚙️ Middleware Customizados

- **ForceJsonRequest:**  
  Obriga todas as requisições POST, PUT, PATCH, DELETE a terem `Content-Type: application/json` e corpo JSON válido.

- **ForceJsonResponse:**  
  Todas as respostas são forçadas no formato JSON.

---

## ♻️ Cache

- Todas as listagens (`users`, `jobs`, `candidates`) são cacheadas no Redis.
- O cache é invalidado em operações de criação, atualização ou deleção.

---

## ✅ Testes Automatizados

Executar:

```bash
php artisan test
```

Abrange:

- Autenticação (Login, Register, Logout)
- CRUD de Usuários
- CRUD de Vagas
- CRUD de Candidatos
- Inscrição em Vagas
- Importação CSV e análise de dados

---

## 🐳 Docker

A stack inclui:

- Laravel PHP (porta `8000`)
- MySQL (porta `3306`)
- Redis (porta `6379`)

Subir com:

```bash
docker-compose up -d
```

---

## 🗺️ Rotas Principais

| Método | Endpoint                               | Descrição                        |
|--------|-----------------------------------------|-----------------------------------|
| POST   | /register                              | Registrar usuário                |
| POST   | /login                                 | Login                             |
| POST   | /logout                                | Logout                            |
| GET    | /users                                 | Listar usuários                   |
| POST   | /users                                 | Criar usuário                     |
| DELETE | /users/bulk-delete                     | Deleção em massa                  |
| GET    | /jobs                                  | Listar vagas                      |
| POST   | /jobs                                  | Criar vaga                        |
| POST   | /jobs/{job}/subscribe/{candidate}      | Inscrever candidato em vaga       |
| DELETE | /jobs/bulk-delete                      | Deleção em massa                  |
| GET    | /candidates                            | Listar candidatos                 |
| POST   | /candidates                            | Criar candidato                   |
| DELETE | /candidates/bulk-delete                | Deleção em massa                  |
| POST   | /import                                | Importar CSV                      |
| GET    | /import/analysis                       | Ver análise dos dados importados  |

---

## 🔥 Validações de Segurança

- JSON inválido → Retorna `400`
- Content-Type incorreto → Retorna `415`
- Campos obrigatórios → Retorna `422`
- Autenticação → Retorna `401` se token ausente ou inválido

---

## 📄 Licença

Este projeto foi desenvolvido exclusivamente para o teste técnico da **Estech**.