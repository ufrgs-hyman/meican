<?php
/*$preMenus = array(
    "Dashboard" => array('url' => array('app' => 'init', 'controller' => 'gui', 'action' => 'welcome')),
    _("Circuits") => array(
        _("New reservation") => array('model' => 'urn_info', 'right' => 'create', 'url' => 
                array('app' => 'circuits', 'controller' => 'reservations', 'action' => 'add')),
        _("Reservations") => array('model' => 'reservation_info', 'url' => 
            array('app' => 'circuits', 'controller' => 'reservations')),
    ),
    _("Topologies") => array(
        _("MEICANs") => array('model' => 'topology', 'url' => 
            array('app' => 'topology', 'controller' => 'meicans', 'action' => 'show')),
        _("Domains") => array('model' => 'domain_info', 'url' => 
            array('app' => 'topology', 'controller' => 'domains', 'action' => 'show')),
        _("Networks") => array('model' => 'network_info', 'url' => 
            array('app' => 'topology', 'controller' => 'networks', 'action' => 'show')),
        _("Devices") => array('model' => 'device_info', 'url' => 
            array('app' => 'topology', 'controller' => 'devices', 'action' => 'show')),
        _("URNs") => array('model' => 'urn_info', 'url' => 
            array('app' => 'topology', 'controller' => 'urns', 'action' => 'show')),
    ),
    _("Users") => array(
        _("Users") => array('model' => 'group_info', 'url' => 
            array('app' => 'aaa', 'controller' => 'users', 'action' => 'show')),
        _("Groups") => array('model' => 'group_info', 'url' => 
            array('app' => 'aaa', 'controller' => 'groups', 'action' => 'show')),
        _("Access control") => array('model' => 'acl', 'url' => 
            array('app' => 'aaa', 'controller' => 'acl', 'action' => 'show')),
    ),
    _("BPM") => array(
        _("Requests") => array('model' => 'request_info', 'url' => 
            array('app' => 'bpm', 'controller' => 'requests', 'action' => 'show')),
        _("ODE") => array('model' => 'request_info', 'url' => 
            array('app' => 'bpm', 'controller' => 'ode', 'action' => 'show')),
    ),
);
$menus = array();
$acl = new AclLoader();
foreach ($preMenus as $name => $sub)
    if (!isset($sub['url'])){
        foreach ($sub as $subname => $link)
            if (empty($link['model']) || $acl->checkACL(isset($link['right'])?$link['right']:'read', $link['model'])){
                if (!isset($menus[$name]))
                    $menus[$name]=array();
                $menus[$name][$subname] = $link['url']; //check link
            }
    } else {
        if (empty($sub['model']) || $acl->checkACL(isset($sub['right'])?$sub['right']:'read', $sub['model'])){
            $menus[$name] = $sub['url']; //check link
        }
    }*/
?>  
  
  
<div id="menu">
	<div id="logo">
		<p>MEICAN</p>
	</div>
    <ul>
    	<?php foreach (MenuItem::getAllMenus() as $menu): ?>
			<?php if (!empty($menu->url)): ?>
				<li><a href="<?php echo $this->url($menu->url); ?>" target="main"><?php echo $menu->label; ?></a></li>
			<?php else: ?>
				<li><?php echo $menu->label; ?></li>
			<?php endif; ?>
			
			<?php if (!empty($menu->sub)): ?>
		    	<ul>
				<?php foreach ($menu->sub as $subMenu): ?>
					<li><a href="<?php echo $this->url($subMenu->url); ?>" target="main"><?php echo $subMenu->label; ?></a></li>
				<?php endforeach; ?>
		        </ul>
			<?php endif; ?>

		<?php endforeach; ?>
</div>
