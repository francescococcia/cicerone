<?php

namespace Config;//configurazione

/**
 * Application configuration
 *
 * PHP version 7.0
 */
class Config
{

	const ROOT_DIR = '';

    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'cicerone';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'root';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = '';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;

    const SECRET_KEY = 'N9nBjog2HqAWyWVC4QJFGOjk3WDSgCfY';

    const EMAIL_USERNAME ='supp.cicerone@gmail.com';

    const EMAIL_PASSWORD = 'supporter01..';

    const EMAIL_HOST = 'smtp.gmail.com';

    const EMAIL_PORT = 587;

    const EMAIL_SMTPSECURE = 'tls';
}
