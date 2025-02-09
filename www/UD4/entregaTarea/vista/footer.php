<?php
$tema = 'light';
if (isset($_COOKIE['tema'])) {
    $tema = $_COOKIE['tema'];
}

$footerBgClass   = 'bg-dark';
$footerTextClass = 'text-white';
?>
<footer class="<?= $footerBgClass ?> <?= $footerTextClass ?> text-center py-2">
    <p>© 2024 Marco Magán Sanz - Todos los derechos reservados.</p>
</footer>
