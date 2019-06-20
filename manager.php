<?php
/**
 * Require add-on files and perform their initial setup
 *
 * @package distributor-clone-fix
 */

/* Require plug-in files */
require_once __DIR__ . '/includes/clone-fix-hub.php';
require_once __DIR__ . '/includes/clone-fix-spoke.php';

/* Call the setup functions */
\Distributor\CloneFixHub\setup();

\Distributor\CloneFixSpoke\setup();
