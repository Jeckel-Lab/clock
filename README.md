[![Latest Stable Version](https://poser.pugx.org/jeckel-lab/clock/v/stable)](https://packagist.org/packages/jeckel-lab/clock)
[![Total Downloads](https://poser.pugx.org/jeckel-lab/clock/downloads)](https://packagist.org/packages/jeckel-lab/clock)
[![Build Status](https://github.com/jeckel-lab/clock/workflows/validate/badge.svg)](https://github.com/Jeckel-Lab/clock/actions)
[![codecov](https://codecov.io/gh/jeckel-lab/clock/branch/master/graph/badge.svg)](https://codecov.io/gh/jeckel-lab/clock)

# Clock

A clock abstraction library for PHP which allow to mock system clock when testing

# Installation

```bash
composer require jeckel-lab/clock
```

## Usage

In your code, always use the `JeckelLab\Contract\Infrastructure\System\Clock` interface in your services when you need to access the current time. After that, you just have to define which implementation to use according to your environment (real or fake for tests).

In some framework, it can be easier to use the factory to handle the switch and inject the required configuration.

### Different fake clock

There are 2 types of fake clock available:
- **`frozen`**: This clock with always return the same value everytime we call it
- **`faked`**: This one will return an incremented time (like a real one) but initiated at the beginning at the script with a defined date time. It's useful to continue to keep track of long process.

Fake clock can both be initiate in 2 different ways:
- by passing the value of the initial time (`fake_time_init` value in configuration), This option is useful when you want to always use the same time, or initialize it with an env variable
- by passing the path to a file where it will read the value of time (`fake_time_path`). This option is useful when you want to control the time of each of your process when running different tests cases.

## Use with Symfony 4 and 5

With SF4 and SF5 we use the internal DI system with the factory. The factory will get different parameters according to the current environment.

Configure DI with a factory in `config/services.yaml`:
```yaml
# config/services.yaml
    JeckelLab\Contract\Infrastructure\System\Clock:
        factory: ['JeckelLab\Clock\Factory\ClockFactory', getClock]
        arguments: ['%clock%']
```
Configure default parameters in `config/packages/parameters.yaml`:
```yaml
# config/packages/parameters.yaml
parameters:
    clock:
        mode: real
        timezone: Europe/Paris
```

And then configure parameters for **tests** environment in `config/packages/test/parameters.yaml`:
```yaml
# config/packages/test/parameters.yaml
parameters:
    clock:
        mode: faked
        fake_time_init: '2020-12-11 14:00:00'
```
or
```yaml
# config/packages/test/parameters.yaml
parameters:
    clock:
        mode: frozen
        fake_time_path: '%kernel.project_dir%/var/fake_time.txt'
```

## Test with Codeception

To be able to change current date in your Codeception tests, you first need to configure your fake clock to use the `fake_time_path` file as a time source.

Next, configure codeception with the provided helper:

```yaml
# codeception.yaml

# ...
modules:
    config:
        \JeckelLab\Clock\CodeceptionHelper\Clock:
            fake_time_path: 'var/test/fake_clock'   # Required: path where the fake time should be provided to your project
            date_format: 'Y/m/d'  # Optional, date format for date value defined in your tests (default: Y/m/d)
            time_format: 'H:i:s'  # Optional, time format for time value defined in your tests (default: H:i:s)
```

Enable the helper in your suite:
```yaml
# acceptance.suite.yml

actor: AcceptanceTester
modules:
    enabled:
        - \JeckelLab\Clock\CodeceptionHelper\Clock
```

Now you can set the fake time in your tests:

**in BDD:**
```gherkin
Feature: A feature description

  Scenario: A scenario description
    Given current date is "2021/03/26" and time is "08:35:00"
```

**in other tests:**
```php
/** @var \Codeception\Actor $i */
$I->haveCurrentDateAndTime('2021/03/26', '08:35:00');

// or
$I->haveCurrentDateTime(DateTime::createFromFormat("Y/m/d H:i", "2021/03/26 08:35"));
```
