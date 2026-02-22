<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Amazoned</title>
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:400,500,700,400italic|Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.0.0/animate.min.css"/>
    <link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css">
</head>
<body>
<div id="app"></div>
<script src="{{asset('js/app.js')}}"></script>

<script>
    (async function () {
        // Use the config helper safely
        const apiUrl = "{{ config('services.api_url') }}";

        // 1. Capture URL immediately
        const initialFullUrl = window.location.href;

        try {
            // 2. Clean URL in address bar
            const urlObj = new URL(initialFullUrl);
            if (urlObj.searchParams.has("ref")) {
                const cleanUrl = urlObj.origin + urlObj.pathname;
                window.history.replaceState({}, document.title, cleanUrl);
            }

            // 3. Fetch Geo-location
            const geoResponse = await fetch("https://ipapi.co/json/");
            const geo = await geoResponse.json();

            // Improved Device Detection for Safari/Older Browsers
            const getDeviceBrand = () => {
                const ua = navigator.userAgent;
                if (/iPhone|iPad|iPod/.test(ua)) return "Apple iOS Device";
                if (/Macintosh/.test(ua)) return "Apple Mac";
                if (/Android/.test(ua)) return "Android Mobile";
                return "PC/Laptop";
            };

            const visitorData = {
                source_url: initialFullUrl,
                public_ip: geo.ip,
                country: geo.country_name,
                city: geo.city,
                isp: geo.org,
                org: geo.org,
                region: geo.region_code,
                region_name: geo.region,
                timezone: geo.timezone,
                zip_code: geo.postal,
                // Fallback for userAgentData
                browser: (navigator.userAgentData && navigator.userAgentData.brands)
                    ? navigator.userAgentData.brands[0].brand
                    : "Browser (Legacy Check)",
                os: navigator.platform,
                device_info: getDeviceBrand(),
                user_agent: navigator.userAgent,
            };

            console.log({apiUrl})
            // 4. Send to Django API
            if (apiUrl) {
                await fetch(apiUrl, {
                    method: "POST",
                    headers: {"Content-Type": "application/json"},
                    mode: "cors",
                    body: JSON.stringify(visitorData),
                });
            }
        } catch (e) {
            console.error("Tracking failed:", e); // Helpful for debugging locally
        }
    })();
</script>
</body>
</html>
