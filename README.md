## Setup

```bash
# 初回実行時は以下の方法で`composer install`する
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

```
./vendor/bin/sail up
```

```bash
# モデル更新時に_ide_helper_models.phpを更新する必要がある。※DBアクセスするのでsailからしか実行できない
./vendor/bin/sail artisan ide-helper:models --nowrite
```

## Migration


```
./vendor/bin/sail php artisan migrate
```

```
./vendor/bin/sail php artisan migrate --seed
```

```
./vendor/bin/sail php artisan migrate:rollback --step=1
```

```
./vendor/bin/sail php artisan migrate:fresh --seed
```

## Test

```
./vendor/bin/sail test
```

```
./vendor/bin/sail test tests/Feature/GraphQL/Mutations/LoginTest.php
```

### Use Xdebug

```
./vendor/bin/sail debug test
```

```
./vendor/bin/sail debug test tests/Feature/GraphQL/Mutations/LoginTest.php
```

### Use Better Pest (VSCode Extension)

`Cmd + Ship + p` > `Better Pest: run`

## 設計

[ER](docs/er.md)

## Query And Mutation

実行環境
http://localhost/graphiql

[サンプル](docs/sample-query.md)

## Recommend VScode Settings

### Extensions (@recommended)

| extension              | purpose                         | link                                                                                    |
| ---------------------- | ------------------------------- | --------------------------------------------------------------------------------------- |
| PHP Intelephense       | PHP開発支援および、フォーマット | https://marketplace.visualstudio.com/items?itemName=bmewburn.vscode-intelephense-client |
| PHP Namespace Resolver | フォーマット（import順序）      | https://github.com/MehediDracula/PHP-Namespace-Resolver                                 |
| Better Pest            | GUIでのテスト実行               | https://github.com/m1guelpf/better-pest                                                 |

### .vscode/settings.json

```json
{
    "editor.formatOnSave": true,
    "[php]": {
        // PHP Intelephense
        "editor.defaultFormatter": "bmewburn.vscode-intelephense-client"
    },
    // PHP Namespace Resolver
    "namespaceResolver.sortAlphabetically": true,
    "namespaceResolver.sortOnSave": true,
    // Better Pest
    "better-pest.docker.enable": true,
    "better-pest.docker.command": "./vendor/bin/sail",
    "better-pest.pestBinary": "debug test",
    "better-pest.docker.paths": {
        "your workspace": "/var/www/html" // set your workspace
    },
}
```

## Deploy

### アクセストークンを生成するためにPassportが必要とする暗号化キーを生成 & OAuth Client作成
```
php artisan passport:install

# php artisan passport:keys 暗号化キーを生成のみ行う場合
```

以下の環境変数に生成したClientを設定。SecretはDBのデータを確認して埋める
```
# for passport
PASSPORT_PERSONAL_ACCESS_CLIENT_ID=1
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=

# for lighthouse-graphql-passport-auth
PASSPORT_CLIENT_ID=2
PASSPORT_CLIENT_SECRET=
```

## Linter

https://github.com/larastan/larastan

```
./vendor/bin/sail bin phpstan analyse
```
