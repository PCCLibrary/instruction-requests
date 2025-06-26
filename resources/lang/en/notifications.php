<?php

return [
    'subjects' => [
        'instructor' => [
            'confirmation' => 'Confirmation: :type library instruction request for :class :date_phrase - :campus',
            'date_phrases' => [
                'on' => 'on :datetime',
                'by' => 'by :date'
            ]
        ],
        'librarian' => [
            'new_request' => 'Library Instruction Request: :type, :date, :campus, :class, :instructor'
        ]
    ],
    'instruction_types' => [
        'on-campus' => 'In-person',
        'remote' => 'Remote',
        'asynchronous' => 'Asynchronous'
    ]
];
