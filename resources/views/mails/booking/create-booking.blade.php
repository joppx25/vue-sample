<div>
    <p>{{ $booking->full_name }}様</p>
    <p>こんにちは。</p>
    <p>『パピマミこども撮影会』を運営しております</p>
    <p>株式会社ビーボの矢村でございます。</p>
    <p>この度は『パピマミ無料こども撮影会』を</p>
    <p>ご予約いただき誠にありがとうございます。</p>
    <p>以下の通りご予約を承りましたので</p>
    <p>ご確認をお願い致します。</p>
</div>
<br>
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
<br>
<div> 
    <p>やむを得ない理由により、ご予約に変更をする場合は、</p>
    <p>前日正午12時までにご連絡または</p>
    <p>ご変更、キャンセルのお手続きを下記URLよりお願い致します。</p>
    <p><a href="{{ route('guest.booking-details', $booking->booking_id) }}">{{ route('guest.booking-details', $booking->booking_id) }}</a></p>
</div>
<div>
    <p>&nbsp;</p>
    <p>また、現在、期間限定で</p>
    <p>『お友達紹介キャンペーン』を実施中です！</p>
    <p>---------------------------------------------</p>
    <p>■キャンペーン概要</p>
    <p>ご友人さまにイベントをおすすめ頂き、</p>
    <p>ご参加されると、</p>
    <p>どちらにも『アマゾンギフト券500円分』を</p>
    <p>プレゼントしています♪</p>
    <p>&nbsp;</p>
    <p>※どちらの方もがイベントにご参加後、</p>
    <p>1週間以内にクーポンコードにてお届けします。</p>
    <p>※別日でのご参加も適用となります。</p>
    <p>※キャンペーン適用確認のため、</p>
    <p>ご紹介先のご友人さまには申し込みフォームに</p>
    <p>{{ $booking->full_name }}さまのフルネームの入力をお願いしてください。</p>
    <p>&nbsp;</p>
    <p>〜イベントのシェアにはこちらのページをどうぞ〜</p>
    <p>https://papimami.jp/familyphoto</p>
    <p>---------------------------------------------</p>
    <p>&nbsp;</p>
    <p>ご不明点等ございましたら、</p>
    <p>お気軽にお申し付けください。</p>
    <p>それでは、{{ $booking->full_name }}さまにお会いできることを</p>
    <p>スタッフ一同心待ちにしております。</p>
    <p>どうぞ宜しくお願い致します。</p>
    <p>&nbsp;</p>
    <p>パピマミ</p>
</div>
