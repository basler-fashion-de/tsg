<?php

namespace BlaubandOneClickSystem\Services;

use BlaubandOneClickSystem\Models\System;

interface SystemServiceInterface
{
    /**
     * Hier muss der Type des System zurückgegeben werden.
     * Dieser muss ausserdem zum Servicenamen in der service.xml passen.
     *
     * blauband_one_click_system.{type}_system_service
     *
     * @return mixed
     */
    public function getType();

    public function createSystem($systemName, $dbHost, $dbUser, $dbPass, $dbName, $dbOverwrite);
    public function deleteSystem(System $system);
}