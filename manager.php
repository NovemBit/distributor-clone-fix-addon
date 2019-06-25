<?php
/**
 * Require add-on files and perform their initial setup
 *
 * @package distributor-clone-fix
 */

/* Require plug-in files */
require_once __DIR__ . '/includes/clone-fix-hub.php';
require_once __DIR__ . '/includes/clone-fix-spoke.php';
require_once __DIR__ . '/includes/ui.php';
require_once __DIR__ . '/includes/utils.php';

/* Call the setup functions */
\DT\NbAddon\CloneFix\Hub\setup();

\DT\NbAddon\CloneFix\Spoke\setup();
\DT\NbAddon\CloneFix\Ui\setup();
