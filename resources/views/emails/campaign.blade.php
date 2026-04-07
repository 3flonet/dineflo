<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #1a1a1a;
            padding: 30px;
            text-align: center;
        }
        .header img {
            max-width: 150px;
        }
        .content {
            padding: 40px;
            color: #333333;
            line-height: 1.6;
        }
        .footer {
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #888888;
            background-color: #fafafa;
            border-top: 1px solid #efefef;
        }
        .branding {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #cccccc;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2563eb;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if($restaurant->logo)
                <img src="{{ url('storage/' . $restaurant->logo) }}" alt="{{ $restaurant->name }}">
            @else
                <h1 style="color: #ffffff; margin: 0;">{{ $restaurant->name }}</h1>
            @endif
        </div>
        
        <div class="content">
            {!! $content !!}
        </div>

        <div class="footer">
            <p><strong>{{ $restaurant->name }}</strong></p>
            <p>{{ $restaurant->address }}</p>
            <p>{{ $restaurant->phone }}</p>
            
            @if($showBranding)
                <div class="branding">
                    <p>Powered by <a href="{{ config('app.url') }}" style="color: #2563eb; text-decoration: none; font-weight: bold;">Dineflo</a></p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
