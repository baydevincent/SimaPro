@echo off
REM SimaPro Docker Setup Script for Windows
REM This script will pull the project from GitHub and setup Docker containers

echo.
echo ====================================
echo    SimaPro Docker Setup
echo ====================================
echo.

REM Configuration
set REPO_URL=https://github.com/baydevincent/SimaPro.git
set PROJECT_DIR=SimaPro

REM Check if Docker is installed
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Docker is not installed. Please install Docker Desktop first.
    pause
    exit /b 1
)

docker-compose --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Docker Compose is not installed.
    pause
    exit /b 1
)

echo [OK] Docker is installed
echo.

REM Check if project directory exists
if exist "%PROJECT_DIR%" (
    echo [INFO] Project directory already exists
    cd "%PROJECT_DIR%"
    
    echo [INFO] Pulling latest changes from GitHub...
    git pull origin main || git pull origin master
    echo [OK] Project updated
) else (
    echo [INFO] Cloning project from GitHub...
    git clone %REPO_URL%
    cd "%PROJECT_DIR%"
    echo [OK] Project cloned
)

echo.
echo [INFO] Building Docker containers...
docker-compose up -d --build

echo.
echo [INFO] Waiting for services to start...
timeout /t 10 /nobreak >nul

echo.
echo [INFO] Setting up Laravel...

REM Copy .env if not exists
if not exist .env (
    echo [INFO] Creating .env file...
    copy .env.docker .env
)

REM Install dependencies
echo [INFO] Installing PHP dependencies...
docker-compose exec -T app composer install --no-interaction

REM Generate application key
echo [INFO] Generating application key...
docker-compose exec -T app php artisan key:generate

REM Run migrations
echo [INFO] Running database migrations...
docker-compose exec -T app php artisan migrate --force

REM Create storage symlink
echo [INFO] Creating storage symlink...
docker-compose exec -T app php artisan storage:link

REM Install NPM dependencies
echo [INFO] Installing NPM dependencies...
docker-compose exec -T node npm install

REM Build assets
echo [INFO] Building assets...
docker-compose exec -T node npm run build

echo.
echo ====================================
echo    Setup Complete!
echo ====================================
echo.
echo Access your application at: http://localhost:8080
echo.
echo Useful commands:
echo   docker-compose ps                    - View running containers
echo   docker-compose logs -f               - View logs
echo   docker-compose exec app bash         - Access app container
echo   docker-compose exec postgresql psql -U simapro -d simapro  - Access database
echo   docker-compose down                  - Stop containers
echo.
pause
