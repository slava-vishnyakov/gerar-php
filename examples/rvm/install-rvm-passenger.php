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
        if (!strstr(Process::runInBash("rvm; true"), 'rvm.io')) {
            Package::named('curl')->shouldBeInstalled();

            User::named('rails')
                ->shouldBePresent();

            Console::log("Installing RVM for user rails");
            Process::runInBash('sudo -u rails bash -lc "\curl -sL https://get.rvm.io | bash"');
        }
        return $this;
    }

    public function rubyShouldBeInstalled($version)
    {
        if (!strstr(Process::runInBash('ruby -v'), $version)) {
            Console::log("Installing Ruby $version");
            Process::runInBash("sudo -u rails 'rvm --install $version'");
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

        if (!strstr(Process::runInBash("passenger; true"), "Passenger Standalone")) {
            Console::log("Installing passenger gem");
            Process::runInBash('sudo -u rails "gem install passenger --no-ri --no-rdoc"');
        }
        return $this;
    }

    public function nginxModuleShouldBeInstalled()
    {
        if (!file_exists('/opt/nginx')) {
            Console::log("Installing passenger-nginx");
            Process::runInBash('sudo -u rails "rvmsudo passenger-install-nginx-module --auto --auto-download --prefix=/opt/nginx"');
        }
        return $this;
    }
}

Ubuntu::fixLocales();

Rvm::manager()
    ->shouldBeInstalled()
    ->rubyShouldBeInstalled('1.9.3');

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
