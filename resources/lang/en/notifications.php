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
            'received' => 'New: :campus, :instructor, :class, :date, :time, :type, Lib Instr Req',
            'assigned' => 'Assigned: :campus, :instructor, :class, :date, :time, :type, Lib Instr Req',
            'accepted' => 'Accepted: :campus, :instructor, :class, :date, :time, :type, Lib Instr Req',
            'rejected' => 'Rejected: :campus, :instructor, :class, :date, :time, :type, Lib Instr Req'
        ]
    ],
    'instruction_types' => [
        'on-campus' => 'In-person',
        'remote' => 'Remote',
        'asynchronous' => 'Asynchronous'
    ]
];
