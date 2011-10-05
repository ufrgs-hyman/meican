<?php
include_once('libs/acl_loader.php');


class MenuView {
    public $item = array();


    static public function readMenuXML() {
        $menu = new MenuView();

        $menuXML = new SimpleXmlElement('apps/init/data/menu.xml',0,true);

        $itemsXML = $menuXML->menu->item;

        $ind = 0;

        foreach ($itemsXML as $i) {

            $menu->item[$ind] = new Item();
            $menu->item[$ind]->name = (string) $i->name;
            $menu->item[$ind]->link = ($i->link) ? (string) $i->link : NULL;

            $ind2 = 0;

            if ($i->subItem) {
                foreach ($i->subItem as $s) {
                    $menu->item[$ind]->subItem[$ind2] = new SubItem();
                    $menu->item[$ind]->subItem[$ind2]->name = (string) $s->name;
                    $menu->item[$ind]->subItem[$ind2]->link = (string) $s->link;
                    $menu->item[$ind]->subItem[$ind2]->right = ($s->right) ? (string) $s->right : NULL;
                    $menu->item[$ind]->subItem[$ind2]->model = ($s->model) ? (string) $s->model : NULL;
                    $ind2++;
                }
            }
            $ind++;
        }
        return $menu;
    }

   static public function buildViewMenu($menu) {

        $menuView = new MenuView();

        $ind = 0;

        foreach ($menu->item as $i) {

            $temp = new Item();
            $temp->name = $i->name;
            $temp->link = $i->link;

            if ($i->subItem) {
                $showItem = FALSE;
                $ind2 = 0;
                foreach ($i->subItem as $s) {

                    $showSubItem = TRUE;
                    if ($s->right && $s->model) {
                        $rights = explode(',',$s->right);
                        $showSubItem = FALSE;
                        $acl = new AclLoader();
                        foreach ($rights as $r) {
                            $showSubItem |= $acl->checkACL($r, $s->model);
                        }
                    }
                   
                    if ($showSubItem) {
                        $temp->subItem[$ind2] = new SubItem();
                        $temp->subItem[$ind2]->name = $s->name;
                        $temp->subItem[$ind2]->link = $s->link;

                        $showItem = TRUE;
                        $ind2++;
                    }
                }
            } else {
                $showItem = TRUE;
            }

            if ($showItem) {
                $menuView->item[$ind] = $temp;
                $ind++;
            }
        }
        return $menuView;
    }
}

class Item {

    public $subItem = array();
    public $name;
    public $link;

}

class SubItem {
    public $name;
    public $link;
    public $right;
    public $model;
}


?>

