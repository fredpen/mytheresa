# MYTHERESA

Documentation

## Installation

Note: PHP "^8.0.2" and composer is required to run this project on any machine.

Clone the repo locally:
Open a terminal or a git client and clone the project using the code below

```sh
git clone https://github.com/fredpen/mytheresa.git
```

Enter into the project directory

```sh
cd mytheresa
```

Install project dependencies:

```sh
composer install
```

Run test suite:

```sh
    ./vendor/bin/pest
```

Run the dev server (the output will give the address):

```sh
php artisan serve
```

You're ready to go! Visit http://127.0.0.1:8000 or the url generated from above in your browser


The Api created for the Test can be found here
All products: http://127.0.0.1:8000/products
Paginate: http://127.0.0.1:8000/products?page=1&per_page=5
Filter by Category: http://127.0.0.1:8000/products?category=Boots
Filter by price: http://127.0.0.1:8000/products?priceLessThan=70000

