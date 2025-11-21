```markdown
# Adept Cinema — Starter проект

Това repo е структурен стартов проект за стрийминг сайт (Adept Cinema). Подходящо за работа в Acode (или друг редактор), push в GitHub и деплой с Docker / VPS.

Съдържание и структура
- public/ — документ-рут (всички публични PHP страници)
- common/ — общи конфигурации и helper-и
- admin/ — админ панел
- api/ — API endpoints
- uploads/ — качени файлове (banners, payments, subtitles, posters)
- sql/ — create_db.sql
- scripts/ — скриптове (seed, create_project)
- .env.example — пример за среда (не комитвай .env)
- docker-compose.yml — за локално dev
- README.md — този файл

Бърз старт (Acode)
1. В Acode създай нова папка и копирай цялата структура и файловете от този проект.
2. Създай `.env` от `.env.example` и попълни стойности:
   - DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
   - APP_URL (например http://localhost:8080)
3. Създай нужните upload директории (ако не съществуват):
   - uploads/banners uploads/payments uploads/subtitles uploads/posters
   - На Linux/termux: `mkdir -p uploads/{banners,payments,subtitles,posters}`

Локален dev с Docker (препоръчително)
1. Инсталирай Docker & docker-compose.
2. От project root изпълни:
   - docker-compose up -d
3. Импортирай схемата в MySQL (или използвай phpMyAdmin на http://localhost:8081):
   - `docker exec -i adept-db mysql -uroot -psecret adept_cinema < sql/create_db.sql`
4. (Опция) Попълни seed данни:
   - `docker exec -it adept-php bash`
   - `php /var/www/html/scripts/seed_data.php`
5. Посети: http://localhost:8080

Работа с Git / GitHub (точни команди)
1. Инициирай repo (ако още нямаш):
   - git init
   - git add .
   - git commit -m "Initial project structure"
   - git branch -M main
   - git remote add origin https://github.com/<your-username>/<repo>.git
   - git push -u origin main
2. В Acode може да използваш вграден Git plugin или Termux за изпълнение на горните команди.

GitHub Actions (CI)
- В `.github/workflows/ci.yml` има workflow, който прави PHP lint (php -l) за всички PHP файлове и опция за build+push на Docker image към GHCR/Registry (изисква secrets: REGISTRY_USER, REGISTRY_TOKEN, IMAGE_NAME).

Production deploy (варианти)
1. Деплой на VPS (NGINX + PHP-FPM):
   - Качи съдържанието на `public/` в document root.
   - Постави `common/`, `admin/`, `api/` предпочитано извън webroot и нагласи include пътищата.
   - Попълни `.env` с production стойности.
   - Импортирай `sql/create_db.sql`.
   - Конфигурирай Nginx да рутва към `public/` и да подава PHP към php-fpm.

2. Деплой с Docker (препоръчително):
   - Използвай `docker-compose.yml` за локално + production модификация (environment variables, volumes).
   - Алтернативно използвай контейнерна платформа (Render, DigitalOcean App Platform) и свържи repo.

Полезни скриптове
- scripts/create_project.sh — създава папките.
- scripts/seed_data.php — създава админ и примерни записи.
- scripts/create_zip.sh — създава zip архив за прехвърляне (включен по-долу).

Security & production notes
- Никога не комитвайте `.env` (в .gitignore).
- Ограничете директен достъп до `uploads/` ако съдържа чувствителни файлове.
- За видеосървинг: prefer redirect към trusted direct-download URLs (Google Drive uc?export=download) вместо проксиране през вашия сървър за големи файлове.
- За реални плащания използвайте одобрен платежен доставчик (Stripe, PayPal) и съхранявайте минимална чувствителна информация.

Ако искаш, подготвям:
- PR/patch за готов репо в твоя GitHub (мога да генерирам инструкции или patch файлове).
- Допълнителни CI стъпки (security scan, deploy).
```
