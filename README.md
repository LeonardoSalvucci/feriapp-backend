# Feriapp (backend)

The original idea of this project was when i must attend a booth on an expo and don't have good memory for people's face! Sorry.

Because of that i thought that i need an application to store some kind of a card info and, of course, take a picture.

But there's two more things that this app must have, share the info with my coworkers and have the ability to make multiple "groups" for every expo.

## Feriapp backend is made with:
* Laravel 6
* MySQL 
* Auth with JWT

## Also has a stack with docker and docker-compose for an easy deploy.
For Laravel has a custom Dockerfile for make the container as small as possible starting from php:7.3-fpm-alpine.

For nginx has a default.conf ready for php-fpm and the root with /app/public.

And for development purpose i add a phpmyadmin at docker-compose.

## Prerequisites
* Docker
* Docker-compose

## Make it alive
**Note:** *This steps could be automated and it's not necessary to make such a hard work but i'll do it just for sharing with anybody who wants to see how can be doing manually.*

For having this working you have to clone the project.
```git clone https://github.com/LeonardoSalvucci/feriapp-backend.git```

Get into the folder of laravel.
```cd feriback-backend/php-fpm/laravel```

And you have to create a .env file copy the .env.example
```cp .env.example .env```

And if you want to use testing must add this variables.
```
DB_HOST_TEST=mysql
DB_PORT_TEST=3306
DB_DATABASE_TEST=feriapp_testing
DB_USERNAME_TEST=root
DB_PASSWORD_TEST=password
```
Once this has been done, we can see docker's magic.
```docker-compose up -d --build```

Note that this command are creating ours containers and we must be sure that everything was find, at final we can see if everything is up using.
```docker-compose ps```

Last fews steps more (sorry if this is very long...)
We have to create secrets for laravel to work and migrate our tables. For that we must "enter" the php-fpm container and execute those commands there... so...
```docker-compose exec php-fpm ash``` 
and we are in.

Here we have to type
```
php artisan key:generate
php artisan jwt:secret
php migrate
php artisan migrate --database=mysql_testing
```
And we have our project working! Yeah!

We can test here if everything is working executing unit tests
```
vendor/bin/phpunit
```

## Routes available

```
/app # php artisan route:list
+--------+----------+------------------------------------+------+----------------------------------------------------------------+--------------+
| Domain | Method   | URI                                | Name | Action                                                         | Middleware   |
+--------+----------+------------------------------------+------+----------------------------------------------------------------+--------------+
|        | GET|HEAD | /                                  |      | Closure                                                        | web          |
|        | POST     | api/auth/login                     |      | App\Http\Controllers\Api\Auth\AuthController@login             | api          |
|        | POST     | api/auth/logout                    |      | App\Http\Controllers\Api\Auth\AuthController@logout            | api,auth:api |
|        | GET|HEAD | api/auth/me                        |      | App\Http\Controllers\Api\Auth\AuthController@me                | api,auth:api |
|        | POST     | api/auth/refresh                   |      | App\Http\Controllers\Api\Auth\AuthController@refresh           | api,auth:api |
|        | POST     | api/auth/register                  |      | App\Http\Controllers\Api\Auth\AuthController@register          | api          |
|        | POST     | api/contact/create                 |      | App\Http\Controllers\Api\Contacts\ContactsController@create    | api,auth:api |
|        | DELETE   | api/contact/{contact_id}/remove    |      | App\Http\Controllers\Api\Contacts\ContactsController@remove    | api,auth:api |
|        | POST     | api/contact/{contact_id}/share     |      | App\Http\Controllers\Api\Contacts\ContactsController@share     | api,auth:api |
|        | POST     | api/group/create                   |      | App\Http\Controllers\Api\Groups\GroupsController@create        | api,auth:api |
|        | POST     | api/group/{group_id}/addContact    |      | App\Http\Controllers\Api\Groups\GroupsController@addContact    | api,auth:api |
|        | POST     | api/group/{group_id}/addUser       |      | App\Http\Controllers\Api\Groups\GroupsController@addUser       | api,auth:api |
|        | DELETE   | api/group/{group_id}/remove        |      | App\Http\Controllers\Api\Groups\GroupsController@remove        | api,auth:api |
|        | DELETE   | api/group/{group_id}/removeContact |      | App\Http\Controllers\Api\Groups\GroupsController@removeContact | api,auth:api |
|        | POST     | api/group/{group_id}/removeUser    |      | App\Http\Controllers\Api\Groups\GroupsController@removeUser    | api,auth:api |
|        | GET|HEAD | api/user/getMyGroups               |      | App\Http\Controllers\Api\Users\UsersController@getMyGroups     | api,auth:api |
|        | GET|HEAD | api/user/getMyProfile              |      | App\Http\Controllers\Api\Users\UsersController@getMyProfile    | api,auth:api |
|        | GET|HEAD | api/user/{user_id}/getGroups       |      | App\Http\Controllers\Api\Users\UsersController@getGroups       | api,auth:api |
|        | GET|HEAD | api/user/{user_id}/getProfile      |      | App\Http\Controllers\Api\Users\UsersController@getProfile      | api,auth:api |
+--------+----------+------------------------------------+------+----------------------------------------------------------------+--------------+
```

## ToDo
* Frontend.
 It's comming soon!!! and made with Vue&Vuetify and with cordova for making mobile

* Documentation.
I just cannot make a docs for this API already but i think that in testing files are some kind of idea of how this works.