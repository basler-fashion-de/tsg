<?php

namespace BlaubandTSG\Installers;

use BlaubandTSG\Services\System\Common\TSGApiService;

class Api
{
    /**
     * @var TSGApiService
     */
    private $apiService;

    public function __construct(TSGApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * @return void
     */
    public function install()
    {
        $this->apiService->register();
    }

    /**
     * @return void
     */
    public function uninstall()
    {
        $this->apiService->deRegister();
    }

    /**
     * @return void
     */
    public function update()
    {
        $this->apiService->register();
    }
}
