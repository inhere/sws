# Sws 

```
     _____      _____
    / __\ \ /\ / / __|
    \__ \\ V  V /\__ \
    |___/ \_/\_/ |___/ Powered by swoole
```

a webSocket application by php swoole.

## TODO

- error catch
- db orm, data model
- rpc data pack protocol: thirft and protobuf

## project

- **github** https://github.com/inhere/sws.git

## extra

### gRpc

- [github](https://github.com/grpc/grpc)
- [grpc php](https://github.com/grpc/grpc/tree/master/src/php)
- [doc](https://grpc.io/docs/)

```text
$ [sudo] pecl install grpc
$ [sudo] pecl install protobuf

composer require grpc/grpc
composer require google/protobuf
```

### thrift

- [github](https://github.com/apache/thrift)
- [doc](http://thrift.apache.org/tutorial/php)

install: 

see http://thrift.apache.org/docs/install/

php lib:

```text
composer require apache/thrift
```

### PSR

http://www.php-fig.org/psr/

#### ACCEPTED

NUM |	TITLE
-------|-------
psr-3 |	Logger Interface
psr-6 |	Caching Interface
psr-7 |	HTTP Message Interface
psr-11 |	Container Interface
psr-16 |	Simple Cache

#### DRAFT

## license

MIT
