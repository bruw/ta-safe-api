<?php

return [
    'device' => [
        'user' => [
            'owner' => 'Somente o proprietário do dispositivo pode realizar esta ação.',
        ],
        'status' => [
            'rejected' => 'O registro de validação do dispositivo precisa estar com status rejeitado.',
            'validated' => 'O registro de validação do dispositivo precisa estar com status validado.',
        ],
        'transfer' => [
            'same_user' => 'Não é possível transferir um dispositivo para si mesmo.',
            'pending' => 'O dispositivo possui uma transferência pendente.',
            'not_pending' => 'A transferência do dispositivo precisa estar pendente.',
            'not_source_user' => 'Somente o usuário de origem da transferência pode realizar esta ação.',
            'not_target_user' => 'Somente o usuário de destino da transferência pode realizar esta ação.',
        ],
    ],
];
