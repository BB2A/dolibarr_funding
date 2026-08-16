<?php
/* Copyright (C) 2026  BB2A
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * Minimal bootstrap for PHPStan analysis of this external module.
 *
 * It defines the Dolibarr constants required by the core classes so that
 * PHPStan can autoload them when scanning, without loading main.inc.php
 * (which would need a full Dolibarr installation with a database).
 *
 * The Dolibarr core must be available under dolibarr_core/htdocs/ (provided
 * by the CI workflow).
 */

if (!defined('DOL_DOCUMENT_ROOT')) {
    define('DOL_DOCUMENT_ROOT', getcwd() . '/dolibarr_core/htdocs');
}
if (!defined('DOL_DATA_ROOT')) {
    define('DOL_DATA_ROOT', getcwd() . '/dolibarr_core/documents');
}
if (!defined('DOL_URL_ROOT')) {
    define('DOL_URL_ROOT', '/');
}
if (!defined('DOL_MAIN_URL_ROOT')) {
    define('DOL_MAIN_URL_ROOT', '/');
}
if (!defined('MAIN_DB_PREFIX')) {
    define('MAIN_DB_PREFIX', 'llx_');
}
if (!defined('NOLOGIN')) {
    define('NOLOGIN', '1');
}
if (!defined('NOSESSION')) {
    define('NOSESSION', '1');
}
if (!defined('NOHTTPSREDIRECT')) {
    define('NOHTTPSREDIRECT', '1');
}
if (!defined('MAIN_VERSION_DISABLE_DB_CHECK')) {
    define('MAIN_VERSION_DISABLE_DB_CHECK', '1');
}
