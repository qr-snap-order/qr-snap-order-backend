## Migration

```
./vendor/bin/sail php artisan migrate --seed --database=pgsql
```

```
./vendor/bin/sail php artisan migrate:fresh --seed --database=pgsql
```

## ER

```mermaid
erDiagram
    users }|--o{ organizations : ""
    organizations ||--o{ shops : ""
    organizations ||--o{ staffs : ""
    shops }o--o{ staffs : ""
```
