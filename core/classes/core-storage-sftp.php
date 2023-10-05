<?php

namespace MeshMVC;

class SFTP extends \MeshMVC\Storage {

    private $link, $username, $host, $port;

    public function __construct($username, $host, $port = 22) {
        $this->username = $username;
        $this->host = $host;
        $this->port = $port;

        return $this;
    }

    private function connect() {
        $this->link = ssh2_connect($this->host, $this->port);
        if (!$this->link) {
            throw New \Exception('SFTP: Failed to connect to ' . $this->host . ' at port' . $this->port);
        }
    }

    public function connectByPassword(#[\SensitiveParameter]$password) {
        @$this->connect();
        if (!ssh2_auth_password($this->link, $this->username, $password)) {
            throw New \Exception('SFTP: Failed to authenticate to ' . $this->host . ' at port' . $this->port. ' with user '.$this->username);
        }

        return $this;
    }

    public function connectByKeys(#[\SensitiveParameter] $public, #[\SensitiveParameter] $private, #[\SensitiveParameter] $secret) {
        @$this->connect();
        if (!ssh2_auth_pubkey_file($this->link, $this->username, $public, $private, $secret)) {
            throw New \Exception('SFTP: Failed to authenticate to ' . $this->host . ' at port' . $this->port. ' with user '.$this->username);
        }
        return $this;
    }

    public function disconnect() {
        ssh2_disconnect($this->link);
        return $this;
    }

    public function store($localFile, $remoteFile) {
        if (!ssh2_scp_send($this->link, $localFile, $remoteFile)) {
            throw New \Exception('Failed to upload the file');
        }
        return $this;
    }
    public function retrieve($remote, $local) {
        if (!ssh2_scp_recv($this->link, $remote, $local)) {
            die('Failed to download the file');
        }
        return $this;
    }

    public function exec($command) {
        if (!ssh2_exec($this->link, $command)) {
            die('Failed to download the file');
        }
        return $this;
    }

}
