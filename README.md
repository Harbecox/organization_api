## Шаги установки

1.
   ```bash
   composer install
   ```

2. 
   ```bash
   cp .env.example .env
   ```

3. 
   ```bash
   sudo ./vendor/bin/sail up -d
   ```

4. 
   ```bash
   sudo chmod -R 777 storage bootstrap/cache
   ```

5. 
   ```bash
   php artisan key:generate
   ```

6. 
   ```bash
   sudo ./vendor/bin/sail artisan migrate
   ```

7. 
   ```bash
   sudo ./vendor/bin/sail artisan db:seed
   ```

8. 
   ```bash
   php artisan config:clear
   ```

---

## Документация API

Документация доступна по следующему адресу:
[http://127.0.0.1:8000/api/documentation](http://127.0.0.1:8000/api/documentation)

---

## API ключ

Для работы с API вам нужно указать токен в файле `.env`:

```text
STATIC_SANCTUM_TOKEN=<ВАШ_ТОКЕН>
```
