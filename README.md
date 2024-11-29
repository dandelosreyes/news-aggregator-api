# News Aggregator

### Prerequisites
For Both Environments (Docker or Laravel Herd)
1. PHP 8.2 or higher
2. Composer
3. Postgresql

## Setup instructions
1. Clone the repository
2. Run `composer install`
3. Create a copy of the `.env.example` file and rename it to `.env`
4. Populate the `.env` file with the necessary configurations and run `php artisan key:generate`
5. After setting up the database, run `php artisan migrate` to migrate the database


### About the project
1. The software design for this project is based on Domain Driven Design (DDD). We are organizing the system into separate domains to address specific business needs, while also decoupling the code from the default framework. This approach allows us to avoid the complexity of modifying the entire codebase when switching frameworks, updating framework versions, or making major changes to the existing system.
2. I utilized spatie/laravel-data for managing data transfer objects (DTOs), which helps us handle the data exchanged between different layers of the application. This package enables us to establish a single source of truth for the data, ensuring consistency and preventing duplication across the application.
3. As for the documentation I used [dedoc/scramble](https://scramble.dedoc.co/) which is an OpenAPI documentation generato for laravel. It automatically generates OpenAPI Docs without require for us to manually write PHPDOc Annotations

### Notes:
1. Since we are using multiple providers for fetching news article. I purposely used the original link to the specific article to avoid any duplication of articles/data. 
2. On fetching of a specific article, we used our own custom id instead of the default id and slug of the article. This is to avoid any conflict with the original article id and slug.
3. The filtering of incoming data from the providers are not properly implemented since the instructions is lacking of what to filter and not to filter.