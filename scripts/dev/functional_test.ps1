param(
    [string]$PhpExe = "C:\xampp\php\php.exe",
    [string]$MysqlExe = "C:\xampp\mysql\bin\mysql.exe",
    [string]$DbName = "petshop_system_v2_test",
    [int]$Port = 8765
)

$ErrorActionPreference = 'Stop'
$root = (Resolve-Path (Join-Path $PSScriptRoot "..\..")).Path
$sessionPath = Join-Path $root ".tmp-sessions"
$stdout = Join-Path $root "php-test-server.log"
$stderr = Join-Path $root "php-test-server-error.log"
$baseUrl = "http://127.0.0.1:$Port"

New-Item -ItemType Directory -Path $sessionPath -Force | Out-Null
$env:DB_NAME = $DbName
$env:BASE_URL = "$baseUrl/"

function Get-CsrfToken($html) {
    $match = [regex]::Match($html, 'name="csrf_token" value="([^"]+)"')
    if (-not $match.Success) {
        throw 'Token CSRF nao encontrado.'
    }
    return $match.Groups[1].Value
}

function Login($email, $password) {
    $session = New-Object Microsoft.PowerShell.Commands.WebRequestSession
    $page = Invoke-WebRequest -Uri "$baseUrl/pages/login.php" -WebSession $session -UseBasicParsing
    $cookieHeader = [string]$page.Headers['Set-Cookie']
    if ($cookieHeader -notmatch 'HttpOnly' -or $cookieHeader -notmatch 'SameSite=Lax') {
        throw 'Cookie de sessao sem HttpOnly ou SameSite=Lax.'
    }
    $response = Invoke-WebRequest -Uri "$baseUrl/pages/login.php" -Method Post -WebSession $session -Body @{
        csrf_token = Get-CsrfToken $page.Content
        email = $email
        senha = $password
    } -UseBasicParsing

    if ($response.BaseResponse.ResponseUri.AbsolutePath -ne '/pages/dashboard.php') {
        throw "Falha no login de $email."
    }
    return $session
}

function Submit-Form($session, $page, $body, $expectedMessage) {
    $currentPage = Invoke-WebRequest -Uri "$baseUrl/pages/$page" -WebSession $session -UseBasicParsing
    $body.csrf_token = Get-CsrfToken $currentPage.Content
    $response = Invoke-WebRequest -Uri "$baseUrl/pages/$page" -Method Post -WebSession $session -Body $body -UseBasicParsing
    if ($response.Content -notmatch [regex]::Escape($expectedMessage)) {
        throw "$page nao exibiu: $expectedMessage"
    }
}

function Query-Value($sql) {
    return (& $MysqlExe -u root -N -D $DbName -e $sql | Select-Object -First 1)
}

$server = $null
$testsPassed = $false
try {
    & $MysqlExe -u root -D $DbName -e @"
DELETE a FROM atendimentos a
JOIN pets p ON p.id = a.pet_id
WHERE p.nome IN ('Pet Teste', 'Pet Teste Atualizado');
DELETE FROM pets WHERE nome IN ('Pet Teste', 'Pet Teste Atualizado');
DELETE FROM servicos WHERE nome IN ('Servico Teste', 'Servico Teste Atualizado');
DELETE FROM clientes WHERE email IN ('teste@example.com', 'teste-sem-cpf@example.com');
"@
    if ($LASTEXITCODE -ne 0) {
        throw 'Falha ao limpar dados de uma execucao anterior.'
    }

    $server = Start-Process -FilePath $PhpExe -ArgumentList @(
        '-d', "session.save_path=$sessionPath",
        '-S', "127.0.0.1:$Port",
        '-t', $root
    ) -WorkingDirectory $root -WindowStyle Hidden -PassThru -RedirectStandardOutput $stdout -RedirectStandardError $stderr

    Start-Sleep -Seconds 2

    $privateRequest = [System.Net.HttpWebRequest]::Create("$baseUrl/pages/dashboard.php")
    $privateRequest.AllowAutoRedirect = $false
    $privateResponse = $privateRequest.GetResponse()
    if ([int]$privateResponse.StatusCode -ne 302) {
        $privateResponse.Close()
        throw 'Pagina privada aceitou acesso sem sessao.'
    }
    $privateResponse.Close()

    $admin = Login 'admin@petshop.com' 'admin123'
    $dashboard = Invoke-WebRequest -Uri "$baseUrl/pages/dashboard.php" -WebSession $admin -UseBasicParsing
    if ($dashboard.Content -notmatch 'Administrador Demo') {
        throw 'Dashboard do administrador nao carregou corretamente.'
    }

    Submit-Form $admin 'clientes.php' @{
        action='create'; nome='Cliente Teste Automatizado'; email='teste@example.com';
        telefone='(11) 3111-1111'; celular=''; cpf='333.333.333-33'; endereco='';
        bairro=''; cidade='Sao Paulo'; estado='SP'; cep=''; observacoes='teste'
    } 'Cliente cadastrado com sucesso!'
    $clientId = Query-Value "SELECT id FROM clientes WHERE email='teste@example.com'"

    Submit-Form $admin 'clientes.php' @{
        action='create'; nome='Cliente Sem CPF'; email='teste-sem-cpf@example.com';
        telefone='(11) 3333-3333'; celular=''; cpf=''; endereco='';
        bairro=''; cidade='Sao Paulo'; estado='SP'; cep=''; observacoes=''
    } 'Cliente cadastrado com sucesso!'
    $secondClientId = Query-Value "SELECT id FROM clientes WHERE email='teste-sem-cpf@example.com'"

    Submit-Form $admin 'clientes.php' @{
        action='update'; id=$clientId; nome='Cliente Teste Atualizado'; email='teste@example.com';
        telefone='(11) 3222-2222'; celular=''; cpf='333.333.333-33'; endereco='';
        bairro=''; cidade='Sao Paulo'; estado='SP'; cep=''; observacoes='atualizado'
    } 'Cliente atualizado com sucesso!'

    Submit-Form $admin 'pets.php' @{
        action='create'; cliente_id=$clientId; nome='Pet Teste'; especie='Cachorro';
        raca='SRD'; sexo='Macho'; cor='Preto'; data_nascimento='';
        peso='10.5'; observacoes='teste'
    } 'Pet cadastrado com sucesso!'
    $petId = Query-Value "SELECT id FROM pets WHERE nome='Pet Teste'"

    Submit-Form $admin 'pets.php' @{
        action='update'; id=$petId; cliente_id=$clientId; nome='Pet Teste Atualizado';
        especie='Cachorro'; raca='SRD'; sexo='Macho'; cor='Preto';
        data_nascimento='2023-01-01'; peso='11'; observacoes='atualizado'
    } 'Pet atualizado com sucesso!'

    Submit-Form $admin 'servicos.php' @{
        action='create'; nome='Servico Teste'; descricao='teste'; preco='50';
        duracao_minutos='30'; categoria='Outro'
    } 'Servico cadastrado com sucesso!'
    $serviceId = Query-Value "SELECT id FROM servicos WHERE nome='Servico Teste'"

    Submit-Form $admin 'servicos.php' @{
        action='update'; id=$serviceId; nome='Servico Teste Atualizado';
        descricao='atualizado'; preco='55'; duracao_minutos='35'; categoria='Outro'
    } 'Servico atualizado com sucesso!'

    Submit-Form $admin 'atendimentos.php' @{
        action='create'; pet_id=$petId; servico_id=$serviceId;
        data_atendimento='2026-06-15'; hora_atendimento='10:00';
        status='Agendado'; valor='55'; observacoes='teste'
    } 'Atendimento agendado com sucesso!'
    $appointmentId = Query-Value "SELECT id FROM atendimentos WHERE pet_id=$petId AND servico_id=$serviceId"

    Submit-Form $admin 'atendimentos.php' @{
        action='update'; id=$appointmentId; pet_id=$petId; servico_id=$serviceId;
        data_atendimento='2026-06-15'; hora_atendimento='11:00';
        status='Concluido'; valor='55'; observacoes='atualizado'
    } 'Atendimento atualizado com sucesso!'

    $filters = @(
        @{ Uri="$baseUrl/pages/clientes.php?search=Atualizado"; Text='Cliente Teste Atualizado' },
        @{ Uri="$baseUrl/pages/pets.php?especie=Cachorro"; Text='Pet Teste Atualizado' },
        @{ Uri="$baseUrl/pages/servicos.php?categoria=Outro"; Text='Servico Teste Atualizado' },
        @{ Uri="$baseUrl/pages/atendimentos.php?status=Concluido&data=2026-06-15"; Text='Pet Teste Atualizado' }
    )
    foreach ($filter in $filters) {
        $response = Invoke-WebRequest -Uri $filter.Uri -WebSession $admin -UseBasicParsing
        if ($response.Content -notmatch [regex]::Escape($filter.Text)) {
            throw "Filtro falhou: $($filter.Uri)"
        }
    }

    Submit-Form $admin 'atendimentos.php' @{ action='delete'; id=$appointmentId } 'Atendimento excluido com sucesso!'
    Submit-Form $admin 'pets.php' @{ action='delete'; id=$petId } 'Pet excluido com sucesso!'
    Submit-Form $admin 'servicos.php' @{ action='delete'; id=$serviceId } 'Servico excluido com sucesso!'
    Submit-Form $admin 'clientes.php' @{ action='delete'; id=$clientId } 'Cliente excluido com sucesso!'
    Submit-Form $admin 'clientes.php' @{ action='delete'; id=$secondClientId } 'Cliente excluido com sucesso!'

    $logout = Invoke-WebRequest -Uri "$baseUrl/pages/logout.php" -WebSession $admin -UseBasicParsing
    if ($logout.BaseResponse.ResponseUri.AbsolutePath -ne '/pages/login.php') {
        throw 'Logout nao redirecionou para o login.'
    }

    $reception = Login 'recepcao@petshop.com' 'recepcao123'
    $receptionDashboard = Invoke-WebRequest -Uri "$baseUrl/pages/dashboard.php" -WebSession $reception -UseBasicParsing
    if ($receptionDashboard.Content -notmatch 'Recepcao Demo') {
        throw 'Dashboard da recepcao nao carregou corretamente.'
    }

    try {
        Invoke-WebRequest -Uri "$baseUrl/pages/login.php" -Method Post -Body @{
            email='admin@petshop.com'; senha='admin123'
        } -UseBasicParsing -ErrorAction Stop | Out-Null
        throw 'POST sem CSRF foi aceito.'
    } catch {
        if ($_.Exception.Response.StatusCode.value__ -ne 403) {
            throw
        }
    }

    Write-Output 'LOGIN_ADMIN=PASS'
    Write-Output 'LOGIN_RECEPCAO=PASS'
    Write-Output 'LOGOUT=PASS'
    Write-Output 'CRUD_CLIENTES=PASS'
    Write-Output 'CRUD_PETS=PASS'
    Write-Output 'CRUD_SERVICOS=PASS'
    Write-Output 'CRUD_ATENDIMENTOS=PASS'
    Write-Output 'DASHBOARD=PASS'
    Write-Output 'FILTROS=PASS'
    Write-Output 'CSRF=PASS'
    Write-Output 'PRIVATE_ACCESS=PASS'
    Write-Output 'SESSION_COOKIE=PASS'
    $testsPassed = $true
} finally {
    if ($server -and -not $server.HasExited) {
        Stop-Process -Id $server.Id -Force
    }
    Remove-Item -LiteralPath $sessionPath -Recurse -Force -ErrorAction SilentlyContinue
    if ($testsPassed) {
        Remove-Item -LiteralPath $stdout, $stderr -Force -ErrorAction SilentlyContinue
    }
}
