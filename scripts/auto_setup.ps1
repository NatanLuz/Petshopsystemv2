<#
auto_setup.ps1

Automatiza: copia da pasta do projeto para htdocs (se necessario) e importa o arquivo SQL
Uso (PowerShell):
  - Abra o PowerShell como Administrador
  - Executar:
      powershell -ExecutionPolicy Bypass -File "C:\Users\User\Desktop\Petshopsystemv2\scripts\auto_setup.ps1"

Parametros opcionais (editar no topo do arquivo ou passar via -SourcePath):
  -SourcePath, -TargetRoot, -DbName, -MysqlExe, -MysqlUser, -MysqlPass
#>

param(
    [string]$SourcePath = "C:\Users\User\Desktop\Petshopsystemv2",
    [string]$TargetRoot = "C:\xampp\htdocs",
    [string]$DbName = "petshop_system",
    [string]$MysqlExe = "C:\xampp\mysql\bin\mysql.exe",
    [string]$MysqlUser = "root",
    [string]$MysqlPass = ""
)

function Write-Info($msg) { Write-Host "[INFO] $msg" -ForegroundColor Cyan }
function Write-Ok($msg) { Write-Host "[OK]   $msg" -ForegroundColor Green }
function Write-Err($msg) { Write-Host "[ERR]  $msg" -ForegroundColor Red }

Write-Info "1) Verificando pre-requisitos"
if (-not (Test-Path $SourcePath)) {
    Write-Err "SourcePath nao encontrado: $SourcePath"
    exit 1
}

if (-not (Test-Path $MysqlExe)) {
    Write-Err "mysql.exe nao encontrado em: $MysqlExe. Ajuste o caminho do XAMPP (ex: C:\xampp\mysql\bin\mysql.exe)"
    exit 2
}

$projName = Split-Path $SourcePath -Leaf
$targetPath = Join-Path $TargetRoot $projName

Write-Info "Projeto: $projName"

if (-not (Test-Path $targetPath)) {
    Write-Info "2) Copiando projeto para: $TargetRoot"
    Copy-Item -Path $SourcePath -Destination $TargetRoot -Recurse -Force
    Write-Ok "Copia concluida: $targetPath"
} else {
    Write-Info "Projeto ja existe em $targetPath - pulando copia"
}

$sqlFile = Join-Path $targetPath "sql\database.sql"
if (-not (Test-Path $sqlFile)) {
    Write-Err "Arquivo SQL nao encontrado: $sqlFile"
    exit 3
}

Write-Info "3) Criando banco (se necessario) e importando dados: $DbName"

# Monta flags de autenticacao
$authFlags = "-u $MysqlUser"
if ($MysqlPass -ne "") { $authFlags += " -p$MysqlPass" }

# Cria banco (com cmd.exe para facilitar a citacao)
$createCmd = '"' + $MysqlExe + '" ' + $authFlags + ' -e "CREATE DATABASE IF NOT EXISTS ' + $DbName + ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"'
Write-Info "Executando criacao do banco..."
cmd.exe /c $createCmd

if ($LASTEXITCODE -ne 0) {
    Write-Err "Falha ao criar banco. Codigo de saida: $LASTEXITCODE"
    exit 4
}

# Importa via pipe (PowerShell) para evitar problemas com redirecionamento
Write-Info "Importando SQL: isso pode demorar alguns segundos..."
try {
    $sql = Get-Content $sqlFile -Raw
    $proc = Start-Process -FilePath $MysqlExe -ArgumentList @($authFlags, $DbName) -NoNewWindow -PassThru -RedirectStandardInput "PIPE" -RedirectStandardOutput "PIPE" -RedirectStandardError "PIPE"
    $proc.StandardInput.Write($sql)
    $proc.StandardInput.Close()
    $stdOut = $proc.StandardOutput.ReadToEnd()
    $stdErr = $proc.StandardError.ReadToEnd()
    $proc.WaitForExit()
    if ($proc.ExitCode -eq 0) {
        Write-Ok "Importacao finalizada com sucesso."
    } else {
        Write-Err "Import falhou (exit $($proc.ExitCode)). Mensagem: $stdErr"
        exit 5
    }
} catch {
    Write-Err "Erro durante import: $_"
    exit 6
}

Write-Ok "Projeto pronto em: http://localhost/$projName"
Write-Host "Credenciais de teste: admin@petshop.com / admin123  | recepcao@petshop.com / recepcao123"

Write-Info "Proximo passo: abra no navegador -> http://localhost/$projName"

exit 0
