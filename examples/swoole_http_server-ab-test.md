
```text
root@php7-dev:/var/www# ab -c 100 -n 1000 localhost:8399/
This is ApacheBench, Version 2.3 <$Revision: 1604373 $>
Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
Licensed to The Apache Software Foundation, http://www.apache.org/

Benchmarking localhost (be patient)
Completed 100 requests
Completed 200 requests
Completed 300 requests
Completed 400 requests
Completed 500 requests
Completed 600 requests
Completed 700 requests
Completed 800 requests
Completed 900 requests
Completed 1000 requests
Finished 1000 requests


Server Software:        swoole-http-server
Server Hostname:        localhost
Server Port:            8399

Document Path:          /
Document Length:        13 bytes

Concurrency Level:      100
Time taken for tests:   0.138 seconds
Complete requests:      1000
Failed requests:        0
Total transferred:      161000 bytes
HTML transferred:       13000 bytes
Requests per second:    7257.68 [#/sec] (mean)
Time per request:       13.778 [ms] (mean)
Time per request:       0.138 [ms] (mean, across all concurrent requests)
Transfer rate:          1141.10 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        1    7   2.7      6      13
Processing:     0    7   1.7      7      12
Waiting:        0    4   2.0      4       8
Total:          8   13   2.8     13      20

Percentage of the requests served within a certain time (ms)
  50%     13
  66%     13
  75%     15
  80%     15
  90%     18
  95%     19
  98%     19
  99%     20
 100%     20 (longest request)
```