<?php

namespace Gerar;

class User
{
    function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param $name
     *
     * @return User
     */
    public static function named($name)
    {
        return new self($name);
    }

    /**
     * @return array
     */
    private static function getSudoerLogins()
    {
        $sudoers = Process::read('cat /etc/sudoers | grep -vP "^(Defaults|#|%|\s*$|root)" | awk {\'print $1\'}');
        $sudoers = explode("\n", $sudoers);

        return $sudoers;
    }

    /**
     * @return string
     */
    public function getHome()
    {
        return trim(Process::read("sudo -u {$this->name} echo \$HOME"));
    }

    /**
     * @return $this
     */
    public function shouldHaveSshKey()
    {
        if (ThisServer::isUbuntu() || ThisServer::isDebian()) {
            $key = $this->getPrivateKeyFilename();
            if (!File::named($key)->exists()) {
                Process::runAndCheckReturnCode(
                    "sudo -u {$this->name} ssh-keygen -t rsa -N '' -f " . escapeshellarg($key)
                );
            }

            return $this;
        }
        Gerar::notImplemented();
    }

    /**
     * @param string $to
     */
    public function mailPublicKeyOnce($to)
    {
        DoOnce::run(
            "mail-ssh-key-{$this->name}",
            function () use ($to) {
                Mail::sendSimple($to, "Public Key of {$this->name}", $this->getPublicKey());
            }
        );
    }

    /**
     * @return string
     */
    public function getPrivateKeyFilename()
    {
        return "{$this->getHome()}/.ssh/id_rsa";
    }

    /**
     * @return string
     */
    public function getPublicKeyFilename()
    {
        return "{$this->getHome()}/.ssh/id_rsa.pub";
    }

    /**
     * @return string
     */
    private function getPublicKey()
    {
        return File::named($this->getPublicKeyFilename())->read();
    }

    /**
     * @return $this
     */
    public function shouldBePresent()
    {
        if (ThisServer::isLinux()) {
            if (!$this->exists()) {
                $this->create();
            }
        }

        return $this;
    }

    /**
     * @return boolean
     */
    public function exists()
    {
        return Process::getReturnCode("id {$this->name} >/dev/null 2>&1") == 0;
    }

    public function create()
    {
        Process::runAndCheckReturnCode("useradd {$this->name} -m");
    }

    /**
     * @param string $filename
     *
     * @return $this
     */
    public function haveSshKeyFrom($filename)
    {
        $content = trim(File::named($filename)->read());
        $file    = $this->authorizedKeysFile();
        if (!$file->exists()) {
            $file->write($content);
            $file->chmod(0600);
        }

        if (!$file->contains($content)) {
            $file->append($content . "\n");
        }

        return $this;
    }

    /**
     * @return File
     */
    private function authorizedKeysFile()
    {
        return File::named($this->getHome() . '/.ssh/authorized_keys');
    }

    /**
     * @param string $groupName
     *
     * @return $this
     */
    public function shouldBeInGroup($groupName)
    {
        if (!$this->inGroup($groupName)) {
            $this->addToGroup($groupName);
        }

        return $this;
    }

    /**
     * @param string $groupName
     *
     * @return bool
     */
    public function inGroup($groupName)
    {
        $groups = $this->getGroups();

        return in_array($groupName, $groups);
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return explode(' ', Process::read("id -Gn {$this->name}"));
    }

    /**
     * @param string $groupName
     */
    private function addToGroup($groupName)
    {
        Process::runAndCheckReturnCode("usermod -aG {$groupName} {$this->name}");
    }

    /**
     * @param string $commands
     *
     * @return $this
     */
    public function shouldHaveNoPasswordSudoFor($commands)
    {
        if (is_string($commands)) {
            $commands = array($commands);
        }

        $sudoers    = File::named('/etc/sudoers');
        $definition = "{$this->name} ALL = NOPASSWD: " . join(', ', $commands) . "\n";

        $sudoers->shouldHaveLine($definition);

        return $this;
    }

    /**
     * @param string $commands
     *
     * @return $this
     */
    public function shouldHaveSudoFor($commands)
    {
        if (is_string($commands)) {
            $commands = array($commands);
        }

        $sudoers    = File::named('/etc/sudoers');
        $definition = "{$this->name} ALL = " . join(', ', $commands) . "\n";

        $sudoers->shouldHaveLine($definition);

        return $this;
    }

    /**
     * @return boolean
     *
     * @throws Exception
     */
    public static function shouldHaveOneSudoUserWithSshKey()
    {
        $sudoers = self::getSudoerLogins();
        if (intval($sudoers) == 0) {
            Console::log("/etc/sudoers must have one sudo user");
            throw new Exception();
        }

        foreach ($sudoers as $user) {
            if (User::named($user)->hasAuthorizedKeysFile()) {
                return true;
            }
            Console::log("At least one sudoer must have authorized_keys file");
            throw new Exception();
        }
    }

    /**
     * @return boolean
     */
    private function hasAuthorizedKeysFile()
    {
        return $this->authorizedKeysFile()->exists();
    }
}