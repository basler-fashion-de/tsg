<?php

namespace BlaubandOneClickSystem\Services\System;

class SystemService
{
    const SYSTEM_STATE_READY = 'ready';

    const SYSTEM_STATE_WAITING_GUEST_MEDIA_FOLDER = 'waiting_media_folder';

    const SYSTEM_STATE_CREATING_WAITING = 'waiting';
    const SYSTEM_STATE_CREATING_HOST_DB_ENTRY = 'creating_host_db_entry';
    const SYSTEM_STATE_CREATING_GUEST_DB = 'create_guest_db';
    const SYSTEM_STATE_CREATING_GUEST_CODEBASE = 'create_guest_codebase';
    const SYSTEM_STATE_CREATING_GUEST_MEDIA_FOLDER = 'create_guest_media_folder';
    const SYSTEM_STATE_CREATING_SET_UP_GUEST_SHOP = 'setting_up_guest_shop';
    const SYSTEM_STATE_CREATING_SET_UP_GUEST_HTACCESS_HTPASSWD = 'setting_up_guest_htaccess_htpasswd';
    const SYSTEM_STATE_CREATING_SET_UP_GUEST_MAILING = 'setting_up_guest_mailing';

    const SYSTEM_STATE_DELETING_WAITING = 'deleting_waiting';
    const SYSTEM_STATE_DELETING_GUEST_DB = 'deleting_guest_db';
    const SYSTEM_STATE_DELETING_GUEST_CODEBASE = 'deleting_guest_codebase';
    const SYSTEM_STATE_DELETING_HOST_DB_ENTRY = 'deleting_host_db_entry';
}