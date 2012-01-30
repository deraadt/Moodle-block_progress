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
 * Progress Bar block common configuration
 *
 * Instructions for adding new modules so they can be monitored
 * ================================================================================================
 * Activies that can be monitored (all resources are treated together) are defined in the $MODULES
 * array.
 *
 * Modules can be added with:
 *  - defaultTime (deadline from module if applicable),
 *  - actions (array if action-query pairs) and
 *  - defaultAction (selected by default in config page and needed for backwards compatability)
 *
 * The module name needs to be the same as the table name for module in the database.
 *
 * Queries need to produce at least one result for completeness to go green, ie there is a record
 * in the DB that indicates the user's completion.
 *
 * Queries may include the following terms that are substituted before the query is run:
 *  - #EVENTID# (the id of the activity in the DB table that relates to it, eg., an assignment id)
 *  - #CMID# (the course module id that identifies the instance of the module within the course),
 *  - #USERID# (the current user's id) and
 *  - $COURSEID# (the current course id)
 *
 * When you add a new module, you need to add a translation for it in the lang files.
 * If you add new action names, you need to add a translation for these in the lang files.
 *
 * Note: Activity completion is automatically available when enabled (sitewide setting) and set for
 * an activity.
 *
 * If you have added a new module to this array and think other's may benefit from the query you
 * have created, please share it by sending it to michaeld@moodle.com
 *
 * @package    contrib
 * @subpackage block_progress
 * @copyright  2010 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Provides information about monitorable modules
 *
 * @return array
 */
function get_monitorable_modules() {
    return array(
        'assignment' => array(
            'defaultTime'=>'timedue',
            'actions'=>array(
                'submitted'    => 'SELECT id FROM {assignment_submissions} '.
                                  'WHERE assignment=\'#EVENTID#\' AND userid=\'#USERID#\' '.
                                  'AND (numfiles=\'1\' OR data2=\'submitted\' OR data2=\'1\' OR grade!=\'-1\')',
                'marked'       => 'SELECT id FROM {assignment_submissions} '.
                                  'WHERE assignment=\'#EVENTID#\' AND userid=\'#USERID#\' '.
                                  'AND grade!=\'-1\''
            ),
            'defaultAction' => 'submitted'
        ),
        'book' => array(
            'actions'=>array(
                'viewed'       => 'SELECT id FROM {log} '.
                                  'WHERE course=\'#COURSEID#\' AND module=\'book\' '.
                                  'AND action=\'view\' AND cmid=\'#CMID#\' AND userid=\'#USERID#\''
            ),
            'defaultAction' => 'viewed'
        ),
        'certificate' => array(
            'actions'=>array(
                'awarded'    => 'SELECT id FROM {certificate_issues} '.
                                'WHERE certificateid=\'#EVENTID#\' AND userid=\'#USERID#\''
            ),
            'defaultAction' => 'awarded'
        ),
        'chat' => array(
            'actions'=>array(
                'posted_to'    => 'SELECT id FROM {chat_messages} '.
                                  'WHERE chatid=\'#EVENTID#\' AND userid=\'#USERID#\''
            ),
            'defaultAction' => 'posted_to'
        ),
        'choice' => array(
            'defaultTime'=>'timeclose',
            'actions'=>array(
                'answered'     => 'SELECT id FROM {choice_answers} '.
                                  'WHERE choiceid=\'#EVENTID#\' AND userid=\'#USERID#\''
            ),
            'defaultAction' => 'answered'
        ),
        'data' => array(
            'defaultTime'=>'timeviewto',
            'actions'=>array(
                'viewed'       => 'SELECT id FROM {log} '.
                                  'WHERE course=\'#COURSEID#\' AND module=\'data\' '.
                                  'AND action=\'view\' AND cmid=\'#CMID#\' AND userid=\'#USERID#\''
            ),
            'defaultAction' => 'viewed'
        ),
        'feedback' => array(
            'defaultTime'=>'timeclose',
            'actions'=>array(
                'responded_to' => 'SELECT id FROM {feedback_completed} '.
                                  'WHERE feedback=\'#EVENTID#\' AND userid=\'#USERID#\''
            ),
            'defaultAction' => 'responded_to'
        ),
        'resource' => array(  // AKA file
            'actions'=>array(
                'viewed'       => 'SELECT id FROM {log} '.
                                  'WHERE course=\'#COURSEID#\' AND module=\'resource\' '.
                                  'AND action=\'view\' AND cmid=\'#CMID#\' AND userid=\'#USERID#\''
            ),
            'defaultAction' => 'viewed'
        ),
        'flashcardtrainer' => array(
            'actions'=>array(
                'viewed' => 'SELECT id FROM {log} '.
                            'WHERE course=\'#COURSEID#\' AND module=\'flashcardtrainer\' '.
                            'AND action=\'view\' AND cmid=\'#CMID#\' AND userid=\'#USERID#\''
            ),
            'defaultAction' => 'viewed'
        ),
        'folder' => array(
            'actions'=>array(
                'viewed'       => 'SELECT id FROM {log} '.
                                  'WHERE course=\'#COURSEID#\' AND module=\'folder\' '.
                                  'AND action=\'view\' AND cmid=\'#CMID#\' AND userid=\'#USERID#\''
            ),
            'defaultAction' => 'viewed'
        ),
        'forum' => array(
            'defaultTime'=>'assesstimefinish',
            'actions'=>array(
                'posted_to'    => 'SELECT id FROM {forum_posts} '.
                                  'WHERE userid=\'#USERID#\' AND discussion IN '.
                                  '(SELECT id FROM {forum_discussions} WHERE forum=\'#EVENTID#\')'
            ),
            'defaultAction' => 'posted_to'
        ),
        'glossary' => array(
            'actions'=>array(
                'viewed'       => 'SELECT id FROM {log} '.
                                  'WHERE course=\'#COURSEID#\' AND module=\'glossary\' '.
                                  'AND action=\'view\' AND cmid=\'#CMID#\' AND userid=\'#USERID#\''
            ),
            'defaultAction' => 'viewed'
        ),
        'hotpot' => array(
            'defaultTime'=>'timeclose',
            'actions'=>array(
                'attempted'    => 'SELECT id FROM {hotpot_attempts} WHERE hotpot=\'#EVENTID#\' AND userid=\'#USERID#\'',
                'finished'     => 'SELECT id FROM {hotpot_attempts} WHERE hotpot=\'#EVENTID#\' AND userid=\'#USERID#\' AND timefinish!=\'0\'',
            ),
            'defaultAction' => 'finished'
        ),
        'imscp' => array(
            'actions'=>array(
                'viewed'       => 'SELECT id FROM {log} '.
                                  'WHERE course=\'#COURSEID#\' AND module=\'imscp\' '.
                                  'AND action=\'view\' AND cmid=\'#CMID#\' AND userid=\'#USERID#\''
            ),
            'defaultAction' => 'viewed'
        ),
        'journal' => array(
            'actions'=>array(
                'posted_to'    => 'SELECT id FROM {journal_entries} '.
                                  'WHERE journal=\'#EVENTID#\' AND userid=\'#USERID#\''
            ),
            'defaultAction' => 'posted_to'
        ),
        'lesson' => array(
            'defaultTime'=>'deadline',
            'actions'=>array(
                'attempted'    => 'SELECT id FROM {lesson_attempts} '.
                                  'WHERE lessonid=\'#EVENTID#\' AND userid=\'#USERID#\''
            ),
            'defaultAction' => 'attempted'
        ),
        'page' => array(
            'actions'=>array(
                'viewed'       => 'SELECT id FROM {log} '.
                                  'WHERE course=\'#COURSEID#\' AND module=\'page\' AND '.
                                  'action=\'view\' AND cmid=\'#CMID#\' AND userid=\'#USERID#\''
            ),
            'defaultAction' => 'viewed'
        ),
        'quiz' => array(
            'defaultTime'=>'timeclose',
            'actions'=>array(
                'attempted'    => 'SELECT id FROM {quiz_attempts} '.
                                  'WHERE quiz=\'#EVENTID#\' AND userid=\'#USERID#\'',
                'finished'     => 'SELECT id FROM {quiz_attempts} '.
                                  'WHERE quiz=\'#EVENTID#\' AND userid=\'#USERID#\' '.
                                  'AND timefinish!=\'0\'',
                'graded'       => 'SELECT id FROM {quiz_grades} '.
                                  'WHERE quiz=\'#EVENTID#\' AND userid=\'#USERID#\''
            ),
            'defaultAction' => 'finished'
        ),
        'scorm' => array(
            'actions'=>array(
                'attempted'    => 'SELECT id FROM {scorm_scoes_track} '.
                                  'WHERE scormid=\'#EVENTID#\' AND userid=\'#USERID#\'',
                'completed'    => 'SELECT id FROM {scorm_scoes_track} '.
                                  'WHERE scormid=\'#EVENTID#\' AND userid=\'#USERID#\' '.
                                  'AND element=\'cmi.core.lesson_status\' AND value=\'completed\'',
                'passed'       => 'SELECT id FROM {scorm_scoes_track} '.
                                  'WHERE scormid=\'#EVENTID#\' AND userid=\'#USERID#\' '.
                                  'AND element=\'cmi.core.lesson_status\' AND value=\'passed\''
            ),
            'defaultAction' => 'attempted'
        ),
        'url' => array(
            'actions'=>array(
                'viewed'       => 'SELECT id FROM {log} '.
                                  'WHERE course=\'#COURSEID#\' AND module=\'url\' '.
                                  'AND action=\'view\' AND cmid=\'#CMID#\' AND userid=\'#USERID#\''
            ),
            'defaultAction' => 'viewed'
        ),
        'wiki' => array(
            'actions'=>array(
                'viewed'       => 'SELECT id FROM {log} '.
                                  'WHERE course=\'#COURSEID#\' AND module=\'wiki\' '.
                                  'AND action=\'view\' AND cmid=\'#CMID#\' AND userid=\'#USERID#\''
            ),
            'defaultAction' => 'viewed'
        )
    );
}

/**
 * Checks if a variable has a value and returns a default value if it doesn't
 *
 * @param mixed $var The variable to check
 * @param mixed $def Default value if $var is not set
 * @return string
 */
function progress_default_value(&$var, $def = null) {
    return isset($var)?$var:$def;
}

/**
 * Filters the modules list to those installed in Moodle instance and used in current course
 *
 * @return array
 */
function modules_in_use() {
    global $COURSE, $DB;
    $dbmanager = $DB->get_manager(); // used to check if tables exist
    $modules = get_monitorable_modules();
    $modulesinuse = array();

    foreach ($modules as $module=>$details) {
        if (
            $dbmanager->table_exists($module) &&
            $DB->record_exists($module, array('course'=>$COURSE->id))
        ) {
            $modulesinuse[$module] = $details;
        }
    }
    return $modulesinuse;
}

/**
 * Gets event information about modules monitored by an instance of a Progress Bar block
 *
 * @param stdClass $config  The block instance configuration values
 * @param array    $modules The modules used in the course
 * @return array
 */
function event_information($config, $modules) {
    global $COURSE, $DB;
    $dbmanager = $DB->get_manager(); // used to check if tables exist
    $events = array();
    $numevents = 0;

    // Check each know module (described in lib.php
    foreach ($modules as $module=>$details) {
        $fields = 'id, name';
        if (array_key_exists('defaultTime', $details)) {
            $fields .= ', '.$details['defaultTime'].' as due';
        }

        // Check if this type of module is used in the course, gather instance info
        $records = $DB->get_records($module, array('course'=>$COURSE->id), '', $fields);
        foreach ($records as $record) {

            // Is the module being monitored?
            if (progress_default_value($config->{'monitor_'.$module.$record->id}, 0)==1) {
                $numevents++;

                // Check the time the module is due
                if (
                    isset($details['defaultTime']) &&
                    $record->due != 0 &&
                    progress_default_value($config->{'locked_'.$module.$record->id}, 0)
                ) {
                    $expected = progress_default_value($record->due);
                }
                else {
                    $expected = $config->{'date_time_'.$module.$record->id};
                }

                // Get the course module info
                $coursemodule = get_coursemodule_from_instance($module, $record->id, $COURSE->id);

                // Check if the module is visible, and if so, keep a record for it
                if ($coursemodule->visible==1) {
                    $events[] = array(
                        'expected'=>$expected,
                        'type'=>$module,
                        'id'=>$record->id,
                        'name'=>format_string($record->name),
                        'cmid'=>$coursemodule->id,
                    );
                }
            }
        }
    }

    if ($numevents==0) {
        return null;
    }

    // Sort by first value in each element, which is time due
    sort($events);

    return $events;
}

/**
 * Checked if a user has attempted/viewed/etc. an activity/resource
 *
 * @param string $type The plugin type name, eg forum
 * @param int    $id   The id of the instance of the plugin
 * @param int    $cmid The course module id for the instance
 * @param int    $user The user's id
 * @return bool
 */
function get_attempts($modules, $config, $events, $userid, $instance) {
    global $COURSE, $DB;
    $attempts = array();

    foreach ($events as $event) {
        $module = $modules[$event['type']];

        // If activity completion is used, check completions table
        if (isset($config->{'action_'.$event['type'].$event['id']}) &&
            $config->{'action_'.$event['type'].$event['id']}=='activity_completion'
        ) {
            $query = 'SELECT id
                        FROM {course_modules_completion}
                       WHERE userid='.$userid.'
                         AND coursemoduleid='.$event['cmid'];
        }

        // Determine the set action and develope a query
        else {
            $action = isset($config->{'action_'.$event['type'].$event['id']})?
                      $config->{'action_'.$event['type'].$event['id']}:
                      $details['defaultAction'];
            $targetstrings = array('#COURSEID#', '#USERID#', '#EVENTID#', '#CMID#');
            $replacements = array($COURSE->id, $userid, $event['id'], $event['cmid']);
            $query = str_replace($targetstrings, $replacements, $module['actions'][$action]);
        }

         // Check if the user has attempted the module
        $attempts[$event['type'].$event['id']] =
            $DB->record_exists_sql($query)?true:false;
    }
    return $attempts;
}

function progress_bar($modules, $config, $events, $userid, $instance, $attempts, $simple = false) {
    global $OUTPUT, $CFG;

    $now = time();
    $numevents = count($events);
    $dateformat = get_string('date_format', 'block_progress');

    $content = '<table class="progressBarProgressTable" cellpadding="0" cellspacing="0">';

    // Place now arrow
    if ($config->displayNow==1 && !$simple) {

        // Find where to put now arrow
        $nowpos = 0;
        while ($nowpos<$numevents && $now>$events[$nowpos]['expected']) {
            $nowpos++;
        }

        $content .= '<tr>';

        $nowstring = get_string('now_indicator', 'block_progress');
        if ($nowpos<$numevents/2) {
            for ($i=0; $i<$nowpos; $i++) {
                $content .= '<td class="progressBarHeader">&nbsp;</td>';
            }
            $content .= '<td colspan="'.($numevents-$nowpos).
                '" style="text-align:left;" class="progressBarHeader">';
            $content .= $OUTPUT->pix_icon('left', $nowstring, 'block_progress');
            $content .= $nowstring.'</td>';
        }
        else {
            $content .= '<td colspan='.($nowpos).' '.
                                    'style="text-align:right;" class="progressBarHeader">';
            $content .= $nowstring;
            $content .= $OUTPUT->pix_icon('right', $nowstring, 'block_progress');
            $content .= '</td>';
            for ($i=$nowpos; $i<$numevents; $i++) {
                $content .= '<td class="progressBarHeader">&nbsp;</td>';
            }
        }
        $content .= '</tr>';
    }

    // Start progress bar
    $width = 100/$numevents;
    $content .= '<tr>';
    foreach ($events as $event) {
        $attempted = $attempts[$event['type'].$event['id']];

	// A block in the progress bar
        $content .= '<td class="progressBarCell" width="'.$width.'%"';
        $content .= ' onclick="document.location=\''.$CFG->wwwroot.'/mod/';
        $content .= $event['type'].'/view.php?'.'id='.$event['cmid'].'\';"';
        $content .= ' onmouseover="M.block_progress.showInfo(\''.$event['type'].'\', \'';
        $content .= get_string($event['type'], 'block_progress').'\', \'';
        $content .= $event['cmid'].'\', \''.addSlashes($event['name']).'\', \'';
        $content .= get_string($config->{'action_'.$event['type'].$event['id']}, 'block_progress');
        $content .= '\', \'';
        $content .= userdate($event['expected'], $dateformat, $CFG->timezone);
        $content .= '\', \'';
        $content .= $instance.'\', \''.$userid.'\', \''.($attempted?'tick':'cross').'\');"';
        $content .= ' style="background-color:';
        if ($attempted) {
            $content.= get_string('attempted_colour', 'block_progress').'" />';
            $content.= $OUTPUT->pix_icon(
                           isset($config->progressBarIcons) && $config->progressBarIcons==1 ?
                           'tick' : 'blank', '', 'block_progress');
        }
        else if ($event['expected'] < $now) {
            $content .= get_string('notAttempted_colour', 'block_progress').'" />';
            $content .= $OUTPUT->pix_icon(
                            isset($config->progressBarIcons) && $config->progressBarIcons==1 ?
                            'cross':'blank', '', 'block_progress');
        }
        else {
            $content .= get_string('futureNotAttempted_colour', 'block_progress').'" />';
            $content .= $OUTPUT->pix_icon('blank', '', 'block_progress');
        }
        $content .= '</a></td>';
    }
    $content .= '</tr></table>';

    // Add the info box below the table
    $content .= '<div class="progressEventInfo" id="progressBarInfo'.$instance.'user'.$userid.'">';
    if (!$simple) {
        $content .= get_string('mouse_over_prompt', 'block_progress');
    }
    $content .= '</div>';

    return $content;
}
