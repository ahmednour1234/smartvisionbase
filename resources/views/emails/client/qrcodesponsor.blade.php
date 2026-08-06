<!DOCTYPE html>
<html lang="en">
@php
    use App\Models\Event;
    use App\Models\HomeSection;
    use App\Models\Setting;

    $event = Event::first();
    $home_slider = HomeSection::where('is_active', true)->where('id', 1)->first();
    $settings = Setting::first();
@endphp

<head>
    <meta charset="UTF-8">
    <title>You're Invited</title>
</head>

<body style="margin:0; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color:#ffffff; color:#000;">

    <!-- ✅ Hero Section with Text over Image using background (email-compatible) -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-image: url('{{ asset($home_slider->media_path) }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <tr>
            <td align="center" style="padding: 60px 30px; background-color: rgba(0,0,0,0.6);">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td align="center" style="color: #fff; max-width: 800px;">
                            <h1 style="font-size: 36px; font-weight: bold; text-transform: uppercase; margin-bottom: 10px; color:white;">Thanks for your sponsorship request</h1>
                            <h2 style="font-size: 22px; font-weight: normal; margin-bottom: 10px;">
                                {{ $event->name_en ?? 'Join us to learn about the latest event trends' }}
                            </h2>
                            <p style="font-size: 15px; opacity: 0.9;">
                                {{ \Carbon\Carbon::parse($event->event_date ?? now())->format('d M Y - h:i A') }}<br>
                                {{ $event->location ?? 'Event Venue' }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- ✅ Main Content -->
    <div style="max-width:800px; margin:40px auto; padding:0 30px; text-align:center;">
        <p style="font-size:17px; line-height:1.7; color:#333;">Dear {{ $client->name }},</p>
        <p style="font-size:17px; line-height:1.7; color:#333;">
            Thank you for submitting your sponsorship request.<br>
            We have received your information and our team will reach out to you shortly.
        </p>

        <!-- ✅ Social Icons -->
        @php
            $socials = [
                'facebook'  => 'facebook.png',
                'twitter'   => 'twitter.png',
                'linkedin'  => 'linkedin.png',
                'youtube'   => 'youtube.png',
                'instagram' => 'instagram.png',
                'x'         => 'x.png',
            ];
            $baseUrl = 'https://toptrustedfxbrokers.iqbrandx.com/public/Socials/';
        @endphp

        @if($settings)
            <div style="margin-top:40px; display:flex; justify-content:center; gap:10px; flex-wrap:wrap;">
                @foreach ($socials as $field => $image)
                    @if (!empty($settings->$field))
                        <a href="{{ $settings->$field }}" target="_blank" style="display:inline-block;">
                            <img src="{{ $baseUrl . $image }}"
                                 alt="{{ $field }} icon"
                                 style="width:30px; height:30px; display:block; border:0;" />
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <!-- ✅ Footer -->
    <div style="text-align:center; font-size:13px; color:#888; margin:20px 0 20px;">
        &copy; {{ now()->year }} Smart Vision. All rights reserved.
    </div>

</body>
</html>
