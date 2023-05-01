
## Prerequisites

- node >= 12.16.1
- npm >= 6.13.4
- php >= 7.3.0
- swoole >= 4.5.3
- hyperf >= 2.2
- vue >= 2.0
- element >= 2.15.3

## Setup

*MacOS

PHP >=7.3

# Install ext swoole

Download swoole-src-4.6.0.tar.gz

```
tar xvzf swoole-src-4.6.0.tar.gz    

cd swoole-src-4.6.0

phpize  

./configure --enable-openssl  --with-openssl-dir=/usr/local/etc/openssl@1.1

make

mkdir /usr/local/opt/php73-swoole

cp modules/swoole.so /usr/local/opt/php73-swoole

sudo vi /usr/local/etc/php/7.3/conf.d/ext-swoole.ini


```

Add lines:

```
extension=/usr/local/opt/php73-swoole/swoole.so
swoole.use_shortname='Off'
```

Check module swoole

```
php -m
```

# Run hyperf-backend

```
php ./bin/hyperf.php migrate    

php ./bin/hyperf.php init:data_seeder

php ./bin/hyperf.php init

php ./bin/hyperf.php start
```

# Run hyperf-frontend

npm run dev


# Screen shot

![Alt text](https://github.com/dearvn/hyperf-dev/raw/main/s1.png?raw=true "s1")

![Alt text](https://github.com/dearvn/hyperf-dev/raw/main/s2.png?raw=true "s2")

![Alt text](https://github.com/dearvn/hyperf-dev/raw/main/s3.png?raw=true "s3")
