<?php

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . "/externallib.php");
require_once($CFG->dirroot.'/blocks/progress/lib.php');

class blocks_progress_external extends external_api
{

/** cinfo **/
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function cinfo_parameters()
    {
        return new external_function_parameters(
            array(
                'pbid' => new external_value(PARAM_INT, 'Progress Bar instance id'),
                'userid' => new external_value(PARAM_INT, 'userid'),
                'courseid' => new external_value(PARAM_INT, 'courseid')
            )
        );
    }

    /**
     * Returns Completion information monitored by a progress bar block
     * @return object array
     */
    public static function cinfo($pbid, $userid, $courseid)
    {
        global $CFG, $DB;

        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
        $context = block_progress_get_course_context($courseid);

        // Get specific block config and context.
        $progressblock = $DB->get_record('block_instances', array('id' => $pbid), '*', MUST_EXIST);
        $progressconfig = unserialize(base64_decode($progressblock->configdata));
        $progressblockcontext = block_progress_get_block_context($pbid);

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
        $userevents = block_progress_filter_visibility($events, $userid, $context, $course);
        $attempts = block_progress_attempts($modules, $progressconfig, $userevents, $userid, $course->id);

        function block_progress_numattempts($events, $attempts)
        {
            $attemptcount = 0;

            foreach ($events as $event) {
                if ($attempts[$event['type'].$event['id']] == 1) {
                    $attemptcount++;
                }
            }
            return (int) $attemptcount;
        }

        $numattempts = block_progress_numattempts($events, $attempts);

        $result[numevents] = $numevents;
        $result[numattempts] = $numattempts;
        $result[progressvalue] = intval($numattempts * 100 / $numevents);

        return $result ;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function cinfo_returns()
    {
        return new external_single_structure(
            array(
                'numevents' => new external_value(PARAM_INT, 'Number of activities monitored by the Progress Bar'),
                'numattempts' => new external_value(PARAM_INT, 'Number of activities completed by the user'),
                'progressvalue' => new external_value(PARAM_INT, 'Percentage of completed activities')
            )
        );
    }

/** pbinst **/
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function pbinst_parameters()
    {
        return new external_function_parameters(
            array(
              'courseid' => new external_value(PARAM_INT, 'courseid')
            )
        );
    }

    /**
     * Returns Progress Bar instances ids for a given course
     * @return object array
     */
    public static function pbinst($courseid)
    {
        global $DB;

        // Get specific block config and context.
        $sql = "SELECT bi.id,
        bp.id AS blockpositionid,
        COALESCE(bp.region, bi.defaultregion) AS region,
        COALESCE(bp.weight, bi.defaultweight) AS weight,
        COALESCE(bp.visible, 1) AS visible,
        bi.configdata
        FROM {block_instances} bi
        LEFT JOIN {block_positions} bp ON bp.blockinstanceid = bi.id
        AND ".$DB->sql_like('bp.pagetype', ':pagetype', false)."
        WHERE bi.blockname = 'progress'
        AND bi.parentcontextid = :contextid
        ORDER BY region, weight, bi.id";

        $modules = block_progress_modules_in_use($courseid);
        $context = block_progress_get_course_context($courseid);
        $params = array('contextid' => $context->id, 'pagetype' => 'course-view-%');
        $blockinstances = $DB->get_records_sql($sql, $params);

        $blockinstancenum = 0 ;

        foreach ($blockinstances as $blockid => $blockinstance) {
            $result[$blockinstancenum] = $blockinstance->id;
            $blockinstancenum = $blockinstancenum +1;
        }

        return $result;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function pbinst_returns()
    {
        return new external_multiple_structure(
            new external_value( PARAM_INT, 'Progress Bar instance id')
        );
    }
}
