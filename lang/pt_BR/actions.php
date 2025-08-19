<?php

return [
    'auth' => [
        'success' => [
            'register' => 'Usuário registrado com sucesso.',
            'login' => 'Usuário autenticado com sucesso.',
            'logout' => 'Usuário desconectado com sucesso.',
        ],
        'errors' => [
            'register' => 'Ocorreu um erro ao registrar o usuário.',
            'login' => 'Ocorreu um erro ao autenticar o usuário.',
            'logout' => 'Ocorreu um erro ao desconectar o usuário.',
        ],
    ],
    'device_validation' => [
        'start' => 'Informações enviadas com sucesso! Seu registro esta em análise.',
        'invalid' => 'Não é possível validar este dispositivo pois a nota fiscal é inválida.',
    ],
    'device' => [
        'success' => [
            'register' => 'Dispositivo registrado com sucesso!',
            'delete' => 'Dispositivo excluído com sucesso!',
        ],
        'errors' => [
            'register' => 'Ocorreu um erro ao registrar o dispositivo.',
        ],
    ],
    'device_transfer' => [
        'success' => [
            'create' => 'Transferência de registro criada com sucesso!',
        ],
        'errors' => [
            'create' => 'Ocorreu um erro ao criar a transferência.',
            'accept' => 'Ocorreu um ao aceitar a transferência.',
        ],
    ],
];
