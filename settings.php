<?php
/*
 * Moodle UI integration
 */

defined('MOODLE_INTERNAL') || die();

if (is_siteadmin() and $DB->get_dbfamily() === 'postgres') {
    $ADMIN->add('server', new admin_externalpage('localphppgadmin', 'phpPgAdmin', new moodle_url('/local/phppgadmin')));
}