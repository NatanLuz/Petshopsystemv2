

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

## Funcionalidades principais

- Autenticação com perfis (admin, recepcionista)
- CRUD de clientes e pets (vínculo cliente → pet)
- Catálogo de serviços (preço, duração, categoria)
- Agendamento de atendimentos com status e observações
- Dashboard com métricas básicas e gráficos (Chart.js)
- Filtros e buscas por data, status e categoria
- Scripts de automação local: `scripts/auto_setup.ps1` e verificador `scripts/test_db.php`

## O que mudou em relação à v1

- Correções de compatibilidade de charset (UTF-8/utf8mb4) e normalização de texto para ambientes Windows problemáticos
- Remoção de scripts de manutenção sensíveis do caminho público após uso (one-time resets)
- Hardening: uso consistente de prepared statements, escapes de saída e verificação de sessões
- README consolidado, com instruções de instalação e testes manuais

## Stack técnico

- Backend: PHP 7.4+ (procedural, mysqli)
- Banco de dados: MySQL / MariaDB (utf8mb4)
- Frontend: HTML5, CSS3, JavaScript (ES6), Chart.js, Font Awesome
- Ambiente local recomendado: XAMPP (Windows)

---

## Instalação (local — Windows / XAMPP)

Siga estes passos em uma máquina de desenvolvimento Windows com XAMPP instalado.

1. Pare o Apache/MySQL no XAMPP Control Panel.
2. Copie o projeto para a pasta do servidor (execute no PowerShell como Administrador):

```powershell
Copy-Item "C:\Users\User\Desktop\Petshopsystemv2" "C:\xampp\htdocs\" -Recurse -Force
```

3. Crie o banco de dados e importe o arquivo SQL (duas opções):

- phpMyAdmin: acesse [http://localhost/phpmyadmin](http://localhost/phpmyadmin) → New → import `sql/database.sql` (garanta UTF-8/utf8mb4)

- CLI (PowerShell): este método usa `cmd.exe` para compatibilidade com redirecionamento no Windows:

```powershell
cmd.exe /c '"C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS petshop_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"'

cmd.exe /c '"C:\xampp\mysql\bin\mysql.exe" -u root petshop_system < "C:\xampp\htdocs\Petshopsystemv2\sql\database.sql"'
```

Se o usuário `root` tiver senha, adicione `-p` e informe a senha quando solicitado.

4. Ajuste a configuração se necessário:

- `config/database.php` — DB_HOST, DB_USER, DB_PASS, DB_NAME
- `config/config.php` — `BASE_URL` (padrão: `http://localhost/Petshopsystemv2/`)

5. Inicie Apache e MySQL no XAMPP Control Panel.

6. Abra no navegador:

````text

 Pet Shop System (v2) — Zero caos. Tudo organizado.

---

Sistema real de agendamento e gestão feito pra petshops que se perdem na agenda, esquecem cliente, cobram errado… Aqui o fluxo é rápido, rastreável e sem dores de cabeça.
Esta é a versão 2, evolução direta do meu primeiro sistema, com várias melhorias sérias: segurança, estabilidade e usabilidade.

 Highlights da v2

- Check-in rápido: cliente → pet → serviço sem enrolar
- Status do atendimento com rastreio total: Agendado → Em Atendimento → Concluído
- Segurança de gente grande: bcrypt + prepared statements
- Scripts automáticos para instalar e testar com 1 comando
- Dashboard com visão financeira e métricas simples

Fiz esse projeto com foco real no dia a dia da recepção — menos clique, mais resultado.

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

📸 Prints do sistema

Login | Dashboard | Agenda | Cadastro de Pets | Relatórios…

 V1 → V2: o que subiu de nível

- UTF-8/utf8mb4 garantido (fim dos acentos quebrados no Windows)
- Hardening geral: prepared statements em tudo
- Proteção de sessão e XSS
- Scripts perigosos removidos do público
- README refeito com instruções completas e testes claros

 Stack técnico

- PHP 7.4+ (procedural, mysqli)
- MySQL/MariaDB (utf8mb4)
- HTML5, CSS3, JavaScript (ES6), Chart.js, Font Awesome
- Ambiente de dev recomendado: XAMPP no Windows

Como faz a Instalação ? (local)

http://localhost/Petshopsystemv2/

<details>
  <summary><strong>Passo a passo completo</strong></summary>

1. Pare serviços no XAMPP

2. Copie o projeto pra htdocs:

```powershell
Copy-Item "C:\Users\User\Desktop\Petshopsystemv2" "C:\xampp\htdocs\" -Recurse -Force
````

3. Crie o banco e importe o SQL via phpMyAdmin ou CLI:

```powershell
cmd.exe /c '"C:\xampp\mysql\bin\mysql.exe" -u root petshop_system < "C:\xampp\htdocs\Petshopsystemv2\sql\database.sql"'
```

4. Ajuste `config/database.php` e `config/config.php`

5. Inicie Apache e MySQL

6. Acesse o link do início

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
📧 natandaluz01@gmail.com


MIT — livre pra estudar e evoluir o código.
