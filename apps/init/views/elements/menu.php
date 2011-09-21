<?php
$menus = array(
    "Dashboard" => $this->url(array('app' => 'init', 'controller' => 'gui', 'action' => 'welcome')),
    _("Circuits") => array(
        _("New reservation") => $this->url(array('app' => 'circuits', 'controller' => 'reservations', 'action' => 'add')),
        _("Reservations") => $this->url(array('app' => 'circuits', 'controller' => 'reservations', 'action' => 'show')),
    ),
    _("Topologies") => array(
        _("MEICANs") => $this->url(array('app' => 'topology', 'controller' => 'meicans', 'action' => 'show')),
        _("Domains") => $this->url(array('app' => 'topology', 'controller' => 'domains', 'action' => 'show')),
        _("Networks") => $this->url(array('app' => 'topology', 'controller' => 'networks', 'action' => 'show')),
        _("Devices") => $this->url(array('app' => 'topology', 'controller' => 'devices', 'action' => 'show')),
        _("URNs") => $this->url(array('app' => 'topology', 'controller' => 'urns', 'action' => 'show')),
    ),
    _("Users") => array(
        _("Users") => $this->url(array('app' => 'aaa', 'controller' => 'users', 'action' => 'show')),
        _("Groups") => $this->url(array('app' => 'aaa', 'controller' => 'groups', 'action' => 'show')),
        _("Access control") => $this->url(array('app' => 'aaa', 'controller' => 'acl', 'action' => 'show')),
    ),
    _("BPM") => array(
        _("Requests") => $this->url(array('app' => 'bpm', 'controller' => 'requests', 'action' => 'show')),
        _("ODE") => $this->url(array('app' => 'bpm', 'controller' => 'ode', 'action' => 'show')),
    ),
);
?>

<?php foreach ($menus as $topName => $v): ?>
    <div class="topItem">
<?php if (!is_array($v)): ?>
            <a href="<?php echo $v; ?>" target="main"><?php echo $topName; ?></a>
    </div>
<?php else: ?>
<?php echo $topName; ?>
    </div>
<?php foreach ($v as $subName => $url): ?>
                <div class="subItem"><a href="<?php echo $url; ?>" target="main"><?php echo $subName; ?></a></div>
<?php endforeach; ?>

<?php endif; ?>

<?php endforeach; ?>
  