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
    organizations ||--o{ employees : ""
    shops }o--o{ employees : ""
```

```mermaid
erDiagram
    menus ||--o{ menu_categories : ""
    menu_categories ||--o{ menu_items : ""
```

## Query And Mutation

http://localhost/graphiql

```graphql
query {
    organization(id: "00000000-0000-0000-0000-000000000000") {
        id
        name
        users {
            id
            name
        }
        shops {
            id
            name
        }
        employees {
            id
            name
        }
    }
}
```

```graphql
mutation {
    createShop(
        name: "hoge"
        organization_id: "00000000-0000-0000-0000-000000000000"
    ) {
        id
        name
        organization {
            id
            name
        }
        employees {
            id
            name
        }
    }
}

mutation {
    updateShop(
        id: "00000000-0000-0000-0000-000000000000"
        name: "hoge"
        employees: {
            sync: ["00000000-0000-0000-0000-000000000000"]
        }
    ) {
        id
        name
        organization {
            id
            name
        }
        employees {
            id
            name
        }
    }
}
```
