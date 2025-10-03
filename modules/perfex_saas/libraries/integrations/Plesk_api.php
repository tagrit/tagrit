<?php

defined('BASEPATH') or exit('No direct script access allowed');

use \PleskX\Api\Client;

@ini_set('memory_limit', '256M');
@ini_set('max_execution_time', 60 * 5);

/**
 * Plesk API Integration Class
 */
class Plesk_api
{
    public $host;
    public $domain;
    public $client;
    public $webspace;
    public $databaseServer;
    public $dbprefix;

    public function __construct()
    {
    }

    /**
     * Initialize Plesk API
     *
     * @param string $host
     * @param string $domain
     * @param string $username
     * @param string $password
     * @param string $dbprefix
     * @return $this
     * @throws \Exception
     */
    public function init($host, $domain, $username, $password = '', $dbprefix = '')
    {
        if ($this->client && !empty($this->webspace->id) && !empty($this->databaseServer->type)) return $this;

        $this->host = $host;
        $this->domain = $domain;
        $this->dbprefix = $dbprefix;

        $client = new Client($host);
        if (empty($password))
            $client->setSecretKey($username);
        else
            $client->setCredentials($username, $password);

        $this->webspace = $client->webspace()->get('name', $this->domain);

        try {
            $databaseServers = $client->databaseServer()->getAll();
            foreach ($databaseServers as $databaseServer) {
                if ($databaseServer->type == 'mysql') {
                    $this->databaseServer = $databaseServer;
                }
            }
        } catch (\Throwable $th) {
            if (stripos($th->getMessage(), 'permission') !== false) {
                // Assume mysql default for case of cloud provisioned plesk where access to database server is disabled.
                $this->databaseServer = (object)['type' => 'mysql', 'port' => 3306, 'host' => 'localhost', 'id' => ''];
            } else {
                throw $th;
            }
        }
        if (!$this->databaseServer) throw new \Exception("Mysql DatabaseSever not found on plesk", 1);

        $this->client = $client;

        return $this;
    }

    /**
     * Add prefix to text if present
     *
     * @param string $text
     * @return string
     */
    public function addPrefix($text)
    {
        if (empty($this->dbprefix)) return $text;

        $text = str_starts_with($text, $this->dbprefix) ? $text : $this->dbprefix . $text;
        return $text;
    }

    /**
     * Create subdomain in Plesk
     *
     * @param string $name
     * @param string $www_root
     * @return mixed
     */
    public function createSubdomain(string $name, string $www_root = 'httpdocs')
    {
        return $this->client->subdomain()->create([
            'parent' => $this->webspace->name,
            'name' => $name,
            'property' => [
                'www_root' => $www_root,
                'php' => true,
            ],
        ]);
    }

    /**
     * Delete subdomain from Plesk
     *
     * @param string $name
     * @return mixed
     */
    public function deleteSubdomain(string $name)
    {
        $name = str_ends_with($name, $this->webspace->name) ? $name : $name . '.' . $this->webspace->name;
        return $this->client->subdomain()->delete('name', $name);
    }

    /**
     * Create site alias in Plesk
     *
     * @param string $name
     * @param array $properties
     * @return mixed
     */
    public function createSiteAlias($name, array $properties = [])
    {
        $properties = array_merge([
            'name' => $name,
            'site-id' => $this->webspace->id,
            'manage-dns' => 0,
        ], $properties);

        $prefs = ['mail' => false, 'seo-redirect' => false];

        return $this->client->siteAlias()->create($properties, $prefs);
    }

    /**
     * Delete site alias
     *
     * @param string $alias i.e demo.domain.com
     * @return mixed
     */
    public function deleteSiteAlias(string $alias)
    {
        return $this->client->siteAlias()->delete('name', $alias);
    }

    /**
     * Create database
     *
     * @param string $name The name of the database
     * @return mixed
     */
    public function createDatabase(string $name)
    {
        $payload = [
            'webspace-id' => $this->webspace->id,
            'name' => $name,
            'type' => $this->databaseServer->type,
        ];

        if (!empty($this->databaseServer->id)) {
            $payload['db-server-id'] = $this->databaseServer->id; // Optional'
        }

        return $this->client->database()->create($payload);
    }

    /**
     * Get a database details by name
     *
     * @param string $name
     * @return object|null Object when resolved otherwise null
     */
    public function getDatabase(string $name)
    {
        $databases = $this->client->database()->getAll('webspace-name', $this->webspace->name);
        foreach ($databases as $db) {
            if ($db->name === $name) return $db;
        }
        return null;
    }

    /**
     * Delete a database by name
     *
     * @param string $name
     * @return void
     */
    public function deleteDatabase(string $name)
    {
        $database = $this->getDatabase($name);
        if (!$database) return false;

        return $this->client->database()->delete('id', $database->id);
    }

    /**
     * Create a database and a user
     *
     * @param string $db_user
     * @param string $db_password
     * @param string $db_name
     * @return mixed
     */
    public function createDatabaseWithUser(string $db_user, string $db_password, string $db_name)
    {
        $database = $this->createDatabase($db_name);
        return $this->createDatabaseUser($database, $db_user, $db_password);
    }

    /**
     * Create a database user
     *
     * @param object $database
     * @param string $name
     * @param string $password
     * @return mixed
     */
    public function createDatabaseUser(object $database, string $name, string $password)
    {
        return $this->client->database()->createUser([
            'db-id' => $database->id,
            'login' => $name,
            'password' => $password,
        ]);
    }

    /**
     * Delete a database user by database ID
     *
     * @param mixed $db_id
     * @return mixed
     */
    public function deleteDatabaseUser($db_id)
    {
        return $this->client->database()->deleteUser('db-id', $db_id);
    }
}
