<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Progress block settings
 *
 * @package   block_progress
 * @copyright 2014 onwards Johan Reinalda (johan at reinalda dot net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_configcolourpicker('block_progress/attempted_colour',
        get_string('attempted_colour_title', 'block_progress'),
        get_string('attempted_colour_descr', 'block_progress'), '#5CD85C', null )
    );

    $settings->add(new admin_setting_configcolourpicker('block_progress/notattempted_colour',
        get_string('notattempted_colour_title', 'block_progress'),
        get_string('notattempted_colour_descr', 'block_progress'), '#FF5C5C', null )
    );

    $settings->add(new admin_setting_configcolourpicker('block_progress/futurenotattempted_colour',
        get_string('futurenotattempted_colour_title', 'block_progress'),
        get_string('futurenotattempted_colour_descr', 'block_progress'), '#5C5CFF', null )
    );
	$settings->add(new admin_setting_configtext('block_progress/defaultrole',
		get_string('defaultroletitle', 'block_progress'),
		get_string('defaultroledesc', 'block_progress'), 'student' , PARAM_RAW, 25));
}