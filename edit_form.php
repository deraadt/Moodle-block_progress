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
 * Progress Bar block configuration form definition
 *
 * @package    contrib
 * @subpackage block_progress
 * @copyright  2010 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');

/**
 * Simple clock block config form class
 *
 * @copyright 2010 Michael de Raadt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_progress_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        global $CFG, $COURSE, $DB, $OUTPUT;
        include_once($CFG->dirroot.'/blocks/progress/lib.php');
        $turnallon = optional_param('turnallon', 0, PARAM_INT);
        $dbmanager = $DB->get_manager(); // loads ddl manager and xmldb classes
        $count = 0;
        $usingweeklyformat = $COURSE->format=='weeks' || $COURSE->format=='weekscss' ||
                             $COURSE->format=='weekcoll';

        // Start block specific section in config form
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // Progress block instance title
        $mform->addElement('text', 'config_progressTitle',
                           get_string('config_title', 'block_progress'));
        $mform->setDefault('config_progressTitle', '');
        $mform->setType('config_progressTitle', PARAM_MULTILANG);
        $mform->addHelpButton('config_progressTitle', 'why_set_the_title', 'block_progress');

        // Allow icons to be turned on/off on the block
        $mform->addElement('selectyesno', 'config_progressBarIcons',
                           get_string('config_icons', 'block_progress').'&nbsp;'.
                           $OUTPUT->pix_icon('tick', '', 'block_progress').'&nbsp;'.
                           $OUTPUT->pix_icon('cross', '', 'block_progress'));
        $mform->setDefault('config_progressBarIcons', 0);
        $mform->addHelpButton('config_progressBarIcons', 'why_use_icons', 'block_progress');

        // Allow NOW to be turned on or off
        $mform->addElement('selectyesno', 'config_displayNow',
                           get_string('config_now', 'block_progress').'&nbsp;'.
                           $OUTPUT->pix_icon('left', '', 'block_progress').
                           get_string('now_indicator', 'block_progress'));
        $mform->setDefault('config_displayNow', 1);
        $mform->addHelpButton('config_displayNow', 'why_display_now', 'block_progress');

        // Allow progress percentage to be turned on for students
        $mform->addElement('selectyesno', 'config_showpercentage',
                           get_string('config_percentage', 'block_progress'));
        $mform->setDefault('config_showpercentage', 0);
        $mform->addHelpButton('config_showpercentage', 'why_show_precentage', 'block_progress');

        $mform->addElement('header', '', get_string('config_monitored', 'block_progress'));

        // Go through each type of activity/resource that can be monitored
        $modules = get_monitorable_modules();
        foreach ($modules as $module => $details) {

            // Get data about activities/resources of this type
            unset($instances);
            if ($dbmanager->table_exists($module)) {
                $sql = 'SELECT id, name';
                if ($module == 'assignment') {
                    $sql .= ', assignmenttype';
                }
                if (array_key_exists('defaultTime', $details)) {
                    $sql .= ', '.$details['defaultTime'].' as due';
                }
                $sql .= ' FROM {'.$module.'} WHERE course=\''.$COURSE->id.'\' ORDER BY name';
                $instances = $DB->get_records_sql($sql);
            }

            // If there are activities/resources of this type, show them
            if (!empty($instances)) {

                // Display each monitorable activity/resource as a row
                foreach ($instances as $i => $instance) {
                    $count++;

                    // Start of module border
                    $mform->addElement('html', '<div class="progressConfigBox">');

                    // Find type labels for assignment types
                    $asslabel = '';
                    if (isset($instance->assignmenttype)) {
                        $type = $instance->assignmenttype;
                        if (get_string_manager()->string_exists('type'.$type, 'mod_assignment')) {
                            $asslabel = get_string('type'.$type, 'assignment');
                        }
                        else {
                            $asslabel  = get_string('type'.$type, 'assignment_'.$type);
                        }
                        $asslabel = ' ('.$asslabel.')';
                    }

                    // Icon, module type and name
                    $mform->addElement('html', $OUTPUT->pix_icon('icon',
                                       get_string('pluginname', $module), 'mod_'.$module).
                                       '&nbsp;<strong>'.get_string($module, 'block_progress').
                                       $asslabel.
                                       ': <em>'.format_string($instance->name).'</em></strong>');

                    // Allow monitoring turned on or off
                    $mform->addElement('selectyesno', 'config_monitor_'.$module.$instance->id,
                                       get_string('config_header_monitored', 'block_progress'));
                    $mform->setDefault('config_monitor_'.$module.$instance->id, $turnallon);
                    $mform->addHelpButton('config_monitor_'.$module.$instance->id,
                                          'what_does_monitored_mean', 'block_progress');

                    // Allow locking turned on or off
                    if (isset($details['defaultTime']) && $instance->due != 0) {
                        $mform->addElement('selectyesno', 'config_locked_'.$module.$instance->id,
                                           get_string('config_header_locked', 'block_progress'));
                        $mform->setDefault('config_locked_'.$module.$instance->id, 1);
                        $mform->disabledif ('config_locked_'.$module.$instance->id,
                                            'config_monitor_'.$module.$instance->id, 'eq', 0);
                        $mform->addHelpButton('config_locked_'.$module.$instance->id,
                                              'what_locked_means', 'block_progress');
                    }

                    // Print the date selector
                    $mform->addElement('date_time_selector',
                                       'config_date_time_'.$module.$instance->id,
                                       get_string('config_header_expected', 'block_progress'));
                    $mform->disabledif ('config_date_time_'.$module.$instance->id,
                                        'config_locked_'.$module.$instance->id, 'eq', 1);
                    $mform->disabledif ('config_date_time_'.$module.$instance->id,
                                        'config_monitor_'.$module.$instance->id, 'eq', 0);

                    // Assume a time/date for a activity/resource
                    $expected = null;
                    $datetimepropery = 'date_time_'.$module.$instance->id;
                    if (
                        isset($this->block->config) &&
                        property_exists($this->block->config, $datetimepropery)
                    ) {
                        $expected = $this->block->config->$datetimepropery;
                    }
                    if (empty($expected)) {

                        // If there is a date associated with the activity/resource, use that
                        if (isset($details['defaultTime']) && $instance->due != 0) {
                            $expected = progress_default_value($instance->due);
                        }

                        // If in positioned in a weekly format, use 5min before end of week
                        else if ($usingweeklyformat) {
                            $cm = get_coursemodule_from_instance($module, $instance->id,
                                                                 $COURSE->id);
                            $params = array('id'=>$cm->section);
                            $section = $DB->get_field('course_sections', 'section', $params);
                            $expected = $COURSE->startdate + ($section>0?$section:1)*604800 - 300;
                        }

                        // Assume 5min before the end of the current week
                        else {
                            $currenttime = time();
                            $timearray = localtime($currenttime, true);
                            $endofweektimearray =
                                localtime($currenttime + (7-$timearray['tm_wday'])*86400, true);
                            $expected = mktime(23,
                                               55,
                                               0,
                                               $endofweektimearray['tm_mon']+1,
                                               $endofweektimearray['tm_mday'],
                                               $endofweektimearray['tm_year']+1900);
                        }
                    }
                    $mform->setDefault('config_date_time_'.$module.$instance->id, $expected);
                    $mform->addHelpButton('config_date_time_'.$module.$instance->id,
                                          'what_expected_by_means', 'block_progress');

                    // Print the action selector for the event
                    $actions = array();
                    foreach ($details['actions'] as $action => $sql) {

                        // Before allowing pass marks, see that Grade to pass value is set
                        if ($action == 'passed') {
                            $params = array('itemmodule'=>$module, 'iteminstance'=>$instance->id);
                            $gradetopass = $DB->get_record('grade_items', $params, 'gradepass');
                            if ($gradetopass && $gradetopass->gradepass > 0) {
                                $actions[$action] = get_string($action, 'block_progress');
                            }
                        }
                        else {
                            $actions[$action] = get_string($action, 'block_progress');
                        }
                    }
                    if (isset($CFG->enablecompletion) && $CFG->enablecompletion==1) {
                        $cm = get_coursemodule_from_instance($module, $instance->id, $COURSE->id);
                        if ($cm->completion!=0) {
                            $actions['activity_completion'] = get_string('activity_completion',
                                                                         'block_progress');
                        }
                    }
                    $mform->addElement('select', 'config_action_'.$module.$instance->id,
                                       get_string('config_header_action', 'block_progress'),
                                       $actions );
                    $mform->setDefault('config_action_'.$module.$instance->id,
                                       $details['defaultAction']);
                    $mform->disabledif ('config_action_'.$module.$instance->id,
                                        'config_monitor_'.$module.$instance->id, 'eq', 0);
                    $mform->addHelpButton('config_action_'.$module.$instance->id,
                                          'what_actions_can_be_monitored', 'block_progress');

                    $mform->addElement('html', '</div>');
                }
            }
        }

        // When there are no activities that can be monitored, prompt teacher to create some
        if ($count==0) {
            $mform->addElement('html', get_string('no_events_config_message', 'block_progress'));
        }
    }
}
