<?php

defined ('__FRAMEWORK') or die ("Invalid access.");

include_once 'libs/controller.php';
include_once 'apps/init/data/menu_view.php';

class menu extends Controller {

    public function menu() {
        $this->app = 'init';
        $this->controller = 'menu';
        $this->defaultAction = 'show';
        $this->setLayout('menu');
    }

    public function show() {
        $menu = MenuView::readMenuXML();
        $menuView = MenuView::buildViewMenu($menu);

        $this->setArgsToBody($menuView);
        $this->render();
    }

    /**
     * dummy function created only to generate the msgid in init.po file to translate the menu items
     * the messages writen bellow should be consistent with tags <name> of menu.xml file
     * this function is never called by the system
     */
    private function dummy() {
        _("Circuits");
        _("Reservations");
        
        _("Domain");
        _("Domains");
        _("Networks");
        _("Devices");
        _("URNs");

        _("Users");
        _("My Account");
        _("Groups");
        _("Access Control List");
        
        _("BPM");
        _("Requests");
        _("ODE");
    }

}

?>