<?php $base = $this->url(''); ?>

<div class="center"><img src="<?= $base ?>webroot/img/meican_new.png" class="logo" alt="MEICAN"/></div>
<h2><?php echo Configure::read('systemName'); ?></h2>

<p>MEICAN allows network end-users to request, in a more user-friendly way, dedicated circuits in 
    <a href="http://en.wikipedia.org/wiki/Dynamic_circuit_network">Dynamic Circuit Networks</a>. 
    MEICAN also enables network operators to evaluate and accept end-user's circuit requests in environments with multiple domains.
    With MEICAN, you can:</p>

<ul>
    <li>
        <img src="<?= $base ?>webroot/img/bullet1.jpg" alt="Bullet" class="bullet"/>
        <b>Request Circuits</b>
        <p>Network end-user's circuits can be scheduled to be set up and teared down when it is more convenient.</p>
    </li>
    <li>
        <img src="<?= $base ?>webroot/img/bullet2.jpg" alt="Bullet" class="bullet"/>
        <b>Authorize Requests</b>
        <p>Network operators can be notified to accept or reject the requests of establishment of new circuits.</p>
    </li>
    <li>
        <img src="<?= $base ?>webroot/img/bullet3.jpg" alt="Bullet" class="bullet"/>
        <b>Build Automated Policies</b>
        <p>Authorization workflows can be used to automate the decision-making process along the multiple domains where end-user's circuits pass through.</p>
    </li>
</ul>