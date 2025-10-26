# Pet Shop System (v2) — Sistema de Agendamento e Gestão

![Status](https://img.shields.io/badge/Status-Completo-success) ![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue) ![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange) ![License](https://img.shields.io/badge/License-MIT-green)

Versão profissional e revisada de um sistema de agendamento focado em petshops locais. Esta release (v2) consolida melhorias de segurança, correções de bugs, experiência da recepção e capacidade de auditoria dos atendimentos.

## Visão resumida

O sistema resolve um problema operacional real de petshops pequenas: organizar atendimentos, evitar conflitos de agenda, manter histórico consolidado por cliente/pet e fornecer métricas simples para controle financeiro. Esta versão é uma atualização de um projeto anterior (v1) com melhorias concretas em segurança, estabilidade e usabilidade.

Principais ganhos desta versão:

- Fluxo de recepcao mais rapido e previsivel (cadastro rapido de clientes/pets/servicos)
- Controle de estado do atendimento (Agendado -> Em Atendimento -> Concluido) para rastreabilidade
- Protecao de credenciais com bcrypt e prepared statements para reduzir riscos de injecao SQL
- Automacao basica para instalacao local e verificacao de integridade do banco

---

🐾 Pet Shop System v2 — Agendamento e Gestão

Sistema profissional de agendamento para petshops locais, versão revisada do meu projeto original (v1) com melhorias de segurança, usabilidade e estabilidade. Resolve conflitos de agenda, organiza histórico de clientes/pets e traz métricas simples para controle financeiro.

## Highlights da v2

- Check-in rápido: cliente → pet → serviço
- Status de atendimento rastreável: Agendado → Em Atendimento → Concluído
- Segurança forte: bcrypt + prepared statements
- Scripts automáticos de instalação e teste
- Dashboard com métricas e gráficos (Chart.js)

## Funcionalidades (resumo)

| Módulo        | Função                                   |
| ------------- | ---------------------------------------- |
| Autenticação  | Admin e recepção com acesso restrito     |
| Clientes/Pets | CRUD completo com vínculo 1:N            |
| Serviços      | Preço, duração, categoria                |
| Agenda        | Gestão por data, status e observações    |
| Dashboard     | Totais + gráficos                        |
| Pesquisas     | Filtros por data, status e categoria     |
| Automação     | Setup local e verificação de integridade |

## Segurança (resumo)

- Senhas: `password_hash()` + `password_verify()`
- Prepared statements em todas entradas
- `htmlspecialchars()` para saída segura
- Soft-delete para histórico

⚙ Stack Técnico (resumo)

- Backend: PHP 7.4+ (procedural, mysqli)
- Banco: MySQL/MariaDB (utf8mb4)
- Frontend: HTML5, CSS3, JS (ES6), Chart.js, Font Awesome
- Ambiente recomendado: XAMPP no Windows

---

## Funcionalidades principais

## O que mudou em relação à v1

- Backend: PHP 7.4+ (procedural, mysqli)
  Clique nas imagens para abrir em tamanho maior.

[![Atendimentos](https://i.postimg.cc/bSXKVmvF/atendimentos.png)](https://postimg.cc/bSXKVmvF) [![Clientes](https://i.postimg.cc/p5Ngcsd6/Clientes.png)](https://postimg.cc/p5Ngcsd6) [![Logado](https://i.postimg.cc/wRKS4wBr/logado-como-recepcionista.png)](https://postimg.cc/wRKS4wBr)

[![Login](https://i.postimg.cc/64sDbMQF/Login.png)](https://postimg.cc/64sDbMQF) [![Login2](https://i.postimg.cc/8f8qXZC2/Login2.png)](https://postimg.cc/8f8qXZC2) [![Novo Atendimento](https://i.postimg.cc/dZMzN51F/novo-atendimento.png)](https://postimg.cc/dZMzN51F)

[![Novo Pet](https://i.postimg.cc/gL9ftgJm/novo-pet.png)](https://postimg.cc/gL9ftgJm) [![Novo Servico](https://i.postimg.cc/Mfkh39Tz/novo-servico.png)](https://postimg.cc/Mfkh39Tz) [![Novo Cliente](https://i.postimg.cc/n986S3zV/novocliente.png)](https://postimg.cc/n986S3zV)

[![Pet](https://i.postimg.cc/jnVG3M5S/petx.png)](https://postimg.cc/jnVG3M5S) [![Servicos](https://i.postimg.cc/ctqVkFHC/servicos.png)](https://postimg.cc/ctqVkFHC)

## Instalação (local — Windows / XAMPP)

Siga estes passos em uma máquina de desenvolvimento Windows com XAMPP instalado.

1. Pare o Apache/MySQL no XAMPP Control Panel.
1. Copie o projeto para a pasta do servidor (execute no PowerShell como Administrador):

```powershell
Copy-Item "C:\Users\User\Desktop\Petshopsystemv2" "C:\xampp\htdocs\" -Recurse -Force
```

1. Crie o banco de dados e importe o arquivo SQL (duas opcoes):

- phpMyAdmin: acesse [http://localhost/phpmyadmin](http://localhost/phpmyadmin) → New → import `sql/database.sql` (garanta UTF-8/utf8mb4)

- CLI (PowerShell): este método usa `cmd.exe` para compatibilidade com redirecionamento no Windows:

```powershell
cmd.exe /c '"C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS petshop_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"'

cmd.exe /c '"C:\xampp\mysql\bin\mysql.exe" -u root petshop_system < "C:\xampp\htdocs\Petshopsystemv2\sql\database.sql"'
```

Se o usuário `root` tiver senha, adicione `-p` e informe a senha quando solicitado.

1. Ajuste a configuracao se necessario:

- `config/database.php` — DB_HOST, DB_USER, DB_PASS, DB_NAME
- `config/config.php` — `BASE_URL` (padrão: `http://localhost/Petshopsystemv2/`)

1. Inicie Apache e MySQL no XAMPP Control Panel.

1. Abra no navegador:



 Pet Shop System (v2).


Sistema real de agendamento e gestão feito pra petshops que se perdem na agenda, esquecem cliente, cobram errado.. Aqui o fluxo é rápido, rastreável e sem dores de cabeça.
Esta é a versão 2, evolução direta do meu primeiro sistema, com várias melhorias: segurança, estabilidade e usabilidade.

 Highlights da v2

- Check-in rápido: cliente → pet → serviço sem enrolar
- Status do atendimento com rastreio total: Agendado → Em Atendimento → Concluído
- Segurança de gente grande: bcrypt + prepared statements
- Scripts automáticos para instalar e testar com 1 comando
- Dashboard com visão financeira e métricas simples

Fiz esse projeto com foco real no dia a dia da recepção do petshop — menos clique, mais resultado.

 Funcionalidades principais

| Módulo | O que faz |
|---|---|
| Autenticação | Admin e recepção, com acesso restrito |
| Clientes/Pets | CRUD completo com vínculo 1:N |
| Serviços | Preço, duração, categoria |
| Agenda | Gestão por data, status e observações |
| Dashboard | Totais + gráficos com Chart.js |
| Pesquisas | Filtros por data, status e categoria |
| Automação | Scripts de setup e verificação |

Login | Dashboard | Agenda | Cadastro de Pets | Relatórios…

 V1 → V2: o que subiu de nível

- UTF-8/utf8mb4 garantido (fim dos acentos quebrados no Windows)
- Hardening geral: prepared statements em tudo
- Proteção de sessão e XSS
- Scripts perigosos removidos do público
- README refeito com instruções completas e testes claros

🛠️ Stack técnico

- PHP 7.4+ (procedural, mysqli)
- MySQL/MariaDB (utf8mb4)
- HTML5, CSS3, JavaScript (ES6), Chart.js, Font Awesome
- Ambiente de dev recomendado: XAMPP no Windows

Como faz a Instalação ? (local)

http://localhost/Petshopsystemv2/

<details>
  <summary><strong>Passo a passo completo</strong></summary>

1. Pare serviços no XAMPP

1. Copie o projeto pra htdocs:

```powershell
Copy-Item "C:\Users\User\Desktop\Petshopsystemv2" "C:\xampp\htdocs\" -Recurse -Force
```

1. Crie o banco e importe o SQL via phpMyAdmin ou CLI:

```powershell
cmd.exe /c '"C:\xampp\mysql\bin\mysql.exe" -u root petshop_system < "C:\xampp\htdocs\Petshopsystemv2\sql\database.sql"'
```

1. Ajuste `config/database.php` e `config/config.php`

1. Inicie Apache e MySQL

1. Acesse o link do início

</details>

Fazendo os Testes rapidamente

```powershell
& php "C:\xampp\htdocs\Petshopsystemv2\scripts\test_db.php"
```

No navegador:

- Logar → criar cliente → criar pet → agendar → trocar status
- Ver dashboard atualizar
- Testar acesso sem login → deve bloquear

 Segurança aplicada

- Senhas: `password_hash()` + `password_verify()`
- Prepared statements em todas as entradas
- `htmlspecialchars()` para saída segura
- Soft-delete pra manter histórico sem quebrar nada

🔧 Publicação e boas práticas

- Tirar configs sensíveis antes de subir público
- Colocar `.gitignore` decente
- Em produção: Docker + DB gerenciado

 Autor

Natan Da Luz
📧 [natandaluz01@gmail.com](mailto:natandaluz01@gmail.com).

Este sistema foi desenvolvido como freelance para um pet shop real.
Os dados apresentados na versão pública são fictícios para preservar a segurança e privacidade do cliente.
