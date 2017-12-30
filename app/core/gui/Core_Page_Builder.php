<?php

class Core_Page_Builder {

    /**
     * @var Core_Page_Interface Page handle
     */
    private $page = null;

    /**
     * @var Core_Lang Language manager
     */
    private $lang = null;

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
        $this->lang = Core_Lang::getLangManager();
        $this->template = $template;
    }

    public function buildHeader()
    {
//------------------------------------------------------------------------------------------------------------+
?><!DOCTYPE html>
    <html ng-app="bgpApp" lang="<?php

    // Language
    echo htmlspecialchars( substr($this->lang->getLanguage(), 0, 2), ENT_QUOTES );

    ?>">
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <!-- Powered By Bright Game Panel -->

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
                    echo "\n";
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
                    echo "\n";
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

    <body ng-controller="bgpCtrl">

        <!-- Powered By Bright Game Panel -->

    <div id="wrapper" cg-busy="bgpPromise">

        <!-- NAVIGATION -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation">
            <?php
            //------------------------------------------------------------------------------------------------------------+

            // Display Navigation Bar
            echo $this->getNavBar();

            //------------------------------------------------------------------------------------------------------------+
            ?>

            <?php
            //------------------------------------------------------------------------------------------------------------+

            // Display Sidebar
            if (!$this->no_sidebar) {
                echo $this->getSideBar();
            }

            //------------------------------------------------------------------------------------------------------------+
            ?>
        </nav>
        <!-- END: NAVIGATION -->

        <!-- Page Content -->
        <div id="page-wrapper">
        <div class="row">
        <!-- MAIN -->
        <div class="col-lg-12">
        <?php
//------------------------------------------------------------------------------------------------------------+

        // Page Header
        // Title

        if (!empty($this->parent_module_title)) {
//------------------------------------------------------------------------------------------------------------+
            ?>
            <h1 class="page-header">
                <i class="<?php echo htmlspecialchars( $this->module_icon, ENT_QUOTES ); ?>"></i>
                <?php echo htmlspecialchars( $this->parent_module_title, ENT_QUOTES ); ?>
                <i class="fa fa-angle-right"></i>
                <small><?php echo htmlspecialchars( $this->module_title, ENT_QUOTES ); ?></small>
            </h1>
            <?php
//------------------------------------------------------------------------------------------------------------+
        }
        else {
//------------------------------------------------------------------------------------------------------------+
            ?>
            <h1 class="page-header"><i class="<?php echo htmlspecialchars( $this->module_icon, ENT_QUOTES ); ?>"></i>&nbsp;<?php echo htmlspecialchars( $this->module_title, ENT_QUOTES ); ?></h1>
            <?php
//------------------------------------------------------------------------------------------------------------+
        }

//------------------------------------------------------------------------------------------------------------+
        ?>
        <!-- ALERTS -->
        <div id="msg" class="alert alert-dismissible" role="alert" ng-show="msg" ng-class="'alert-' + msgType">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <strong ng-bind="msg"></strong>
        </div>
        <?php
//------------------------------------------------------------------------------------------------------------+

        /**
         * Alerts Handler
         */

        if ( !empty($_SESSION['ALERT']) && !empty($_SESSION['ALERT']['MSG-TYPE']) )
        {

//------------------------------------------------------------------------------------------------------------+
            ?>
            <div id="alert" class="alert alert-dismissible alert-<?php echo htmlspecialchars( $_SESSION['ALERT']['MSG-TYPE'], ENT_QUOTES ); ?>" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <?php
                //------------------------------------------------------------------------------------------------------------+

                if (!empty($_SESSION['ALERT']['MSG-STRONG'])) {
//------------------------------------------------------------------------------------------------------------+
                    ?>
                    <strong><?php echo htmlspecialchars( $_SESSION['ALERT']['MSG-STRONG'], ENT_QUOTES ); ?></strong>&nbsp;
                    <?php
//------------------------------------------------------------------------------------------------------------+
                }

                if (!empty($_SESSION['ALERT']['MSG-BODY'])) {
//------------------------------------------------------------------------------------------------------------+
                    ?>
                    <?php echo htmlspecialchars( $_SESSION['ALERT']['MSG-BODY'], ENT_QUOTES ); ?>
                    <?php
//------------------------------------------------------------------------------------------------------------+
                }

                //------------------------------------------------------------------------------------------------------------+
                ?>
            </div>
            <?php
//------------------------------------------------------------------------------------------------------------+

            unset($_SESSION['ALERT']);
        }

//------------------------------------------------------------------------------------------------------------+
        ?>
        <!-- END: ALERTS -->
<?php
//------------------------------------------------------------------------------------------------------------+
    }

    public function buildFooter() {
        // TODO : implement
    }
}