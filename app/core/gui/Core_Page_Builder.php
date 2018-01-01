<?php

class Core_Page_Builder {

    /**
     * @var Core_Page_Interface Page handle
     */
    private $page = null;

    /**
     * @var Core_Lang Language manager handle
     */
    private $lang_manager = null;

    /**
     * @var Core_AuthService_Session manager handle
     */
    private $auth_manager = null;

    /**
     * @var string Bootstrap 3 Template Filename
     */
    private $template = '';

    /**
     * Core_Page_Builder constructor.
     * @param Core_Page_Interface $page The page to build
     * @param string $template Bootstrap template name
     */
    public function __construct($page, $template = CONF_DEFAULT_TEMPLATE)
    {
        $this->page = $page;
        $this->lang_manager = Core_Lang::getLangManager();
        $this->auth_manager = Core_AuthService_Session::getService();
        $this->template = $template;
    }

    public function buildHeader()
    {
//------------------------------------------------------------------------------------------------------------+
?><!DOCTYPE html>
<html ng-app="bgpApp" lang="<?php

    // Language
    echo htmlspecialchars( substr($this->lang_manager->getLanguage(), 0, 2), ENT_QUOTES );

    ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Powered By Bright Game Framework -->

        <title><?php

        // Tab Title
        echo htmlspecialchars( $this->page->getModuleTitle() . ' - ' . BGP_PANEL_NAME, ENT_QUOTES );

        ?></title>

        <base href="<?php echo BASE_URL; ?>">

        <!-- Style -->
        <!-- Bootstrap 3 -->
        <link href="./res/bootstrap3/css/<?php echo htmlspecialchars( $this->template, ENT_QUOTES ); ?>" rel="stylesheet">
        <!-- MetisMenu -->
        <link href="./res/metisMenu/css/metisMenu.min.css" rel="stylesheet">
        <!-- Font Awesome 4 -->
        <link href="./res/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <link href="./res/font-awesome/css/font-awesome-animation.min.css" rel="stylesheet">
        <!-- DataTables -->
        <link href="./res/datatables/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <!-- SB Admin 2 -->
        <link href="./res/bootstrap3/css/dashboard.css" rel="stylesheet"><?php

        // Load CSS Dependencies
        $stylesheets = $this->page->getStylesheets();
        if ( !empty($stylesheets) ) {
            foreach ($stylesheets as $depend)
            {
        ?>

        <!-- <?php echo $depend['comment']; ?> -->
        <link href="<?php echo $depend['href']; ?>" rel="stylesheet"><?php
            }
        }

        ?>

        <!-- Javascript -->
        <script src="./res/angularjs/js/angular.min.js"></script>
        <script src="./res/angularjs/js/angular-animate.min.js"></script>
        <script src="./res/angularjs/js/angular-sanitize.min.js"></script>
        <script src="./res/angularjs/js/angular.file-saver.bundle.min.js"></script>
        <script src="./res/jquery/js/jquery.min.js"></script>
        <script src="./res/bootstrap3/js/bootstrap.min.js"></script>
        <!-- Angular Busy -->
        <script src="./res/angularjs/js/angular.busy.min.js"></script>
        <!-- Angular Schema Form Dependencies -->
        <script src="./res/libs/js/tv4.min.js"></script>
        <script src="./res/libs/js/objectpath.js"></script>
        <!-- Angular Schema Form -->
        <script src="./res/angularjs/js/angular.schema-form.min.js"></script>
        <script src="./res/angularjs/js/angular.bootstrap-decorator.min.js"></script>
        <!-- Metis Menu Plugin -->
        <script src="./res/metisMenu/js/metisMenu.min.js"></script>
        <!-- DataTables -->
        <script src="./res/datatables/js/jquery.dataTables.min.js"></script>
        <script src="./res/datatables/js/dataTables.bootstrap.min.js"></script>
        <!-- SB Admin 2 -->
        <script src="./res/bootstrap3/js/sb-admin-2.js"></script>
        <!-- Javascript Functions -->
        <script src="./res/libs/js/toCamel.func.js"></script><?php

        // Load Javascript Dependencies
        $js = $this->page->getJavascript();
        if ( !empty($js) ) {
            foreach ($js as $depend)
            {
        ?>

        <!-- <?php echo $depend['comment']; ?> -->
        <script src="<?php echo $depend['src']; ?>"></script><?php
            }
        }

        ?>

        <!-- Favicon -->
        <link rel="icon" href="./res/img/favicon.ico">
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <!-- Powered By Bright Game Framework -->

    <body id="wrapper" ng-controller="bgpCtrl" cg-busy="bgpPromise">

        <!-- NAVIGATION -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation">
        <?php

        $this->buildNavigationBar();
        $this->buildSideBar();

        ?>
        </nav>
        <!-- END: NAVIGATION -->

        <!-- PAGE WRAPPER -->
        <div id="page-wrapper">

            <!-- MAIN ROW -->
            <div class="row">

                <!-- MAIN CELL -->
                <div class="col-lg-12">
                    <h1 class="page-header">
                        <i class="<?php echo htmlspecialchars( $this->page->getIcon(), ENT_QUOTES ); ?>"></i>&nbsp;
                        <?php echo htmlspecialchars( $this->page->getPageTitle(), ENT_QUOTES ); ?>

                        <small><?php echo htmlspecialchars( $this->page->getPageDescription(), ENT_QUOTES ); ?></small>
                    </h1>

                    <!-- ALERTS --><?php

                    $this->buildAlert();

                    ?><!-- END: ALERTS -->

                    <!-- PAGE BODY -->
<?php
    }

    public function buildFooter()
    {
?>
                    <!-- END: PAGE BODY -->

                    <hr>

                    <a href="#" class="go-top"><i class="fa fa-chevron-circle-up fa-fw"></i></a>

                    <!-- FOOTER -->
                    <footer>
                        <div class="pull-left">
                            Copyleft <img id="footer-copyleft-logo" height="12" src="./res/img/copyleft.png"> 2018. Released under the <a href="http://www.gnu.org/licenses/gpl.html" target="_blank">GPLv3</a>.<br />
                            All images are copyrighted by their respective owners.
                        </div>

                        <div class="pull-right text-right">
                            <a href="http://www.bgpanel.net/" target="_blank">Bright Game Panel</a>&nbsp;V2
                            <br />
                            <a href="https://github.com/warhawk3407/bgpanelv2/" target="_blank"><i class="fa fa-github fa-fw"></i></a>&nbsp;
                            <a href="https://twitter.com/BrightGamePanel/" target="_blank"><i class="fa fa-twitter fa-fw"></i></a>&nbsp;
                            <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=7SDPVBR9EMQZS" target="_blank"><i class="fa fa-paypal fa-fw"></i></a>&nbsp;
                        </div>
                    </footer>
                    <!-- END: FOOTER -->
                </div>
                <!-- END: MAIN CELL -->
            </div>
            <!-- END: MAIN ROW -->
        </div>
        <!-- END: PAGE WRAPPER -->
    </body>

    <!-- Powered By Bright Game Framework -->

</html>
<?php
    }

    private function buildNavigationBar()
    {
?>

            <!-- TOP HEADER NAVBAR -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Bright Game Panel V2</a>
            </div>
            <!-- END: TOP HEADER NAVBAR -->

            <!-- Breadcrumbs --><?php

            // Breadcrumbs
            $this->buildNavigationBarBreadcrumbs();

            ?>

            <!-- END: Breadcrumbs -->

            <!-- TOP NAVBAR -->
            <ul class="nav navbar-top-links navbar-right"><?php

            if (!array_key_exists('empty_navbar', $this->page->getOptions())) {
                if ($this->auth_manager->isLoggedIn()) {
                    ?>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user" role="menu">
                        <li role="presentation" class="dropdown-header"><?php

                        echo htmlspecialchars(
                            $this->auth_manager->getFirstname() .
                            ' ' .
                            $this->auth_manager->getLastname() .
                            ' @' .
                            $this->auth_manager->getLoggedUser()
                            , ENT_QUOTES);

                        ?></li>
                        <li class="divider"></li>
                        <li>
                            <a href="./myaccount">
                                <i class="fa fa-gear fa-fw"></i>&nbsp;<?php echo T_('Settings'); ?>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="./logout"><i class="fa fa-sign-out fa-fw"></i></a>
                </li>
                <?php
                }
            }

            ?></ul>
            <!-- END: TOP NAVBAR -->

<?php
    }

    private function buildNavigationBarBreadcrumbs()
    {
?>

            <div class="nav navbar-left"><?php

            if (!array_key_exists('empty_navbar', $this->page->getOptions()))
            {
                ?>

                <ol class="navbar-breadcrumbs">
                    <li>
                        <a href="#"><span class="fa fa-home fa-fw"></span><?php echo T_('Home'); ?></a>
                    </li>
                    <li>
                        <a href="<?php echo $this->page->getModuleHRef(); ?>">
                            <?php echo htmlspecialchars( $this->page->getModuleTitle(), ENT_QUOTES ); ?>

                        </a>
                    </li><?php

                /**
                 * Build page hierarchy
                 *
                 * @var Core_Page_Interface $parent
                 */
                $parents = array();
                $parent = $this->page->getParent();
                while (!empty($parent)) {
                    $parents[] = $parent;
                    $parent = $parent->getParent();
                }
                for($i = (count($parents) - 1); $i >= 0; $i--) {
                    $parent = $parents[$i];
                    ?>
                    <li>
                    <a href="<?php echo $parent->getHRef(); ?>">
                        <?php echo htmlspecialchars( $parent->getPageTitle(), ENT_QUOTES ); ?>

                    </a>
                    </li><?php
                }

                if (!empty($this->page->getName())) {

                    ?>
                    <li class="active">
                        <a><?php echo htmlspecialchars($this->page->getPageTitle(), ENT_QUOTES); ?></a>
                    </li><?php
                }

                ?>

                </ol><?php
            }

            ?>

            </div><?php
    }

    private function buildSideBar()
    {
        if (array_key_exists('no_sidebar', $this->page->getOptions())) {
            return;
        }
    }

    private function buildAlert()
    {
        return;
    }
}