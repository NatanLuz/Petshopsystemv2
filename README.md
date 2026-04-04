## Pet Shop System (v2) — Sistema Real de Agendamento e Gestão Operacional

![Status](https://img.shields.io/badge/Status-Completo-success) ![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue) ![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange) ![License](https://img.shields.io/badge/License-MIT-green)

Sistema de agendamento e gestão desenvolvido tanto para petshops quanto para clínicas veterinárias que enfrentam problemas recorrentes como conflitos de agenda, perda de histórico de clientes/pets e inconsistências no controle financeiro.

A **v2** é a evolução direta da primeira versão do projeto, com foco em:

- Robustez e previsibilidade operacional
- Segurança aplicada de forma consistente
- Melhoria do fluxo da recepção (menos cliques, mais resultado)

O objetivo não foi apenas “refatorar código”, mas **estruturar o sistema para operar de forma confiável em ambiente real**: fluxo rápido, rastreável e sem dores de cabeça.

---

## Proposta de valor

O sistema atua diretamente em três frentes críticas do negócio:

- **Organização operacional** — agenda estruturada com controle de status e prevenção de inconsistências.
- **Rastreabilidade** — histórico consolidado por cliente e pet, com registro completo de atendimentos.
- **Visão gerencial simplificada** — métricas essenciais para controle financeiro e tomada de decisão.

Toda a solução foi pensada para a rotina da recepção do petshop: **fluxo de check-in direto (cliente → pet → serviço)**, menos fricção, menos telas desnecessárias e maior previsibilidade no dia a dia.

---

## Highlights da v2

- **Fluxo otimizado de check-in**: cliente → pet → serviço, sem enrolação.
- **Controle de estado do atendimento**: Agendado → Em Atendimento → Concluído.
- **Hardening de segurança**: `password_hash()`/`password_verify()` (bcrypt) + prepared statements em 100% das interações com banco.
- **Scripts automatizados** para instalação local e verificação de integridade do ambiente.
- **Dashboard operacional** com indicadores e gráficos via Chart.js.
- **Padronização de encoding**: UTF-8 / utf8mb4 (fim dos acentos quebrados no Windows).

Projeto desenvolvido e utilizado em **petshop real e clinica veterinaria**, com versão pública sanitizada para preservar privacidade.

---

## Arquitetura e estrutura técnica

Embora desenvolvido em **PHP procedural (mysqli)**, o projeto foi estruturado com foco em clareza e evolução futura:

- Separação clara de responsabilidades por módulo.
- Organização modular de pastas (config, módulos, views, scripts, SQL).
- Camada de configuração isolada para ambiente/banco.
- Padronização de validações e tratamento de erros.
- Tratamento consistente de entrada (sanitização) e saída (escape).

A base foi pensada para facilitar uma futura migração para uma arquitetura mais desacoplada (**MVC** ou **API-first / REST**), sem reescrever tudo do zero.

---

## Funcionalidades principais

| Módulo        | Responsabilidade / O que faz                           |
| ------------- | ------------------------------------------------------ |
| Autenticação  | Controle de acesso por perfil (admin/recepção)         |
| Clientes/Pets | CRUD completo com vínculo relacional 1:N               |
| Serviços      | Gestão de preço, duração e categorização               |
| Agenda        | Agendamento por data, controle de status e observações |
| Dashboard     | Indicadores operacionais e gráficos (Chart.js)         |
| Filtros       | Pesquisa por data, status e categoria                  |
| Automação     | Scripts de setup e verificação de integridade          |

Fluxo típico: **Login → cadastro cliente → cadastro pet → agendamento → atualização de status → acompanhamento no dashboard**.

---

## Evolução técnica — v1 → v2

Principais avanços estruturais da v2 em relação à v1:

- Correção definitiva de problemas de **encoding** (UTF-8/utf8mb4).
- Substituição completa de queries vulneráveis por **prepared statements**.
- Proteção contra **XSS** via escape consistente de saída (`htmlspecialchars()`).
- Gestão de sessão mais segura (validação, regeneração e restrição de acesso).
- Remoção de scripts sensíveis do ambiente público.
- README técnico estruturado com instruções **reproduzíveis** e testes claros.
- Redução significativa da dívida técnica acumulada na v1.

Em resumo, a v2 não é apenas “mais uma refatoração”, mas um **hardening completo da base** para uso real.

---

## Segurança aplicada

- **Senhas**: `password_hash()` + `password_verify()` (bcrypt).
- **Banco de dados**: prepared statements em todas as interações (mysqli).
- **Saída HTML**: `htmlspecialchars()` para evitar XSS em campos exibidos.
- **Soft-delete** para preservar histórico sem quebrar integridade relacional.
- **Controle de acesso baseado em sessão** (admin x recepção, bloqueio sem login).

O foco é reduzir os vetores clássicos de ataque em aplicações PHP tradicionais: SQL Injection, XSS, exposição de scripts sensíveis e sessões frágeis.

---

## Stack técnico

- **Backend**: PHP 7.4+ (procedural, mysqli)
- **Banco de dados**: MySQL / MariaDB (utf8mb4)
- **Frontend**: HTML5, CSS3
- **JavaScript**: ES6 + Chart.js
- **Ícones/UI**: Font Awesome
- **Ambiente de desenvolvimento recomendado**: XAMPP (Windows)

---

## Instalação (local — Windows / XAMPP)

Ambiente alvo de desenvolvimento: Windows + XAMPP.

URL padrão após instalação:

````text
http://localhost/Petshopsystemv2/
``

<details>
  <summary><strong>Passo a passo completo</strong></summary>

1. **Pare o Apache/MySQL** no XAMPP Control Panel.
2. **Copie o projeto** para a pasta do servidor (PowerShell como Administrador):

	```powershell
	Copy-Item "C:\Users\User\Desktop\Petshopsystemv2" "C:\xampp\htdocs\" -Recurse -Force
	```

3. **Crie o banco de dados e importe o SQL** (via phpMyAdmin ou CLI):

	- Via phpMyAdmin: acesse `http://localhost/phpmyadmin` → *New* → crie o banco `petshop_system` em **utf8mb4** → aba *Import* → selecione `sql/database.sql`.

	- Via CLI (PowerShell, usando `cmd.exe` para redirecionamento):

	  ```powershell
	  cmd.exe /c '"C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS petshop_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"'

	  cmd.exe /c '"C:\xampp\mysql\bin\mysql.exe" -u root petshop_system < "C:\xampp\htdocs\Petshopsystemv2\sql\database.sql"'
	  ```

	  > Se o usuário `root` tiver senha, adicione `-p` e informe a senha quando solicitado.

4. **Ajuste as configurações se necessário**:

	- `config/database.php` — `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`.
	- `config/config.php` — `BASE_URL` (padrão: `http://localhost/Petshopsystemv2/`).

5. **Inicie Apache e MySQL** no XAMPP Control Panel.
6. **Acesse no navegador**: `http://localhost/Petshopsystemv2/`.

</details>

---

## Testes rápidos (sanidade do ambiente)

Para validar rapidamente a conexão com o banco e a estrutura básica:

```powershell
& php "C:\xampp\htdocs\Petshopsystemv2\scripts\test_db.php"
````

No navegador, recomenda-se o seguinte fluxo mínimo de teste:

- Realizar login com usuário de teste.
- Criar um **cliente**.
- Criar um **pet** vinculado a esse cliente.
- Criar um **agendamento** para o pet.
- Alterar o status do atendimento: Agendado → Em Atendimento → Concluído.
- Verificar o **dashboard** atualizando métricas.
- Tentar acessar rotas internas sem login → deve ser bloqueado/redirecionado para login.

---

## Publicação e boas práticas

Para ambiente produtivo, recomenda-se:

- Remover **configurações sensíveis** do repositório (credenciais, senhas, etc.).
- Usar um `.gitignore` adequado para evitar upload de arquivos transitórios/logs.
- Isolar variáveis de ambiente (por exemplo, via `.env` ou mecanismo equivalente).
- Considerar **containerização com Docker** para padronizar ambiente.
- Utilizar banco de dados gerenciado (evitando uso de `root` em produção).

---

## Roadmap (evoluções futuras)

- API REST para desacoplamento frontend/backend.
- Controle granular de permissões (RBAC).
- Logs estruturados para auditoria e rastreabilidade avançada.
- Versionamento de serviços e preços.
- Rotina de backup automatizado.
- Integração com WhatsApp para confirmação de agendamentos.
- Recursos com IA (previsão de demanda, sugestão de agenda inteligente, insights operacionais).

---

## Screenshots (versão demonstrativa)

Clique nas imagens para visualizar em tamanho maior:

[![Atendimentos](https://i.postimg.cc/bSXKVmvF/atendimentos.png)](https://postimg.cc/bSXKVmvF)
[![Clientes](https://i.postimg.cc/p5Ngcsd6/Clientes.png)](https://postimg.cc/p5Ngcsd6)
[![Logado](https://i.postimg.cc/wRKS4wBr/logado-como-recepcionista.png)](https://postimg.cc/wRKS4wBr)

[![Login](https://i.postimg.cc/64sDbMQF/Login.png)](https://postimg.cc/64sDbMQF)
[![Login 2](https://i.postimg.cc/8f8qXZC2/Login2.png)](https://postimg.cc/8f8qXZC2)
[![Novo Atendimento](https://i.postimg.cc/dZMzN51F/novo-atendimento.png)](https://postimg.cc/dZMzN51F)

[![Novo Pet](https://i.postimg.cc/gL9ftgJm/novo-pet.png)](https://postimg.cc/gL9ftgJm)
[![Novo Serviço](https://i.postimg.cc/Mfkh39Tz/novo-servico.png)](https://postimg.cc/Mfkh39Tz)
[![Novo Cliente](https://i.postimg.cc/n986S3zV/novocliente.png)](https://postimg.cc/n986S3zV)

[![Pet](https://i.postimg.cc/jnVG3M5S/petx.png)](https://postimg.cc/jnVG3M5S)
[![Serviços](https://i.postimg.cc/ctqVkFHC/servicos.png)](https://postimg.cc/ctqVkFHC)

---

## Autor

- **Nome**: Natan Da Luz
- **E-mail**: [natandaluz01@gmail.com](mailto:natandaluz01@gmail.com)

Sistema desenvolvido para uso real em petshop local.  
Todos os dados presentes na versão demonstrativa pública são fictícios, preservando a segurança e a privacidade do cliente real.
