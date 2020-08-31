<html>
    <head>
    </head>
    <body>
        <div>
            <p>{{ $booking['full_name'] }}さま</p>
            <br/>
            <p>お世話になっております。</p>
            <p>『パピマミこども撮影会』を運営します、</p>
            <p>株式会社ビーボの{{ $booking['staff_name'] }}でございます。</p>
            <br/>
            <p>この度は『パピマミ無料こども撮影会』をご予約いただきまして、</p>
            <p>誠にありがとうございます。</p>
            <br/>
            <p>明日はいよいよ撮影会となりますのでご連絡致しました！</p>
            <br/>
            <p>◯リマインド</p>
            <p>以下ご予約情報をご確認の上、お気をつけてお越しくださいませ。</p>
        </div>
        <br/>
        <div>
            <p>================</p>
            <p>■日時</p>
            <p>{{ $booking['event_date'] }}({{ $booking['event_day_of_week'] }})　{{ $booking['event_start_time'] }}
                    〜{{ $booking['event_end_time'] }}</p>
            <br/>
            <p>■会場</p>
            <p>{{ $booking['place'] }}</p>
            @if (!empty($booking['google_map_url']))
                @if (filter_var($booking['google_map_url'], FILTER_VALIDATE_URL) !== false)
                    <a href="{{ $booking['google_map_url'] }}">{{ $booking['google_map_url'] }}</a>
                @else
                    <p>{{ $booking['google_map_url'] }}</p>
                @endif
            @endif
            <br/>
            <p>■連絡先</p>
            <p>{{ $booking['staff_contact_number'] }}({{ $booking['staff_name'] }})</p>
            <br/>
            <p>■衣装につきまして</p>
            <p>お子様に着させたいお洋服がございましたら</p>
            <p>ぜひご持参くださいませ。</p>
            <p>またご家族でお洋服の色味を合わせていただけますと</p>
            <p>すてきに撮れます！</p>
            <br/>
            <p>■キャンペーンのお知らせ</p>
            <p>『お友達紹介キャンペーン』を実施しています。</p>
            <p>ご友人様とイベントにご参加頂くと、</p>
            <p>どちらの方にもアマゾンギフト券500円分を</p>
            <p>プレゼントしています♪</p>
            <br/>
            <p>※ご友人様のご参加は同日でも別日でも構いません！</p>
            <p>※どちらの方もイベントに参加された後、</p>
            <p>1週間以内にクーポンコードにて</p>
            <p>ギフト券をお届けします。</p>
            <p>※キャンペーン適用確認のため、</p>
            <p>紹介先のご友人様には申し込みフォーム内で</p>
            <p>{{ $booking['full_name'] }}さまのフルネームの記載をお願いしてください。</p>
            <p>※イベントのシェアにはこちらのページをどうぞ！</p>
            <a href="https://papimami.jp/familyphoto">https://papimami.jp/familyphoto</a>
            <p>================</p>
        </div>
        <br/>
        <div>
            <p>それでは、{{ $booking['full_name'] }}さまにお会いできることを</p>
            <p>スタッフ一同心待ちにしております。</p>
            <p>どうぞ宜しくお願い致します。</p>
            <br/>
            <p>パピマミ {{ $booking['staff_name'] }}</p>
        </div>
    </body>
</html>
