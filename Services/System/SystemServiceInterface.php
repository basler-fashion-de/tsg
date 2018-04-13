<?php

namespace BlaubandTSG\Services\System;

use BlaubandTSG\Models\System;

interface SystemServiceInterface
{
    /**
     * Hier muss der Type des System zurückgegeben werden.
     * Dieser muss ausserdem zum Servicenamen in der service.xml passen.
     *
     * blauband_tsg.{type}_system_service
     *
     * @return mixed
     */
    public function getType();

    public function createSystem($systemName, $parameters);
    public function deleteSystem(System $system);
}