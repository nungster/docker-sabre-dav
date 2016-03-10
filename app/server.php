<?php

  use Sabre\DAV,
      Sabre\DAV\Auth;

  // The autoloader
  require 'vendor/autoload.php';

  // Now we're creating a whole bunch of objects
  $rootDirectory = new DAV\FS\Directory('files');

  // The server object is responsible for making sense out of the WebDAV protocol
  $server = new DAV\Server($rootDirectory);

  // If your server is not on your webroot, make sure the following line has the
  // correct information
  $server->setBaseUri('/');

  // The lock manager is reponsible for making sure users don't overwrite
  // each others changes.
  $lockBackend = new DAV\Locks\Backend\File('data/locks');
  $lockPlugin = new DAV\Locks\Plugin($lockBackend);
  $server->addPlugin($lockPlugin);

  // This ensures that we get a pretty index in the browser, but it is
  // optional.
  $server->addPlugin(new DAV\Browser\Plugin());

  // Authorization 
  $authBackend = new DAV\Auth\Backend\File('/etc/nginx/.htpasswd');
  $authBackend->setRealm(getenv('REALM'));

  // Creating the plugin.
  $authPlugin = new DAV\Auth\Plugin($authBackend);

  // Adding the plugin to the server.
  $server->addPlugin($authPlugin);

  // All we need to do now, is to fire up the server
  $server->exec();
