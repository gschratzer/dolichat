DOLICHAT FOR DOLIBARR
=====================

Dolichat is a chat module for Dolibarr that connects Dolibarr users with each
other for simple internal communication.

The module integrates into Dolibarr as an external module and adds chat access
to the communication area and the top-right menu.

MAIN FEATURES
=============

- Internal chat between Dolibarr users
- Integration into Dolibarr menus
- Access from the top-right area of the interface
- User rights for chat usage and administration
- Optional picture upload support
- Language files, SQL install files, CSS and JavaScript assets included

REQUIREMENTS
============

- Dolibarr 21.0
- PHP 7.4 or newer

INSTALLATION
============

Recommended installation path for external modules:

    htdocs/custom/dolichat

Example:

    /var/www/html/dolibarr/htdocs/custom/dolichat

Do NOT extract the module into the old root-level layout described in earlier
versions of this project. For current Dolibarr versions, the correct place for
an external module is the "custom" directory.

INSTALLATION METHODS
====================

Method 1 - Manual installation
------------------------------

1. Download or clone the module.
2. Copy the folder "dolichat" into:

       htdocs/custom/

3. Check that the module descriptor exists here:

       htdocs/custom/dolichat/core/modules/modDolichat.class.php

4. In Dolibarr, go to:

       Home -> Setup -> Modules / Applications

5. Search for "Dolichat" and enable the module.

Method 2 - Deploy/install external module
-----------------------------------------

If your Dolibarr instance allows module deployment from the interface:

1. Go to:

       Home -> Setup -> Modules / Applications

2. Open:

       Deploy/install external app/module

3. Upload the module package and install it.
4. Enable the module afterwards if needed.

IF THE MODULE DOES NOT APPEAR
=============================

If Dolichat does not appear in the module list:

1. Verify the directory path is exactly:

       htdocs/custom/dolichat

2. Verify the module descriptor exists:

       htdocs/custom/dolichat/core/modules/modDolichat.class.php

3. Refresh the browser and open the modules page again.
4. If your Dolibarr setup hides experimental or development modules, set:

       MAIN_FEATURES_LEVEL = 2

ACTIVATION
==========

After enabling the module:

- a communication menu entry should become available
- a Dolichat entry should appear in the left menu of the communication area
- chat access can also be integrated through the top-right menu hook

FIRST USE
=========

Once enabled, users with the correct permission can open Dolichat and start
communicating with other Dolibarr users.

Typical first steps:

1. Enable the module
2. Grant chat permissions to the desired user or user roles
3. Open the communication menu
4. Start a chat session

PERMISSIONS
===========

Dolichat defines several permissions, including:

- Use chat
- Chat administration
- Picture upload
- Read picture access

If a user can see the module but cannot use it, check the user or group rights
in Dolibarr.

DIRECTORY OVERVIEW
==================

The repository currently contains a structure similar to this:

- admin/       administration pages
- class/       PHP classes
- core/        module descriptor and AJAX integration
- css/         stylesheets
- js/          JavaScript files
- langs/       translations
- lib/         helper libraries
- sql/         SQL resources
- sound/       sound assets
- images/, img/  image assets

DATABASE
========

The module creates and/or uses chat-related tables during initialization,
including tables for:

- chat status
- chat messages
- chat pictures

These are created automatically by the module initialization process.

UPGRADES
========

If you are upgrading from a very old Dolichat installation:

- back up your database first
- back up your Dolicharr custom directory first
- replace the old module files with the new version
- re-enable or test the module in a staging environment before production use

Older installation instructions that refer to Dolibarr 3.x or to placing the
module directly into the old root/htdocs layout are obsolete.

DEVELOPMENT
===========

This repository contains the develop branch of Dolichat.

For developers:

- keep the module inside htdocs/custom/dolichat in your Dolibarr instance
- test with a compatible Dolibarr 21 environment
- verify menu integration, permissions, chat sending, and uploads after changes

SUPPORT
=======

If you need help installing or adapting Dolichat, open an issue in the GitHub
repository or contact the maintainer through the project links.

LICENSE
=======

Dolichat is distributed under the GNU GPL v3 or later.

PROJECT
=======

Repository:
https://github.com/gschratzer/dolichat

Website:
https://www.7reasons.eu
