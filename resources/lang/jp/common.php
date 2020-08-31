<?php

return [
    // days
    'sunday'    => '日',
    'monday'    => '月',
    'tuesday'   => '火',
    'wednesday' => '水',
    'thursday'  => '木',
    'friday'    => '金',
    'saturday'  => '土',

    'booking_slot_full'                  => '予約枠がいっぱいです。',
    'email_subject_create_booking'       => '【パピマミ】:name 様の予約を受け付けました。（自動送信メール）',
    'email_subject_update_booking'       => '【パピマミ】:name 様の予約の変更を受け付けました。（自動送信メール）',
    'email_subject_cancel_booking'       => '【パピマミ】:name 様の予約をキャンセルを受け付けました。（自動送信メール）',
    'email_subject_booking_reminder_1d'  => '【パピマミこども撮影会】当日お会いできることを心待ちにしております！',
    'email_subject_booking_reminder_5d'  => '【パピマミこども撮影会】5日前の予約のご確認',
    'email_subject_booking_reminder_2w'  => '【パピマミこども撮影会】2週間前の予約のご確認',
    'invalid_tel_format'                 => '電話番号の形式不正です。',
    'required'                           => '必須',
    'error'                              => '問題が発生しました。',
    'email'                              => 'メールアドレス',
    'password'                           => 'パスワード',
    'event_created_successfully'         => 'イベント作成成功！',
    'event_updated_successfully'         => 'イベント更新成功！',
    'delete_this_event_question'         => 'イベントを削除してもよろしいですか？',
    'delete_this_schedule_question'      => '予約枠を削除してもよろしいですか？',
    'yes_delete_it'                      => 'はい、削除します',
    'no_keep_it'                         => 'いいえ',
    'deleted'                            => '削除しました！',
    'no_records'                         => 'レコードが見つかりません',
    'reserved'                           => 'ご予約済み',
    'full'                               => '満員',

    // validations
    'validation' => [
        'date'                              => 'イベント日付は必須です',
        'capacity'                          => '定員は1以上の数値を指定してください',
        'start_time_less_than_current_time' => '現在日時以降の時間を指定してください',
        'past_date_not_allowed'             => '過去日は指定できません',
        'same_schedule_not_allowed'         => '同じスケジュールは許可されていません',
        'file_too_large'                    => '添付ファイルのサイズは、2MBまでです。',
        'incorrect_format_or_invalid'       => '開始時間は、終了時間より前の時間を設定してください',
        'less_than_count_booking'           => '定員は予約数を下回る値で更新できません',
    ],
];
