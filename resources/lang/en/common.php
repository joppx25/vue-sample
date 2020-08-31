<?php

return [
    // days
    'sunday'    => 'Sunday',
    'monday'    => 'Monday',
    'tuesday'   => 'Tuesday',
    'wednesday' => 'Wednesday',
    'thursday'  => 'Thursday',
    'friday'    => 'Friday',
    'saturday'  => 'Saturday',

    'booking_slot_full'                  => 'Booking is full',
    'email_subject_create_booking'       => '【Papimami Booking】:name received your booking。 (Automatically sent mail)',
    'email_subject_update_booking'       => '【Papimami Booking】:name have received a change of your booking. (Automatically sent mail)',
    'email_subject_cancel_booking'       => '【Papimami Booking】:name Cancel your booking (Automatically sent mail)',
    'email_subject_booking_reminder_1d'  => '【Papimami Children Photo Session】We look forward to seeing you on the day!',
    'email_subject_booking_reminder_5d'  => '【Papimami Children Photo Session】Confirmation of reservation 5 days in advance',
    'email_subject_booking_reminder_2w'  => '【Papimami Children Photo Session】Confirmation of reservation two weeks in advance',
    'invalid_tel_format'                 => 'Invalid tel format',
    'required'                           => 'Required',
    'error'                              => 'There was a problem',
    'email'                              => 'Email',
    'password'                           => 'Password',
    'event_created_successfully'         => 'Event created successfully',
    'event_updated_successfully'         => 'Event updated successfully',
    'delete_this_event_question'         => 'Are you sure you want to delete this event?',
    'delete_this_schedule_question'      => 'Are you sure you want to delete this schedule?',
    'yes_delete_it'                      => 'Yes delete it!',
    'no_keep_it'                         => 'No, keep it!',
    'deleted'                            => 'Successfully deleted!',
    'no_records'                         => 'No record(s) found',
    'reserved'                           => 'reserved',
    'full'                               => 'full',

    // validations
    'validation' => [
        'date'                              => 'Date is required',
        'capacity'                          => 'Please specify a number greater than or equal to 1',
        'start_time_less_than_current_time' => 'Expected allowed time for start time should not be less than the current time',
        'past_date_not_allowed'             => 'Past date is not allowed',
        'same_schedule_not_allowed'         => 'Same schedule is not allowed',
        'file_too_large'                    => 'File is too large. Allowed maximum size should only be 2mb or less',
        'incorrect_format_or_invalid'       => 'Incorrect format or Invalid start time and end time',
        'less_than_count_booking'           => 'Capacity cannot be lower down to the book count',
    ],
];
