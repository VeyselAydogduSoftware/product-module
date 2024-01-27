
# Basic Product Module

It is a test project, a simple product module has been prepared.



## Api Securitiy

Add "ApiSecurityKey" : "1234567890" in the Header in each request so that you can send requests from Api.

## Fake User Login

automatic user login middleware is written in app/Http/Middleware/FakeLoginMiddleware.php file.
## Install project

Clone the project

```bash
  git clone https://github.com/VeyselAydogduSoftware/product-module.git
```

Navigate to the project directory

```bash
  cd product-module
```

Install the required packages

```bash
  composer update
```

Set up a database

```bash
  php artisan migrate
```

```bash
  php artisan db:seed
```

  
## Kullanılan Teknolojiler

Laravel 10x, Rest Api
  