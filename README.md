## ER

```mermaid
erDiagram
    users }|--o{ organizations : ""
    organizations ||--o{ shops : ""
    organizations ||--o{ staffs : ""
    shops }o--o{ staffs : ""
```
