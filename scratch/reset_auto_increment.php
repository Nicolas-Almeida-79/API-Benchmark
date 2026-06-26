<?php

// Inicializa a aplicação Lumen
$app = require __DIR__.'/../bootstrap/app.php';

use Illuminate\Support\Facades\DB;

try {
    // Altera o auto_increment para reiniciar do 1 (ou do próximo ID vago caso já existam registros)
    DB::statement('ALTER TABLE benchmarks AUTO_INCREMENT = 1;');
    echo "AUTO_INCREMENT da tabela benchmarks reiniciado com sucesso!\n";
} catch (\Exception $e) {
    echo "Erro ao reiniciar AUTO_INCREMENT: " . $e->getMessage() . "\n";
}
