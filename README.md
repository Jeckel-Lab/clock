# Clock

A clock abstraction library for PHP which allow to mock system clock when testing

# Installation

```bash
composer require jeckel-lab/clock
```

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
