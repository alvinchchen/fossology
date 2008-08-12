<?php


/***********************************************************
 Copyright (C) 2008 Hewlett-Packard Development Company, L.P.

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 version 2 as published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License along
 with this program; if not, write to the Free Software Foundation, Inc.,
 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 ***********************************************************/

/**
 * Upload a file from the server using the UI
 *
 *@TODO need to make sure testing folder exists....
 *@TODO needs setup and account to really work well...
 *
 * @version "$Id: $"
 *
 * Created on Aug 1, 2008
 */

/*
 * NOTE this test is difficult in that the material uploaded MUST be
 * available to the test.  If multiple agent systems are used, then the
 * material must be available there as well.
 *
 * One possibility is to modify the readme to include the creation of
 * a test user and material.  Since it takes sudo, the test cannot
 * automatically do it. Well it could, but it's a bad idea.
 */

require_once ('../../../../tests/fossologyWebTestCase.php');
require_once ('../../../../tests/TestEnvironment.php');

global $URL;

class UploadSrvTest extends fossologyWebTestCase
{
  function setUp()
  {
    /* check to see if the user and material exist*/
    $this->assertTrue(file_exists('/home/fosstester/.bashrc'),
                      "FAILURE! .bashrc not found\n");
    $this->assertTrue(file_exists('/home/fosstester/ReadMe'),
                      "FAILURE! Readme in ~fosstester not found\n");
  }

  function testUploadUSrv()
  {
    global $URL;

    print "starting UploadUSrvTest\n";
    $this->useProxy('http://web-proxy.fc.hp.com:8088', 'web-proxy', '');
    $browser = & new SimpleBrowser();
    $page = $browser->get($URL);
    $this->assertTrue($page);
    $this->assertTrue(is_object($browser));
    $cookie = $this->repoLogin($browser);
    $host = $this->getHost($URL);
    $browser->setCookie('Login', $cookie, $host);

    $loggedIn = $browser->get($URL);
    $this->assertTrue($this->assertText($loggedIn, '/Upload/'),
                      'Did not find Upload Menu');
    $this->assertTrue($this->assertText($loggedIn, '/From Server/'),
                      'Did not find From Server Menu');

    $page = $browser->get("$URL?mod=upload_srv_files");
    $this->assertTrue($this->assertText($page, '/Upload from Server/'),
                      'Did not find Upload from Server Title');
    $this->assertTrue($this->assertText($page, '/on the server to upload:/'),
                      'Did not find the sourcefile Selection Text');

    /* select Testing folder */

    $FolderId = $this->getFolderId('Testing', $page);
    $this->assertTrue($browser->setField('folder', $FolderId));
    $this->assertTrue($browser->setField('sourcefiles', '/home/fosstester/archives/simpletest_1.0.1.tar.gz'));
    $desc = 'File uploaded by test UploadSrvTest to folder Testing';
    $this->assertTrue($browser->setField('description', "$desc"));
    /* we won't select any agents this time' */
    $page = $browser->clickSubmit('Upload!');
    $this->assertTrue(page);
    $this->assertTrue($this->assertText($page,
                     '/Upload jobs for \/home\/fosstester\/archives\/simpletest_1\.0\.1\.tar\.gz/'),
                      "FAIL! Did not match Upload message\n");

    print "************ page after Upload! *************\n$page\n";
  }
}
?>
