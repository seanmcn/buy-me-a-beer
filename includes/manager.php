<?php

class BuyMeABeer
{
    protected $loader;
    protected $plugin_slug;
    protected $version;

    public function __construct()
    {
        $this->plugin_slug = 'buyMeABeer';
        $this->version = '0.0.2';

        $this->loadDependencies();
        $this->defineAdminHooks();
        $this->definePublicHooks();
    }

    private function loadDependencies()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/public.php';
        require_once plugin_dir_path(__FILE__) . 'loader.php';
        $this->loader = new BuyMeABeerLoader();
    }

    private function defineAdminHooks()
    {
        $admin = new BuyMeABeerAdmin($this->getVersion());
        $this->loader->addAction('admin_menu', $admin, 'adminMenu');
        $this->loader->addAction('admin_enqueue_styles', $admin, 'adminEnqueueStyles');
        $this->loader->addAction('admin_enqueue_scripts', $admin, 'adminEnqueueScripts');
    }

    private function definePublicHooks()
    {
        $public = new BuyMeABeerPublic($this->getVersion());
    }

    public function activatePlugin() {
        // check user can activate plugin
        if ( ! current_user_can( 'activate_plugins' ) )  return;
        $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
        check_admin_referer( "activate-plugin_{$plugin}" );

        $admin = new BuyMeABeerAdmin($this->getVersion());
        $admin->installation();
    }

    public function run()
    {
        $this->loader->run();
    }

    public function getVersion()
    {
        return $this->version;
    }

}