<?php
/**
 * Silex app creation
 *
 * @package app
 * @author ZUBER Lionel <lionel.zuber@armaklan.org>
 * @version 0.1
 * @copyright (C) 2013 ZUBER Lionel <lionel.zuber@armaklan.org>
 * @license BSD
 */

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

class MyApplication extends Silex\Application
{
    use Application\TwigTrait;
    use Application\UrlGeneratorTrait;
}

$app = new MyApplication();

