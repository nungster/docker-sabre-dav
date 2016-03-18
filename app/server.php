<?php

  use Sabre\DAV,
      Sabre\CalDAV,
      Sabre\DAVACL,
      Sabre\DAV\Auth;

  // settings
  date_default_timezone_set('Canada/Eastern');

  /* Database */
  $pdo = new PDO('sqlite:data/db.sqlite');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  //Mapping PHP errors to exceptions
  function exception_error_handler($errno, $errstr, $errfile, $errline) {
      throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
  }  
  set_error_handler("exception_error_handler");

  // The autoloader
  require 'vendor/autoload.php';

  
  // Authorization
  $authBackend = new Sabre\DAV\Auth\Backend\File('/etc/nginx/.htpasswd');
  $authBackend->setRealm(getenv('REALM'));

  // Backends
  //$authBackend      = new Sabre\DAV\Auth\Backend\PDO($pdo);
  $principalBackend = new Sabre\DAVACL\PrincipalBackend\PDO($pdo);
  $carddavBackend   = new Sabre\CardDAV\Backend\PDO($pdo);
  $caldavBackend    = new Sabre\CalDAV\Backend\PDO($pdo);
  
  // Setting up the directory tree //
  $nodes = [
      new Sabre\DAVACL\PrincipalCollection($principalBackend),
//      new Sabre\CalDAV\CalendarRoot($authBackend, $caldavBackend),
      new Sabre\CardDAV\AddressBookRoot($principalBackend, $carddavBackend),
  ];

  // The object tree needs in turn to be passed to the server class
  $server = new Sabre\DAV\Server($nodes);


  // Now we're creating a whole bunch of objects
  $rootDirectory = new Sabre\DAV\FS\Directory('files');

  // The server object is responsible for making sense out of the WebDAV protocol
  $server = new Sabre\DAV\Server($rootDirectory);

  // If your server is not on your webroot, make sure the following line has the
  // correct information
  $server->setBaseUri('/');

  // The lock manager is reponsible for making sure users don't overwrite
  // each others changes.
  $lockBackend = new Sabre\DAV\Locks\Backend\File('data/locks');
  $lockPlugin = new Sabre\DAV\Locks\Plugin($lockBackend);
  $server->addPlugin($lockPlugin);

  // This ensures that we get a pretty index in the browser, but it is
  // optional.
  $server->addPlugin(new Sabre\DAV\Browser\Plugin());

  // Plugins
  $server->addPlugin(new Sabre\DAV\Auth\Plugin($authBackend));
  $server->addPlugin(new Sabre\DAV\Browser\Plugin());
  $server->addPlugin(new Sabre\CalDAV\Plugin());
  $server->addPlugin(new Sabre\CardDAV\Plugin());
  $server->addPlugin(new Sabre\DAVACL\Plugin());
  $server->addPlugin(new Sabre\DAV\Sync\Plugin());


  // All we need to do now, is to fire up the server
  $server->exec();
