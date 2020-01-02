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
 * User profile page renderable.
 *
 * @package    block_stash
 * @copyright  2019 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use moodle_url;
use block_stash\external\user_item_summary_exporter;

class user_profile extends \block_stash\output\block_content implements renderable, templatable {

    public function export_for_template(renderer_base $output) {
    	global $USER;
    	$data = parent::export_for_template($output);
    	$data['courseid'] = $this->manager->get_courseid();
    	$data['userid'] = $this->userid;
    	$data['myuserid'] = $USER->id;

        return $data;
    }

}
