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
 * Progress Bar block definition
 *
 * @package    contrib
 * @subpackage block_progress
 * @copyright  2010 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/blocks/progress/lib.php');

/**
 * Progress Bar block class
 *
 * @copyright 2010 Michael de Raadt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_progress extends block_base {

    /**
     * Sets the block title
     *
     * @return void
     */
    public function init() {
        $this->title = get_string('config_default_title', 'block_progress');
    }

    /**
     * Controls the block title based on instance configuration
     *
     * @return bool
     */
    public function specialization() {
        if (isset($this->config->progressTitle) && trim($this->config->progressTitle) != '') {
            $this->title = format_string($this->config->progressTitle);
        }
    }

    /**
     * Controls whether multiple instances of the block are allowed on a page
     *
     * @return bool
     */
    public function instance_allow_multiple() {
        return true;
    }

    /**
     * Defines where the block can be added
     *
     * @return array
     */
    public function applicable_formats() {
        return array(
            'course-view'    => true,
            'site'           => false,
            'mod'            => false,
            'my'             => false
        );
    }

    /**
     * Creates the blocks main content
     *
     * @return string
     */
    public function get_content() {
        global $USER, $COURSE, $CFG, $OUTPUT;

        // If content has already been generated, don't waste time generating it again.
        if ($this->content !== null) {
            return $this->content;
        }
        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        // Check if any activities/resources have been created.
        $modules = block_progress_modules_in_use();
        if (empty($modules)) {
            if (has_capability('moodle/block:edit', $this->context)) {
                $this->content->text .= get_string('no_events_config_message', 'block_progress');
            }
            return $this->content;
        }

        // Check if activities/resources have been selected in config.
        $events = block_progress_event_information($this->config, $modules);
        if ($events === null || $events === 0) {
            if (has_capability('moodle/block:edit', $this->context)) {
                $this->content->text .= get_string('no_events_message', 'block_progress');
                if ($USER->editing) {
                    $parameters = array('id' => $COURSE->id, 'sesskey' => sesskey(),
                                        'bui_editid' => $this->instance->id);
                    $url = new moodle_url('/course/view.php', $parameters);
                    $label = get_string('selectitemstobeadded', 'block_progress');
                    $this->content->text .= $OUTPUT->single_button($url, $label);
                    if ($events === 0) {
                        $url->param('turnallon', '1');
                        $label = get_string('addallcurrentitems', 'block_progress');
                        $this->content->text .= $OUTPUT->single_button($url, $label);
                    }
                }
            }
            return $this->content;
        } else if (empty($events)) {
            if (has_capability('moodle/block:edit', $this->context)) {
                $this->content->text .= get_string('no_visible_events_message', 'block_progress');
            }
            return $this->content;
        }

        // Display progress bar.
        $attempts = block_progress_attempts($modules, $this->config, $events, $USER->id, $this->instance->id);
        $this->content->text = block_progress_bar($modules, $this->config, $events, $USER->id, $this->instance->id, $attempts);

        // Organise access to JS.
        $jsmodule = array(
            'name' => 'block_progress',
            'fullpath' => '/blocks/progress/module.js',
            'requires' => array(),
            'strings' => array(
                array('time_expected', 'block_progress'),
            ),
        );
        $displaydate = (!isset($this->config->orderby) || $this->config->orderby == 'orderbytime') &&
                       (!isset($this->config->displayNow) || $this->config->displayNow == 1);
        $arguments = array($CFG->wwwroot, array_keys($modules), $displaydate);
        $this->page->requires->js_init_call('M.block_progress.init', $arguments, false, $jsmodule);

        // Allow teachers to access the overview page.
        if (has_capability('block/progress:overview', $this->context)) {
            $parameters = array('progressbarid' => $this->instance->id, 'courseid' => $COURSE->id);
            $url = new moodle_url('/blocks/progress/overview.php', $parameters);
            $label = get_string('overview', 'block_progress');
            $options = array('class' => 'overviewButton');
            $this->content->text .= $OUTPUT->single_button($url, $label, 'post', $options);
        }

        return $this->content;
    }
}
