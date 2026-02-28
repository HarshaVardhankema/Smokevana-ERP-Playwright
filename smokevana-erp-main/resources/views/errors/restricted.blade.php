<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Restricted</title>
    <style>
        :root {
            --bg: #0f172a;
            --card: #1f2937;
            --accent: #10b981;
            --text: #e5e7eb;
            --muted: #9ca3af;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 10% 20%, #111827 0, #0b1222 25%, #0f172a 50%, #0b1222 75%, #0a1020 100%);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: var(--text);
        }
        .card {
            background: linear-gradient(135deg, rgba(31, 41, 55, 0.9), rgba(17, 24, 39, 0.9));
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 15px 60px rgba(0, 0, 0, 0.45);
            border-radius: 18px;
            padding: 28px 32px;
            max-width: 480px;
            width: min(90vw, 500px);
            text-align: center;
            backdrop-filter: blur(6px);
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(16, 185, 129, 0.12);
            color: #6ee7b7;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.02em;
            border: 1px solid rgba(16, 185, 129, 0.35);
        }
        h1 {
            margin: 18px 0 8px;
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: 0.01em;
        }
        p {
            margin: 8px 0;
            line-height: 1.5;
            color: var(--muted);
        }
        .agent {
            margin-top: 18px;
            padding: 12px 14px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px dashed rgba(255, 255, 255, 0.12);
            font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace;
            color: #cbd5e1;
            word-break: break-all;
        }
        .footer {
            margin-top: 20px;
            font-size: 0.9rem;
            color: var(--muted);
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="badge">Access Restricted</div>
        <h1>Only approved agent can view this page</h1>
        <p>Please access the application using the authorized user agent.</p>
        <!-- <p>Allowed User-Agent: <strong>{{ $allowedAgent }}</strong></p>

        @if(!empty($error))
            <div class="agent" style="border-color:#f87171;color:#fecdd3;background:rgba(248,113,113,0.08);">
                {{ $error }}
            </div>
        @endif

        @if(!empty($userAgent))
            <div class="agent">{{ $userAgent }}</div>
        @else
            <div class="agent">No User-Agent was sent with this request.</div>
        @endif -->

        <div class="footer">If you believe this is a mistake, contact your administrator.</div>
    </div>

    {{-- Hidden unlock control (clickable but visually hidden) --}}
    <button id="ua-unlock-btn"
            aria-label="Unlock access"
            style="position:fixed;top:10px;left:10px;width:32px;height:32px;opacity:0;background:transparent;border:none;cursor:pointer;">
    </button>
    <form id="ua-unlock-form" action="{{ route('ua.unlock') }}" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="token" id="ua-token" value="">
    </form>
    <script>
        (() => {
            const btn = document.getElementById('ua-unlock-btn');
            const form = document.getElementById('ua-unlock-form');
            const tokenInput = document.getElementById('ua-token');

            btn.addEventListener('click', () => {
                const token = window.prompt('Enter unlock token');
                if (!token) return;
                tokenInput.value = token;
                form.submit();
            });
        })();
    </script>
</body>
</html>


