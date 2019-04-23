# Clock
[![Latest Stable Version](https://poser.pugx.org/jeckel/clock/v/stable)](https://packagist.org/packages/jeckel/clock)
[![Total Downloads](https://poser.pugx.org/jeckel/clock/downloads)](https://packagist.org/packages/jeckel/clock)
[![Build Status](https://travis-ci.org/jeckel/clock.svg?branch=master)](https://travis-ci.org/jeckel/clock)
[![codecov](https://codecov.io/gh/jeckel/clock/branch/master/graph/badge.svg)](https://codecov.io/gh/jeckel/clock)
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fjeckel%2Fclock.svg?type=shield)](https://app.fossa.io/projects/git%2Bgithub.com%2Fjeckel%2Fclock?ref=badge_shield)


A clock abstraction library for PHP which allow to mock system clock when testing

## Usage

In your code, always use the ClockInterface in your services when you need to access the current time. After that, you just had to define which implementation to use according to your environement (real or fake for tests).

In some framework, It can be easier to use the factory to handle the switch and inject the require configuration.

### Symfony 4

With SF4 we use the internal DI system with the factory. The factory will get different parameters according to the current environment.

Configure DI with a factory in `config/services.yaml`:
```yaml
# config/services.yaml
    Jeckel\Clock\ClockInterface:
        factory: ['Jeckel\Clock\ClockFactory', getClock]
        arguments: ['%fake_clock%', '%fake_clock_file%']
```
Configure default parameters in `config/packages/parameters.yaml`:
```yaml
# config/packages/parameters.yaml
parameters:
    fake_clock: false
    fake_clock_file: '%kernel.project_dir%/var/clock'
```

And then configure parameters for **tests** environment in `config/packages/test/parameters.yaml`:
```yaml
# config/packages/test/parameters.yaml
parameters:
    fake_clock: true
```

## License
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fjeckel%2Fclock.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2Fjeckel%2Fclock?ref=badge_large)
