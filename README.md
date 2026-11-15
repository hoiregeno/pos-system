# POS System

A simple, lightweight point of sale system built with PHP and MySQL.

## Features

- Manage products (add, edit, delete)
- Sell screen with a cart
- Save completed sales
- View sales history

## Tech stack

- PHP (mysqli)
- MySQL
- HTML and CSS
- XAMPP for local development

## Setup

1. Put the project folder in your XAMPP `htdocs` directory
2. Start Apache and MySQL in XAMPP
3. Create a database named `pos_db`
4. Copy `includes/db.example.php` to `includes/db.php` and fill in your database details
5. Open `http://localhost/pos` in your browser

## Project structure

```
pos/
├── index.php
├── sell.php
├── products.php
├── sales.php
├── includes/
│   ├── db.php
│   ├── header.php
│   └── footer.php
└── css/
    └── style.css
```

## Status

In development.
