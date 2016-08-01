To install Dolichat on Dolibarr 3.8.3 you have to extract the module Dolichat to the root directory of dolibarr.

/var/www/html/web/dolibarr/

The module has an htdocs/ folder for integrating itself in the htdocs/ folder of dolibarr, but you can manually put the extracted dolichat folder in the htdocs/ folder of your Dolibarr to.

(Dolibarr only scan for modules in the htdocs folder. As an example dolibarr/htdocs/dolichat/core/modDolichat.class.php. If it finds a module it will show up in the module configuration page.)

We can also provide a support solution where we are installing Dolichat on your system for free if this is necessary.

When the folder is presence in the htdocs folder than you should be able to activate it under the module tab interfaces modules

When you have enable it you can see a chat icon next to your user or logout button


To use Dolichat you have a new icon near your user name and logout icon.

If you get a message from any other user it will show a call sign.