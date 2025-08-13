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
        ],
    ],
];
