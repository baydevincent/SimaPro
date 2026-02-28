@echo off
echo ========================================
echo Restore Laravel Mix Configuration
echo ========================================
echo.

echo Copying backup files...
copy /Y "backup-pre-vite\package.json" "package.json"
copy /Y "backup-pre-vite\webpack.mix.js" "webpack.mix.js"
copy /Y "backup-pre-vite\app.js" "resources\js\app.js"
copy /Y "backup-pre-vite\bootstrap.js" "resources\js\bootstrap.js"
copy /Y "backup-pre-vite\app.scss" "resources\sass\app.scss"

echo.
echo Deleting Vite configuration...
if exist "vite.config.js" del "vite.config.js"

echo.
echo Restoring layouts/app.blade.php...
git checkout resources/views/layouts/app.blade.php

echo.
echo Installing Laravel Mix dependencies...
call npm install

echo.
echo ========================================
echo Restore completed successfully!
echo ========================================
echo.
echo You can now run:
echo   npm run dev       - Development build
echo   npm run watch     - Watch for changes
echo   npm run prod      - Production build
echo ========================================
pause
