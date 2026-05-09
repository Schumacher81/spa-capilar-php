# ✂️ Spa Capilar — Sistema de Gestão

Sistema de gestão interno para spa capilar desenvolvido
com PHP puro, MySQL e arquitetura MVC com POO.

## 🚀 Tecnologias

- PHP 8+
- MySQL
- HTML5 + CSS3 + JavaScript puro
- Arquitetura MVC
- Programação Orientada a Objetos
- XAMPP (ambiente local)

## 📋 Funcionalidades

- ✅ Login com sessão e BCrypt
- ✅ Controle de acesso por perfil (ADMIN/PROFISSIONAL)
- ✅ Dashboard com dados em tempo real
- ✅ CRUD de Clientes
- ✅ CRUD de Agendamentos com verificação de conflito
- ✅ Registro de Atendimentos
- ✅ Diagnóstico Capilar
- ✅ Agenda Diária por Profissional
- ✅ Agenda Mensal e Semanal

## ⚙️ Como executar localmente

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/spa-capilar-php.git
```

### 2. Configure o ambiente

- Instale o [XAMPP](https://www.apachefriends.org/)
- Copie a pasta para `C:\xampp\htdocs\spa-capilar-php`

### 3. Configure o banco de dados

- Inicie o Apache e MySQL no XAMPP
- Abra o phpMyAdmin ou MySQL Workbench
- Execute o script `banco/spa_capilar.sql`

### 4. Configure o projeto

```bash
cp config/config.example.php config/config.php
```

Edite `config/config.php` com suas configurações.

Edite `config/Database.php` com usuário e senha do MySQL.

### 5. Acesse o sistema

http://localhost/spa-capilar-php

## 🔐 Usuários padrão

Após executar o SQL, gere os hashes das senhas acessando:

http://localhost/spa-capilar-php/gerar_senhas.php

| Login     | Senha    | Perfil       |
| --------- | -------- | ------------ |
| admin     | admin123 | ADMIN        |
| ana.paula | 123456   | PROFISSIONAL |

## 📁 Estrutura do Projeto

spa-capilar-php/
├── config/ → configurações e conexão
├── controllers/ → lógica de controle (MVC)
├── models/ → acesso ao banco (MVC)
├── views/ → interface do usuário (MVC)
├── public/ → CSS, JS e imagens
└── banco/ → script SQL

## 👨‍💻 Desenvolvido por

Roberto Schumacher — Projeto de Extensão Universitária
