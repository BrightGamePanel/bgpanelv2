<?php
/**
 * Created by PhpStorm.
 * User: nikita
 * Date: 28/12/2017
 * Time: 22:09
 */

class Wizard_Page extends Core_Abstract_Page
{

    /**
     * Body of this page
     * This method is called by render()
     */
    public function body()
    {
?>
                    <!-- License -->
                    <pre class="pre-scrollable">
                        <?php

                        $license = fopen(LICENSE_FILE, 'r');
                        while ($rows = fgets($license)) {
                            echo $rows;
                        }
                        fclose($license);

                        ?>
                    </pre>
                    <form method="post" target="_self">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="agreement">&nbsp;I Accept the Terms of the License Agreement
                            </label>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-default">Submit</button>
                        </div>
                    </form>
<?php
    }

    /**
     * Process the page
     * This method is executed by default when a POST request is submitted with the page as the target
     *
     * @param array $query_args Request parameters
     * @return int
     */
    public function process($query_args = array())
    {
        echo $this->parent_module->getController()->format(
            $this->parent_module->getController()->invoke('acceptLicense', $query_args)
        );
        return 0;
    }
}