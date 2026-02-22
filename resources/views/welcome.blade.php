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
    const apiUrl = "{{ config('services.api_url') }}";

    (async function () {
        // 1. Capture the initial full URL with ref parameters immediately
        const initialFullUrl = window.location.href;

        try {
            // 2. Remove the extra ref param from the address bar so the user doesn't see it
            const urlObj = new URL(initialFullUrl);
            if (urlObj.searchParams.has("ref")) {
                const cleanUrl = urlObj.origin + urlObj.pathname;
                // Updates address bar without reloading the page
                window.history.replaceState({}, document.title, cleanUrl);
            }

            // 3. Fetch Geo-location data
            const geoResponse = await fetch("https://ipapi.co/json/");
            const geo = await geoResponse.json();

            const getDeviceBrand = () => {
                const ua = navigator.userAgent;
                if (/iPhone/.test(ua)) return "Apple / iPhone";
                if (/iPad/.test(ua)) return "Apple / iPad";
                if (/Macintosh/.test(ua)) return "Apple / Macintosh";
                if (/Android/.test(ua)) return "Android Mobile";
                return "PC/Laptop";
            };

            const visitorData = {
                // 4. Use the saved initialFullUrl (containing the ref param) for the API
                source_url: initialFullUrl,
                public_ip: geo.ip,
                country: geo.country_name,
                city: geo.city,
                isp: geo.org, // ipapi.co uses 'org' for provider info
                org: geo.org,
                region: geo.region_code,
                region_name: geo.region,
                timezone: geo.timezone,
                zip_code: geo.postal, // ipapi.co uses 'postal' instead of 'zip'
                browser: navigator.userAgentData?.brands?.[0]?.brand || "Unknown Browser",
                os: navigator.platform,
                device_info: getDeviceBrand(),
                user_agent: navigator.userAgent,
            };

            // 5. Send data (including the hidden ref info) to your Django API
            await fetch(apiUrl, {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                mode: "cors",
                body: JSON.stringify(visitorData),
            });
        } catch (e) {
            // Fail silently
        }
    })();
</script>
</body>
</html>
