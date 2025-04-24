<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

// Se estiver usando Lumen/Laravel, esta classe geralmente estende uma classe base do framework
// e pode conter métodos helper adicionais, como a criação da aplicação.
// Para PHP puro com PHPUnit, estender diretamente PHPUnit\Framework\TestCase é comum.

abstract class TestCase extends BaseTestCase
{
    // Você pode adicionar métodos helper ou configurações comuns aqui
    // por exemplo, um método para criar a instância da aplicação se necessário
    // para testes de integração/funcionais.

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application|\Illuminate\Foundation\Application
     */
    /*
    public function createApplication()
    {
        // Este é um exemplo para Lumen/Laravel, ajuste se não for o caso
        $app = require __DIR__.'/../bootstrap/app.php'; 
        // $app->make(Kernel::class)->bootstrap(); // Exemplo Laravel
        return $app;
    }
    */
} 