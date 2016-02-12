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

define('USER_SMALL_CLASS', 20);   // Below this is considered small.
define('USER_LARGE_CLASS', 200);  // Above this is considered large.
define('DEFAULT_PAGE_SIZE', 20);
define('SHOW_ALL_PAGE_SIZE', 5000);

// Gather form data.
$id       = required_param('progressbarid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);
$page     = optional_param('page', 0, PARAM_INT); // Which page to show.
$perpage  = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT); // How many per page.
$group    = optional_param('group', 0, PARAM_INT); // Group selected.

// Determine course and context.
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = block_progress_get_course_context($courseid);

// Get specific block config and context.
$progressblock = $DB->get_record('block_instances', array('id' => $id), '*', MUST_EXIST);
$progressconfig = unserialize(base64_decode($progressblock->configdata));
$progressblockcontext = block_progress_get_block_context($id);

// Set up page parameters.
$PAGE->set_course($course);
$PAGE->requires->css('/blocks/progress/styles.css');
$PAGE->set_url(
    '/blocks/progress/overview.php',
    array(
        'progressbarid' => $id,
        'courseid' => $courseid,
        'page' => $page,
        'perpage' => $perpage,
    )
);
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
$modules = block_progress_modules_in_use($course->id);
if (empty($modules)) {
    echo get_string('no_events_config_message', 'block_progress');
    echo $OUTPUT->container_end();
    echo $OUTPUT->footer();
    die();
}

// Check if activities/resources have been selected in config.
$events = block_progress_event_information($progressconfig, $modules, $course->id);
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
$groupselected = 0;
$groupuserid = $USER->id;
if (has_capability('moodle/site:accessallgroups', $context)) {
    $groupuserid = 0;
}
$groupids = array();
$groups = groups_get_all_groups($course->id, $groupuserid);
if (!empty($groups)) {
    $groupstodisplay = array(0 => get_string('allparticipants'));
    foreach ($groups as $groupid => $groupobject) {
        $groupstodisplay[$groupid] = $groupobject->name;
        $groupids[] = $groupid;
    }
    if (!in_array($group, $groupids)) {
        $group = 0;
    }
    echo get_string('groupsvisible');
    echo $OUTPUT->single_select($PAGE->url, 'group', $groupstodisplay, $group);
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
$groupjoin = '';
if ($group && $group != 0) {
    $groupjoin = 'JOIN {groups_members} g ON (g.groupid = :groupselected AND g.userid = u.id)';
    $params['groupselected'] = $group;
} else if ($groupuserid != 0 && !empty($groupids)) {
    $groupjoin = 'JOIN {groups_members} g ON (g.groupid IN ('.implode(',', $groupids).') AND g.userid = u.id)';
}

// Get the list of users enrolled in the course.
$picturefields = user_picture::fields('u');
$sql = "SELECT DISTINCT $picturefields, COALESCE(l.timeaccess, 0) AS lastonlinetime
          FROM {user} u
          JOIN {role_assignments} a ON (a.contextid = :contextid AND a.userid = u.id $rolewhere)
          $groupjoin
     LEFT JOIN {user_lastaccess} l ON (l.courseid = :courseid AND l.userid = u.id)";
$params['contextid'] = $context->id;
$params['courseid'] = $course->id;
$userrecords = $DB->get_records_sql($sql, $params);
if (get_config('block_progress', 'showinactive') != 1) {
    extract_suspended_users($context, $userrecords);
}
$userids = array_keys($userrecords);
$users = array_values($userrecords);
$numberofusers = count($users);
$paged = $numberofusers > $perpage;
if (!$paged) {
    $page = 0;
}

// Form for messaging selected participants.
$formattributes = array('action' => $CFG->wwwroot.'/user/action_redir.php', 'method' => 'post', 'id' => 'participantsform');
echo html_writer::start_tag('form', $formattributes);
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'returnto', 'value' => s($PAGE->url->out(false))));

// Setup submissions table.
$table = new flexible_table('mod-block-progress-overview');
$table->pagesize($perpage, $numberofusers);
$tablecolumns = array('select', 'picture', 'fullname', 'lastonline', 'progressbar', 'progress');
$table->define_columns($tablecolumns);
$tableheaders = array(
                  '',
                  '',
                  get_string('fullname'),
                  get_string('lastonline', 'block_progress'),
                  get_string('progressbar', 'block_progress'),
                  get_string('progress', 'block_progress')
                );
$table->define_headers($tableheaders);
$table->sortable(true);

$table->set_attribute('class', 'overviewTable');
$table->column_style_all('padding', '5px');
$table->column_style_all('text-align', 'left');
$table->column_style_all('vertical-align', 'middle');
$table->column_style('select', 'text-align', 'right');
$table->column_style('select', 'padding', '5px 0 5px 5px');
$table->column_style('progressbar', 'width', '200px');
$table->column_style('progress', 'text-align', 'center');

$table->no_sorting('select');
$select = '';
$table->no_sorting('picture');
$table->no_sorting('progressbar');
if ($paged) {
    $table->no_sorting('progress');
}
$table->define_baseurl($PAGE->url);
$table->setup();

// Sort the users (except by progress).
$sort = $table->get_sql_sort();
$sortbyprogress = strncmp($sort, 'progress', 8) == 0;
if (!$sort || ($paged && $sortbyprogress)) {
     $sort = 'lastname DESC';
}
if (!$sortbyprogress) {
    usort($users, 'block_progress_compare_rows');
}

// Get range of students for page.
$startuser = $page * $perpage;
$enduser = ($startuser + $perpage > $numberofusers) ? $numberofusers : ($startuser + $perpage);

// Build table of progress bars as they are marked.
$rows = array();
for ($i = $startuser; $i < $enduser; $i++) {
    if ($CFG->enablenotes || $CFG->messaging) {
        $selectattributes = array('type' => 'checkbox', 'class' => 'usercheckbox', 'name' => 'user'.$users[$i]->id);
        $select = html_writer::empty_tag('input', $selectattributes);
    }
    $picture = $OUTPUT->user_picture($users[$i], array('course' => $course->id));
    $namelink = html_writer::link($CFG->wwwroot.'/user/view.php?id='.$users[$i]->id.'&course='.$course->id, fullname($users[$i]));
    if (empty($users[$i]->lastonlinetime)) {
        $lastonline = get_string('never');
    } else {
        $lastonline = userdate($users[$i]->lastonlinetime);
    }
    $userevents = block_progress_filter_visibility($events, $users[$i]->id, $context, $course);
    if (!empty($userevents)) {
        $attempts = block_progress_attempts($modules, $progressconfig, $userevents, $users[$i]->id, $course->id);
        $progressbar = block_progress_bar($modules, $progressconfig, $userevents, $users[$i]->id, $progressblock->id, $attempts,
            $course->id, true);
        $progressvalue = block_progress_percentage($userevents, $attempts, true);
        $progress = $progressvalue.'%';
    } else {
        $progressbar = get_string('no_visible_events_message', 'block_progress');
        $progressvalue = 0;
        $progress = '?';
    }

    $rows[] = array(
        'firstname' => $users[$i]->firstname,
        'lastname' => strtoupper($users[$i]->lastname),
        'select' => $select,
        'picture' => $picture,
        'fullname' => $namelink,
        'lastonlinetime' => $users[$i]->lastonlinetime,
        'lastonline' => $lastonline,
        'progressbar' => $progressbar,
        'progressvalue' => $progressvalue,
        'progress' => $progress
    );
}

// Build the table content and output.
if ($sortbyprogress) {
    usort($rows, 'block_progress_compare_rows');
}
if ($numberofusers > 0) {
    foreach ($rows as $row) {
        $table->add_data(array($row['select'], $row['picture'],
            $row['fullname'], $row['lastonline'],
            $row['progressbar'], $row['progress']));
    }
}
$table->print_html();

$perpageurl = clone($PAGE->url);
if ($paged) {
    $perpageurl->param('perpage', SHOW_ALL_PAGE_SIZE);
    echo $OUTPUT->container(html_writer::link($perpageurl, get_string('showall', '', $numberofusers)), array(), 'showall');
} else if ($numberofusers > DEFAULT_PAGE_SIZE) {
    $perpageurl->param('perpage', DEFAULT_PAGE_SIZE);
    echo $OUTPUT->container(html_writer::link($perpageurl, get_string('showperpage', '', DEFAULT_PAGE_SIZE)), array(), 'showall');
}

// Output messaging controls.
if ($CFG->enablenotes || $CFG->messaging) {
    echo html_writer::start_tag('div', array('class' => 'buttons'));
    echo html_writer::empty_tag('input', array('type' => 'button', 'id' => 'checkall', 'value' => get_string('selectall')));
    echo html_writer::empty_tag('input', array('type' => 'button', 'id' => 'checknone', 'value' => get_string('deselectall')));
    $displaylist = array();
    if (!empty($CFG->messaging) && has_capability('moodle/course:bulkmessaging', $context)) {
        $displaylist['messageselect.php'] = get_string('messageselectadd');
    }
    if (!empty($CFG->enablenotes) && has_capability('moodle/notes:manage', $context)) {
        $displaylist['addnote.php'] = get_string('addnewnote', 'notes');
        $displaylist['groupaddnote.php'] = get_string('groupaddnewnote', 'notes');
    }
    echo html_writer::tag('label', get_string("withselectedusers"), array('for' => 'formactionid'));
    echo html_writer::select($displaylist, 'formaction', '', array('' => 'choosedots'), array('id' => 'formactionid'));
    echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'id', 'value' => $course->id));
    echo html_writer::start_tag('noscript', array('style' => 'display:inline;'));
    echo html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('ok')));
    echo html_writer::end_tag('noscript');
    echo $OUTPUT->help_icon('withselectedusers');
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('form');
}

// Organise access to JS for messaging.
$module = array('name' => 'core_user', 'fullpath' => '/user/module.js');
$PAGE->requires->js_init_call('M.core_user.init_participation', null, false, $module);

// Organise access to JS for progress bars.
$jsmodule = array('name' => 'block_progress', 'fullpath' => '/blocks/progress/module.js');
$arguments = array(array($progressblock->id), $userids);
$PAGE->requires->js_init_call('M.block_progress.init', $arguments, false, $jsmodule);

echo $OUTPUT->container_end();
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
        if (is_array($a)) {
            $first = $a[$aspect];
            $second = $b[$aspect];
        } else {
            $first = $a->$aspect;
            $second = $b->$aspect;
        }

        if (preg_match('/name/', $aspect)) {
            $first = strtolower($first);
            $second = strtolower($second);
        }

        if ($first < $second) {
            return $ascdesc == 'ASC' ? 1 : -1;
        }
        if ($first > $second) {
            return $ascdesc == 'ASC' ? -1 : 1;
        }
    }

    // If previous ordering fails, consider values equal.
    return 0;
}
