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

## Menu

```graphql
query {
    menu(id: "00000000-0000-0000-0000-000000000000") {
        id
        name
        menuSections {
            id
            name
            menuItems {
                id
                name
                price
            }
        }
    }
}

mutation {
    createMenu(
        name: "メニュー"
        menuSections: [
            {
                name: "ドリンク"
                menuItems: [
                    {
                        name: "烏龍茶"
                        price: 200
                    },
                    {
                        name: "コーラ"
                        price: 250
                    },
                ]
            }
        ]
    ) {
        id
        name
        menuSections {
            id
            name
            menuItems {
                id
                name
                price
            }
        }
    }
}

mutation {
    updateMenu(
        id: "00000000-0000-0000-0000-000000000000"
        name: "メニュー"
        menuSections: [
            {
                name: "ドリンク"
                menuItems: [
                    {
                        name: "烏龍茶"
                        price: 200
                    },
                    {
                        name: "コーラ"
                        price: 250
                    },
                ]
            }
        ]
    ) {
        id
        name
        menuSections {
            id
            name
            menuItems {
                id
                name
                price
            }
        }
    }
}

mutation {
    deleteMenu(
        id: "00000000-0000-0000-0000-000000000000"
    ) {
        id
    }
}
```
