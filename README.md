# asia-yo
this is a small homework

使用的是symfony5, 並且使用docker-compose

### 必要環境
[Install Docker Compose](https://docs.docker.com/compose/install)

### 一鍵安裝
```
docker-compose up -d --build
```

### 跑測試碼
```
docker exec -it asia_yo_php php ./vendor/bin/phpunit
```

### 使用api demo(有介面的owo)
```
http://localhost/api/doc
```

### 使用api
```
curl 'http://localhost/api/rate'
example:
{"result":"ok","ret":{"TWD":{"TWD":1,"JPY":3.669,"USD":0.03281},"JPY":{"TWD":0.26956,"JPY":1,"USD":0.00885},"USD":{"TWD":30.444,"JPY":111.801,"USD":1}}}

curl -X 'POST' 'http://localhost/api/rate/exchange' -d 'from_currency=twd&to_currency=jpy&amount=2000'
example:
{"result":"ok","ret":{"from_currency":"TWD","to_currency":"JPY","rate":3.669,"amount":"7,338.00"}}%
```
