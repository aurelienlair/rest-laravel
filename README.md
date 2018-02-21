# Rest API usage on a Laravel 5 simple project 

the scope of the current project is to show an implementation of
a REST (REpresentational State Transfer) api with actors.

### Prerequisites
the following project assumes you have on your machine:
- apache2
- PHP => 7
- Sqlite >= 3
- vim >= 7

### Installing
```
# Paste the below content to the Apache config file
aurelien@linux:~$ /usr/bin/vim /etc/apache2/sites-available/restisthebest.conf

<VirtualHost *:80>
    ServerName www.restisthebest.com
    DocumentRoot "/var/www/rest-laravel/public"
    SetEnv APPLICATION_ENV development

    <Directory "/var/www/rest-laravel/public">
        Options FollowSymlinks
        AllowOverride None
        Allow from All

        <IfModule mod_rewrite.c>
            AllowOverride all
        </IfModule>
    </Directory>
    ErrorLog /var/log/apache2/restisthebest.error.log
    CustomLog /var/log/apache2/restisthebest.access.log combined
</VirtualHost>

aurelien@linux:~$ /bin/ln -s /etc/apache2/sites-available/restisthebest.conf
aurelien@linux:~$ /usr/sbin/service apache2 restart
```

Add this host www.restisthebest.com to /etc/hosts 
```
aurelien@linux:~$ echo "127.0.0.1 www.restisthebest.com" >> /etc/hosts
```

Check the file permissions
```
/bin/chown 777 /var/www/rest-laravel/database/database.sqlite
```

Launch the migrations
```
aurelien@linux:~$ cd /var/www/rest-laravel
aurelien@linux:~$ /usr/bin/php artisan migrate --database=sqlite
```

Enabling the project and restart apache
```
aurelien@linux:~$ /usr/sbin/a2ensite restisthebest.conf 
aurelien@linux:~$ /bin/chown www-data: /var/www/rest-laravel 
aurelien@linux:~$ /usr/sbin/service apache2 restart
```

### Testing the APIs

Now you are ready to try the set-up
```
aurelien@linux:~$ /usr/bin/curl -v "http://www.restisthebest.com/api/ping" \
-H "Accept: application/json"

> GET /api/ping HTTP/1.1
> User-Agent: /usr/bin/curl/7.55.1
> Host: www.restisthebest.com
> Accept: application/json
>
< HTTP/1.1 200 OK
< Date: Wed, 21 Feb 2018 15:20:13 GMT
< Cache-Control: no-cache, private
< X-RateLimit-Limit: 60
< X-RateLimit-Remaining: 59
< Content-Length: 6
< Connection: close
< Content-Type: application/json
<

"pong"
```

Let's create an actor
```
/usr/bin/curl -v "http://www.restisthebest.com/api/actors" \
-X POST \
-H "Accept: application/json" \
-d "firstname=al&lastname=pacino&country=US"

> POST /api/actors HTTP/1.1
> Host: www.restisthebest.com
> User-Agent: /usr/bin/curl/7.55.1
> Accept: application/json
> Content-Length: 39
> Content-Type: application/x-www-form-urlencoded
>
< HTTP/1.1 201 Created
< Date: Wed, 21 Feb 2018 15:33:40 GMT
< Cache-Control: no-cache, private
< Location: http://restisthebest.com/api/actors/69cf706d-23ed-4f20-acca-6e0d483b882b
< X-RateLimit-Limit: 60
< X-RateLimit-Remaining: 59
< Content-Length: 2
< Connection: close
< Content-Type: application/json
<
```

Let's get this new created resource
```
/usr/bin/curl -v "http://restisthebest.com/api/actors/69cf706d-23ed-4f20-acca-6e0d483b882b" \
-H "Accept: application/json" 

> GET /api/actors/69cf706d-23ed-4f20-acca-6e0d483b882b HTTP/1.1
> Host: www.restisthebest.com
> User-Agent: /usr/bin/curl/7.55.1
> Accept: application/json
> 
< HTTP/1.1 200 OK
< Date: Wed, 21 Feb 2018 15:35:28 GMT
< Cache-Control: no-cache, private
< X-RateLimit-Limit: 60
< X-RateLimit-Remaining: 59
< Content-Length: 99
< Connection: close
< Content-Type: application/json
< 
{"uuid":"69cf706d-23ed-4f20-acca-6e0d483b882b","firstname":"al","lastname":"pacino","country":"US"}
```

Let's get this new created resource
```
/usr/bin/curl -v "http://www.restisthebest.com/api/actors/69cf706d-23ed-4f20-acca-6e0d483b882b" \
> -X PUT \
> -H "Content-Type: application/x-www-form-urlencoded" \
> -H "Accept: application/json" \
> -d "firstname=anto james&lastname=pacino&country=US"

> PUT /api/actors/69cf706d-23ed-4f20-acca-6e0d483b882b HTTP/1.1
> Host: www.restisthebest.com
> User-Agent: /usr/bin/curl/7.55.1
> Accept: application/json
> Content-Length: 47
> Content-Type: application/x-www-form-urlencoded
>
< HTTP/1.1 200 OK
< Date: Wed, 21 Feb 2018 15:37:19 GMT
< Cache-Control: no-cache, private
< X-RateLimit-Limit: 60
< X-RateLimit-Remaining: 59
< Content-Length: 107
< Connection: close
< Content-Type: application/json
<
{"uuid":"69cf706d-23ed-4f20-acca-6e0d483b882b","firstname":"anto james","lastname":"pacino","country":"US"}
```

Some useful commands for [Sqlite](https://www.sqlite.org/index.html)
```
aurelien@linux:~$ /usr/bin/sqlite3
.open /var/www/rest-laravel/database/database.sqlite
.database
.tables
.schema actors
.headers on
.mode column
SELECT * FROM actors;
.quit
```
