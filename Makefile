.PHONY: up down logs shell migrate fresh seed test lint key

up:      ; docker compose up -d --build
down:    ; docker compose down
logs:    ; docker compose logs -f app api
shell:   ; docker compose exec app sh
migrate: ; docker compose exec app php artisan migrate
fresh:   ; docker compose exec app php artisan migrate:fresh --seed
seed:    ; docker compose exec app php artisan db:seed
test:    ; docker compose exec app php artisan test
lint:    ; docker compose exec app ./vendor/bin/pint
key:     ; docker compose exec app php artisan key:generate --show
