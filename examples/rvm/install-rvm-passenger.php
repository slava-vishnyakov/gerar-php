<?php namespace Gerar;

if (!ThisServer::isUbuntu()) {
    throw new Exception("Works only on Ubuntu yet");
}

class Rvm
{
    public static function manager()
    {
        return new self;
    }

    public function shouldBeInstalled()
    {
        User::named('rails')
            ->shouldBePresent();

        if (!strstr(Process::runInBashAs("rails", "rvm 2>/dev/null; true"), 'rvm.io')) {
            Package::named('curl')->shouldBeInstalled();

            Console::log("Installing RVM for user rails");
            Process::runInBash('curl -sL https://get.rvm.io | sudo -H -u rails bash');
        }
        return $this;
    }

    public function rubyShouldBeInstalled($version)
    {
        if (!strstr(Process::runInBashAs('rails', 'ruby1 -v 2>/dev/null; true'), $version)) {
            Package::named('g++ gcc make libc6-dev libreadline6-dev zlib1g-dev libssl-dev libyaml-dev libsqlite3-dev '.
            'sqlite3 autoconf libgdbm-dev libncurses5-dev automake libtool bison pkg-config libffi-dev')->shouldBeInstalled();
            Console::log("Installing Ruby $version");
            Process::runInBashAs('rails', "source ~/.profile; rvm install $version --autolibs=read-fail");
        }
        return $this;
    }
}

class Passenger
{
    public static function gem()
    {
        return new self;
    }

    public function shouldBeInstalled()
    {
        $hasSslLib = Package::named('libcurl4-openssl-dev')->isInstalled() ||
            Package::named('libcurl4-gnutls-dev')->isInstalled();

        if (!$hasSslLib) {
            Package::named('libcurl4-openssl-dev')->shouldBeInstalled();
        }

        if (!strstr(Process::runInBashAs('rails', "passenger 2>/dev/null; true"), "Passenger Standalone")) {
            Console::log("Installing passenger gem");
            Process::runInBashAs('rails', "source ~/.profile; gem install passenger --no-ri --no-rdoc");
        }
        return $this;
    }

    public function nginxModuleShouldBeInstalled()
    {
        if (!file_exists('/opt/nginx')) {
            Console::log("Installing passenger-nginx");

            File::named('/etc/sudoers')->shouldHaveLine('rails ALL = NOPASSWD: ALL');
            Process::runInBashAs('rails', 'rvmsudo_secure_path=1 rvmsudo passenger-install-nginx-module --auto --auto-download --prefix=/opt/nginx');
            File::named('/etc/sudoers')->replaceIfPresent('rails ALL = NOPASSWD: ALL', '');
        }
        return $this;
    }
}

Ubuntu::fixLocales();

Rvm::manager()
    ->shouldBeInstalled()
    ->rubyShouldBeInstalled('2.0');

Passenger::gem()
    ->shouldBeInstalled()
    ->nginxModuleShouldBeInstalled();

Service::named('apache2')->shouldBeRemoved();

Process::runInBash('wget https://raw.github.com/slava-vishnyakov/gerar-php/master/examples/rvm/files/nginx-init.d -O /etc/init.d/nginx');
Process::runInBash('chmod o+x /etc/init.d/nginx');
Process::runInBash('update-rc.d nginx defaults');

if(!File::named('/opt/nginx/conf/nginx.conf')->contains('include /opt/nginx/conf/rails-sites/*.conf;')) {
    File::named('/opt/nginx/conf/nginx.conf')->replaceIfPresent(
        "    server {",
        "    include /opt/nginx/conf/rails-sites/*.conf;\n\n    server {"
    );
}

Service::named('nginx')
    ->shouldBeRunning()
    ->shouldBeRunningAtReboots();

Process::runInBash('mkdir -p /opt/nginx/conf/rails-sites/');

Process::runInBash('wget https://raw.github.com/slava-vishnyakov/gerar-php/master/examples/rvm/files/.nginx-scripts -O /root/.nginx-scripts');

File::named('/root/.bashrc')->shouldHaveLine('source /root/.nginx-scripts');

Process::runInBash('source /root/.nginx-scripts');