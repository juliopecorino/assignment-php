CI checks: 

[![PHPStan + Psalm + Doctrine validation](https://github.com/juliopecorino/assignment-php/actions/workflows/php_analysis.yml/badge.svg)](https://github.com/juliopecorino/assignment-php/actions/workflows/php_analysis.yml) 

Coverage:
[![codecov](https://codecov.io/gh/juliopecorino/assignment-php/branch/master/graph/badge.svg?token=2HSAL6CXTJ)](https://codecov.io/gh/juliopecorino/assignment-php)


## Build the container:

 ```
docker-compose up -d
 ```
 
## App Setup:
 ```
docker-compose exec php bash -c "php composer.phar update"
docker-compose exec php bash -c "php bin/console cache:clear"
docker-compose exec php bash -c "php bin/console assets:install"
docker-compose exec php bash -c "php bin/console lexik:jwt:generate-keypair"

docker-compose exec php bash -c "php bin/console doctrine:database:drop --force"
docker-compose exec php bash -c "php bin/console doctrine:database:create"
 ```
## Tests:
 ```
docker-compose exec php bin/phpunit
 ```
 
## Access:

Visit http://127.0.0.1:80/api to see the API documentation.

You can ask for a token: to the URL `/api/authentication_token`

Read + Write access:
```
login: admin@example.com
pass: admin
```

Reader access:
```
login: reader@example.com
pass: reader
```

Example:

 ```
curl -X 'POST' \
  'http://127.0.0.1:80/api/authentication_token' \
  -H 'accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{
  "email": "admin@example.com",
  "password": "admin"
}'
 ```
 
This will return a token that will be later use to access the API using `Bearer MYTOKEN`
 ```
{
    "token": "MYTOKEN"
}
 ```
 
You can also use the UI to ask for the token. Click in `Try it out` then `Execute`:

![login](https://user-images.githubusercontent.com/90846983/134195123-804c8631-95a0-41db-9997-627aece72998.png)

Once you have the token, you can use it by clicking in the `Authorize` button:

![auth](https://user-images.githubusercontent.com/90846983/134195294-fef9a896-693e-4bdf-b90b-2d65c564973f.png)

And add the token like this: `Bearer MYTOKEN`

![token](https://user-images.githubusercontent.com/90846983/134197465-399c3595-38ed-4872-997e-a6ed5b08d0a5.png)


# Lokalise PHP homework
Hello and welcome!

Your task is to create a simple REST API for a mini Translation Management System (TMS) plus the underlying MySQL database structure. You will have a Dockerized development environment as well as a couple of tools provided to get you started, however, you have full creative freedom in choosing the way you want to complete this task.

**Please read the description carefully.**

## Description!

The goal of our little TMS is to allow multiple different `Translation` texts in different `Languages` to be linked to a single `Key`. For example, you can have a `Key` with the name `main.title` with `Hello world` and `Hallo Welt` translations for English and German respectively.

In order to perform requests to the API, an authentication token must be provided. A token can have either `read` or `read/write` access.

### Entities
You would need to create the following entities (feel free to add or modify them if you think it would benefit the final result):
#### Language
- Has a name
- Has an ISO code
- Can be LTR (Left To Right by default) or RTL

#### Translation
- Has a text value
- Has a language associated with it
- Belongs to a `Key`

#### Key
- Has a unique name
- Has one translation per Language at all times

#### Token
- Is unique
- Can have `read` or `read/write` access

### API Functionality
- API token authentication
- List available languages
- Manage `Keys`
    - List
    - Retrieve
    - Create
    - Rename
    - Delete
- Manage `Key` `Translations`
    - Ability to update `Translation` value for any language for a given key
- Export all `Keys` and their `Translations` in `.json` or `.yml` format as a `zip` archive
    - `.json` export should have one file per language (e.g. `[language-iso].json`) with the following format:
        ```
        {
            <key.name>: <translation.value>,
            ...
        }
    - `.yaml` export should contain all languages in a single `translations.yaml` file with the following format:
        ```
        <language.iso>:
            <key.name>: <translation.value>
            ...
        <language2.iso>:
            <key.name>: <translation.value>
            ...
### Submitting the task
- This repository should be forked and the final result should be submitted as a link to that fork
- SQL of the final database structure/data should be included
- If you change the development environment, please make sure that you include some instructions on how to run the final result

### Notes
- You can use any PHP framework that you are comfortable with (though using Symfony will be considered a plus)
- The quality and structure of the code is important, so use the best coding practices that you know
- Unit tests will be considered a plus

## Included development environment
You can find a simple Dockerized development environment included with this assignment. Please follow the steps below to set it up (or use your own environment, however, please make sure to include instructions on how to run it).

#### 1. Install Docker CE & Docker Compose
+ [Mac](https://docs.docker.com/docker-for-mac/)
+ [Debian](https://docs.docker.com/engine/installation/linux/docker-ce/debian/)
+ [Docker Compose](https://docs.docker.com/compose/install/)

Bonus: [Linux Post-install](https://docs.docker.com/engine/installation/linux/linux-postinstall/)

#### 2. Build the containers and bring them online
- Make sure that Docker is running
- Navigate to the project folder using a Terminal
- Run the `docker-compose up -d` command to bring the containers online

#### 3. Running `composer install`
After making modifications to `composer.json` you can run the `docker-compose exec php bash -c "php composer.phar install"` command to perform the installation

#### 4. Accessing the MySQL database
The environment includes the `phpmyadmin` tool. In order to access it, use the http://127.0.0.1:8080 URL. Use `mysql` as the Server name, `root` as the User and you can find the password inside the `docker-compose.yml` file.

#### 5. Running the application
Use the http://127.0.0.1 URL to access your application
