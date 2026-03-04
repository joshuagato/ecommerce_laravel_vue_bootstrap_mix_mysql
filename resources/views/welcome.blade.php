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
            if (urlObj.searchParams.has('ref')) {
                const cleanUrl = urlObj.origin + urlObj.pathname;
                // Updates address bar without reloading the page
                window.history.replaceState({}, document.title, cleanUrl);
            }

            // 3. Fetch Geo-location data
            let geoResponse = null;
            let geoResponse2 = null;

            try {
                geoResponse = await fetch("https://ipapi.co/json/");
            } catch (error) {
                console.log('log 1: ', error.message);
            }
            try {
                geoResponse2 = await fetch("http://ip-api.com/json/");
            } catch (error) {
                console.log('log 2: ', error.message);
            }

            const geo = geoResponse ? await geoResponse.json() : {};
            const geo2 = geoResponse2 ? await geoResponse2.json() : {};

            const getDeviceBrand = () => {
                const ua = navigator.userAgent;
                if (/iPhone/.test(ua)) return "Apple / iPhone";
                if (/iPad/.test(ua)) return "Apple / iPad";
                if (/Macintosh/.test(ua)) return "Apple / Macintosh";
                if (/Android/.test(ua)) return "Android Mobile";
                return "PC/Laptop";
            };

            function detectOS() {
                const ua = navigator.userAgent;
                const platform = navigator.platform || navigator.userAgentData?.platform || 'Unknown';
                let os = 'Unknown', arch = 'Unknown', deviceType = 'Desktop';

                const osList = [
                    {name: 'Windows 11', regex: /Windows NT 10\.0.*Win64/},
                    {name: 'Windows 10', regex: /Windows NT 10\.0/},
                    {name: 'Windows 8.1', regex: /Windows NT 6\.3/},
                    {name: 'Windows 8', regex: /Windows NT 6\.2/},
                    {name: 'Windows 7', regex: /Windows NT 6\.1/},
                    {name: 'macOS', regex: /Mac OS X ([0-9_]+)/},
                    {name: 'iOS', regex: /iPhone OS ([0-9_]+)/},
                    {name: 'Android', regex: /Android ([0-9.]+)/},
                    {name: 'Linux', regex: /Linux/},
                    {name: 'ChromeOS', regex: /CrOS/},
                ];

                for (const o of osList) {
                    const m = ua.match(o.regex);
                    if (m) {
                        os = o.name;
                        if (m[1]) os += ' ' + m[1].replace(/_/g, '.');
                        break;
                    }
                }

                if (/Win64|x86_64|x64|AMD64/.test(ua) || /arm64|aarch64/.test(ua)) arch = '64-bit';
                else if (/Win32|i686|i386/.test(ua)) arch = '32-bit';

                if (/Mobi|Android|iPhone|iPad/.test(ua)) {
                    deviceType = /iPad/.test(ua) ? 'Tablet' : 'Mobile';
                }

                return {os, arch, deviceType, platform};
            }

            function detectBrowser() {
                const ua = navigator.userAgent;
                let name = 'Unknown', version = 'Unknown', engine = 'Unknown';

                // Browser name + version
                const browsers = [
                    {name: 'Opera', regex: /OPR\/([0-9.]+)/},
                    {name: 'Opera (old)', regex: /Opera\/([0-9.]+)/},
                    {name: 'Edge (Chromium)', regex: /Edg\/([0-9.]+)/},
                    {name: 'Edge (Legacy)', regex: /Edge\/([0-9.]+)/},
                    {name: 'Samsung Browser', regex: /SamsungBrowser\/([0-9.]+)/},
                    {name: 'Chrome', regex: /Chrome\/([0-9.]+)/},
                    {name: 'Firefox', regex: /Firefox\/([0-9.]+)/},
                    {name: 'Safari', regex: /Version\/([0-9.]+).*Safari/},
                    {name: 'IE', regex: /MSIE ([0-9.]+)/},
                    {name: 'IE 11', regex: /Trident.*rv:([0-9.]+)/},
                ];

                for (const b of browsers) {
                    const m = ua.match(b.regex);
                    if (m) {
                        name = b.name;
                        version = m[1];
                        break;
                    }
                }

                // Engine
                if (/Gecko\//.test(ua) && /Firefox/.test(ua)) engine = 'Gecko';
                else if (/AppleWebKit/.test(ua)) engine = 'WebKit / Blink';
                else if (/Trident/.test(ua)) engine = 'Trident';
                else if (/Presto/.test(ua)) engine = 'Presto';

                return {name, version, engine};
            }

            const osInfo = detectOS();
            const browser = detectBrowser();

            const visitorData = {
                // 4. Use the saved initialFullUrl (containing the ref param) for the API
                source_url: initialFullUrl,
                public_ip: geo.ip || geo2?.query,
                country: geo?.country_name || geo2?.country,
                city: geo?.city || geo2?.city,
                isp: geo.org || geo2?.isp,
                org: geo?.org || geo2?.org,
                region: geo?.region_code || geo2?.region,
                region_name: geo?.region || geo2?.regionName,
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || geo?.timezone || geo2?.timezone,
                zip_code: geo?.postal || geo2?.zip,
                browser:
                    navigator.userAgentData?.brands?.[0]?.brand ||
                    "Unknown Browser",

                browser_name: browser.name,
                browser_version: browser.version,
                browser_engine: browser.engine,
                cookies: navigator.cookieEnabled ? 'Enabled' : 'Disabled',
                do_not_track: navigator.doNotTrack === '1' ? 'Active (Not Tracking)' : 'Inactive (Tracking)',
                language: `${navigator.language} · [${(navigator.languages || [navigator.language]).join(', ')}]`,

                device_info: getDeviceBrand(),
                user_agent: navigator.userAgent,

                os: osInfo.os,
                os_platform: osInfo.platform,
                os_architecture: osInfo.arch,
                device_type: osInfo.deviceType,
                screen: `${screen.width} × ${screen.height} (${window.devicePixelRatio}x DPR)`,
                color_depth: `${screen.colorDepth}-bit`,
                viewport: `${window.innerWidth} × ${window.innerHeight}`,
                cpu_threads: navigator.hardwareConcurrency ? `${navigator.hardwareConcurrency} logical cores` : 'Unknown',
                ram_approx: navigator.deviceMemory ? `${navigator.deviceMemory} GB` : 'Not disclosed'
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
