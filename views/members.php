<?php

/**
 * Group item view.
 *
 * @category   apps
 * @package    groups
 * @subpackage views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/groups/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.  
//  
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// Load dependencies
///////////////////////////////////////////////////////////////////////////////

$this->lang->load('groups');
$this->lang->load('users');
$this->lang->load('base');

///////////////////////////////////////////////////////////////////////////////
// Buttons
///////////////////////////////////////////////////////////////////////////////

$group_name = $group_info['core']['group_name'];
$safe_group_name = strtr($group_name, '$ ', '~:'); // spaces and dollars not allowed, so munge

if ($account_app) {
    $base_app = '/app/accounts/plugins';
    $form = '/accounts/policy/members/' . preg_replace('/_plugin/', '', $safe_group_name);
} else if (empty($basename)) {
    $base_app = '/app/groups';
    $form = '/groups/edit_members/' . $safe_group_name;
} else {
    $base_app = '/app/' . $basename . '/policy';
    $form = $basename . '/policy/edit_members/' . $safe_group_name;
}

if ($mode === 'view') {
    $buttons = array(anchor_cancel($base_app));
    $read_only = TRUE;
} else {
    $buttons = array(anchor_cancel($base_app, 'high'), form_submit_update('submit', 'high'));
    $read_only = FALSE;
}

///////////////////////////////////////////////////////////////////////////////
// Headers
///////////////////////////////////////////////////////////////////////////////

$headers = array(
    lang('base_username'),
);

///////////////////////////////////////////////////////////////////////////////
// Items
///////////////////////////////////////////////////////////////////////////////

foreach ($users as $username => $details) {
    // A period is not permitted as key, so translate it into a colon
    $item['title'] = $username;
    $item['name'] = 'users[' . preg_replace('/\./', ':', $username) . ']';
    $item['state'] = (in_array($username, $group_info['core']['members'])) ? TRUE : FALSE;
    $item['details'] = array(
        $username,
    );

    $items[] = $item;
}

///////////////////////////////////////////////////////////////////////////////
// List table
///////////////////////////////////////////////////////////////////////////////

$description = $group_info['core']['group_name'] . ' - ' . $group_info['core']['description'];
$description = (strlen($description) > 40) ? substr($description, 0, 40) . ' ...' : $description;

echo form_open($form);

echo list_table(
    $description,
    $buttons,
    $headers,
    $items,
    array('read_only' => $read_only)
);

echo form_close();
