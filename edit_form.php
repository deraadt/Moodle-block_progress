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
require_once($CFG->dirroot.'/blocks/progress/lib.php');

define('YEARS_TO_SHOW', 20);

/**
 * Progress Bar block config form class
 *
 * @copyright 2010 Michael de Raadt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_progress_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        global $CFG, $COURSE, $DB, $OUTPUT, $SCRIPT;
        $loggingenabled = true;
        $coursestartdate = localtime($COURSE->startdate, true);
        $minyear = $coursestartdate['tm_year']+1900;

        // The My home version is not configurable.
        if (block_progress_on_site_page()) {
            return;
        }

        // Check that logging is enabled in 2.7 onwards.
        if (function_exists('get_log_manager')) {
            $logmanager = get_log_manager();
            $readers = $logmanager->get_readers();
            $loggingenabled = !empty($readers);
            if(!$loggingenabled) {
                $warningstring = get_string('config_warning_logstores', 'block_progress');
                $warning = HTML_WRITER::tag('div', $warningstring, array('class' => 'warning progressWarningBox'));
                $mform->addElement('html', $warning);
            }
        }

        // Check that logs will be available during course.
        if(isset($CFG->loglifetime) && $CFG->loglifetime > 0) {
            $warningstring = get_string('config_warning_loglifetime', 'block_progress', $CFG->loglifetime);
            $warning = HTML_WRITER::tag('div', $warningstring, array('class' => 'warning progressWarningBox'));
            $mform->addElement('html', $warning);
        }

        $turnallon = optional_param('turnallon', 0, PARAM_INT);
        $dbmanager = $DB->get_manager(); // Loads ddl manager and xmldb classes.
        $count = 0;
        $usingweeklyformat = $COURSE->format == 'weeks' || $COURSE->format == 'weekscss' ||
                             $COURSE->format == 'weekcoll';

        // Start block specific section in config form.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // Set Progress block instance title.
        $mform->addElement('text', 'config_progressTitle',
                           get_string('config_title', 'block_progress'));
        $mform->setDefault('config_progressTitle', '');
        $mform->setType('config_progressTitle', PARAM_MULTILANG);
        $mform->addHelpButton('config_progressTitle', 'why_set_the_title', 'block_progress');

        // Allow icons to be turned on/off on the block.
        $mform->addElement('selectyesno', 'config_progressBarIcons',
                           get_string('config_icons', 'block_progress').'&nbsp;'.
                           $OUTPUT->pix_icon('tick', '', 'block_progress', array('class' => 'iconOnConfig')).'&nbsp;'.
                           $OUTPUT->pix_icon('cross', '', 'block_progress', array('class' => 'iconOnConfig')));
        $mform->setDefault('config_progressBarIcons', 0);
        $mform->addHelpButton('config_progressBarIcons', 'why_use_icons', 'block_progress');

        // Control order of items in Progress Bar.
        $orderingoptions = array(
            'orderbytime'   => get_string('config_orderby_due_time', 'block_progress'),
            'orderbycourse' => get_string('config_orderby_course_order', 'block_progress'),
        );
        $orderbylabel = get_string('config_orderby', 'block_progress');
        $mform->addElement('select', 'config_orderby', $orderbylabel, $orderingoptions);
        $mform->setDefault('config_orderby', 'orderbytime');
        $mform->addHelpButton('config_orderby', 'how_ordering_works', 'block_progress');

        // Allow NOW to be turned on or off.
        $mform->addElement('selectyesno', 'config_displayNow',
                           get_string('config_now', 'block_progress').'&nbsp;'.
                           $OUTPUT->pix_icon('left', '', 'block_progress').
                           get_string('now_indicator', 'block_progress'));
        $mform->setDefault('config_displayNow', 1);
        $mform->addHelpButton('config_displayNow', 'why_display_now', 'block_progress');
        $mform->disabledif('config_displayNow', 'config_orderby', 'eq', 'orderbycourse');

        // Allow progress percentage to be turned on for students.
        $mform->addElement('selectyesno', 'config_showpercentage',
                           get_string('config_percentage', 'block_progress'));
        $mform->setDefault('config_showpercentage', 0);
        $mform->addHelpButton('config_showpercentage', 'why_show_precentage', 'block_progress');

        // Allow the block to be visible to a single group.
        $groups = groups_get_all_groups($COURSE->id);
        if (!empty($groups)) {
            $groupsmenu = array();
            $groupsmenu[0] = get_string('allparticipants');
            foreach ($groups as $group) {
                $groupsmenu[$group->id] = format_string($group->name);
            }
            $grouplabel = get_string('config_group', 'block_progress');
            $mform->addElement('select', 'config_group', $grouplabel, $groupsmenu);
            $mform->setDefault('config_group', '0');
            $mform->addHelpButton('config_group', 'how_group_works', 'block_progress');
        }

        // Get course section information.
        $sections = block_progress_course_sections($COURSE->id);

        // Determine the time at the end of the week, less 5min.
        if (!$usingweeklyformat) {
            $currenttime = time();
            $timearray = localtime($currenttime, true);
            $endofweektimearray = localtime($currenttime + (7 - $timearray['tm_wday']) * 86400, true);
            $endofweektime = mktime(23,
                                    55,
                                    0,
                                    $endofweektimearray['tm_mon'] + 1,
                                    $endofweektimearray['tm_mday'],
                                    $endofweektimearray['tm_year'] + 1900);
        }

        // Go through each type of activity/resource that can be monitored to find instances in the course.
        $modules = block_progress_monitorable_modules();
        $unsortedmodules = array();
        foreach ($modules as $module => $details) {

            // Get data about instances of activities/resources of this type in this course.
            unset($instances);
            if ($dbmanager->table_exists($module)) {
                $sql = 'SELECT id, name';
                if ($module == 'assignment') {
                    $sql .= ', assignmenttype';
                }
                if (
                    array_key_exists('defaultTime', $details) &&
                    $dbmanager->field_exists($module, $details['defaultTime'])
                ) {
                    $sql .= ', '.$details['defaultTime'].' as due';
                }
                $sql .= ' FROM {'.$module.'} WHERE course=\''.$COURSE->id.'\' ORDER BY name';
                $instances = $DB->get_records_sql($sql);
            }

            // If there are instances of activities/resources of this type, get more info about them.
            if (!empty($instances)) {
                foreach ($instances as $i => $instance) {
                    $count++;
                    $moduleinfo = new stdClass();
                    $moduleinfo->module = $module;
                    $moduleinfo->instanceid = $instance->id;
                    $moduleinfo->uniqueid = $module.$instance->id;
                    $moduleinfo->label = get_string($module, 'block_progress');
                    $moduleinfo->instancename = $instance->name;
                    $moduleinfo->lockpossible = isset($details['defaultTime']);
                    $moduleinfo->instancedue = isset($instance->due) && ($instance->due != 0);

                    // Get position of activity/resource on course page.
                    $coursemodule = get_coursemodule_from_instance($module, $instance->id, $COURSE->id);
                    $moduleinfo->section = $coursemodule->section;
                    $moduleinfo->position = array_search($coursemodule->id, $sections[$coursemodule->section]->sequence);
                    $moduleinfo->coursemoduleid = $coursemodule->id;
                    $moduleinfo->completion = $coursemodule->completion;
                    $moduleinfo->completionexpected = $coursemodule->completionexpected;

                    // Find type labels for assignment types.
                    $asslabel = '';
                    if (isset($instance->assignmenttype)) {
                        $type = $instance->assignmenttype;
                        if (get_string_manager()->string_exists('type'.$type, 'mod_assignment')) {
                            $asslabel = get_string('type'.$type, 'assignment');
                        } else {
                            $asslabel  = get_string('type'.$type, 'assignment_'.$type);
                        }
                        $moduleinfo->label .= ' ('.$asslabel.')';
                    }

                    // Determine a time/date for a activity/resource.
                    $expected = null;
                    $datetimepropery = 'date_time_'.$module.$instance->id;
                    if (
                        isset($this->block->config) &&
                        property_exists($this->block->config, $datetimepropery)
                    ) {
                        $expected = $this->block->config->$datetimepropery;
                    }

                    // If there is a date associated with the activity/resource, use that.
                    $lockedproperty = 'locked_'.$module.$instance->id;

                    if (
                        isset($details['defaultTime']) &&
                        isset($instance->due) &&
                        $instance->due != 0 && (
                            (
                                isset($this->block->config) &&
                                property_exists($this->block->config, $lockedproperty) &&
                                $this->block->config->$lockedproperty == 1
                            ) ||
                            empty($expected)
                        )
                    ) {
                        $expected = progress_default_value($instance->due);
                        if (
                            isset($this->block->config) &&
                            property_exists($this->block->config, $datetimepropery)
                        ) {
                            $this->block->config->$datetimepropery = $expected;
                            $this->block->config->$lockedproperty = 1;
                        }
                    }

                    if (empty($expected)) {

                        // If a expected date is set in the activity completion, use that.
                        if ($moduleinfo->completion != 0 && $moduleinfo->completionexpected != 0) {
                            $expected = $moduleinfo->completionexpected;
                        }

                        // If positioned in a weekly format, use 5min before end of week.
                        else if ($usingweeklyformat) {
                            $expected = $COURSE->startdate + ($moduleinfo->section > 0 ? $moduleinfo->section : 1) * 604800 - 300;
                        }

                        // Assume 5min before the end of the current week.
                        else {
                            $expected = $endofweektime;
                        }
                    }
                    $moduleinfo->expected = $expected;

                    // Get the list of possible actions for the event.
                    $actions = array();
                    foreach ($details['actions'] as $action => $sql) {

                        // Before allowing pass marks, see that Grade to pass value is set.
                        if ($action == 'passed' || $action == 'passedby') {
                            $params = array('courseid' => $COURSE->id, 'itemmodule' => $module, 'iteminstance' => $instance->id);
                            $gradetopass = $DB->get_record('grade_items', $params, 'id,gradepass', IGNORE_MULTIPLE);
                            if ($gradetopass && $gradetopass->gradepass > 0) {
                                $actions[$action] = get_string($action, 'block_progress');
                            }
                        } else {
                            $actions[$action] = get_string($action, 'block_progress');
                        }
                    }
                    if (!empty($CFG->enablecompletion)) {
                        if ($moduleinfo->completion != 0) {
                            $actions['activity_completion'] = get_string('activity_completion', 'block_progress');
                        }
                    }
                    $moduleinfo->actions = $actions;

                    // Add the module to the array.
                    $unsortedmodules[] = $moduleinfo;
                }
            }
        }

        // Sort the array by coursemodule.
        $modulesinform = array();
        foreach ($unsortedmodules as $key => $moduleinfo) {
            $modulesinform[$moduleinfo->coursemoduleid] = $moduleinfo;
        }

        // Output the form elements for each module.
        if ($count > 0) {

            $dateselectoroptions = array(
                'optional' => false,
                'hideyuicalendar' => true,
                'startyear' => $minyear - 1,
                'stopyear' => $minyear + YEARS_TO_SHOW
            );

            foreach ($sections as $i => $section) {
                if (count($section->sequence) > 0) {

                    // Output the section header.
                    $sectionname = get_string('section').':&nbsp;'.get_section_name($COURSE, $section);
                    $mform->addElement('header', 'section'.$i, format_string($sectionname));
                    if (method_exists($mform, 'setExpanded')) {
                        $mform->setExpanded('section'.$i);
                    }

                    // Display each monitorable activity/resource as a row.
                    foreach ($section->sequence as $coursemoduleid) {
                        if (array_key_exists($coursemoduleid, $modulesinform)) {
                            $moduleinfo = $modulesinform[$coursemoduleid];

                            // Start box.
                            $attributes = array('class' => 'progressConfigBox');
                            $moduleboxstart = HTML_WRITER::start_tag('div', $attributes);
                            $mform->addElement('html', $moduleboxstart);

                            // Icon, module type and name.
                            $modulename = get_string('pluginname', $moduleinfo->module);
                            $icon = $OUTPUT->pix_icon('icon', $modulename, 'mod_'.$moduleinfo->module);
                            $text = '&nbsp;'.$moduleinfo->label.':&nbsp;'.format_string($moduleinfo->instancename);
                            $attributes = array('class' => 'progressConfigModuleTitle');
                            $moduletitle = HTML_WRITER::tag('div', $icon.$text, $attributes);
                            $mform->addElement('html', $moduletitle);

                            // Allow monitoring turned on or off.
                            $mform->addElement('selectyesno', 'config_monitor_'.$moduleinfo->uniqueid,
                                               get_string('config_header_monitored', 'block_progress'));
                            $mform->setDefault('config_monitor_'.$moduleinfo->uniqueid, $turnallon);
                            $mform->addHelpButton('config_monitor_'.$moduleinfo->uniqueid,
                                                  'what_does_monitored_mean', 'block_progress');

                            // Allow locking turned on or off.
                            if ($moduleinfo->lockpossible && $moduleinfo->instancedue) {
                                $mform->addElement('selectyesno', 'config_locked_'.$moduleinfo->uniqueid,
                                                   get_string('config_header_locked', 'block_progress'));
                                $mform->setDefault('config_locked_'.$moduleinfo->uniqueid, 1);
                                $mform->disabledif ('config_locked_'.$moduleinfo->uniqueid,
                                                    'config_monitor_'.$moduleinfo->uniqueid, 'eq', 0);
                                $mform->addHelpButton('config_locked_'.$moduleinfo->uniqueid,
                                                      'what_locked_means', 'block_progress');
                            }

                            // Print the date selector.
                            $mform->addElement('date_time_selector',
                                               'config_date_time_'.$moduleinfo->uniqueid,
                                               get_string('config_header_expected', 'block_progress'),
                                               $dateselectoroptions);
                            $mform->disabledif ('config_date_time_'.$moduleinfo->uniqueid,
                                                'config_locked_'.$moduleinfo->uniqueid, 'eq', 1);
                            $mform->disabledif ('config_date_time_'.$moduleinfo->uniqueid,
                                                'config_monitor_'.$moduleinfo->uniqueid, 'eq', 0);
                            $mform->disabledif('config_date_time_'.$moduleinfo->uniqueid,
                                               'config_orderby', 'eq', 'orderbycourse');
                            if ($moduleinfo->lockpossible && $moduleinfo->instancedue) {
                                $mform->disabledif('config_locked_'.$moduleinfo->uniqueid,
                                                   'config_orderby', 'eq', 'orderbycourse');
                            }
                            $mform->setDefault('config_date_time_'.$moduleinfo->uniqueid, $moduleinfo->expected);
                            $mform->addHelpButton('config_date_time_'.$moduleinfo->uniqueid,
                                                  'what_expected_by_means', 'block_progress');

                            // Print the action selector for the event.
                            if (count($moduleinfo->actions) == 1) {
                                $moduleinfo->actions = array_keys($moduleinfo->actions);
                                $action = $moduleinfo->actions[0];
                                $mform->addElement('static', 'config_action_static_'.$moduleinfo->uniqueid,
                                                   get_string('config_header_action', 'block_progress'),
                                                   get_string($action, 'block_progress'));
                                $mform->addElement('hidden', 'config_action_'.$moduleinfo->uniqueid, $action);
                            } else {
                                $mform->addElement('select', 'config_action_'.$moduleinfo->uniqueid,
                                                   get_string('config_header_action', 'block_progress'),
                                                   $moduleinfo->actions );
                                if (
                                    (!$moduleinfo->lockpossible || $moduleinfo->instancedue == 0) &&
                                    array_key_exists('activity_completion', $moduleinfo->actions)
                                ) {
                                    $defaultaction = 'activity_completion';
                                } else {
                                    $defaultaction = $details['defaultAction'];
                                }
                                $mform->setDefault('config_action_'.$moduleinfo->uniqueid, $defaultaction);
                                $mform->disabledif ('config_action_'.$moduleinfo->uniqueid,
                                                    'config_monitor_'.$moduleinfo->uniqueid, 'eq', 0);
                            }
                            $mform->setType('config_action_'.$moduleinfo->uniqueid, PARAM_ALPHANUMEXT);
                            $mform->addHelpButton('config_action_'.$moduleinfo->uniqueid,
                                                  'what_actions_can_be_monitored', 'block_progress');

                            // End box.
                            $moduleboxend = HTML_WRITER::end_tag('div');
                            $mform->addElement('html', $moduleboxend);
                        }
                    }
                }
            }
        }

        // When there are no activities that can be monitored, prompt teacher to create some.
        else {
            $mform->addElement('html', get_string('no_events_config_message', 'block_progress'));
        }
    }
}
