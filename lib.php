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
 * Progress Bar block common configuration and helper functions
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
 * Queries may include the following placeholders that are substituted when the query is run. Note
 * that each placeholder can only be used once in each query.
 *  :eventid (the id of the activity in the DB table that relates to it, eg., an assignment id)
 *  :cmid (the course module id that identifies the instance of the module within the course),
 *  :userid (the current user's id) and
 *  :courseid (the current course id)
 *
 * When you add a new module, you need to add a translation for it in the lang files.
 * If you add new action names, you need to add a translation for these in the lang files.
 *
 * Note: Activity completion is automatically available when enabled (sitewide setting) and set for
 * an activity.
 *
 * If you have added a new module to this array and think other's may benefit from the query you
 * have created, please share it by sending it to michaeld@moodle.com
 * ================================================================================================
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
    global $DB;

    return array(
        'assign' => array(
            'defaultTime'=>'duedate',
            'actions'=>array(
                'submitted'    => "SELECT id
                                     FROM {assign_submission}
                                    WHERE assignment = :eventid
                                      AND userid = :userid
                                      AND status = 'submitted'",
                'marked'       => "SELECT g.rawgrade
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'assign'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND g.finalgrade IS NOT NULL",
                'passed'       => "SELECT g.rawgrade
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'assign'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND g.finalgrade >= i.gradepass"
            ),
            'defaultAction' => 'submitted'
        ),
        'assignment' => array(
            'defaultTime'=>'timedue',
            'actions'=>array(
                'submitted'    => "SELECT id
                                     FROM {assignment_submissions}
                                    WHERE assignment = :eventid
                                      AND userid = :userid
                                      AND (
                                          numfiles >= 1
                                          OR {$DB->sql_compare_text('data2')} <> ''
                                      )",
                'marked'       => "SELECT g.rawgrade
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'assignment'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND g.finalgrade IS NOT NULL",
                'passed'       => "SELECT g.rawgrade
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'assignment'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND g.finalgrade >= i.gradepass"
            ),
            'defaultAction' => 'submitted'
        ),
        'bigbluebuttonbn' => array(
            'defaultTime'=>'timedue',
            'actions'=>array(
                'viewed'       => "SELECT id
                                     FROM {log}
                                    WHERE course = :courseid
                                      AND module = 'bigbluebuttonbn'
                                      AND action = 'view'
                                      AND cmid = :cmid
                                      AND userid = :userid"
            ),
            'defaultAction' => 'viewed'
        ),
        'recordingsbn' => array(
            'actions'=>array(
                'viewed'       => "SELECT id
                                     FROM {log}
                                    WHERE course = :courseid
                                      AND module = 'recordingsbn'
                                      AND action = 'view'
                                      AND cmid = :cmid
                                      AND userid = :userid"
            ),
            'defaultAction' => 'viewed'
        ),
        'book' => array(
            'actions'=>array(
                'viewed'       => "SELECT id
                                     FROM {log}
                                    WHERE course = :courseid
                                      AND module = 'book'
                                      AND action = 'view'
                                      AND cmid = :cmid
                                      AND userid = :userid"
            ),
            'defaultAction' => 'viewed'
        ),
        'certificate' => array(
            'actions'=>array(
                'awarded'    => "SELECT id
                                   FROM {certificate_issues}
                                  WHERE certificateid = :eventid
                                    AND userid = :userid"
            ),
            'defaultAction' => 'awarded'
        ),
        'chat' => array(
            'actions'=>array(
                'posted_to'    => "SELECT id
                                     FROM {chat_messages}
                                    WHERE chatid = :eventid
                                      AND userid = :userid"
            ),
            'defaultAction' => 'posted_to'
        ),
        'choice' => array(
            'defaultTime'=>'timeclose',
            'actions'=>array(
                'answered'     => "SELECT id
                                     FROM {choice_answers}
                                    WHERE choiceid = :eventid
                                      AND userid = :userid"
            ),
            'defaultAction' => 'answered'
        ),
        'data' => array(
            'defaultTime'=>'timeviewto',
            'actions'=>array(
                'viewed'       => "SELECT id
                                     FROM {log}
                                    WHERE course = :courseid
                                      AND module = 'data'
                                      AND action = 'view'
                                      AND cmid = :cmid
                                      AND userid = :userid"
            ),
            'defaultAction' => 'viewed'
        ),
        'feedback' => array(
            'defaultTime'=>'timeclose',
            'actions'=>array(
                'responded_to' => "SELECT id
                                     FROM {feedback_completed}
                                    WHERE feedback = :eventid
                                      AND userid = :userid"
            ),
            'defaultAction' => 'responded_to'
        ),
        'resource' => array(  // AKA file
            'actions'=>array(
                'viewed'       => "SELECT id
                                     FROM {log}
                                    WHERE course = :courseid
                                      AND module = 'resource'
                                      AND action = 'view'
                                      AND cmid = :cmid
                                      AND userid = :userid"
            ),
            'defaultAction' => 'viewed'
        ),
        'flashcardtrainer' => array(
            'actions'=>array(
                'viewed'       => "SELECT id
                                     FROM {log}
                                    WHERE course = :courseid
                                      AND module = 'flashcardtrainer'
                                      AND action = 'view'
                                      AND cmid = :cmid
                                      AND userid = :userid"
            ),
            'defaultAction' => 'viewed'
        ),
        'folder' => array(
            'actions'=>array(
                'viewed'       => "SELECT id
                                     FROM {log}
                                    WHERE course = :courseid
                                      AND module = 'folder'
                                      AND action = 'view'
                                      AND cmid = :cmid
                                      AND userid = :userid"
            ),
            'defaultAction' => 'viewed'
        ),
        'forum' => array(
            'defaultTime'=>'assesstimefinish',
            'actions'=>array(
                'posted_to'    => "SELECT id
                                     FROM {forum_posts}
                                    WHERE userid = :userid AND discussion IN (
                                          SELECT id
                                            FROM {forum_discussions}
                                           WHERE forum = :eventid
                                    )"
            ),
            'defaultAction' => 'posted_to'
        ),
        'glossary' => array(
            'actions'=>array(
                'viewed'       => "SELECT id
                                     FROM {log}
                                   WHERE course = :courseid
                                     AND module = 'glossary'
                                     AND action = 'view'
                                     AND cmid = :cmid
                                     AND userid = :userid"
            ),
            'defaultAction' => 'viewed'
        ),
        'hotpot' => array(
            'defaultTime'=>'timeclose',
            'actions'=>array(
                'attempted'    => "SELECT id
                                    FROM {hotpot_attempts}
                                   WHERE hotpotid = :eventid
                                     AND userid = :userid",
                'finished'     => "SELECT id
                                     FROM {hotpot_attempts}
                                    WHERE hotpotid = :eventid
                                      AND userid = :userid
                                      AND timefinish <> 0",
            ),
            'defaultAction' => 'finished'
        ),
        'imscp' => array(
            'actions'=>array(
                'viewed'       => "SELECT id
                                    FROM {log}
                                   WHERE course = :courseid
                                     AND module = 'imscp'
                                     AND action = 'view'
                                     AND cmid = :cmid
                                     AND userid = :userid"
            ),
            'defaultAction' => 'viewed'
        ),
        'journal' => array(
            'actions'=>array(
                'posted_to'    => "SELECT id
                                     FROM {journal_entries}
                                    WHERE journal = :eventid
                                      AND userid = :userid"
            ),
            'defaultAction' => 'posted_to'
        ),
        'lesson' => array(
            'defaultTime'=>'deadline',
            'actions'=>array(
                'attempted'    => "SELECT id
                                     FROM {lesson_attempts}
                                    WHERE lessonid = :eventid
                                      AND userid = :userid
                                UNION ALL
                                   SELECT id
                                     FROM {lesson_branch}
                                    WHERE lessonid = :eventid1
                                      AND userid = :userid1",
                'graded'       => "SELECT g.rawgrade
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'lesson'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND g.finalgrade IS NOT NULL"
            ),
            'defaultAction' => 'attempted'
        ),
        'page' => array(
            'actions'=>array(
                'viewed'       => "SELECT id
                                     FROM {log}
                                    WHERE course = :courseid
                                      AND module = 'page'
                                      AND action = 'view'
                                      AND cmid = :cmid
                                      AND userid = :userid"
            ),
            'defaultAction' => 'viewed'
        ),
        'quiz' => array(
            'defaultTime'=>'timeclose',
            'actions'=>array(
                'attempted'    => "SELECT id
                                     FROM {quiz_attempts}
                                    WHERE quiz = :eventid
                                      AND userid = :userid",
                'finished'     => "SELECT id
                                     FROM {quiz_attempts}
                                    WHERE quiz = :eventid
                                      AND userid = :userid
                                      AND timefinish <> 0",
                'graded'       => "SELECT g.rawgrade
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'quiz'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND g.finalgrade IS NOT NULL",
                'passed'       => "SELECT g.rawgrade
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'quiz'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND g.finalgrade >= i.gradepass"
            ),
            'defaultAction' => 'finished'
        ),
        'scorm' => array(
            'actions'=>array(
                'attempted'    => "SELECT id
                                     FROM {scorm_scoes_track}
                                    WHERE scormid = :eventid
                                      AND userid = :userid",
                'completed'    => "SELECT id
                                     FROM {scorm_scoes_track}
                                    WHERE scormid = :eventid
                                      AND userid = :userid
                                      AND element = 'cmi.core.lesson_status'
                                      AND {$DB->sql_compare_text('value')} = 'completed'",
                'passed'       => "SELECT id
                                     FROM {scorm_scoes_track}
                                    WHERE scormid = :eventid
                                      AND userid = :userid
                                      AND element = 'cmi.core.lesson_status'
                                      AND {$DB->sql_compare_text('value')} = 'passed'"
            ),
            'defaultAction' => 'attempted'
        ),
        'turnitintool' => array(
            'defaultTime'=>'defaultdtdue',
            'actions'=>array(
                'submitted'    => "SELECT id
                                     FROM {turnitintool_submissions}
                                    WHERE turnitintoolid = :eventid
                                      AND userid = :userid
                                      AND submission_score IS NOT NULL"
            ),
            'defaultAction' => 'submitted'
        ),
        'url' => array(
            'actions'=>array(
                'viewed'       => "SELECT id
                                     FROM {log}
                                    WHERE course = :courseid
                                      AND module = 'url'
                                      AND action = 'view'
                                      AND cmid = :cmid
                                      AND userid = :userid"
            ),
            'defaultAction' => 'viewed'
        ),
        'wiki' => array(
            'actions'=>array(
                'viewed'       => "SELECT id
                                     FROM {log}
                                    WHERE course = :courseid
                                      AND module = 'wiki'
                                      AND action = 'view'
                                      AND cmid = :cmid
                                      AND userid = :userid"
            ),
            'defaultAction' => 'viewed'
        ),
        'workshop' => array(
            'defaultTime'=>'assessmentend',
            'actions'=>array(
                'submitted'    => "SELECT id
                                     FROM {workshop_submissions}
                                    WHERE workshopid = :eventid
                                      AND authorid = :userid",
                'assessed'     => "SELECT s.id
                                     FROM {workshop_assessments} a, {workshop_submissions} s
                                    WHERE s.workshopid = :eventid
                                      AND s.id = a.submissionid
                                      AND a.reviewerid = :userid
                                      AND a.grade IS NOT NULL",
                'graded'       => "SELECT g.rawgrade
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'workshop'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND g.finalgrade IS NOT NULL"
            ),
            'defaultAction' => 'submitted'
        ),
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

    foreach ($modules as $module => $details) {
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
 * @return mixed   returns array of visible events monitored,
 *                 empty array if none of the events are visible,
 *                 null if all events are configured to "no" monitoring and
 *                 0 if events are available but no config is set
 */
function event_information($config, $modules) {
    global $COURSE, $DB;
    $dbmanager = $DB->get_manager(); // used to check if tables exist
    $events = array();
    $numevents = 0;
    $numeventsconfigured = 0;

    if(isset($config->orderby) && $config->orderby == 'orderbycourse') {
        $sections = $DB->get_records('course_sections', array('course'=>$COURSE->id), 'section', 'id,sequence');
        foreach ($sections as $section) {
            $section->sequence = explode(',', $section->sequence);
        }
    }

    // Check each known module (described in lib.php)
    foreach ($modules as $module => $details) {
        $fields = 'id, name';
        if (array_key_exists('defaultTime', $details)) {
            $fields .= ', '.$details['defaultTime'].' as due';
        }

        // Check if this type of module is used in the course, gather instance info
        $records = $DB->get_records($module, array('course'=>$COURSE->id), '', $fields);
        foreach ($records as $record) {

            // Is the module being monitored?
            if (isset($config->{'monitor_'.$module.$record->id})) {
                $numeventsconfigured++;
            }
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
                    $event = array(
                        'expected'=>$expected,
                        'type'=>$module,
                        'id'=>$record->id,
                        'name'=>format_string($record->name),
                        'cmid'=>$coursemodule->id,
                    );
                    if(isset($config->orderby) && $config->orderby == 'orderbycourse') {
                        $event['section'] = $coursemodule->section;
                        $event['position'] = array_search($coursemodule->id, $sections[$coursemodule->section]->sequence);
                    }
                    $events[] = $event;
                }
            }
        }
    }

    if ($numeventsconfigured==0) {
        return 0;
    }
    if ($numevents==0) {
        return null;
    }

    // Sort by first value in each element, which is time due
    if(isset($config->orderby) && $config->orderby == 'orderbycourse') {
        usort($events, 'compare_events');
    }
    else {
        sort($events);
    }
    return $events;
}

/**
 * Used to compare two activities/resources based on order on course page
 *
 * @param array $a array of event information
 * @param array $b array of event information
 * @return <0, 0 or >0 depending on order of activities/resources on course page
 */
function compare_events($a, $b) {
    if($a['section'] != $b['section']) {
        return $a['section'] - $b['section'];
    }
    else {
        return $a['position'] - $b['position'];
    }
}

/**
 * Checked if a user has attempted/viewed/etc. an activity/resource
 *
 * @param array    $modules The modules used in the course
 * @param stdClass $config  The blocks configuration settings
 * @param array    $events  The possible events that can occur for modules
 * @param int      $userid  The user's id
 * @return array   an describing the user's attempts based on module+instance identifiers
 */
function get_attempts($modules, $config, $events, $userid, $instance) {
    global $COURSE, $DB;
    $attempts = array();

    foreach ($events as $event) {
        $module = $modules[$event['type']];
        $uniqueid = $event['type'].$event['id'];

        // If activity completion is used, check completions table
        if (isset($config->{'action_'.$uniqueid}) &&
            $config->{'action_'.$uniqueid}=='activity_completion'
        ) {
            $query = 'SELECT id
                        FROM {course_modules_completion}
                       WHERE userid = :userid
                         AND coursemoduleid = :cmid
                         AND completionstate = 1';
        }

        // Determine the set action and develop a query
        else {
            $action = isset($config->{'action_'.$uniqueid})?
                      $config->{'action_'.$uniqueid}:
                      $module['defaultAction'];
            $query =  $module['actions'][$action];
        }
        $parameters = array('courseid' => $COURSE->id, 'courseid1' => $COURSE->id,
                            'userid' => $userid, 'userid1' => $userid,
                            'eventid' => $event['id'], 'eventid1' => $event['id'],
                            'cmid' => $event['cmid'], 'cmid1' => $event['cmid'],
                      );

         // Check if the user has attempted the module
        $attempts[$uniqueid] = $DB->record_exists_sql($query, $parameters)?true:false;
    }
    return $attempts;
}

/**
 * Draws a progress bar
 *
 * @param array    $modules  The modules used in the course
 * @param stdClass $config   The blocks configuration settings
 * @param array    $events   The possible events that can occur for modules
 * @param int      $userid   The user's id
 * @param int      instance  The block instance (incase more than one is being displayed)
 * @param array    $attempts The user's attempts on course activities
 * @param bool     $simple   Controls whether instructions are shown below a progress bar
 */
function progress_bar($modules, $config, $events, $userid, $instance, $attempts, $simple = false) {
    global $OUTPUT, $CFG;

    $now = time();
    $numevents = count($events);
    $dateformat = get_string('date_format', 'block_progress');
    $tableoptions = array('class' => 'progressBarProgressTable',
                          'cellpadding' => '0',
                          'cellspacing' => '0');
    $content = HTML_WRITER::start_tag('table', $tableoptions);

    // Place now arrow
    if ((!isset($config->orderby) || $config->orderby=='orderbytime') && $config->displayNow==1 && !$simple) {

        // Find where to put now arrow
        $nowpos = 0;
        while ($nowpos<$numevents && $now>$events[$nowpos]['expected']) {
            $nowpos++;
        }

        $content .= HTML_WRITER::start_tag('tr');
        $nowstring = get_string('now_indicator', 'block_progress');
        if ($nowpos<$numevents/2) {
            for ($i=0; $i<$nowpos; $i++) {
                $content .= HTML_WRITER::tag('td', '&nbsp;', array('class' => 'progressBarHeader'));
            }
            $celloptions = array('colspan' => $numevents-$nowpos,
                                 'class' => 'progressBarHeader',
                                 'style' => 'text-align:left;');
            $content .= HTML_WRITER::start_tag('td', $celloptions);
            $content .= $OUTPUT->pix_icon('left', $nowstring, 'block_progress');
            $content .= $nowstring;
            $content .= HTML_WRITER::end_tag('td');
        }
        else {
            $celloptions = array('colspan' => $nowpos,
                                 'class' => 'progressBarHeader',
                                 'style' => 'text-align:right;');
            $content .= HTML_WRITER::start_tag('td', $celloptions);
            $content .= $nowstring;
            $content .= $OUTPUT->pix_icon('right', $nowstring, 'block_progress');
            $content .= HTML_WRITER::end_tag('td');
            for ($i=$nowpos; $i<$numevents; $i++) {
                $content .= HTML_WRITER::tag('td', '&nbsp;', array('class' => 'progressBarHeader'));
            }
        }
        $content .= HTML_WRITER::end_tag('tr');
    }

    // Start progress bar
    $width = 100/$numevents;
    $content .= HTML_WRITER::start_tag('tr');
    foreach ($events as $event) {
        $attempted = $attempts[$event['type'].$event['id']];
        $action = isset($config->{'action_'.$event['type'].$event['id']})?
                  $config->{'action_'.$event['type'].$event['id']}:
                  $modules[$event['type']]['defaultAction'];

        // A cell in the progress bar
        $celloptions = array(
            'class' => 'progressBarCell',
            'width' => $width.'%',
            'onclick' => 'document.location=\''.$CFG->wwwroot.'/mod/'.$event['type'].
                '/view.php?id='.$event['cmid'].'\';',
            'onmouseover' => 'M.block_progress.showInfo('.
                '\''.$event['type'].'\', '.
                '\''.get_string($event['type'], 'block_progress').'\', '.
                '\''.$event['cmid'].'\', '.
                '\''.addslashes($event['name']).'\', '.
                '\''.get_string($action, 'block_progress').'\', '.
                '\''.addslashes(userdate($event['expected'], $dateformat, $CFG->timezone)).'\', '.
                '\''.$instance.'\', '.
                '\''.$userid.'\', '.
                '\''.($attempted?'tick':'cross').'\''.
                ');',
             'style' => 'background-color:');
        if ($attempted) {
            $celloptions['style'] .= get_string('attempted_colour', 'block_progress').';';
            $cellcontent = $OUTPUT->pix_icon(
                               isset($config->progressBarIcons) && $config->progressBarIcons==1 ?
                               'tick' : 'blank', '', 'block_progress');
        }
        else if ((!isset($config->orderby) || $config->orderby=='orderbytime') && $event['expected'] < $now) {
            $celloptions['style'] .= get_string('notAttempted_colour', 'block_progress').';';
            $cellcontent = $OUTPUT->pix_icon(
                               isset($config->progressBarIcons) && $config->progressBarIcons==1 ?
                               'cross':'blank', '', 'block_progress');
        }
        else {
            $celloptions['style'] .= get_string('futureNotAttempted_colour', 'block_progress').';';
            $cellcontent = $OUTPUT->pix_icon('blank', '', 'block_progress');
        }
        $content .= HTML_WRITER::tag('td', $cellcontent, $celloptions);
    }
    $content .= HTML_WRITER::end_tag('tr');
    $content .= HTML_WRITER::end_tag('table');

    // Add the info box below the table
    $divoptions = array('class' => 'progressEventInfo',
                        'id'=>'progressBarInfo'.$instance.'user'.$userid);
    $content .= HTML_WRITER::start_tag('div', $divoptions);
    if (!$simple) {
        if (isset($config->showpercentage) && $config->showpercentage==1) {
            $progress = get_progess_percentage($events, $attempts);
            $content .= get_string('progress', 'block_progress').': ';
            $content .= $progress.'%'.HTML_WRITER::empty_tag('br');
        }
        $content .= get_string('mouse_over_prompt', 'block_progress');
    }
    $content .= HTML_WRITER::end_tag('div');

    return $content;
}

/**
 * Calculates an overall percentage of progress
 *
 * @param array $events   The possible events that can occur for modules
 * @param array $attempts The user's attempts on course activities
 */
function get_progess_percentage($events, $attempts) {
    $attemptcount = 0;

    foreach ($events as $event) {
        if ($attempts[$event['type'].$event['id']]==1) {
            $attemptcount++;
        }
    }

    $progressvalue = $attemptcount==0?0:$attemptcount/count($events);

    return (int)($progressvalue*100);
}
