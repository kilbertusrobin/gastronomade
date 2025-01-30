# Gastronomade

## Prerequisites

Before installing Gastronomade, make sure you have:
- A SQL server (such as WampServer or XAMPP)
- PHP 
- Composer
- Symfony CLI

## Installation Steps

1. **Start SQL Server**
   - Launch your SQL server (WampServer or XAMPP)

2. **Get the Project**
   ```bash
   git clone [repository-url]
   cd gastronomade
   ```

3. **Install Dependencies**
   ```bash
   composer install
   ```

4. **Launch Symfony Server**
   ```bash
   symfony serve
   ```

5. **Set Up Database**
   ```bash
   # Create the database
   php bin/console doctrine:database:create

   # Create database schema
   php bin/console doctrine:schema:create

   # Load fixtures (sample data)
   php bin/console doctrine:fixtures:load
   ```

## Documentation

You can access the API documentation through:
- Web interface: `http://localhost:8000/api/doc`
- Bruno (integrated API client)

## Tests

- Tests were made by using PHPUnit
- You can run them by using : 'php bin/phpunit' in the Terminal


## Additional Information

- The application will run by default on `http://localhost:8000`
- Make sure all required ports are available before starting the server
- For any issues, please check the Symfony server logs or your SQL server logs
