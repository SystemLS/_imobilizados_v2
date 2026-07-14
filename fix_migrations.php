<?php
$dir = __DIR__ . '/database/migrations';
$files = glob($dir . '/*.php');
foreach ($files as $file) {
    $c = file_get_contents($file);
    $c = str_replace(
        ["on('bens')", "table('bens'", "on('salas')", "table('salas'", "on('pisos')", "table('pisos'", "on('subcategorias')", "table('subcategorias'", "on('materiais')", "table('materiais'", "on('estado_conservacao')", "table('estado_conservacao'"],
        ["on('Bens')", "table('Bens'", "on('Salas')", "table('Salas'", "on('Pisos')", "table('Pisos'", "on('Subcategorias')", "table('Subcategorias'", "on('Materiais')", "table('Materiais'", "on('EstadoConservacao')", "table('EstadoConservacao'"],
        $c
    );
    file_put_contents($file, $c);
}
echo "Correcoes feitas!\n";
