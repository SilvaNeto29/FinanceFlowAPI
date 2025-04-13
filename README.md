# FinanceFlowAPI

Uma API REST feita em PHP puro com Composer para controle financeiro pessoal. O objetivo é oferecer uma base sólida e simples para quem quiser usar ou estudar uma API bem estruturada, com autenticação, boas práticas, testes, CI/CD e Docker.

## Funcionalidades

- Cadastro e login de usuários com JWT
- Gerenciamento de contas (ex: carteira, banco digital, etc.)
- Registro de entradas e saídas (transações)
- Agrupamento por categorias (ex: salário, alimentação, lazer)
- Cálculo de saldo por conta ou geral
- Filtros por período
- Conversão de moedas via API externa

## Tecnologias e recursos

- PHP 8 com Composer
- ORM Medoo
- Autenticação com JWT
- Testes com PHPUnit
- Docker para facilitar o ambiente
- CI/CD com GitHub Actions
- Documentação de rotas com Postman

## Instalação

```bash
git clone https://github.com/seu-usuario/FinanceFlowAPI.git
cd FinanceFlowAPI
composer install
cp .env.example .env
```
