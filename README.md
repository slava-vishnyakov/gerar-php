# PHP configuration management

... because I'm too lazy to remember Puppet or Chef syntax.

This is **an experiment** in configuration management. Open `example.php` if you will.

Currently only **Ubuntu** is implemented. Probably going to implement CentOS later.

# Installation (global)

All commands must be run as **root**

```sh
apt-get update
apt-get install php5-cli
wget -qO - http://git.io/FhkM4A | php
```

run same command to update Gerar

## Example run

```sh
gerar --set-hostname your-host-name.com
gerar some-file.php
```

## Examples

* Install RVM, Ruby 1.9.3, Passenger and create `passenger-add-host` command

Read the source at http://git.io/oVoLFg

```sh
wget -qO - http://git.io/oVoLFg | gerar
passenger-add-ruby-host test.com
```

You should now have directory to run your Passenger/Nginx Rails application.

## Other examples

```php
<?php namespace Gerar;

Hostname::change('vagrant.local');
```

### Run stuff on specific hosts

```php
Hostname::run('vagrant.local', function() {
    Console::log('Hello!');
});

Hostname::run('vagrant.local', function() {
    Ubuntu::fixLocales();
    Ubuntu::update();
});
```

### Package and service management

```php
Package::named('php5-cli mysql-server git imagemagick')
    ->shouldBeInstalled();

Service::named('apache2')
    ->shouldBeInstalled()
    ->shouldBeRunning()
    ->shouldBeRunningAtReboots();
```

### One-time tasks

```php
DoOnce::run('initializeServer', function() {
    Console::log('We have an ignition!');
});
```

### HTTP requests

```php
File::named('/var/www/index.html')->write('Bender is great!');

Http::request('http://localhost/')
    ->onSuccess(function($request, $response) {
        Console::log('My new code is "' . $response->getBody() . '"');
    })
    ->run();
```

### Port monitoring

```php
Port::number(810)
    ->ifNotResponding(function() {
        Console::log('Oops, port 810 is not responding, maybe we should start apache? Hint: won\'t help');
        Service::named('apache2')->start();

    });
```

### File management

```php
File::named('test.txt')->write(microtime(true));

File::named('test.txt')
    ->whenChanges(function() {
        Console::log('Hello, World appears!');
    })
    ->write('Hello, world!')
    ->replaceIfPresent('world', 'Gerar')
    ->replaceIfPresent(new Regexp('/He..o/'), 'Howdy');
```

### User management

```php
User::named('root')
    ->shouldHaveSshKey()
    ->mailPublicKeyOnce('user@super-server.com');

User::named('alex')
    ->shouldBePresent()
    ->haveSshKeyFrom('files/alex.ssh')
    ->shouldBeInGroup('sudo')
    ->shouldHaveNoPasswordSudoFor('/usr/sbin/service')
    ->shouldHaveSudoFor('/bin/vi');
```

### IPs

```php
Console::log('My external IP is ' . ThisServer::mainExternalIp());
```

### /etc/hosts

```php
EtcHosts::file()
    ->shouldResolve('rarestblog.com', '127.0.0.1')
    ->shouldNotResolve('rarestblog.com');
```

### SSH server

```php
Ssh::server()->securify();
```

What it does:

```php
User::shouldHaveOneSudoUserWithSshKey();
Ssh::server()
    ->shouldNotAllowRoot();
    ->shouldNotAllowPlainTextPasswords();
```

## Does not work yet

### Git projects

```php
Git::src('git@github:')
    ->shouldBeAt('/home/git')
    ->shouldBeOnBranch('origin/production');
```

### Cron

```php
Cron::named('artisan-cleanup')
    ->ofUser('root')->shouldRun()->daily()->run('php artisan cleanup');
```

### Nginx, Apache, serving rails and PHP

```php
Nginx::server()
    ->shouldBeInstalled()
    ->shouldProxy('http://rarestblog.com/update-328719', 'http://localhost:8000/')
    ->shouldProxy('http://rarestblog.com/', 'http://localhost:9000/')
    ->shouldServePhpFpm('rarestblog.com', '/home/user/php/myApp/public')
    ->shouldServePhpFpm('rarestblog.com', '/home/user/php/myApp/public/index.php');

NginxPassenger::server()
    ->shouldBeInstalled()
    ->shouldServeRailsApp('/home/user/rails/my_app/public');

Apache::server()
    ->shouldServePhp('rarestblog.com', '/home/user/php/myApp/public');
```

### Git webhooks and simple apps

```php
WebApp('127.0.0.1', 8000)
    ->url('/update-182739')->shouldRun('cd /home/user && php update.php');
```

### Testing

```php
SshHost('backup')
    ->shouldBeAccessibleForUser('user');
```

### Mysql

```php
$password = RandomPassword::named('rarestblog-db-password');

MySQL::server()
    ->shouldHaveDatabase('rarestblog')
    ->shouldHaveUser('rarestblog', '127.0.0.1', $password)
    ->userShouldHaveFullAccessTo('rarestblog', '127.0.0.1', 'rarestblog.*');

File::named('/var/www/database.php')
    ->fromTemplate('files/database.php', array('password', $password));
```

### Firewall

```php
Firewall::rules()
    ->shouldAllowToPort(22, '234.234.234.234')
    ->shouldNotAllowAnyoneElseToPort(22);

Gerar()->shouldUpdateItselfEvery(15, 'minutes');
```
