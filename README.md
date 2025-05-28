# dotknock/ask-db

**A Laravel package to convert natural language queries into Eloquent queries with AI assistance.**

---

## Overview

`dotknock/ask-db` helps you build natural language interfaces for your Laravel application to query databases securely and dynamically.  
By defining your Eloquent models and their metadata (fields, types, descriptions), the package leverages OpenAI to translate user text queries into safe, validated Eloquent query code snippets.

---

## Features

- Define models with field metadata and descriptions for better AI understanding.
- Allow or disallow specific fields to enhance security and control.
- AI-powered natural language parsing into Laravel Eloquent query code.
- Prevents AI from generating queries on unauthorized models or fields.
- Supports field type validation and token-efficient interactions.
- Streamlines building conversational or chatbot-like database query interfaces.

---

## Installation

Install via Composer:

```bash
composer require dotknock/ask-db
```

Publish configuration (if applicable):

```bash
php artisan vendor:publish --provider="Dotknock\AskDb\AskDbServiceProvider"
```

Set your OpenAI API key in `.env`:

```env
OPENAI_API_KEY=your_openai_api_key_here
```

---

## Usage

### Step 1: Define your models and fields

Define the models you want AI to understand with field names, data types, and descriptions, for example:

```php
use Dotknock\AskDb\Model;

Model::define('Product', [
    'fields' => [
        'id' => 'integer',
        'name' => 'string',
        'category' => 'string',
        'price' => 'float',
        'description' => 'string',
    ],
    'description' => 'Products available in the store, including categories and prices.'
]);
```

### Step 2: Configure field permissions

You can allow or disallow certain fields for querying:

```php
Model::allow('Product', ['name', 'category', 'price']);
Model::disallow('Product', ['description']);
```

### Step 3: Process user natural language query

Use the package to convert a user query into Eloquent:

```php
use Dotknock\AskDb\QueryBuilder;

$query = QueryBuilder::fromUserInput('Show me products under 50 dollars in the cosmetics category');
echo $query;
```

Output:

```php
$products = Product::where('price', '<=', 50)
                   ->where('category', 'cosmetics')
                   ->get();
```

---

## Security

- Queries generated only for allowed models and fields.
- Strict filtering to prevent unauthorized data access.
- No direct execution of generated code; you control when and how to run it.

---

## Requirements

- PHP 8.1+
- Laravel 10.x+
- OpenAI PHP SDK (`openai-php/client`)
- Internet connection for API calls

---

## Contributing

Contributions, issues, and feature requests are welcome!  
Please open an issue or submit a pull request.

---

## License

MIT © Saad Majeed

---

## Contact

Saad Majeed – saadmajeed.dev@gmail.com  
[GitHub Repository](https://github.com/yourusername/ask-db)
