<?php $base = $this->url(''); ?>

<div class="center"><img src="<?= $base ?>webroot/img/meican_new.png" class="logo" alt="MEICAN"/></div>
<h2><?php echo Configure::read('systemName'); ?></h2>

<p>MEICAN permite que usuários de redes requisitem , de maneira amigável, circuitos dedicados em
    <a href="http://en.wikipedia.org/wiki/Dynamic_circuit_network">Redes de Circuitos Dinâmicas</a>. 
    MEICAN também permite operadores de rede avaliar e aceitar requisições de circuitos de outros usuários em ambientes com 
    multiplos domínios.
    Com MEICAN, você pode:</p>

<ul>
    <li>
        <img src="<?= $base ?>webroot/img/bullet1.jpg" alt="Bullet" class="bullet"/>
        <b>Requisitar Circuitos</b>
        <p>Circuitos de usuários finais podem ser agendados para serem criados e desativados quando for mais conveniente.</p>
    </li>
    <li>
        <img src="<?= $base ?>webroot/img/bullet2.jpg" alt="Bullet" class="bullet"/>
        <b>Autorisar Requisições</b>
        <p>Operadores de rede podem ser notificados para aceitar ou rejeitar requisições de estabelecimento de novos circuitos.</p>
    </li>
    <li>
        <img src="<?= $base ?>webroot/img/bullet3.jpg" alt="Bullet" class="bullet"/>
        <b>Costrução de Políticas Automatizadas</b>
        <p>Workflows de autorização podem ser usados para automatizar o processo de decisão entre múltiplos domínios onde passam circuitos de usuários finais.</p>
    </li>
</ul>