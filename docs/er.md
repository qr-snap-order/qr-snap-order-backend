## Shop User

```mermaid
erDiagram
    users }|--o{ tenants : ""
    tenants ||--o{ shops : ""
    tenants ||--o{ employees : ""
    shops }o--o{ employees : ""
```

## Menu

```mermaid
erDiagram
    menus ||--o{ menu_categories : ""
    menu_categories ||--o{ menu_items : ""
```
