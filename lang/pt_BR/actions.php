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
    'user' => [
        'success' => [
            'update' => 'Perfil atualizado com sucesso.',
        ],
        'errors' => [
            'update' => 'Ocorreu um erro ao atualizar o perfil.',
        ],
    ],
    'device' => [
        'success' => [
            'register' => 'Dispositivo registrado com sucesso!',
            'delete' => 'Dispositivo excluído com sucesso!',
            'token' => 'Código de visualização gerado com sucesso!',
            'validate' => 'Informações enviadas com sucesso! Seu registro esta em análise.',
            'invalidate' => 'Não é possível validar este dispositivo pois a nota fiscal é inválida.',
        ],
        'errors' => [
            'register' => 'Ocorreu um erro ao registrar o dispositivo.',
            'delete' => 'Ocorreu um erro ao excluir o dispositivo.',
            'token' => 'Ocorreu um erro ao gerar o código de visualização.',
            'validate' => 'Ocorreu um erro ao validar o dispositivo.',
            'invalidate' => 'Ocorreu um erro ao invalidar o dispositivo.',
        ],
    ],
    'device_transfer' => [
        'success' => [
            'create' => 'Transferência de registro criada com sucesso!',
            'accept' => 'Transferência aceita com sucesso!',
            'cancel' => 'Transferência cancelada com sucesso!',
            'reject' => 'Transferência rejeitada com sucesso!',
        ],
        'errors' => [
            'create' => 'Ocorreu um erro ao criar a transferência.',
            'accept' => 'Ocorreu um erro ao aceitar a transferência.',
            'cancel' => 'Ocorreu um erro ao cancelar a transferência.',
            'reject' => 'Ocorreu um erro ao rejeitar a transferência.',
        ],
    ],
];
