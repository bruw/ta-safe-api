<?php

return [
    'auth' => [
        'success' => [
            'login' => 'Usuário autenticado com sucesso.',
            'logout' => 'Usuário desconectado com sucesso.',
        ],
        'errors' => [
            'login' => 'Essas credenciais não foram encontradas em nossos registros.',
        ],
    ],
    'device' => [
        'errors' => [
            'create' => 'Houve um problema ao tentar registrar o dispositivo. ',
            'delete' => 'Houve um problema ao tentar excluir o dispositivo. ',
        ],
    ],
    'device_validation' => [
        'start' => 'Informações enviadas com sucesso! Seu registro esta em análise.',
        'invalid' => 'Não é possível validar este dispositivo pois a nota fiscal é inválida.',
    ],
];
