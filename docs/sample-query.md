## Tenant

```graphql
query {
    tenant(id: "00000000-0000-0000-0000-000000000000") {
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

## Shop

```graphql
mutation {
    createShop(
        name: "hoge"
        tenant_id: "00000000-0000-0000-0000-000000000000"
    ) {
        id
        name
        tenant {
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
        tenant {
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
