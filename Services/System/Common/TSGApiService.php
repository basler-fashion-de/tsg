<?php

namespace BlaubandTSG\Services\System\Common;

use BlaubandTSG\Exceptions\SystemDBException;
use BlaubandTSG\Exceptions\SystemFileSystemException;
use BlaubandTSG\Exceptions\TokenException;
use Doctrine\DBAL\Connection;

class TSGApiService
{
    private $schema = 'http://';

    private $apiIp = '18.184.61.86:3361';

    private $dbApi = '/api/db';

    private $registerApi = '/api/register';

    private $deRegisterApi = '/api/de-register';

    private $tokenPath = null;

    /**
     * @var \Enlight_Components_Snippet_Manager
     */
    private $snippets;

    private $connectionService;

    /** @var Connection */
    private $shopConnection;

    public function __construct(
        \Enlight_Components_Snippet_Manager $snippets,
        DBConnectionService $connectionService,
        Connection $shopConnection,
        $tokenPath
    )
    {
        $this->snippets = $snippets;
        $this->connectionService = $connectionService;
        $this->shopConnection = $shopConnection;
        $this->tokenPath = $tokenPath;
    }

    public function createDatabase()
    {
        $token = $this->loadToken();

        try {
            $url = $this->schema . $this->apiIp . $this->dbApi;
            $data = array('token' => $token);

            $options = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);

            if ($result === FALSE) {
                throw new \Exception('');
            }

            $result = json_decode($result, true);
        } catch (\Exception $e) {
            throw new SystemDBException(
                $this->snippets->getNamespace('blauband/tsg')->get('unableToCreateRemoteDatabase')
            );
        }

        if ($result['success'] === false) {
            throw new SystemDBException(
                $this->snippets->getNamespace('blauband/tsg')->get(trim($result['message'], '__'))
            );
        }

        return $this->connectionService->createConnection($result['host'], $result['username'], $result['password'], $result['dbname'], $result['port']);
    }

    public function register()
    {
        try {
            $shop = $this->shopConnection->fetchArray('
                SELECT id, host 
                FROM s_core_shops 
                WHERE `default` = 1'
            );

            if (empty($shop) || empty($shop[0]) || empty($shop[1])) {
                throw new \Exception('Missing Parameter');
            }

            list($shopId, $domain) = $shop;

            $owner = $this->shopConnection->fetchArray('
                SELECT val.value 
                FROM s_core_config_values AS val 
                JOIN s_core_config_elements AS ele ON (ele.id = val.element_id) 
                WHERE val.shop_id = ? AND ele.name = "mail"',
                [$shopId]
            );

            if (empty($owner) || empty($owner[0])) {
                throw new \Exception('Missing Parameter');
            }

            $owner = unserialize($owner[0]);

            $url = $this->schema . $this->apiIp . $this->registerApi;
            $data = array('domain' => $domain, 'owner' => $owner);

            $options = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded",
                    'method' => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);

            if ($result === FALSE) {
                throw new \Exception('No Result');
            }

            $result = json_decode($result, true);

            if ($result['success']) {
                $this->saveToken($result['token']);
            } else {
                throw new \Exception('Error');
            }
        } catch (\Exception $e) {
            if($e instanceof SystemFileSystemException){
                throw $e;
            }

            // Alle Errors abfangen und ohne genauere Beschreibung ausgeben.
            throw new SystemDBException(
                $this->snippets->getNamespace('blauband/tsg')->get('unableToRegisterToApi')
            );


        }

        return true;
    }

    public function deRegister()
    {
        try {
            $token = $this->loadToken();
        } catch (\Exception $e) {
            //Kein Token, nichts zum de-registrieren
            return;
        }

        try {
            $url = $this->schema . $this->apiIp . $this->deRegisterApi;
            $data = array('token' => $token);

            $options = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded",
                    'method' => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);

            if ($result === FALSE) {
                throw new \Exception($url);
            }

            if ($result['success']) {
                $this->deleteToken();
            } else {
                throw new \Exception('');
            }
        } catch (\Exception $e) {
            throw new SystemDBException(
                $this->snippets->getNamespace('blauband/tsg')->get('unableToCreateRemoteDatabase')
            );
        }

        return true;
    }

    private function loadToken()
    {
        return 'no-token';
        //Temporär

        $token = file_get_contents($this->tokenPath);

        if (empty($token)) {
            throw new TokenException(
                $this->snippets->getNamespace('blauband/tsg')->get('missingToken')
            );
        } else {
            return $token;
        }
    }

    private function saveToken($token)
    {
        if (empty($token)) {
            throw new TokenException(
                $this->snippets->getNamespace('blauband/tsg')->get('missingToken')
            );
        }

        if (file_put_contents($this->tokenPath, $token) === false) {
            throw new SystemFileSystemException(
                sprintf(
                    $this->snippets->getNamespace('blauband/tsg')->get('cantSaveToken'),
                    $this->tokenPath
                )
            );
        }

    }

    private function deleteToken()
    {
        @unlink($this->tokenPath);
    }
}