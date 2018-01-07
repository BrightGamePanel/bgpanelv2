<?php
/**
 * Created by PhpStorm.
 * User: nikita
 * Date: 28/12/2017
 * Time: 22:09
 */

class Wizard_Step1_Page extends Core_Abstract_Page
{

    /**
     * Body of this page
     * This method is called by render()
     */
    public function body()
    {
        $results = $this->parent_module->getController()->invoke('checkRequirements', array());
        $results = $results['data'];
        $failure = false;
        ?>
                    <!-- Check Requirements -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Requirement</th>
                                <th>Status</th>
                                <th>Help</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php

                        // iterate over each software requirement
                        foreach ($results as $requirement => $result) {
                            if ($result === false) {
                                $failure = true;
                            }
?>
                            <tr class="<?php echo ($result === true) ? 'success' : 'danger'; ?>">
                                <td><?php echo $requirement; ?></td>
                                <td><span class="label label-<?php echo ($result === true) ? 'success' : 'danger'; ?>">
                                        <?php echo ($result === true) ? 'OK' : 'FAILURE'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php

                                    if ($result === false) {
                                    ?>
                                    <a href="https://wiki.bgpanel.org/install#<?php echo urlencode($requirement); ?>">
                                        <i class="fa fa-external-link" aria-hidden="true"></i>
                                        &nbsp;<?php echo $requirement; ?> resolution procedure.
                                    </a>
                                    <?php
                                    }

                                    ?>
                                </td>
                            </tr>
<?php
                        }

                        ?>
                        </tbody>
                    </table>
                    <div class="text-center">
                        <nav aria-label="...">
                            <ul class="pager">
                                <li><a href="./wizard"><span aria-hidden="true">&larr;</span> Previous</a></li>
                                <li><button class="btn btn-primary" onclick="window.location.reload();">Check Again</button></li>
                                <?php

                                if (!$failure) {
                                ?>
                                <li><a href="./wizard/step2">Next <span aria-hidden="true">&rarr;</span></a></li>
                                <?php
                                }

                                ?>
                            </ul>
                        </nav>
                    </div>
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
        return 0;
    }

    /**
     * Schema of the form as a JSON String
     *
     * @return string
     */
    public function schema()
    {
        return "";
    }

    /**
     * Form body as a JSON String
     *
     * @return string
     */
    public function form()
    {
        return "";
    }

    /**
     * Model of the forms a JSON String
     *
     * @return string
     */
    public function model()
    {
        return "";
    }

    /**
     * Appends to the response the redirection on a successful query
     *
     * @param $response
     * @return void
     */
    public function redirectionOnSuccess(&$response)
    {
        return;
    }
}