# PHPComplete
This is a repository where I store my PHP modules that I frequently use on other projects, such as database manager, login and register. The point isn't to make a working website.

## Ready modules
 - **Login** - Basic login script using sessions
 - **Register** - All in one Registration script to securely add new users
 
## Usage
Login & Register modules return an array that signals whether the operation failed or succeeded. This array will also return all errors in the event of failure.

Examples:
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
```
Array
(
    [success] => false
    [0] => That email address is already in use
    [1] => That username is not available
)
```
```
Array
(
    [success] => true
)
```

## Planned modules/features
 - **Login** - Adding remember me functionality
 - **Register** - Adding email confirmation support
 - **User management** - Basic api to elevate/delete/edit/list all users
 
