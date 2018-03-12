<?php

namespace BlaubandOneClickSystem\Installers;

use BlaubandOneClickSystem\Services\System\Common\OCSApiService;

class Api
{
    /**
     * @var OCSApiService
     */
    private $apiService;

    public function __construct(OCSApiService $apiService)
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
