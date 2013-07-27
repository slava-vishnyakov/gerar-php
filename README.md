This is what I'm trying to achieve:

    <?php

    namespace Gerar;

    Hostname::run('vagrant.local', function() {
        Console::log('Hello!');
    });

    Hostname::run('vagrant.local', function() {
        Ubuntu::fixLocales();
    //    Ubuntu::update();

        Package::named("php5-cli mysql-server git imagemagick")
            ->shouldBeInstalled();

        Service::named('apache2')
            ->shouldBeInstalled()
            ->shouldBeRunning()
            ->shouldBeRunningAtReboots();

        User::named('root')
            ->shouldHaveSshKey()
            ->mailPublicKeyOnce('user@super-server.com');

        File::named('/var/www/index.html')->write("Bender is great!");

        $key = null;
        DoOnce::run('initializeServer2', function() {
            Console::log("We have an ignition!");
        });

        Http::request('http://localhost/')
            ->onSuccess(function($request, $response) {
                Console::log('My new code is "' . $response->getBody() . '"');
            })
            ->run();


        Port::number(810)
            ->ifNotResponding(function() {
                Console::log("Oops, port 810 is not responding, maybe we should start apache? Hint: won't help");
                Service::named('apache2')->start();

            });

    //    File::named('test.txt')->write(microtime(true));

        File::named('test.txt')
            ->whenChanges(function() {
                Console::log('Hello, World appears!');
            })
            ->write('Hello, world!')
            ->replaceIfPresent('world', 'Gerar')
            ->replaceIfPresent(new Regexp('/He..o/'), 'Howdy');

        User::named('alex')
            ->shouldBePresent()
            ->haveSshKeyFrom('files/alex.ssh')
            ->shouldBeInGroup('sudo')
            ->shouldHaveNoPasswordSudoFor('/usr/sbin/service')
            ->shouldHaveSudoFor('/bin/vi');

        Git::src('git@github:')
            ->shouldBeAt('/home/git')
            ->shouldBeOnBranch('origin/production');

        Console::log(ThisServer::mainExternalIp());

        EtcHosts::file()
            ->shouldResolve('rarestblog.com', '127.0.0.1')
            ->shouldNotResolve('rarestblog1.com');

        Cron::named('artisan-cleanup')
            ->shouldRun()->daily()->run('php artisan cleanup');


        Rvm::version('1.9')
            ->shouldBeInstalledFor('root');

        Nginx::server()
            ->shouldBeInstalled()
            ->shouldForward('http://rarestblog.com/update-328719', 'http://localhost:8000/')
            ->shouldForward('http://rarestblog.com/', 'http://localhost:9000/')
            ->shouldServePhpFpm('rarestblog.com', '/home/user/php/myApp/public');

        NginxPassenger::server()
            ->shouldBeInstalled()
            ->shouldServeRailsApp('/home/user/rails/my_app/public');

        Apache::server()
            ->shouldServePhp('rarestblog.com', '/home/user/php/myApp/public');

        WebApp('127.0.0.1', 8000)
            ->url('/update-182739')->shouldRun('cd /home/user && php update.php');

        SshHost('backup')
            ->shouldBeAccessibleForUser('user');

        $password = RandomPassword::named('rarestblog-db-password');

        MySQL::server()
            ->shouldHaveDatabase('rarestblog')
            ->shouldHaveUser('rarestblog', '127.0.0.1', $password)
            ->userShouldHaveFullAccessTo('rarestblog', '127.0.0.1', 'rarestblog.*');

        File::named('/var/www/database.php')
            ->fromTemplate('files/database.php', array('password', $password));

        Ssh::server()->securify();
            # check one sudo user present
            # ->shouldNotAllowRoot()
            # ->shouldNotAllowPlainTextPasswords();

        Firewall::rules()
            ->shouldAllowToPort(22, '234.234.234.234')
            ->shouldNotAllowAnyoneElseToPort(22);

    });

    Gerar()->shouldUpdateItselfEvery(15, 'minutes');
