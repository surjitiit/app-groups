<?php

/**
 * Group manager engine.
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

use \clearos\apps\base\Engine as Engine;
use \clearos\apps\base\File as File;

clearos_load_library('base/Engine');
clearos_load_library('base/File');

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Group manager engine.
 *
 * @category   apps
 * @package    groups
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/groups/
 */

class Group_Manager_Engine extends Engine
{
    ///////////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Group manager constructor.
     */

    public function __construct()
    {
        clearos_profile(__METHOD__, __LINE__);
    }

    /**
     * Returns hint on how to handle displaying Windows groups.
     *
     * @return boolean TRUE if Windows groups should be exposed
     * @throws Engine_Exception
     */

    public function show_windows_groups()
    {
        clearos_profile(__METHOD__, __LINE__);

        // TODO: a bit kludgy.  Use API instead.
        if (file_exists('/var/clearos/samba_directory/initialized') || file_exists('/var/clearos/samba_common/initialized'))
            return TRUE;
        else
            return FALSE;
    }

    /**
     * Loads groups from Posix.
     *
     * @return array group information
     * @throws Engine_Exception
     */

    protected function _get_details_from_posix()
    {
        clearos_profile(__METHOD__, __LINE__);

        $file = new File(Group_Engine::FILE_POSIX_GROUPS);
        $contents = $file->get_contents_as_array();

        $group_data = array();

        foreach ($contents as $line) {
            $data = explode(":", $line);

            $gid = $data[2];

            if (($gid >= Group_Engine::GID_RANGE_SYSTEM_MIN) && ($gid <= Group_Engine::GID_RANGE_SYSTEM_MAX)) {
                $assoc_data['core']['group_name'] = $data[0];
                $assoc_data['core']['type'] = Group_Engine::TYPE_SYSTEM;
                $assoc_data['core']['description'] = '';
                $assoc_data['core']['members'] = explode(',', $data[3]);
                $group_data[$data[0]] = $assoc_data;
            }
        }

        ksort($group_data);

        return $group_data;
    }
}
