<?php

/**
 * ClearOS group engine.
 *
 * @category   apps
 * @package    groups
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/groups/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// N A M E S P A C E
///////////////////////////////////////////////////////////////////////////////

namespace clearos\apps\groups;

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('groups');

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

// Classes
//--------

use \clearos\apps\base\Engine as Engine;
use \clearos\apps\base\File as File;

clearos_load_library('base/Engine');
clearos_load_library('base/File');

// Exceptions
//-----------

use \clearos\apps\base\File_No_Match_Exception as File_No_Match_Exception;

clearos_load_library('base/File_No_Match_Exception');

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * ClearOS group engine.
 *
 * @category   apps
 * @package    groups
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/groups/
 */

class Group_Engine extends Engine
{
    ///////////////////////////////////////////////////////////////////////////////
    // C O N S T A N T S
    ///////////////////////////////////////////////////////////////////////////////

    // Files and paths
    //----------------

    const FILE_POSIX_GROUPS = '/etc/group';

    // Group ID ranges
    //----------------

    const GID_RANGE_SYSTEM_MIN = '0';
    const GID_RANGE_SYSTEM_MAX = '499';

    // Group types
    //------------

    const TYPE_SYSTEM = 'system';
    const TYPE_NORMAL = 'normal';
    const TYPE_BUILTIN = 'builtin';
    const TYPE_WINDOWS = 'windows';
    const TYPE_PLUGIN = 'plugin';
    const TYPE_HIDDEN = 'hidden';
    const TYPE_UNKNOWN = 'unknown';

    // Group filters
    // -------------
    // When using some API calls, it is handy to filter for only certain types of 
    // groups.  The following filter flags can be used where applicable.

    const FILTER_SYSTEM = 1;    // System groups
    const FILTER_NORMAL = 2;    // User-defined groups
    const FILTER_BUILTIN = 4;   // Builtin groups
    const FILTER_WINDOWS = 8;   // Windows reserved groups
    const FILTER_PLUGIN = 16;   // Hidden groups

    const FILTER_ALL = 31;
    const FILTER_DEFAULT = 128;  // Driver will decide a sane default

    ///////////////////////////////////////////////////////////////////////////////
    // V A R I A B L E S
    ///////////////////////////////////////////////////////////////////////////////

    public static $hidden_list = array(
        'domain_controllers',
        'domain controllers',
        'domain_computers',
        'domain computers',
        'domain_users',
        'domain users',
        'read-only_domain_controllers',
        'read-only domain controllers'
    );

    public static $builtin_list = array(
        'allusers'
    );

    public static $windows_list = array(
        'account_operators',
        'account operators',
        'administrators',
        'backup_operators',
        'backup operators',
        'cert_publishers',
        'cert publishers',
        'dnsadmins',
        'dnsupdateproxy',
        'domain_admins',
        'domain admins',
        'domain_guests',
        'domain guests',
        'enterprise_admins',
        'enterprise admins',
        'enterprise_read-only_domain_controllers',
        'enterprise read-only domain controllers',
        'group_policy_creator_owners',
        'group policy creator owners',
        'guests',
        'power_users',
        'power users',
        'print_operators',
        'print operators',
        'ras_and_ias_servers',
        'ras and ias servers',
        'schema_admins',
        'schema admins',
        'server_operators',
        'server operators',
        'telnetclients',
        'users'
    );

    ///////////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Group_Engine constructor.
     */

    public function __construct()
    {
        clearos_profile(__METHOD__, __LINE__);
    }

    ///////////////////////////////////////////////////////////////////////////////
    // P R I V A T E   M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Loads group from Posix.
     *
     * @return void
     * @throws Engine_Exception
     */

    protected function _load_group_from_posix()
    {
        clearos_profile(__METHOD__, __LINE__);

        $info = array();

        $file = new File(self::FILE_POSIX_GROUPS);

        try {
            $line = $file->lookup_line('/^' . $this->group_name . ':/i');
        } catch (File_No_Match_Exception $e) {
            return array();;
        }

        $parts = explode(':', $line);

        if (count($parts) != 4)
            return;

        $info['core']['group_name'] = $parts[0];
        $info['core']['gid_number'] = $parts[2];
        $info['core']['members'] = explode(',', $parts[3]);

        // Sanity check: check for non-compliant group ID
        //-----------------------------------------------

        if (($info['core']['gid_number'] >= self::GID_RANGE_SYSTEM_MIN) && ($info['core']['gid_number'] <= self::GID_RANGE_SYSTEM_MAX)) {
            $info['core']['type'] = self::TYPE_SYSTEM;
        } else {
            $info['core']['type'] = self::TYPE_UNKNOWN;
        }

        return $info;
    }
}
