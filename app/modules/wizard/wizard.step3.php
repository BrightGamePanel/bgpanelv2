<?php
/**
 * Created by PhpStorm.
 * User: nikita
 * Date: 20/12/2017
 * Time: 14:59
 */

//---------------------------------------------------------+

?>
<div class="well">
    <div class="alert alert-block">
        <strong>DELETE THE INSTALL FOLDER</strong><br />
        <?php echo getcwd(); ?>

    </div>
    <?php
    if (@$_GET['version'] == 'full') // Full install case
    {
        ?>
        <h2>Install Complete!</h2>
        <legend>Login Information :</legend>
        Root Username: <b>root</b><br />
        Root Password: <b>password</b><br />
        <hr>
        <i class="icon-share-alt"></i>&nbsp;<a href="../login">@Login</a>
        <hr>
        <div class="alert alert-error">
            <strong>Wait!</strong>
            Remember to change the root username and password.
        </div>
        <?php
    }
    else // Update Case
    {
        ?>
        <h2>Your system is now up-to-date.</h2>
        <legend>Changelog:</legend>
        <div style="width:auto;height:480px;overflow:scroll;overflow-y:scroll;overflow-x:hidden;">
            <?php
            $log = fopen('CHANGELOG', 'r');

            while ($rows = fgets($log))
            {
                echo $rows.'<br />';
            }

            fclose($log);
            ?>
        </div>
        <hr>
        <i class="icon-share-alt"></i>&nbsp;<a href="../../../">Login</a>
        <?php
    }
    ?>
    <hr>
    <h1>Thanks for using BrightGamePanel V2 :-)</h1>
</div>
