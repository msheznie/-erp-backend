# ERP Backend Development Setup

[![Laravel](https://img.shields.io/badge/Laravel-5.5-orange.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-%5E7.2-blue.svg)](https://php.net)

## Requirements

- PHP ^7.2
- PHP extensions: pdo, openssl, tokenizer, xml, mbstring, curl, ldap, intl, pdo_mysql, zip, gd, ctype, date, dom, exif, ftp, gettext, hash, libxml, pcre
- Composer - ^1.10.1
- Node - ^14
- MySQL - 8.0.22

## Server Configuration (PHP):

Ensure the following PHP configurations in your php.ini file:

- memory_limit: 128M.
- max_execution_time: 30
- max_file_uploads: 20
- post_max_size: 30M
- upload_max_filesize: 30M

## Installation

1. Clone the repository: `git clone git@github.com:pbsgears/Gears_BackEnd.git`
2. Navigate to the project directory: `cd Gears_BackEnd`
3. Install PHP dependencies: `composer install`
4. Copy the example environment file: `cp .env.example .env`
5. Generate an application key: `php artisan key:generate`
6. Generate an passport keys: `php artisan passport:keys`
7. Configure your database in the `.env` file
8. Run the development server: `php artisan serve`
9. Visit `http://localhost:8000` in your browser

## Application Configuration:

Env File Configuration:
Update the following configurations in your .env file:

- Database configuration
- Mail configuration
- Storage configuration
   
## Usage

This project serves as the backend for an ERP application and integrates with various third-party applications through its APIs. To get started, follow the installation steps mentioned above.

### Running the Application

To run the application, use the default Laravel Artisan serve command:

```bash
php artisan serve
```
By default, this will host the application at http://localhost:8000. If you need to change the port, make sure to update it not only in the Artisan serve command but also in the frontend of the application. The frontend uses a proxy to connect to the backend, and the port configuration must be consistent.

### Running through docker-composer

Pull the frontend and backend into same directory, and create a docker-compose.yml in the root directory with following content

```
version: "3.5"
services:
  backend:
    image: ososerp/ubuntu-php-7.2:latest
    container_name: erp-backend
    ports:
      - 8000:8080
    volumes:
      - ./Gears_BackEnd:/var/www/html:delegated
    working_dir: /var/www/html
    networks:
      - gears

  frontend:
    image: node:10.16.3-alpine
    container_name: erp-frontend
    working_dir: /app
    ports:
      - 4200:4200
    volumes:
      - ./Gears_FrontEnd:/app:delegated
      - /app/node_modules
    command: sh -c "npm install && npm start -- --host 0.0.0.0 --port 4200 --disable-host-check --proxy-config proxy.conf.docker.json"
    networks:
      - gears
    depends_on:
      - backend
    environment:
      - NODE_ENV=development
      - NODE_OPTIONS=--max-old-space-size=4096

networks:
  gears:
    driver: bridge
    name: gears
```

then docker-compose up

## Development Instructions

### Creating Models from Database Table

To create a model from a database table, you can use the following Artisan command provided by InfyOm. For example, let's create a model for the `users` table:

```bash
php artisan infyom:api_scaffold User --fromTable --tableName=users --skip=scaffold_controller,scaffold_requests,scaffold_routes,views
```
### Rolling Back Created Models
If you need to rollback the models created using InfyOm, you can use the following command. For instance, to rollback the User model created earlier, use:

```bash
php artisan infyom:rollback User api_scaffold
```
### Queue Server
To process jobs in the background, it's recommended to run the queue server. Since most of the jobs in this project use the sync driver, the queue is set to database. Start the queue server with the following command:

```bash
php artisan queue:work database --tries=3
```
Additionally, we have jobs specifically designed to run on a single worker, please run the following command:
```bash
php artisan queue:work database --tries=3 --queue=single
```
This ensures that the queue server handles jobs with the 'single' queue designation separately,

### Conventional Commits
To use conventional commits for better versioning and changelog management, install Node.js dependencies by running:
```bash
npm install
```
After making changes, commit them using:
```bash
npm run commit
```
Follow the interactive prompts to create a standardized commit message based on the [Conventional Commits specification](https://www.conventionalcommits.org/en/v1.0.0/)

### Pull Request Titles

When submitting a pull request, please ensure that the title follows the conventional commit format. The format should be as follows:

```markdown
feat(inventory|procurement|sourcing management|supplier management|custom report|sales & marketing|POS|treasury management|logistics|configuration|approval setup|navigation|group report|system admin|asset management|general ledger|accounts receivable|accounts payable): A short message [Jira issue no]
