## MenuItemGroup

```graphql
query {
    menuItemGroups {
        id
        name
        menuItems {
            id
            name
        }
    }
}

query {
    menuItemGroup(id: "00000000-0000-0000-0000-000000000000") {
        id
        name
        menuItems {
            id
            name
        }
    }
}

mutation {
    createMenuItemGroup(
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
    updateMenuItemGroup(
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
    deleteMenuItemGroup(
        id: "00000000-0000-0000-0000-000000000000"
    ) {
        id
    }
}
```
