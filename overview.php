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
 * Progress Bar block overview page
 *
 * @package    contrib
 * @subpackage block_progress
 * @copyright  2010 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Include required files.
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/blocks/progress/lib.php');
require_once($CFG->libdir.'/tablelib.php');

// Gather form data.
$id       = required_param('progressbarid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

// Determine course and context.
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
if (class_exists('context_course')) {
    $context = context_course::instance($courseid);
} else {
    $context = get_context_instance(CONTEXT_COURSE, $courseid);
}

// Get specific block config and context.
$progressblock = $DB->get_record('block_instances', array('id' => $id), '*', MUST_EXIST);
$progressconfig = unserialize(base64_decode($progressblock->configdata));
if (class_exists('context_block')) {
    $progressblockcontext = context_block::instance($id);
} else {
    $progressblockcontext = get_context_instance(CONTEXT_BLOCK, $courseid);
}

// Set up page parameters.
$PAGE->set_course($course);
$PAGE->requires->css('/blocks/progress/styles.css');
$PAGE->set_url('/blocks/progress/overview.php', array('progressbarid' => $id, 'courseid' => $courseid));
$PAGE->set_context($context);
$title = get_string('overview', 'block_progress');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->navbar->add($title);
$PAGE->set_pagelayout('standard');

// Check user is logged in and capable of grading.
require_login($course, false);
require_capability('block/progress:overview', $progressblockcontext);

// Start page output.
echo $OUTPUT->header();
echo $OUTPUT->heading($title, 2);
echo $OUTPUT->container_start('block_progress');

// Get the modules to check progress on.
$modules = block_progress_modules_in_use();
if (empty($modules)) {
    echo get_string('no_events_config_message', 'block_progress');
    echo $OUTPUT->container_end();
    echo $OUTPUT->footer();
    die();
}

// Check if activities/resources have been selected in config.
$events = block_progress_event_information($progressconfig, $modules);
if ($events == null) {
    echo get_string('no_events_message', 'block_progress');
    echo $OUTPUT->container_end();
    echo $OUTPUT->footer();
    die();
}
if (empty($events)) {
    echo get_string('no_visible_events_message', 'block_progress');
    echo $OUTPUT->container_end();
    echo $OUTPUT->footer();
    die();
}
$numevents = count($events);

// Determine if a role has been selected.
$sql = "SELECT DISTINCT r.id, r.name
          FROM {role} r, {role_assignments} a
         WHERE a.contextid = :contextid
           AND r.id = a.roleid
           AND r.shortname = :shortname";
$params = array('contextid' => $context->id, 'shortname' => 'student');
$studentrole = $DB->get_record_sql($sql, $params);
if ($studentrole) {
    $studentroleid = $studentrole->id;
} else {
    $studentroleid = 0;
}
$roleselected = optional_param('role', $studentroleid, PARAM_INT);
$rolewhere = $roleselected != 0 ? "AND a.roleid = $roleselected" : '';

// Output group selector if there are groups in the course.
echo $OUTPUT->container_start('progressoverviewmenus');
$groupuserid = 0;
if (!has_capability('moodle/site:accessallgroups', $context)) {
    $groupuserid = $USER->id;
}
$groups = groups_get_all_groups($course->id);
if (!empty($groups)) {
    $course->groupmode = 1;
    groups_print_course_menu($course, $PAGE->url);
}

// Output the roles menu.
$sql = "SELECT DISTINCT r.id, r.name, r.shortname
          FROM {role} r, {role_assignments} a
         WHERE a.contextid = :contextid
           AND r.id = a.roleid";
$params = array('contextid' => $context->id);
$roles = role_fix_names($DB->get_records_sql($sql, $params), $context);
$rolestodisplay = array(0 => get_string('allparticipants'));
foreach ($roles as $role) {
    $rolestodisplay[$role->id] = $role->localname;
}
echo '&nbsp;'.get_string('role');
echo $OUTPUT->single_select($PAGE->url, 'role', $rolestodisplay, $roleselected);
echo $OUTPUT->container_end();

// Apply group restrictions.
$params = array();
$groupwhere = '';
$groupsfrom = '';
$groupselected = groups_get_course_group($course);
if ($groupselected && $groupselected != 0) {
    $groupsfrom = ', {groups_members} g ';
    $groupwhere = " AND g.groupid = :groupselected AND g.userid = u.id";
    $params['groupselected'] = $groupselected;
}

// Get the list of users enrolled in the course.
$picturefields = user_picture::fields('u');
$sql = "SELECT DISTINCT $picturefields, u.lastaccess
         FROM {user} u, {role_assignments} a $groupsfrom
        WHERE a.contextid = :contextid
          AND a.userid = u.id
          $rolewhere
          $groupwhere";
$params['contextid'] = $context->id;
$users = array_values($DB->get_records_sql($sql, $params));
$numberofusers = count($users);

// Setup submissions table.
$table = new flexible_table('mod-block-progress-overview');
$tablecolumns = array('picture', 'fullname', 'lastonline', 'progressbar', 'progress');
$table->define_columns($tablecolumns);
$tableheaders = array(
                  '',
                  get_string('fullname'),
                  get_string('lastonline', 'block_progress'),
                  get_string('progressbar', 'block_progress'),
                  get_string('progress', 'block_progress')
                );
$table->define_headers($tableheaders);
$table->sortable(true);

$table->set_attribute('class', 'generalbox');
$table->column_style_all('padding', '5px 10px');
$table->column_style_all('text-align', 'left');
$table->column_style_all('vertical-align', 'middle');
$table->column_style('progressbar', 'width', '200px');
$table->column_style('progress', 'text-align', 'center');

$table->no_sorting('picture');
$table->no_sorting('progressbar');
$table->define_baseurl($PAGE->url);
$table->setup();

// Build table of progress bars as they are marked.
for ($i = 0; $i < $numberofusers; $i++) {
    $picture = $OUTPUT->user_picture($users[$i], array('course' => $course->id));
    $name = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$users[$i]->id.'&course='.
            $course->id.'">'.fullname($users[$i]).'</a>';
    if ($users[$i]->lastaccess == 0) {
        $lastonline = get_string('never');
    } else {
        $lastonline = userdate($users[$i]->lastaccess);
    }
    $attempts = block_progress_attempts($modules, $progressconfig, $events, $users[$i]->id, $course->id);
    $progressbar = block_progress_bar($modules, $progressconfig, $events, $users[$i]->id, $course->id, $attempts,
                                true);
    $progressvalue = block_progress_percentage($events, $attempts);
    $progress = $progressvalue.'%';

    $rows[] = array(
        'firstname' => $users[$i]->firstname,
        'lastname' => strtoupper($users[$i]->lastname),
        'picture' => $picture,
        'fullname' => $name,
        'lastonlinetime' => $users[$i]->lastaccess,
        'lastonline' => $lastonline,
        'progressbar' => $progressbar,
        'progressvalue' => $progressvalue,
        'progress' => $progress
    );
}

// Build the table content and output.
if (!$sort = $table->get_sql_sort()) {
     $sort = 'lastname DESC';
}
if ($numberofusers > 0) {
    usort($rows, 'block_progress_compare_rows');
    foreach ($rows as $row) {
        $table->add_data(array($row['picture'], $row['fullname'], $row['lastonline'],
            $row['progressbar'], $row['progress']));
    }
}
$table->print_initials_bar();
$table->print_html();
echo $OUTPUT->container_end();

// Organise access to JS.
$jsmodule = array(
    'name' => 'block_progress',
    'fullpath' => '/blocks/progress/module.js',
    'requires' => array(),
    'strings' => array(
        array('time_expected', 'block_progress'),
    ),
);
$arguments = array($CFG->wwwroot, array_keys($modules));
$PAGE->requires->js_init_call('M.block_progress.init', $arguments, false, $jsmodule);

echo $OUTPUT->footer();

/**
 * Compares two table row elements for ordering.
 *
 * @param  mixed $a element containing name, online time and progress info
 * @param  mixed $b element containing name, online time and progress info
 * @return order of pair expressed as -1, 0, or 1
 */
function block_progress_compare_rows($a, $b) {
    global $sort;

    // Process each of the one or two orders.
    $orders = explode(',', $sort);
    foreach ($orders as $order) {

        // Extract the order information.
        $orderelements = explode(' ', trim($order));
        $aspect = $orderelements[0];
        $ascdesc = $orderelements[1];

        // Compensate for presented vs actual.
        switch ($aspect) {
            case 'name':
                $aspect = 'lastname';
                break;
            case 'lastonline':
                $aspect = 'lastonlinetime';
                break;
            case 'progress':
                $aspect = 'progressvalue';
                break;
        }

        // Check of order can be established.
        if ($a[$aspect] < $b[$aspect]) {
            return $ascdesc == 'ASC'?1:-1;
        }
        if ($a[$aspect] > $b[$aspect]) {
            return $ascdesc == 'ASC'?-1:1;
        }
    }

    // If previous ordering fails, consider values equal.
    return 0;
}
