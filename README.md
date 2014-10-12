maybe-monad
===========

Simple monad library for PHP. Makes your life easier by making your code safer.

# Principle
When accessing values, variables, properties, calling methods or even chaining methods you usually don't know if subject of operation is null or not. That's is where my library comes. What you need to do is just wrap your subject around with class called `Maybe` and at the endpoint call method `value()`.

# Installation
Composer:

```
{
    "require": {
        "pirminis/maybe-monad": "~1.0"
    },
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/pirminis/maybe-monad.git"
        }
    ]
}
```

# Examples
Dont be a jerk and check `example.php`, it is simple and easy to understand.

# Tests
Use phpunit in project folder:
```
phpunit tests
```

# Bugs?
I `$maybeCare = new Maybe('that much'); echo $maybeCare->val('not that much');`
