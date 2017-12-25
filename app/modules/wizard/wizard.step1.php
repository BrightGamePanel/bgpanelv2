<?php
/**
 * Created by PhpStorm.
 * User: nikita
 * Date: 20/12/2017
 * Time: 13:26
 */

// Include HEADER

?>
<table class="table table-bordered">
    <thead>
    <tr>
        <th>Action</th>
        <th>Status</th>
        <th>Note</th>
    </tr>
    </thead>
    <tbody>
    <tr class="success">
        <td>Checking for CONFIGURATION files</td>
        <td><span class="label label-success">FOUND</span></td>
        <td></td>
    </tr>

<?php
if ($versioncompare == -1)
{
    ?>
    <tr class="error">
        <td>Checking your version of PHP</td>
        <td><span class="label label-important">FAILED (<?php echo PHP_VERSION; ?>)</span></td>
        <td>Upgrade to PHP 5.4.0 or greater</td>
    </tr>
    <?php
    $error = TRUE;
}
else
{
    ?>
    <tr class="success">
        <td>Checking your version of PHP</td>
        <td><span class="label label-success"><?php echo PHP_VERSION; ?></span></td>
        <td></td>
    </tr>
    <?php
}

if ($apache2Check === FALSE)
{
    ?>
    <tr class="error">
        <td>Checking your server software</td>
        <td><span class="label label-important">FAILED (<?php echo $_SERVER['SERVER_SOFTWARE']; ?>)</span></td>
        <td>BrightGamePanel V2 requires an Apache2 setup</td>
    </tr>
    <?php
    $error = TRUE;
}
else
{
    ?>
    <tr class="success">
        <td>Checking your server software</td>
        <td><span class="label label-success"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></span></td>
        <td></td>
    </tr>
    <?php
}

if (ini_get('safe_mode'))
{
    ?>
    <tr class="error">
        <td>Checking for PHP safe mode</td>
        <td><span class="label label-important">ON</span></td>
        <td>Please, disable safe mode !!!</td>
    </tr>
    <?php
    $error = TRUE;
}
else
{
    ?>
    <tr class="success">
        <td>Checking for PHP safe mode</td>
        <td><span class="label label-success">OFF</span></td>
        <td></td>
    </tr>
    <?php
}

if ($htaccessCheck === FALSE)
{
    ?>
    <tr class="error">
        <td>Checking .htaccess override with Apache/2.x.x w/ mod_rewrite</td>
        <td><span class="label label-important">FAILED</span></td>
        <td>BrightGamePanel V2 requires the directive <code>"AllowOverride All"</code> in your <code>'httpd.conf'</code> configuration file for this
            <code>&lt;Directory "<?php echo BASE_DIR; ?>"&gt;</code>.
            Verify also that <code>"mod_rewrite"</code> is installed and activated.</td>
    </tr>
    <?php
    $error = TRUE;
}
else
{
    ?>
    <tr class="success">
        <td>Checking .htaccess override with Apache/2.x.x w/ mod_rewrite</td>
        <td><span class="label label-success">It Works!</span></td>
        <td></td>
    </tr>
    <?php
}

if (!extension_loaded('pdo'))
{
    ?>
    <tr class="error">
        <td>Checking for PDO extension</td>
        <td><span class="label label-important">FAILED</span></td>
        <td>PDO extension could not be found or is not installed. (<a href="http://php.net/manual/en/pdo.installation.php">PDO Installation</a>).</td>
    </tr>
    <?php
    $error = TRUE;
}
else
{
    ?>
    <tr class="success">
        <td>Checking for PDO extension</td>
        <td><span class="label label-success">INSTALLED</span></td>
        <td></td>
    </tr>
    <?php

    try {
        // Connect to the SQL server
        if (DB_DRIVER == 'sqlite') {
            $dbh = new PDO( DB_DRIVER.':'.DB_FILE );
        }
        else {
            $dbh = new PDO( DB_DRIVER.':host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD );
        }

        // Set ERRORMODE to exceptions
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $e) {
        $pdo_error = $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
    }

    if ( empty($dbh) )
    {
        ?>
        <tr class="error">
            <td>Checking for SQL server connection</td>
            <td><span class="label label-important">FAILED</span></td>
            <td>Message: "<?php echo $pdo_error; ?>"</td>
        </tr>
        <?php

        $error = TRUE;
    }
    else
    {
        ?>
        <tr class="success">
            <td>Checking for SQL server connection</td>
            <td><span class="label label-success">SUCCESSFUL</span></td>
            <td></td>
        </tr>
        <?php
        unset($dbh);
    }
}

if (!function_exists('fsockopen'))
{
    ?>
    <tr class="error">
        <td>Checking for FSOCKOPEN function</td>
        <td><span class="label label-important">FAILED</span></td>
        <td></td>
    </tr>
    <?php
    $error = TRUE;
}
else
{
    ?>
    <tr class="success">
        <td>Checking for FSOCKOPEN function</td>
        <td><span class="label label-success">SUCCESSFUL</span></td>
        <td></td>
    </tr>
    <?php
}

if (!function_exists('mail')) {
    ?>
    <tr class="error">
        <td>Checking for MAIL function</td>
        <td><span class="label label-important">FAILED</span></td>
        <td></td>
    </tr>
    <?php
    $error = TRUE;
} else {
    ?>
    <tr class="success">
        <td>Checking for MAIL function</td>
        <td><span class="label label-success">SUCCESSFUL</span></td>
        <td></td>
    </tr>
    <?php
}

if (!extension_loaded('curl')) {
    ?>
    <tr class="error">
        <td>Checking for Curl extension</td>
        <td><span class="label label-important">FAILED</span></td>
        <td>Curl extension is not installed. (<a href="http://php.net/curl">Curl</a>).</td>
    </tr>
    <?php
    $error = TRUE;
} else {
    ?>
    <tr class="success">
        <td>Checking for Curl extension</td>
        <td><span class="label label-success">INSTALLED</span></td>
        <td></td>
    </tr>
    <?php
}

if (!extension_loaded('mbstring'))
{
    ?>
    <tr class="error">
        <td>Checking for MBSTRING extension (LGSL - Used to show UTF-8 server and player names correctly)</td>
        <td><span class="label label-important">FAILED</span></td>
        <td>mbstring extension is not installed. (<a href="http://php.net/mbstring">mbstring</a>).</td>
    </tr>
    <?php
    $error = TRUE;
}
else
{
    ?>
    <tr class="success">
        <td>Checking for MBSTRING extension (LGSL - Used to show UTF-8 server and player names correctly)</td>
        <td><span class="label label-success">INSTALLED</span></td>
        <td></td>
    </tr>
    <?php
}

if (!extension_loaded('bz2'))
{
    ?>
    <tr class="error">
        <td>Checking for BZIP2 extension (LGSL - Used to show Source server settings over a certain size)</td>
        <td><span class="label label-important">FAILED</span></td>
        <td>BZIP2 extension is not installed. (<a href="http://php.net/bzip2">BZIP2</a>).</td>
    </tr>
    <?php
    $error = TRUE;
}
else
{
    ?>
    <tr class="success">
        <td>Checking for BZIP2 extension (LGSL - Used to show Source server settings over a certain size)</td>
        <td><span class="label label-success">INSTALLED</span></td>
        <td></td>
    </tr>
    <?php
}

if (!extension_loaded('zlib'))
{
    ?>
    <tr class="error">
        <td>Checking for ZLIB extension</td>
        <td><span class="label label-important">FAILED</span></td>
        <td>ZLIB extension is not installed. (<a href="http://php.net/zlib">ZLIB</a>).</td>
    </tr>
    <?php
    $error = TRUE;
}
else
{
    ?>
    <tr class="success">
        <td>Checking for ZLIB extension</td>
        <td><span class="label label-success">INSTALLED</span></td>
        <td></td>
    </tr>
    <?php
}


if (!extension_loaded('gd') && !extension_loaded('gd2'))
{
    ?>
    <tr class="error">
        <td>Checking for GD extension (pChart Requirement)</td>
        <td><span class="label label-important">FAILED</span></td>
        <td>GD / GD2 extensions are not installed. (<a href="http://php.net/book.image.php">GD</a>).</td>
    </tr>
    <?php
    $error = TRUE;
}
else
{
    ?>
    <tr class="success">
        <td>Checking for GD extension (pChart Requirement)</td>
        <td><span class="label label-success">INSTALLED</span></td>
        <td></td>
    </tr>
    <?php
}


if (!function_exists('imagettftext'))
{
    ?>
    <tr class="error">
        <td>Checking for FreeType extension (securimage Requirement)</td>
        <td><span class="label label-important">FAILED</span></td>
        <td>FreeType extension is not installed. (<a href="http://php.net/manual/en/image.installation.php">FreeType</a>).</td>
    </tr>
    <?php
    $error = TRUE;
}
else
{
    ?>
    <tr class="success">
        <td>Checking for FreeType extension (securimage Requirement)</td>
        <td><span class="label label-success">INSTALLED</span></td>
        <td></td>
    </tr>
    <?php
}

if (!extension_loaded('simplexml'))
{
    ?>
    <tr class="error">
        <td>Checking for SimpleXML extension</td>
        <td><span class="label label-important">FAILED</span></td>
        <td>SimpleXML extension is not installed. (<a href="http://php.net/simplexml">SimpleXML</a>).</td>
    </tr>
    <?php
    $error = TRUE;
}
else
{
    ?>
    <tr class="success">
        <td>Checking for SimpleXML extension</td>
        <td><span class="label label-success">INSTALLED</span></td>
        <td></td>
    </tr>
    <?php
}

if (!class_exists('XMLReader'))
{
    ?>
    <tr class="error">
        <td>Checking for XMLReader extension</td>
        <td><span class="label label-important">FAILED</span></td>
        <td>XMLReader extension is not installed. (<a href="http://php.net/xmlreader">XMLReader</a>).</td>
    </tr>
    <?php
    $error = TRUE;
}
else
{
    ?>
    <tr class="success">
        <td>Checking for XMLReader extension</td>
        <td><span class="label label-success">INSTALLED</span></td>
        <td></td>
    </tr>
    <?php
}


//
// PHPSECLIB REQUIREMENTS
//

if (!extension_loaded('openssl'))
{
    ?>
    <tr class="error">
        <td>Checking for OpenSSL (phpseclib)</td>
        <td><span class="label label-important">FAILED</span></td>
        <td>OpenSSL extension is not installed. (<a href="http://php.net/manual/en/book.openssl.php">OpenSSL</a>).</td>
    </tr>
    <?php
    $error = TRUE;
}
else
{
    ?>
    <tr class="success">
        <td>Checking for OpenSSL (phpseclib)</td>
        <td><span class="label label-success">INSTALLED</span></td>
        <td></td>
    </tr>
    <?php
}

if (!extension_loaded('mcrypt'))
{
    ?>
    <tr class="error">
        <td>Checking for MCRYPT extension (phpseclib)</td>
        <td><span class="label label-important">FAILED</span></td>
        <td>MCRYPT extension is not installed. (<a href="http://php.net/manual/en/book.mcrypt.php">MCRYPT</a>).</td>
    </tr>
    <?php
    $error = TRUE;
}
else
{
    ?>
    <tr class="success">
        <td>Checking for MCRYPT extension (phpseclib)</td>
        <td><span class="label label-success">INSTALLED</span></td>
        <td></td>
    </tr>
    <?php
}

if (!extension_loaded('gmp'))
{
    ?>
    <tr class="error">
        <td>Checking for GMP extension (phpseclib)</td>
        <td><span class="label label-important">FAILED</span></td>
        <td>GMP extension is not installed. (<a href="http://php.net/manual/en/book.gmp.php">GNU Multiple Precision</a>).</td>
    </tr>
    <?php
    $error = TRUE;
}
else
{
    ?>
    <tr class="success">
        <td>Checking for GMP extension (phpseclib)</td>
        <td><span class="label label-success">INSTALLED</span></td>
        <td></td>
    </tr>
    <?php
}

if (!function_exists('hash'))
{
    ?>
    <tr class="error">
        <td>Checking for hash() function</td>
        <td><span class="label label-important">FAILED</span></td>
        <td>Hash extension is not installed. (<a href="http://php.net/manual/en/book.hash.php">Hash</a>).</td>
    </tr>
    <?php
    $error = TRUE;
}
else
{
    ?>
    <tr class="success">
        <td>Checking for hash() function</td>
        <td><span class="label label-success">INSTALLED</span></td>
        <td></td>
    </tr>
    <?php
}


if (!defined('APP_API_KEY'))
{
    if (is_writable( CONF_API_KEY_INI ))
    {
        ?>
        <tr class="success">
            <td>Checking for API configuration file write permission</td>
            <td><span class="label label-success">OK</span></td>
            <td></td>
        </tr>
        <?php
    }
    else
    {
        ?>
        <tr class="error">
            <td>Checking for API configuration file write permission</td>
            <td><span class="label label-important">FAILED</span></td>
            <td></td>
        </tr>
        <?php
        $error = TRUE;
    }
}

if (!defined('APP_SSH_KEY'))
{
    if (is_writable( CONF_SECRET_INI ))
    {
        ?>
        <tr class="success">
            <td>Checking for secret keys file write permission</td>
            <td><span class="label label-success">OK</span></td>
            <td></td>
        </tr>
        <?php
    }
    else
    {
        ?>
        <tr class="error">
            <td>Checking for secret keys file write permission</td>
            <td><span class="label label-important">FAILED</span></td>
            <td></td>
        </tr>
        <?php
        $error = TRUE;
    }
}

if (!defined('RSA_PRIVATE_KEY') || !defined('RSA_PUBLIC_KEY'))
{
    if (is_writable( RSA_KEYS_DIR ))
    {
        ?>
        <tr class="success">
            <td>Checking for SSH and RSA keys directory write permission</td>
            <td><span class="label label-success">OK</span></td>
            <td></td>
        </tr>
        <?php
    }
    else
    {
        ?>
        <tr class="error">
            <td>Checking for SSH and RSA keys directory write permission</td>
            <td><span class="label label-important">FAILED</span></td>
            <td></td>
        </tr>
        <?php
        $error = TRUE;
    }
}


if (is_writable( CONF_PHPSECLIB_INI ))
{
    ?>
    <tr class="success">
        <td>Checking for PHPSECLIB configuration file write permission</td>
        <td><span class="label label-success">OK</span></td>
        <td></td>
    </tr>
    <?php
}
else
{
    ?>
    <tr class="error">
        <td>Checking for PHPSECLIB configuration file write permission</td>
        <td><span class="label label-important">FAILED</span></td>
        <td></td>
    </tr>
    <?php
    $error = TRUE;
}









</tbody>
        </table>
if (isset($error))
{
    ?>
    <div style="text-align: center;">
        <h3><b>Fatal Error(s) Found.</b></h3><br />
        <button class="btn" onclick="window.location.reload();">Check Again</button>
    </div>
    <?php
}
else
{
    ?>
    <div style="text-align: center;">
        <ul class="pager">
            <li>
                <a href="index.php?step=two">Next Step &rarr;</a>
            </li>
        </ul>
    </div>
    <?php
}