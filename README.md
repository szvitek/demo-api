API Demo in Symfony
========

This is my Symfony API demo project created on September 30, 2016.

Install guide step-by-step:

1. Download the zip file or clone the repo:
    ```
    git clone https://github.com/szvitek/demo-api.git
    ```

2. Go to the downloaded demo-api directory and run:
    ```
    composer install
    ```

3. During the composer install you can configure parameters for your database (or you can do it later manually in the app/config/parameters.yml) 
    ```
    - database_host
    - database_port
    - database_name 
    - database_user 
    - database_password
    ```

4. Create the database:
    ```
    php bin/console doctrine:database:create
    ```

5. Create the schema:
    ```
    php bin/console doctrine:schema:create
    ```

6. Load fixtures:
    ```
    php bin/console doctrine:fixtures:load
    ```

6. Run the built in web server
    ```
    php bin/console server:start
    ```

Now the demo application is ready to use at your localhost

Web URIs:
```
* GET         /   
* GET         /movie
* GET         /movie/new
* POST        /movie/new
* GET         /movie/{id}
* GET         /movie/{id}/edit
* POST        /movie/{id}/edit
* DELETE      /movie/{id}
```

API URIs:
```
* GET         /api/movies
* GET         /api/movies/{id}
* POST        /api/movies
    required parameters:
    title:      string
    date:       YYYY-MM-DD
    genre:      string
    mainChar:   string
       
```


If you want to run the tests:

1. Create test database:
```
php bin/console doctrine:database:create --env=test
```

2. Create schema for the test database:
```
php bin/console doctrine:schema:create --env=test
```

3. Run the tests:
```
vendor/bin/phpunit tests/AppBundle/Controller/Api/MovieControllerTest.php
```