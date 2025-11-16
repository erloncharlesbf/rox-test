# Rox Test - API de Gerenciamento de Filmes e Livros

API RESTful desenvolvida com Laravel para gerenciamento de filmes e livros, incluindo sistema de anexos.

## Estrutura do Projeto

O projeto utiliza:
- **Laravel 12** como framework principal
- **API REST** com rotas em `routes/api.php`
- **Models**: `Movie`, `Book`, `Attachment`, `User`
- **Repository Pattern**: Camada de abstração para acesso aos dados (`app/Repositories`)
- **Service Layer**: Lógica de negócio separada dos controllers (`app/Services`)
- **Contratos/Interfaces**: Dependency Injection via interfaces (`app/Contracts`)
- **Princípios SOLID**: Separação de responsabilidades, inversão de dependências e código testável
- **Pest** para testes automatizados
- **Scribe** para documentação da API
- **Laravel Pint** para linting
- **PHPStan** e **Rector** para análise estática


## Como Baixar o Projeto

```bash
# Clone o repositório
git clone <url-do-repositorio>
cd rox-test
```

## Instalação e Inicialização

### Opção 1: Docker com Laravel Sail

```bash
# Instalar dependências via Docker
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs

# Configurar ambiente
cp .env.example .env

# Iniciar containers
./vendor/bin/sail up -d

# Gerar chave e executar migrations
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate

# A aplicação estará disponível em http://localhost
```

### Opção 2: Laravel Herd

> **Laravel Herd** é um ambiente de desenvolvimento PHP nativo que gerencia automaticamente servidores PHP e Nginx sem containers ou VMs. Saiba mais em [herd.laravel.com](https://herd.laravel.com/)

```bash
# 1. Instale o Laravel Herd em https://herd.laravel.com/

# 2. Adicione o projeto ao Herd via UI ou PowerShell

# 3. Instalar dependências
composer install

# 4. Configurar ambiente
cp .env.example .env
php artisan key:generate

# 5. Configurar banco de dados no .env e executar migrations
php artisan migrate

# O servidor é gerenciado automaticamente pelo Herd
# Acesse via: http://rox-test.test (ou conforme configurado)
```

### Opção 3: Linha de Comando (PHP + Composer)

```bash
# Instalar dependências
composer install
npm install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Configurar banco de dados no .env e executar migrations
php artisan migrate

# Iniciar servidor e serviços
php artisan serve
# Aplicação disponível em http://localhost:8000

# Em terminais separados (opcional):
php artisan queue:listen  # Para processar filas
npm run dev              # Para assets front-end

# Ou use o comando composer que inicia tudo:
composer dev
```

## Documentação da API

Para gerar e acessar a documentação:

```bash
# Gerar documentação
php artisan scribe:generate

# Acessar em: http://localhost/docs
```

### Downloads da Documentação

Após gerar a documentação, você pode baixar os arquivos de especificação da API:

- **Postman Collection**: [`download`](https://github.com/erloncharlesbf/rox-test/tree/main/public/scribe/collection.json)
- **OpenAPI (Swagger) Spec**: [`download`](https://github.com/erloncharlesbf/rox-test/tree/main/public/scribe/openapi.yaml)

Estes arquivos podem ser importados em ferramentas como Postman, Insomnia, Swagger UI, Scalar entre outras.

Se preferir também há uma interface em http://localhost/docs para utilizar a documentacao

## Importando a Coleção no Postman

Para testar a API usando o Postman, siga os passos abaixo:

### Passo 1: Obter o arquivo da coleção

O arquivo da coleção Postman está localizado em:
```
public/scribe/collection.json
```

### Passo 2: Importar no Postman

1. Abra o Postman
2. Clique no botão **"Import"** no canto superior esquerdo
3. Selecione a aba **"File"**
4. Clique em **"Choose Files"** ou arraste o arquivo `collection.json`
5. Navegue até `public/scribe/collection.json` no seu projeto
6. Clique em **"Import"**

### Passo 3: Configurar Variáveis de Ambiente (Opcional)

Para facilitar os testes, configure as seguintes variáveis:

1. Clique no ícone de engrenagem ⚙️ no canto superior direito
2. Crie um novo ambiente (ex: "Rox Test - Local")
3. Adicione as variáveis:
   - `base_url`: `http://localhost` (ou `http://localhost:8000` se usando `php artisan serve`)
   - `token`: (será preenchido após login)

### Passo 4: Autenticação

1. Execute o endpoint **POST /api/register** ou **POST /api/login**
2. Copie o token retornado no campo `token`
3. Configure o header `Authorization: Bearer {seu-token}` nas requisições protegidas
4. Ou adicione o token na variável de ambiente `token` para uso automático

### Endpoints Disponíveis

A coleção inclui todos os endpoints da API:

**Autenticação:**
- POST `/api/register` - Registrar novo usuário
- POST `/api/login` - Fazer login
- POST `/api/logout` - Fazer logout (requer autenticação)
- POST `/api/forgot-password` - Solicitar reset de senha
- POST `/api/reset-password` - Resetar senha

**Filmes (requer autenticação):**
- GET `/api/movies` - Listar filmes (com filtros e paginação)
- POST `/api/movies` - Criar novo filme
- GET `/api/movies/{id}` - Ver detalhes do filme
- PUT/PATCH `/api/movies/{id}` - Atualizar filme
- DELETE `/api/movies/{id}` - Deletar filme

**Livros (requer autenticação):**
- GET `/api/books` - Listar livros (com filtros e paginação)
- POST `/api/books` - Criar novo livro
- GET `/api/books/{id}` - Ver detalhes do livro
- PUT/PATCH `/api/books/{id}` - Atualizar livro
- DELETE `/api/books/{id}` - Deletar livro

## Testes

O projeto possui **cobertura de testes de 100%**, garantindo máxima qualidade e confiabilidade do código.

### Framework de Testes: PEST PHP

O projeto utiliza **PEST PHP**, um framework de testes elegante com sintaxe expressiva e moderna para PHP.

**Plugins instalados:**
- ✅ `pestphp/pest` - Framework principal
- ✅ `pestphp/pest-plugin-laravel` - Integração com Laravel
- ✅ `pestphp/pest-plugin-arch` - Testes arquiteturais
- ✅ `pestphp/pest-plugin-mutate` - Testes de mutação
- ✅ `brianium/paratest` v7.8.4 - Execução paralela de testes

### Executando os Testes

```bash
# Executar todos os testes
composer test

# Executar testes em paralelo
php artisan test --parallel

# Testes com cobertura (mínimo 100%)
composer test:coverage

# Ver relatório detalhado de cobertura
export DB_CONNECTION=sqlite && export DB_DATABASE=:memory: && php artisan test --coverage

# Executar testes de um grupo específico
php artisan test --group=auth
php artisan test --group=api

# Análise estática com PHPStan
composer phpstan

# Linting com Laravel Pint
composer lint
```

### Estrutura de Testes

O projeto possui testes abrangentes divididos em:

**Testes Feature (Integração):**
- `tests/Feature/AuthApiTest.php` - 15 testes de autenticação (register, login, logout, password reset)
- `tests/Feature/MovieApiTest.php` - 9 testes de CRUD de filmes
- `tests/Feature/BookApiTest.php` - 10 testes de CRUD de livros

**Testes Unit (Unitários):**
- `tests/Unit/MovieRepositoryTest.php` - 9 testes do repositório de filmes
- `tests/Unit/BookRepositoryTest.php` - 9 testes do repositório de livros
- `tests/Unit/MovieServiceTest.php` - 9 testes do serviço de filmes
- `tests/Unit/BookServiceTest.php` - 9 testes do serviço de livros
- `tests/Unit/BaseRepositoryTest.php` - 7 testes do repositório base
- `tests/Unit/UserRepositoryTest.php` - 4 testes do repositório de usuários
- `tests/Unit/AttachmentModelTest.php` - 2 testes do modelo de anexos

**Total:** 85 testes com 244+ assertions

### Cobertura por Módulo

**TODOS os módulos possuem 100% de cobertura:**
- ✅ **Repositories**: 100%
- ✅ **Services**: 100%
- ✅ **Models**: 100%
- ✅ **Controllers**: 100%
- ✅ **Requests**: 100%
- ✅ **Resources**: 100%
- ✅ **Providers**: 100%
- ✅ **Contracts**: 100%

### CI/CD - GitHub Actions

O projeto inclui workflow automatizado do GitHub Actions que:
- Executa os testes em PHP 8.2 e 8.3
- **Verifica cobertura mínima de 100%**
- Roda em cada push/PR para branches `main` e `develop`
- Gera relatórios de cobertura para Codecov

Veja o workflow em: `.github/workflows/tests.yml`

### Extensões de Cobertura

O projeto está configurado para usar **Xdebug** para cobertura de código (já incluído no phpunit.xml). Se você preferir usar **PCOV** (mais rápido), siga as instruções:

**Instalando PCOV (Opcional):**

```bash
# Via PECL
pecl install pcov

# Habilitar no php.ini
echo "extension=pcov.so" >> $(php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||")

# Configurar PCOV
echo "pcov.enabled=1" >> $(php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||")
echo "pcov.directory=." >> $(php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||")

# Desabilitar Xdebug quando usar PCOV
php -d xdebug.mode=off artisan test --coverage
```

**Observação:** O projeto já vem configurado com Xdebug otimizado para cobertura no `phpunit.xml`, então PCOV é opcional.

## Comandos Úteis

```bash
# Executar linting, rector e testes
composer wip

# Apenas verificar código sem corrigir
composer lint:check
composer rector
```
