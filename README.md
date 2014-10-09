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
        "pirminis/maybe-monad": "dev-master"
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

# Bugs?
I care $maybeCare = new Maybe(null); echo $maybeCare->value('that much');
