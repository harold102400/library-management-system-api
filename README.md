# Hi 👋, here is a quick tour about my API REST <img src="https://user-images.githubusercontent.com/100805029/223028332-3b1a5d6b-3c19-4cce-8c6b-8826467047f7.png" width="60px" height="60px"></img>

## These are the programs you'll need to run this api on your computer <img src="https://media.tenor.com/_iHP2IIpDyUAAAAM/gato-papu.gif" width="50px" height="50px"></img>

- XAMPP
- PHP
- Postman

## INSTRUCTIONS <img src="https://static.wikia.nocookie.net/meme/images/1/16/Received_849786515507154.jpeg/revision/latest?cb=20200504124034" width="50px" height="50px"></img>

- Clone this repository to your local machine.
- Install XAMPP and start/activate the Apache and MySQL modules.
- Install Composer in your computer, you can get it from this website https://getcomposer.org/download/ and once it is installed in your computer, you need to run in your terminal inside the project the command **composer install** to install the project dependencies.
- In Postman you will use http://localhost/api/users, which is the endpoint for the routes.

## Database Migration with Phinx

For database functionality, we use a library called **Phinx** for migrations. To configure your database, locate the `phinx.php` file in the **config** folder under **src**, and modify any settings necessary before running the migration.

### Steps to Create and Apply Migrations

#### 1. Create a new migration:
  Once you're ready, run the following command to create a new migration:
    
    composer phinx-create name_of_your_table
    
This will generate a migration file in the format YYYYMMDDHHMMSS_my_new_migration.php, where the first 14 characters represent the current timestamp, down to the second.

#### 2. Define your table and columns:
  Inside this generated file, define the table and its columns as needed.

#### 3. Apply the migration:
  Once you're ready, run the following command to create a new migration:
    
    composer phinx-migrate
    
This will apply the migration and create the table in your database.

#### 4. Rollback the migration (if needed):
  If you encounter any issues and need to undo the migration, you can rollback the changes using:
    
    composer phinx-rollback
    
This will remove the table from your database.

    
## ABOUT THE PROJECT <img src="https://i.pinimg.com/736x/b0/4a/09/b04a095495f81d26da9801a1d58ec0c3.jpg" width="50px" height="50px"></img>

This is an API REST using the MVC pattern design with object-oriented programming, and the project uses Composer, which is a package management tool in PHP. To handle the routes, the Bramus Router library was installed via Composer.

## ENJOY! <img src="https://i.pinimg.com/736x/f6/37/3a/f6373a8a19ab5af63784e4d303d84581.jpg" width="50px" height="50px"></img>