# simplexmlparser

```
git ...

cd backend (from root repo)

cp .env.example .env


cd app (from root repo)

cp .env.example .env
cp docker-compose.example.yml docker-compose.yml


cd app (from root repo)

docker-compose up --build -d

docker-compose ps

wait for redis container up...

docker exec -it simplexmlparser_backend bash
php artisan queue:work

in another console tab:

cd app (from root repo)
docker exec -it simplexmlparser_backend bash
composer install
php artisan migrate:fresh --seed


maybe:
cd backend (from root repo)
npm run build

```

# urls:

```
Adminer database PostgreSQL

http://127.0.0.1:8983/?pgsql=db&username=root&db=simplexmlparser_db
password: root

Redis Commander (redis viewer)
http://127.0.0.1:8383/

Dashboard (Upload form && echo log)
http://127.0.0.1:8083/dashboard

List sorting rows
http://127.0.0.1:8083/list

RabbitMQ, if u need:
127.0.0.1:15672
quest quest
```