<div>
    <p>{{ $booking->full_name }}様</p>
    <p>こんにちは。</p>
    <p>『パピマミこども撮影会』を運営しております</p>
    <p>株式会社ビーボの矢村でございます。</p>
    <p>この度は『パピマミ無料こども撮影会』の</p>
    <p>キャンセルのお手続き、ありがとうございます。</p>
    <p>以下の通りキャンセルを承りましたので、</p>
    <p>ご確認をお願い致します。</p>
</div>
<div>
<p>------------------------</p>
    <p>＜ご予約情報＞</p>
    <p>■ご予約者名</p>
    <p>{{ $booking->full_name }}さま</p>
    <p>■ご予約日時</p>
    <p>{{ $booking->scheduleInfo->date . ' ' . $booking->scheduleInfo->start_time . '〜' . $booking->scheduleInfo->end_time }}</p>
    <p>■会場</p>
    <p>{!! $booking->scheduleInfo->eventInfo->place !!}</p>
    <p>■アクセス：</p>
    <p>{!! $booking->scheduleInfo->eventInfo->access_direction !!}</p>
    <p>------------------------</p>
<br>
</div>
<div>
<p>首都圏を中心にほぼ毎日開催しておりますので、</p>
<p>よろしければ別日程もご覧頂けると幸いです。</p>
<p>【URL】</p>
<p>https://papimami.jp/familyphoto-schedule/</p>
<p>またお目にかかれることを心待ちにしております♪</p>
<p>どうぞ宜しくお願い致します。</p>
<p>パピマミ</p>
</div>