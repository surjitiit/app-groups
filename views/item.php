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

$this->lang->load('base');
$this->lang->load('groups');
$this->lang->load('users');

///////////////////////////////////////////////////////////////////////////////
// Form modes
///////////////////////////////////////////////////////////////////////////////

$group_name = $group_info['core']['group_name'];
$safe_group_name = strtr($group_name, '$ ', '~:'); // spaces and dollars not allowed, so munge

if ($form_type === 'edit') {
    $description_read_only = FALSE;
    $group_name_read_only = TRUE;

    $form_path = '/groups/edit/' . $safe_group_name;
    $buttons = array(
        form_submit_update('submit'),
        anchor_custom('/app/groups/edit_members/' . $safe_group_name, lang('groups_edit_members'), 'low'),
        anchor_cancel('/app/groups/'),
        anchor_delete('/app/groups/delete/' . $safe_group_name)
    );
} else if ($form_type === 'view') {
    $description_read_only = TRUE;
    $group_name_read_only = TRUE;

    $form_path = '/groups/view/' . $safe_group_name;
    $buttons = array(
        anchor_cancel('/app/groups/')
    );
} else {
    $description_read_only = FALSE;
    $group_name_read_only = FALSE;

    $form_path = '/groups/add';
    $buttons = array(
        form_submit_add('submit'),
        anchor_cancel('/app/groups/')
    );
}

///////////////////////////////////////////////////////////////////////////////
// Main form
///////////////////////////////////////////////////////////////////////////////

echo form_open($form_path);
echo form_header(lang('groups_group'));

echo fieldset_header(lang('base_settings'));
echo field_input('group_name', $group_name, lang('groups_group_name'), $group_name_read_only);
echo field_input('description', $group_info['core']['description'], lang('groups_description'), $description_read_only);
echo fieldset_footer();

///////////////////////////////////////////////////////////////////////////////
// Extensions
///////////////////////////////////////////////////////////////////////////////

foreach ($info_map['extensions'] as $extension => $parameters) {

    // Echo out the specific info field
    //---------------------------------

    $fields = '';

    if (! empty($parameters)) {
        foreach ($parameters as $key_name => $details) {
            $name = "group_info[extensions][$extension][$key_name]";
            $value = $group_info['extensions'][$extension][$key_name];
            $description =  $details['description'];
            $field_read_only = $read_only;

            if (isset($details['field_priority']) && ($details['field_priority'] === 'hidden')) {
                continue;
            } else if (isset($details['field_priority']) && ($details['field_priority'] === 'read_only')) {
                if ($form_type === 'add')
                    continue;

                $field_read_only = TRUE;
            }

            if ($details['field_type'] === 'list') {
                $fields .= field_dropdown($name, $details['field_options'], $value, $description, $field_read_only);
            } else if ($details['field_type'] === 'simple_list') {
                $fields .= field_simple_dropdown($name, $details['field_options'], $value, $description, $field_read_only);
            } else if ($details['field_type'] === 'text') {
                $fields .= field_input($name, $value, $description, $field_read_only);
            } else if ($details['field_type'] === 'integer') {
                $fields .= field_input($name, $value, $description, $field_read_only);
            } else if ($details['field_type'] === 'text_array') {
                $fields .= field_input($name . "[0]", $value[0], $description, $field_read_only);

                for ($inx = 1; $inx < count($value); $inx++) {
                    $description = ($inx === 0) ? $description : '';
                    $fields .= field_input($name . "[1]", $value[$inx], $description, $field_read_only);
                }
                // Show an extra blank field
                if ($form_type !== 'view')
                    $fields .= field_input($name . "[$inx]", '', '', $field_read_only);
            }
        }
    }

    if (! empty($fields)) {
        echo fieldset_header($extensions[$extension]['nickname']);
        echo $fields;
        echo fieldset_footer();
    }
}

///////////////////////////////////////////////////////////////////////////////
// Form close
///////////////////////////////////////////////////////////////////////////////

echo field_button_set($buttons);

echo form_footer();
echo form_close();
