# coachtech フリマ

ある企業が開発した独自のフリマアプリ

## 作成した目的

アイテムの出品と購入を行うためのフリマアプリを開発する

## 機能一覧

- 新規会員登録
- ログイン
- ログアウト
- プロフィール設定
- 商品の出品
- 商品の詳細表示
- 商品に対して『いいね』
- 商品に対してコメント送信
- 商品の検索
- 『いいね』した商品の表示（マイリスト）
- 出品した商品の一覧（マイページ）
- 商品の購入
- 配送先の変更
- 購入した商品の一覧（マイページ）

## 使用技術(実行環境)

- PHP8.3.0
- Laravel8.83.27
- MySQL8.0.26

## テーブル設計

![テーブル]table.drawio.png

## ER 図

![ER図](er.drawio.png)

## 環境構築

**Docker ビルド**

1. https://github.com/youhonami/furima.git
2. DockerDesktop アプリを立ち上げる
3. `docker-compose up -d --build`

> _Mac の M1・M2 チップの PC の場合、`no matching manifest for linux/arm64/v8 in the manifest list entries`のメッセージが表示されビルドができないことがあります。
> エラーが発生する場合は、docker-compose.yml ファイルの「mysql」内に「platform」の項目を追加で記載してください_

```bash
mysql:
    platform: linux/x86_64(この文追加)
    image: mysql:8.0.26
    environment:
```

**Laravel 環境構築**

1. `docker-compose exec php bash`
2. `composer install`
3. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.env ファイルを作成
4. .env に以下の環境変数を追加

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

5. アプリケーションキーの作成

```bash
php artisan key:generate
```

6. マイグレーションの実行

```bash
php artisan migrate
```

7. シーディングの実行

```bash
php artisan db:seed
```

## URL

- 開発環境：http://localhost
- phpMyAdmin:：http://localhost:8080/
