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

namespace block_stash\external;

use context_course;
// use context_user;
use external_api;
use external_function_parameters;
use external_value;
use external_warnings;
// use external_format_value;
use external_single_structure;
use external_multiple_structure;
use stdClass;
use moodle_url;

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot.'/grade/lib.php');

/**
 * External swap item list
 *
 * @package    block_stash
 * @copyright  2023 Adrian Greeve <abgreeve@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class userlist extends external_api {
    /**
     * Describes the parameters for get_users_for_course.
     *
     * @return external_function_parameters
     */
    public static function get_users_for_search_widget_parameters(): external_function_parameters {
        return new external_function_parameters (
            [
                'courseid' => new external_value(PARAM_INT, 'Course Id', VALUE_REQUIRED)
            ]
        );
    }

    /**
     * Given a course ID find the
     *
     * @param int $courseid
     * @return array Users and warnings to pass back to the calling widget.
     */
    protected static function get_users_for_search_widget(int $courseid): array {
        global $DB;


        $params = self::validate_parameters(
            self::get_users_for_search_widget_parameters(),
            [
                'courseid' => $courseid,
            ]
        );

        $warnings = [];
        $coursecontext = context_course::instance($params['courseid']);
        parent::validate_context($coursecontext);

        $manager = \block_stash\manager::get($params['courseid']);

        $course = $DB->get_record('course', ['id' => $params['courseid']], '*', MUST_EXIST);

        // Get all enrolled users.
        $enrolledusers = get_enrolled_users($coursecontext);

        $users = [];
        foreach ($enrolledusers as $user) {
            $url = new moodle_url('/blocks/stash/tradecenter.php', ['courseid' => $params['courseid'], 'user' => $user->id]);
            $users[] = (object) [
                'id' => $user->id,
                'fullname' => fullname($user),
                'url' => $url->out(false),
                'active' => true
            ];
        }

        return [
            'users' => $users,
            'warnings' => $warnings,
        ];
    }

    /**
     * Returns description of what the user search for the widget should return.
     *
     * @return external_single_structure
     */
    public static function get_users_for_search_widget_returns(): external_single_structure {
        return new external_single_structure([
            'users' => new external_multiple_structure(
                new external_single_structure([
                    'id'    => new external_value(PARAM_INT, 'ID of the user', VALUE_OPTIONAL),
                    'fullname' => new external_value(PARAM_TEXT, 'The full name of the user', VALUE_OPTIONAL),
                    'url' => new external_value(
                        PARAM_URL,
                        'The link to the trade center',
                        VALUE_OPTIONAL
                    ),
                    'active' => new external_value(PARAM_BOOL, 'Are we currently on this user?', VALUE_REQUIRED)
                ])
            ),
            'warnings' => new external_warnings(),
        ]);
    }
}
