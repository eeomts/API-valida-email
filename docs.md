# API de Validação de Email

API Laravel para validação de emails através de tokens com expiração.

---

## Índice

1. [Requisitos](#requisitos)
2. [Instalação](#instalação)
3. [Configuração do Banco de Dados](#configuração-do-banco-de-dados)
4. [Executando o Servidor](#executando-o-servidor)
5. [Rotas da API](#rotas-da-api)
6. [Exemplos de Uso](#exemplos-de-uso)
7. [Estrutura do Banco de Dados](#estrutura-do-banco-de-dados)

---

## Requisitos

- **PHP** >= 8.2
- **Composer** >= 2.0
- **MySQL** >= 8.0 ou **MariaDB** >= 10.4
- **Node.js** >= 18 (opcional, para assets)

---

## Instalação

### 1. Instalar o Composer (se ainda não tiver)

**Windows:**
1. Baixe o instalador em: https://getcomposer.org/Composer-Setup.exe
2. Execute o instalador e siga as instruções
3. Verifique a instalação:
```bash
composer --version
```

**Linux/macOS:**
```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

### 2. Clonar o Projeto

```bash
git clone <url-do-repositorio>
cd API-valida-email
```

### 3. Instalar Dependências do PHP

```bash
composer install
```

### 4. Configurar Ambiente

Copie o arquivo de ambiente:
```bash
cp .env.example .env
```

Gere a chave da aplicação:
```bash
php artisan key:generate
```

---

## Configuração do Banco de Dados

### 1. Criar o Banco de Dados

Acesse seu MySQL e crie o banco:
```sql
CREATE DATABASE api_valida_email;
```

### 2. Configurar o .env

Edite o arquivo `.env` com suas credenciais:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=api_valida_email
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 3. Executar as Migrations

```bash
php artisan migrate
```

Isso criará a tabela `validados` com a seguinte estrutura:

| Campo       | Tipo                | Descrição                        |
|-------------|---------------------|----------------------------------|
| id          | bigint              | ID único                         |
| email       | string              | Email a ser validado             |
| token       | longText            | Token único de validação         |
| origem      | string              | Origem da requisição (app, web)  |
| validado    | tinyint (0 ou 1)    | Status de validação              |
| expires_at  | timestamp           | Data/hora de expiração           |
| created_at  | timestamp           | Data de criação                  |
| updated_at  | timestamp           | Data de atualização              |

---

## ▶Executando o Servidor

Inicie o servidor de desenvolvimento:

```bash
php artisan serve
```

O servidor estará disponível em: `http://127.0.0.1:8000`

---

##  Rotas da API

### Rotas Disponíveis

| Método | Rota                            | Descrição                          |
|--------|----------------------------------|-------------------------------------|
| POST   | `/api/gerar-validacao`          | Gera um link de validação          |
| GET    | `/api/validar-email/{email}`    | Verifica se email foi validado     |
| GET    | `/confirmar-email/{token}`      | Página para confirmar o email      |

---

## Exemplos de Uso

### 1. Gerar Link de Validação

**Requisição:**
```http
POST /api/gerar-validacao
Content-Type: application/json

{
    "email": "usuario@exemplo.com",
    "origin": "app"
}
```

**Resposta de Sucesso (200):**
```json
{
    "link": "http://127.0.0.1:8000/confirmar-email/550e8400-e29b-41d4-a716-446655440000",
    "expires_at": "2026-02-05T16:30:00+00:00"
}
```

**Resposta de Erro (422):**
```json
{
    "message": "The email field is required.",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

---

### 2. Verificar Status de Validação

**Requisição:**
```http
GET /api/validar-email/usuario@exemplo.com
```

**Resposta (Email Validado):**
```json
{
    "email": "usuario@exemplo.com",
    "validado": true
}
```

**Resposta (Email Não Validado):**
```json
{
    "email": "usuario@exemplo.com",
    "validado": false
}
```

---

### 3. Confirmar Email (Navegador)

O usuário acessa o link gerado no navegador:

```
http://127.0.0.1:8000/confirmar-email/550e8400-e29b-41d4-a716-446655440000
```

**Possíveis Páginas:**

| Situação          | Página Exibida                              |
|-------------------|---------------------------------------------|
| Token válido      | Sucesso - "Email verificado com sucesso!" |
| Token expirado    | Expirado - "Link expirado"                |
| Token inválido    | Erro - "Erro ao validar email"            |

---

##  Testando com cURL

### Gerar Validação:
```bash
curl -X POST http://127.0.0.1:8000/api/gerar-validacao \
  -H "Content-Type: application/json" \
  -d '{"email": "teste@email.com", "origin": "app"}'
```

### Verificar Status:
```bash
curl http://127.0.0.1:8000/api/validar-email/teste@email.com
```

---

## Testando com Postman/Insomnia

1. **Gerar Validação:**
   - Método: `POST`
   - URL: `http://127.0.0.1:8000/api/gerar-validacao`
   - Body (JSON):
     ```json
     {
         "email": "teste@email.com",
         "origin": "meuapp"
     }
     ```

2. **Verificar Status:**
   - Método: `GET`
   - URL: `http://127.0.0.1:8000/api/validar-email/teste@email.com`

---

## ⏱Tempo de Expiração

O link de validação expira em **30 minutos** após ser gerado.

Para alterar, edite o arquivo `app/Http/Controllers/EmailValidationController.php`:

```php
// Linha 21 - Altere o tempo conforme necessário:
$expiresAt = now()->addMinutes(30);  // 30 minutos
$expiresAt = now()->addHours(1);     // 1 hora
$expiresAt = now()->addHours(24);    // 24 horas
$expiresAt = now()->addDays(7);      // 7 dias
```

---

## Comandos Úteis

```bash
# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Listar todas as rotas
php artisan route:list

# Resetar banco de dados
php artisan migrate:fresh

# Verificar status do servidor
php artisan serve --host=0.0.0.0 --port=8000
```

---

## Estrutura de Arquivos Principais

```
API-valida-email/
├── app/
│   └── Http/
│       └── Controllers/
│           ├── EmailValidationController.php  # Lógica da API
│           └── EmailConfirmWebController.php  # Confirmação Web
├── database/
│   └── migrations/
│       └── 2026_02_05_150537_create_validados_table.php
├── resources/
│   └── views/
│       ├── success.blade.php   # Página de sucesso
│       ├── expired.blade.php   # Página de expirado
│       └── error.blade.php     # Página de erro
├── routes/
│   ├── api.php    # Rotas da API
│   └── web.php    # Rotas Web
└── .env           # Configurações de ambiente
```

---

## Suporte

Em caso de dúvidas ou problemas, verifique os logs em:
```
storage/logs/laravel.log

```

## CONTATO

github: eeomts
instagram: olvmateuss