<?php

namespace BlaubandOneClickSystem\Services\System\Local;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOMySql\Driver;

class DBConnectionService
{
    /**
     * @var \Enlight_Components_Snippet_Manager
     */
    private $snippets;

    public function __construct(\Enlight_Components_Snippet_Manager $snippets)
    {
        $this->snippets = $snippets;
    }

    public function createConnection($dbHost, $dbUser, $dbPass){
        try{
            return new Connection(['host' => $dbHost, 'user' => $dbUser, 'password' => $dbPass], new Driver());
        }catch (\Exception $e){
            throw new \SystemDBException(
                $this->snippets->getNamespace('blauband/ocs')->get('unableToConnect')
            );
        }
    }
}