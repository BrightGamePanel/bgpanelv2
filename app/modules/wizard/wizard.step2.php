<div class="well">
    <h2>Checking for existing tables . . . . .</h2>
<?php


if (isset($currentVersion))
{
?>
    <div class="alert alert-block">
        <strong>FOUND !</strong> Tables exist in the database.<br />
        You can update your previous version of BrightGamePanel V2 or perform a clean install <u>which will overwrite all data (BGP tables with the same prefix) in the database.</u><br />
        It is recommend you back up your database first.<br />
    </div>
    <h4>Current Version:</h4>&nbsp;<span class="label label-info"><?php echo $currentVersion['value']; ?></span>&nbsp;<?php if ($currentVersion['value'] == LASTBGPVERSION) { echo "(up-to-date)"; } ?><br /><br />
    <h4>Select Action :</h4><br />
    <form action="index.php" method="get">
        <input type="hidden" name="step" value="three" />
        <?php
        if ($currentVersion['value'] != LASTBGPVERSION) {
            ?>
            <input name="version" type="radio" value="update" checked="checked" /><b>&nbsp;Update to the Last Version (<?php echo LASTBGPVERSION; ?>)</b><br /><br /><br />
            <?php
        }
        ?>
        <input name="version" type="radio" value="full" <?php if ($currentVersion['value'] == LASTBGPVERSION) { echo "checked=\"checked\""; } ?> /><b>&nbsp;<span class="label label-warning">Perform Clean Install</span>&nbsp;- Version <?php echo LASTBGPVERSION; ?></b><br /><br />
        <button type="submit" class="btn btn-primary">Install SQL Database</button>
    </form>
</div>
<?php
}
else
{
    ?>
    <span class="label label-success">No tables found in the database</span><br /><br />
    <form action="index.php" method="get">
        <input type="hidden" name="step" value="three" />
        <input name="version" type="radio" value="full" checked="checked" /><b>&nbsp;Install BGP Version <?php echo LASTBGPVERSION; ?></b><br /><br />
        <button type="submit" class="btn btn-primary">Install SQL Database</button>
    </form>
    </div>
    <?php
}

?>
<div style="text-align: center;">
    <ul class="pager">
        <li>
            <a href="index.php?step=one">&larr; Previous Step</a>
        </li>
    </ul>
</div>

}