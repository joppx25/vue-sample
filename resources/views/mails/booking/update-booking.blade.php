<div>
<p>{{ $booking->full_name }}様</p>
    <p>こんにちは。</p>
    <p>『パピマミこども撮影会』を運営しております</p>
    <p>株式会社ビーボの矢村でございます。</p>
    <p>この度は『パピマミ無料こども撮影会』の</p>
    <p>ご予約変更、誠にありがとうございます♪</p>
    <p>以下の通りご変更を承りましたので、</p>
    <p>ご確認お願い致します。</p>
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
    <p>■連絡先：</p>
    <p>お電話：090-6027-5091</p>
    <p>メール：media@bbo.co.jp</p>
    <p>------------------------</p>
</div>
<div>
<p>それでは、{{ $booking->full_name }}さまにお会いすることを</p>
<p>スタッフ一同心待ちにしております♪</p>
<p>どうぞ宜しくお願い致します。</p>
<p>パピマミ</p>
</div>