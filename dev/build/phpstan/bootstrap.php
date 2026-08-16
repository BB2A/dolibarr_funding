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



// Load the API stubs so FundingApi's parent class (DolibarrApi) and helpers
// (DolibarrApiAccess, RestException) exist at analysis time.
$_api_stub = __DIR__ . '/stubs/dolibarr_api.php';
if (is_file($_api_stub)) {
    @include_once $_api_stub;
}

// Load the Dolibarr global helper functions so PHPStan can resolve calls to
// isModEnabled(), getDolGlobalString(), GETPOST(), ... at analysis time.
$_core_funcs = DOL_DOCUMENT_ROOT . '/core/lib/functions.lib.php';
if (is_file($_core_funcs)) {
    @include_once $_core_funcs;
}
$_core_funcs2 = DOL_DOCUMENT_ROOT . '/core/lib/functions2.lib.php';
if (is_file($_core_funcs2)) {
    @include_once $_core_funcs2;
}

/**
 * Minimal SPL autoloader for the Dolibarr core classes used by this module.
 * This allows PHPStan to resolve "extends CommonObject" and similar
 * inheritance without a full Dolibarr autoloader/install.
 */
spl_autoload_register(function ($className) {
    static $map = array(
        'CommonObject' => 'core/class/commonobject.class.php',
        'CommonDocGenerator' => 'core/class/commondocgenerator.class.php',
        'DolibarrModules' => 'core/modules/DolibarrModules.class.php',
        'DolibarrTriggers' => 'core/triggers/dolibarrtriggers.class.php',
        'DolibarrApi' => 'core/class/dolibarrapi.class.php',
        'Form' => 'core/class/html.form.class.php',
        'FormFile' => 'core/class/html.formfile.class.php',
        'FormCompany' => 'core/class/html.formcompany.class.php',
        'FormProjet' => 'core/class/html.formprojet.class.php',
        'FormActions' => 'core/class/html.formactions.class.php',
        'ExtraFields' => 'core/class/extrafields.class.php',
        'Notify' => 'core/class/notify.class.php',
        'Translate' => 'core/class/translate.class.php',
        'Conf' => 'core/class/conf.class.php',
        'HookManager' => 'core/class/hookmanager.class.php',
        'Societe' => 'societe/class/societe.class.php',
        'Propal' => 'comm/propal/class/propal.class.php',
        'Commande' => 'commande/class/commande.class.php',
        'Facture' => 'compta/facture/class/facture.class.php',
        'User' => 'user/class/user.class.php',
        'Project' => 'projet/class/project.class.php',
        'DoliDB' => 'core/db/database.class.php',
    );
    if (isset($map[$className])) {
        $file = DOL_DOCUMENT_ROOT . '/' . $map[$className];
        if (is_file($file)) {
            include_once $file;
        }
    }
});
