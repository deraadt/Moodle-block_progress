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

// Include required files
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/blocks/progress/lib.php');
require_once($CFG->libdir.'/tablelib.php');

// Global variables needed
global $DB, $PAGE, $OUTPUT;

// Gather form data
$id       = required_param('id', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

// Determine course and context
$course   = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context  = get_context_instance(CONTEXT_COURSE, $courseid);

// Set up page parameters
$PAGE->set_course($course);
$PAGE->requires->css('/blocks/progress/styles.css');
$PAGE->set_url('/blocks/progress/overview.php', array('id'=>$id, 'courseid'=>$courseid));
$PAGE->set_context($context);
$title = get_string('overview', 'block_progress');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->navbar->add($title);
$PAGE->set_pagelayout('standard');

// Check user is logged in and capable of grading
require_login($courseid, false);
require_capability('block/progress:overview', $context);

// Get specific block config
$block = $DB->get_record('block_instances', array('id' => $id));
$config = unserialize(base64_decode($block->configdata));

// Start page output
echo $OUTPUT->header();
echo $OUTPUT->heading($title, 2);
echo HTML_WRITER::start_tag('div', array('class' => 'block_progress'));

// Get the modules to check progress on
$modules = modules_in_use();
if (empty($modules)) {
    echo get_string('no_events_config_message', 'block_progress');
    echo $OUTPUT->footer();
    die();
}

// Check if activities/resources have been selected in config
$events = event_information($config, $modules);
if ($events==null) {
    echo get_string('no_events_message', 'block_progress');
    echo $OUTPUT->footer();
    die();
}
if (empty($events)) {
    echo get_string('no_visible_events_message', 'block_progress');
    echo $OUTPUT->footer();
    die();
}
$numevents = count($events);

// Get the list of users enrolled in the course
$sql = 'SELECT u.id, firstname, lastname, lastaccess, picture, imagealt, email '.
       'FROM {role_assignments} r, {user} u '.
       'WHERE r.contextid = '.$context->id.' '.
       'AND r.userid = u.id';
$users = array_values($DB->get_records_sql($sql));
$numberofusers = count($users);

// Setup submissions table
$table = new flexible_table('mod-block-progress-overview');
$tablecolumns = array('picture', 'name', 'lastonline', 'progressbar', 'progress');
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
$table->pageable(false);
$table->collapsible(false);
$table->initialbars(true);
$table->collapsible(true);

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

// Build table of progress bars as they are marked
for ($i=0; $i<$numberofusers; $i++) {
    $picture = $OUTPUT->user_picture($users[$i], array('course'=>$course->id));
    $name = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$users[$i]->id.'&course='.
            $course->id.'">'.fullname($users[$i]).'</a>';
    if ($users[$i]->lastaccess == 0) {
        $lastonline = get_string('never');
    }
    else {
        $lastonline = userdate($users[$i]->lastaccess);
    }
    $attempts = get_attempts($modules, $config, $events, $users[$i]->id, $course->id);
    $progressbar = progress_bar($modules, $config, $events, $users[$i]->id, $course->id, $attempts,
                                true);
    $attemptcount = 0;
    foreach ($events as $event) {
        if ($attempts[$event['type'].$event['id']]==1) {
            $attemptcount++;
        }
    }
    $progressvalue = $attemptcount==0?0:$attemptcount/$numevents;
    $progress = (int)($progressvalue*100).'%';

    $rows[] = array(
        'firstname'=>$users[$i]->firstname,
        'lastname'=>strtoupper($users[$i]->lastname),
        'picture'=>$picture,
        'name'=>$name,
        'lastonlinetime'=>$users[$i]->lastaccess,
        'lastonline'=>$lastonline,
        'progressbar'=>$progressbar,
        'progressvalue'=>$progressvalue,
        'progress'=>$progress
    );
}

// Build the table content and output
if (!$sort = $table->get_sql_sort()) {
     $sort = 'name DESC';
}
if ($numberofusers > 0) {
    usort($rows, 'compare_rows');

    foreach ($rows as $row) {
        $table->add_data(array($row['picture'], $row['name'], $row['lastonline'],
            $row['progressbar'], $row['progress']));
    }
}
$table->print_html();
echo HTML_WRITER::end_tag('div');

// Organise access to JS
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
 * Compares two table row elements for ordering
 *
 * @param  mixed $a element containing name, online time and progress info
 * @param  mixed $b element containing name, online time and progress info
 * @return order of pair expressed as -1, 0, or 1
 */
function compare_rows ($a, $b) {
    global $sort;

    // Process each of the one or two orders
    $orders = explode(',', $sort);
    foreach ($orders as $order) {

        // Extract the order information
        $orderelements = explode(' ', trim($order));
        $aspect = $orderelements[0];
        $ascdesc = $orderelements[1];

        // Compensate for presented vs actual
        switch ($aspect) {
            case 'name':
                $aspect='lastname';
                break;
            case 'lastonline':
                $aspect='lastonlinetime';
                break;
            case 'progress':
                $aspect='progressvalue';
                break;
        }

        // Check of order can be established
        if ($a[$aspect]<$b[$aspect]) {
            return $ascdesc=='ASC'?1:-1;
        }
        if ($a[$aspect]>$b[$aspect]) {
            return $ascdesc=='ASC'?-1:1;
        }
    }

    // If previous ordering fails, consider values equal
    return 0;
}
