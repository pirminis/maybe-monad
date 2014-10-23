maybe-monad
===========

Simple `Maybe` monad library for `PHP`. Makes your life easier by making your code safer and by keeping your code stylish.

# What is this library about
Graceful handling of `null` values.

# Installation
Composer:

```
"require": {
    "pirminis/maybe-monad": "~1.2"
},
"autoload": {
    "files": ["vendor/pirminis/maybe-monad/lib/global.php"]
}
```

# Examples
Dont be a jerk and check `example.php`, it is simple and easy to understand:

```
php example.php
```

# Tests
Use phpunit in project folder:
```
phpunit tests
```

# Bugs?
`$how_much_I_care = \Maybe('that much'); echo $how_much_I_care->val('not that much');`
