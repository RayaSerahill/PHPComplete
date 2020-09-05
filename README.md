# PHPComplete
This is a repository where I store my PHP modules that I frequently use on other projects, such as database manager, login and register. The point isn't to make a working website.

## Ready modules
 - **Login** - Basic login script using sessions
 - **Register** - All in one Registration script to securely add new users
 
## Usage


### Database

#### connection
```
$pdo = (new SQLiteConnection())->connect();
```
This function will return a PDO database connection that you will need to use the other functions.

#### setup
```
(new SQLiteUtils($pdo))->createTables();
```
This function will create all necessary tables and events for the rest of the functions to work.

### Registration / Login
```
(new registerHandler($pdo))->newUser(string $email, string $username, string $password);
(new loginHandler($pdo))->login(string $email, string $password, bool $rememberme);
```
If everything goes well these functions will return Boolean ``True``.
In case of errors you will get an array listing all errors.
example:
```
Array
(
    [success] => false
    [0] => Invalid email address
    [1] => Username must include at least 1 letter and be 3-15 characters long
    [2] => Password should be min 8 characters and max 32 characters
    [3] => Password should contain at least one digit
    [4] => Password should contain at least one Capital Letter
    [5] => Password should contain at least one special character
    [6] => Passwords don't match
)
```

**Login** will also set ``$_SESSION["id"]`` to the user's id. This is how these scripts recognize a logged in user.


### Remember me
```
(new loginHandler($pdo))->rememberMe();
```
This function will check if visitor has a "remember me" cookie and validate it against the database so that remember me can function.

This will return a Boolean ``true`` is user gets logged in using this function and a boolean ``false`` when that doesn't happen.


## Planned modules/features
 - **Register** - Adding email confirmation support
 - **Password reset** - Basic ability to change password in case user forgets it.
 - **User management** - Basic api to elevate/delete/edit/list all users
 
