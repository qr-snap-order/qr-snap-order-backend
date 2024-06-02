## Setup

`make setup`を実行して、セットアップおよびコンテナを起動する

Laravel PassportのClient secretがログに出力されるので.envに設定する

```
# for passport
PASSPORT_PERSONAL_ACCESS_CLIENT_ID=1
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=

# for lighthouse-graphql-passport-auth
PASSPORT_CLIENT_ID=2
PASSPORT_CLIENT_SECRET=
```

|コマンド|意味|
|---|---|
|make setup|各種セットアップ、コンテナ起動|
|make up|コンテナ起動|
|make stop|コンテナ停止|
|make down|コンテナ削除|
|make destroy|コンテナ削除（Volumesも削除）|

## Migration

|コマンド|意味|
|---|---|
|make migrate|マイグレーション|
|make seed|シードデータ取り込み|
|make fresh|全テーブルをドロップ & マイグレーション|
|make rollback|ロールバック（1ステップ）|

## Test

|コマンド|意味|
|---|---|
|make test|テスト実行|
|make test-debug|デバックモードでテスト実行|

### Use Better Pest (VSCode Extension)

`Cmd + Ship + p` > `Better Pest: run`

下記の.vscode/settings.jsonを設定

## Linter

https://github.com/larastan/larastan

|コマンド|意味|
|---|---|
|make lint|Linterの実行|

## 設計

[ER](docs/er.md)

## Query And Mutation

実行環境
http://test.localhost/graphiql

[サンプル](docs/sample-query.md)

Headers
```
{
    "Authorization": "Bearer User-Token"
}
```

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

## Tasks

https://naotake51.atlassian.net/jira/software/projects/QSO/boards/1

