# Run the app container against MySQL on your Windows host (e.g. Laragon).
# PowerShell uses the backtick ` for line continuation — NOT backslash \.
#
# Usage:
#   .\docker\run-with-host-mysql.ps1
#
# Requires: a successful `docker build -t daily-habit-builder .` first.

docker run --rm -p 8080:80 `
    -e APP_URL="http://localhost:8080" `
    -e DB_CONNECTION=mysql `
    -e DB_HOST=host.docker.internal `
    -e DB_PORT=3306 `
    -e DB_DATABASE=daily_habit_builder `
    -e DB_USERNAME=root `
    -e "DB_PASSWORD=" `
    -e SESSION_DRIVER=file `
    -e CACHE_STORE=file `
    -e QUEUE_CONNECTION=sync `
    -e RUN_MIGRATIONS=true `
    daily-habit-builder
