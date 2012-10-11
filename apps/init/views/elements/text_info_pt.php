<?php $base = $this->url(''); ?>

<div class="center"><img src="<?= $base ?>webroot/img/meican_new.png" class="logo" alt="MEICAN"/></div>
<h2><?php echo Configure::read('systemName'); ?></h2>

<p>O MEICAN permite que usuários de redes requisitem, de uma maneira amigável, circuitos dedicados em
    <a href="http://en.wikipedia.org/wiki/Dynamic_circuit_network">Redes de Circuitos Dinâmicos</a>. 
    O MEICAN também permite que operadores de rede avaliem e aceitem requisições de circuitos de outros usuários em ambientes com 
    multiplos domínios.
    Com o MEICAN, você pode:</p>

<ul>
    <li>
        <img src="<?= $base ?>webroot/img/bullet1.jpg" alt="Bullet" class="bullet"/>
        <b>Requisitar Circuitos</b>
        <p>Circuitos de usuários finais podem ser agendados para serem configurados e removidos quando for mais conveniente.</p>
    </li>
    <li>
        <img src="<?= $base ?>webroot/img/bullet2.jpg" alt="Bullet" class="bullet"/>
        <b>Autorizar Requisições</b>
        <p>Operadores de rede podem ser notificados para aceitar ou rejeitar requisições de estabelecimento de novos circuitos.</p>
    </li>
    <li>
        <img src="<?= $base ?>webroot/img/bullet3.jpg" alt="Bullet" class="bullet"/>
        <b>Costruir Políticas Automatizadas</b>
        <p>Workflows de autorização podem ser utilizados para automatizar o processo de tomada de decisão entre os múltiplos domínios por onde passam os circuitos de usuários finais.</p>
    </li>
</ul>