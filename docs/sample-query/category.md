## Category

```graphql
query {
    categories {
        id
        name
        menuItems {
            id
            name
        }
    }
}

query {
    category(id: "00000000-0000-0000-0000-000000000000") {
        id
        name
        menuItems {
            id
            name
        }
    }
}

mutation {
    createCategory(
        name: "夏季限定"
        menuItems: ["00000000-0000-0000-0000-000000000000"]
    ) {
        id
        name
        menuItems {
            id
            name
        }
    }
}

mutation {
    updateCategory(
        id: "00000000-0000-0000-0000-000000000000"
        name: "夏季限定"
        menuItems: ["00000000-0000-0000-0000-000000000000"]
    ) {
        id
        name
        menuItems {
            id
            name
        }
    }
}

mutation {
    deleteCategory(
        id: "00000000-0000-0000-0000-000000000000"
    ) {
        id
    }
}
```
