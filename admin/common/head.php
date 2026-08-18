
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
        <link href="assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
        <link href="assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/global/plugins/morris/morris.css" rel="stylesheet" type="text/css" />
        <link href="assets/global/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/global/plugins/jqvmap/jqvmap/jqvmap.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <link href="assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/layouts/layout/css/styles.css" rel="stylesheet" type="text/css" />
        <link href="assets/layouts/layout/css/animate.css" rel="stylesheet" type="text/css" />
        
        <!-- Ajax Dropdown for product add  -->
         <script src="assets/global/plugins/DynamicDrp/jquery.min.js" type="text/javascript"></script>
        
        <!-- auto Price set  -->
         <script type="text/javascript" src="assets/global/plugins/numaricFunction/autoNumeric.js"></script>
        
         <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="assets/pages/css/invoice.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL STYLES -->
        

        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="../assets/img/logo/voltix_logo.png" type="image/png" /> 

        <link href="assets/layouts/layout/css/celander_jquery-ui.css" rel="stylesheet" type="text/css" />

<style>
:root {
    --bg: #f8fafc;
    --surface: #ffffff;
    --ink: #0f172a;
    --muted: #64748b;
    --line: #e2e8f0;
    --accent: #0284c7;
    --accent-d: #0369a1;
    --accent-soft: #e0f2fe;
    --sidebar: #090d16;
    --sidebar-ink: #cbd5e1;
    --sidebar-active: #00f0ff;
    --ok: #10b981;
    --err: #ef4444;
}

body, .page-header, .page-content, .portlet {
    font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
}

/* Override sidebar layout to hide sidebar and use full width */
.page-sidebar-wrapper { display: none !important; }
.page-content-wrapper .page-content { margin-left: 0 !important; }
.page-header.navbar .menu-toggler { display: none !important; }

/* Hide logo */
.page-logo { display: none !important; }

/* Fix for fixed navbar - ensure content doesn't overlap */
.page-header-fixed .page-container {
    margin-top: 46px !important;
}

/* Page header - fixed at top */
.page-header.navbar {
    position: fixed !important;
    top: 0;
    left: 0;
    right: 0;
    z-index: 9995;
    height: 46px;
    background-color: var(--sidebar);
}

/* Header inner flex layout */
.page-header-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 46px;
    padding: 0 10px;
    position: relative;
}

/* Horizontal menu - always visible, NO overflow hidden */
.hor-menu {
    display: block !important;
    flex: 1;
    margin: 0;
    position: static;
}

.hor-menu,
.hor-menu .navbar-nav,
.hor-menu .navbar-nav > li {
    background-color: var(--sidebar) !important;
}

.hor-menu .navbar-nav {
    display: flex;
    flex-wrap: nowrap;
    margin: 0;
    padding: 0;
    list-style: none;
}

.hor-menu .navbar-nav > li {
    position: relative;
    flex-shrink: 0;
}

.hor-menu .navbar-nav > li > a {
    display: block;
    padding: 14px 10px;
    color: var(--sidebar-ink);
    font-size: 13px;
    text-decoration: none;
    white-space: nowrap;
    background-color: transparent;
}

.hor-menu .navbar-nav > li > a:hover {
    background-color: var(--sidebar-active) !important;
    color: #fff !important;
}

.hor-menu .navbar-nav > li.active > a {
    background-color: var(--sidebar-active) !important;
    color: #fff !important;
}

.hor-menu .navbar-nav > li.open > a,
.hor-menu .navbar-nav > li.open > a:hover,
.hor-menu .navbar-nav > li.open > a:focus {
    background-color: var(--sidebar-active) !important;
    color: #fff !important;
}

/* Dropdown menu styles - VISIBLE OUTSIDE HEADER */
.hor-menu .navbar-nav > li > .dropdown-menu {
    display: none;
    position: absolute;
    top: 46px; /* Same as header height */
    left: 0;
    margin: 0;
    padding: 0;
    background-color: var(--sidebar);
    border: 0;
    border-radius: 0;
    z-index: 99999;
    min-width: 200px;
    box-shadow: none;
    list-style: none;
}

.hor-menu .navbar-nav > li:hover > .dropdown-menu,
.hor-menu .navbar-nav > li.open > .dropdown-menu {
    display: block;
}

.hor-menu .dropdown-menu > li {
    list-style: none;
}

.hor-menu .dropdown-menu > li > a {
    color: var(--sidebar-ink);
    padding: 12px 16px;
    display: block;
    white-space: nowrap;
    text-decoration: none;
    font-size: 16px;
    line-height: 1.2;
    background-color: transparent;
}

.hor-menu .dropdown-menu > li.active > a,
.hor-menu .dropdown-menu > li > a:focus {
    background-color: var(--sidebar-active);
    color: #fff;
    font-weight: 600;
    outline: none;
}

.hor-menu .dropdown-menu > li > a:hover {
    background-color: var(--sidebar-active);
    color: #fff;
    outline: none;
}

.hor-menu .dropdown-menu > li > a i {
    margin-right: 8px;
    width: 16px;
}

.page-header.navbar .hor-menu .navbar-nav>li .dropdown-menu li>a,
.page-header.navbar .hor-menu .navbar-nav>li .dropdown-menu li>a>i {
    color: var(--sidebar-ink) !important;
}

.page-header.navbar .hor-menu .navbar-nav>li .dropdown-menu li:hover>a,
.page-header.navbar .hor-menu .navbar-nav>li .dropdown-menu li:hover>a>i,
.page-header.navbar .hor-menu .navbar-nav>li .dropdown-menu li.active>a,
.page-header.navbar .hor-menu .navbar-nav>li .dropdown-menu li.active>a:hover,
.page-header.navbar .hor-menu .navbar-nav>li .dropdown-menu li.active>a>i,
.page-header.navbar .hor-menu .navbar-nav>li .dropdown-menu li.current>a,
.page-header.navbar .hor-menu .navbar-nav>li .dropdown-menu li.current>a:hover,
.page-header.navbar .hor-menu .navbar-nav>li .dropdown-menu li.current>a>i {
    background-color: var(--sidebar-active) !important;
    color: #fff !important;
}

.page-header.navbar .hor-menu .navbar-nav>li .dropdown-submenu>a:after {
    border-left-color: var(--sidebar-ink) !important;
}

/* Submenu (nested dropdown) handling */
.dropdown-submenu {
    position: relative;
}

.dropdown-submenu > .dropdown-menu {
    display: none;
    position: absolute;
    top: 0;
    left: 100%;
    margin: 0;
    padding: 0;
    background-color: var(--sidebar);
    border-radius: 0;
    z-index: 100000;
    min-width: 200px;
    box-shadow: none;
}

.dropdown-submenu:hover > .dropdown-menu {
    display: block;
}

.dropdown-submenu > a:after {
    content: "";
    display: inline-block;
    float: right;
    width: 0;
    height: 0;
    border-style: solid;
    border-width: 5px 0 5px 5px;
    border-color: transparent transparent transparent #ccc;
    margin-top: 6px;
    margin-left: 10px;
}

.dropdown-submenu:hover > a:after {
    border-left-color: #fff;
}

/* Divider in dropdown */
.hor-menu .dropdown-menu > li.divider {
    height: 1px;
    margin: 5px 0;
    background-color: #4a3528;
}

/* Top menu (user dropdown) */
.top-menu {
    flex-shrink: 0;
    margin-left: 10px;
    position: relative;
}

.top-menu .navbar-nav {
    margin: 0;
    padding: 0;
}

.top-menu .navbar-nav > li {
    display: inline-block;
    position: relative;
}

/* User dropdown */
.dropdown-user .dropdown-toggle {
    display: flex;
    align-items: center;
    padding: 8px 10px;
    color: #f0dfca;
    text-decoration: none;
    cursor: pointer;
}

.dropdown-user .dropdown-toggle img {
    width: 30px;
    height: 30px;
    margin-right: 8px;
    border-radius: 50%;
}

.dropdown-user .dropdown-toggle .username {
    margin-right: 5px;
}

.dropdown-user .dropdown-menu {
    display: none;
    position: absolute;
    top: 46px;
    right: 0;
    left: auto;
    margin: 0;
    padding: 5px 0;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    z-index: 100000;
    min-width: 160px;
    list-style: none;
}

.dropdown-user:hover .dropdown-menu,
.dropdown-user.open .dropdown-menu {
    display: block;
}

.dropdown-user .dropdown-menu > li > a {
    display: block;
    padding: 10px 15px;
    color: #333;
    text-decoration: none;
}

.dropdown-user .dropdown-menu > li > a:hover {
    background-color: #f5f5f5;
}

.dropdown-user .dropdown-menu > li > a i {
    margin-right: 8px;
    width: 16px;
}

/* ==================== PAGE BAR STYLES ==================== */
.page-content,
.page-content-white .page-content {
    background-color: var(--bg) !important;
}

.page-content-white .page-bar,
.page-container-bg-solid .page-bar {
    background-color: var(--bg) !important;
    border-bottom: 1px solid var(--line) !important;
}

.page-bar {
    background-color: var(--accent-soft);
    padding: 10px 15px;
    margin: 0 -20px 20px -20px;
    min-height: 40px;
}

.page-bar .page-breadcrumb {
    display: none !important;
}

/* ==================== RESPONSIVE - Keep menu same, just adjust sizing ==================== */
@media (max-width: 1199px) {
    .hor-menu .navbar-nav > li > a {
        padding: 14px 8px;
        font-size: 11px;
    }
}

@media (max-width: 991px) {
    .hor-menu .navbar-nav > li > a {
        padding: 14px 6px;
        font-size: 10px;
    }
    
    .top-menu .username {
        display: none;
    }
    
    .page-bar {
        margin: 0 -20px 15px -20px;
        padding: 8px 15px;
    }
}

@media (max-width: 767px) {
    .page-header-inner {
        padding: 0 5px;
    }
    
    .hor-menu .navbar-nav > li > a {
        padding: 14px 4px;
        font-size: 9px;
    }
    
    .dropdown-user .dropdown-toggle img {
        width: 24px;
        height: 24px;
        margin-right: 0;
    }
    
    .dropdown-user .dropdown-toggle .username,
    .dropdown-user .dropdown-toggle .fa-angle-down {
        display: none;
    }
    
    .page-bar {
        margin: 0 -10px 15px -10px;
        padding: 8px 10px;
    }
}

.page-content-wrapper{
     margin-top: 48px;
}
</style>



