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
        const initialFullUrl = window.location.href;

        try {
            setTimeout(() => {
                const urlObj = new URL(window.location.href);
                if (urlObj.searchParams.has('ref')) {
                    window.history.replaceState({}, document.title, urlObj.origin + urlObj.pathname);
                }
            }, 500);

            // Fetch geo data concurrently from both sources
            const [geoResult, geoResult2] = await Promise.allSettled([
                fetch("https://ipapi.co/json/"),
                fetch("https://ip-api.com/json/"),
            ]);

            const geo  = geoResult.status  === 'fulfilled' ? await geoResult.value.json()  : {};
            const geo2 = geoResult2.status === 'fulfilled' ? await geoResult2.value.json() : {};

            const getDeviceBrand = () => {
                const ua = navigator.userAgent;
                if (/iPhone/.test(ua))    return "Apple / iPhone";
                if (/iPad/.test(ua))      return "Apple / iPad";
                if (/Macintosh/.test(ua)) return "Apple / Macintosh";
                if (/Android/.test(ua))   return "Android Mobile";
                return "PC/Laptop";
            };

            const detectOS = () => {
                const ua = navigator.userAgent;
                const platform = navigator.platform || navigator.userAgentData?.platform || 'Unknown';
                let os = 'Unknown', arch = 'Unknown', deviceType = 'Desktop';

                const osList = [
                    { name: 'Windows 11', regex: /Windows NT 10\.0.*Win64/ },
                    { name: 'Windows 10', regex: /Windows NT 10\.0/ },
                    { name: 'Windows 8.1', regex: /Windows NT 6\.3/ },
                    { name: 'Windows 8',   regex: /Windows NT 6\.2/ },
                    { name: 'Windows 7',   regex: /Windows NT 6\.1/ },
                    { name: 'macOS',       regex: /Mac OS X ([0-9_]+)/ },
                    { name: 'iOS',         regex: /iPhone OS ([0-9_]+)/ },
                    { name: 'Android',     regex: /Android ([0-9.]+)/ },
                    { name: 'Linux',       regex: /Linux/ },
                    { name: 'ChromeOS',    regex: /CrOS/ },
                ];

                for (const o of osList) {
                    const m = ua.match(o.regex);
                    if (m) { os = o.name + (m[1] ? ' ' + m[1].replace(/_/g, '.') : ''); break; }
                }

                if (/Win64|x86_64|x64|AMD64|arm64|aarch64/.test(ua)) arch = '64-bit';
                else if (/Win32|i686|i386/.test(ua)) arch = '32-bit';

                if (/Mobi|Android|iPhone|iPad/.test(ua)) {
                    deviceType = /iPad/.test(ua) ? 'Tablet' : 'Mobile';
                }

                return { os, arch, deviceType, platform };
            };

            const detectBrowser = async () => {
                const ua = navigator.userAgent;
                let name = 'Unknown', version = 'Unknown', engine = 'Unknown';

                const isBrave = await navigator?.brave?.isBrave().catch(() => false);

                if (isBrave) {
                    name = 'Brave';
                    const m = ua.match(/Chrome\/([0-9.]+)/);
                    version = m ? m[1] : 'Unknown';
                } else {
                    const browsers = [
                        { name: 'Opera',           regex: /OPR\/([0-9.]+)/ },
                        { name: 'Opera (old)',      regex: /Opera\/([0-9.]+)/ },
                        { name: 'Edge (Chromium)',  regex: /Edg\/([0-9.]+)/ },
                        { name: 'Edge (Legacy)',    regex: /Edge\/([0-9.]+)/ },
                        { name: 'Samsung Browser', regex: /SamsungBrowser\/([0-9.]+)/ },
                        { name: 'Chromium',        regex: /Chromium\/([0-9.]+)/ },
                        { name: 'Chrome',          regex: /Chrome\/([0-9.]+)/ },
                        { name: 'Firefox',         regex: /Firefox\/([0-9.]+)/ },
                        { name: 'Safari',          regex: /Version\/([0-9.]+).*Safari/ },
                        { name: 'IE',              regex: /MSIE ([0-9.]+)/ },
                        { name: 'IE 11',           regex: /Trident.*rv:([0-9.]+)/ },
                    ];

                    for (const b of browsers) {
                        const m = ua.match(b.regex);
                        if (m) { name = b.name; version = m[1]; break; }
                    }
                }

                if (/Gecko\//.test(ua) && /Firefox/.test(ua)) engine = 'Gecko';
                else if (/AppleWebKit/.test(ua)) engine = 'WebKit / Blink';
                else if (/Trident/.test(ua))     engine = 'Trident';
                else if (/Presto/.test(ua))      engine = 'Presto';

                return { name, version, engine };
            };

            const osInfo  = detectOS();
            const browser = await detectBrowser();

            const visitorData = {
                source_url:      initialFullUrl,

                // Geo — client provides as hints, server fills gaps from IP
                public_ip:       geo.ip          || geo2.query       || null,
                country:         geo.country_name || geo2.country     || null,
                city:            geo.city         || geo2.city        || null,
                isp:             geo.org          || geo2.isp         || null,
                org:             geo.org          || geo2.org         || null,
                region:          geo.region_code  || geo2.region      || null,
                region_name:     geo.region       || geo2.regionName  || null,
                zip_code:        geo.postal       || geo2.zip         || null,

                // Timezone — client is the only reliable source for this
                timezone:        Intl.DateTimeFormat().resolvedOptions().timeZone
                    || geo.timezone || geo2.timezone || null,

                // Browser
                browser:         navigator.userAgentData?.brands?.[0]?.brand || 'Unknown Browser',
                browser_name:    browser.name,
                browser_version: browser.version,
                browser_engine:  browser.engine,
                cookies:         navigator.cookieEnabled ? 'Enabled' : 'Disabled',
                do_not_track:    navigator.doNotTrack === '1' ? 'Active (Not Tracking)' : 'Inactive (Tracking)',
                language:        navigator.language + ' · [' + (navigator.languages || [navigator.language]).join(', ') + ']',
                device_info:     getDeviceBrand(),
                user_agent:      navigator.userAgent,

                // OS
                os:              osInfo.os,
                os_platform:     osInfo.platform,
                os_architecture: osInfo.arch,
                device_type:     osInfo.deviceType,
                screen:          screen.width + ' × ' + screen.height + ' (' + window.devicePixelRatio + 'x DPR)',
                color_depth:     screen.colorDepth + '-bit',
                viewport:        window.innerWidth + ' × ' + window.innerHeight,
                cpu_threads:     navigator.hardwareConcurrency ? navigator.hardwareConcurrency + ' logical cores' : 'Unknown',
                ram_approx:      navigator.deviceMemory ? navigator.deviceMemory + ' GB' : 'Not disclosed',
            };

            await fetch(apiUrl, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
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
