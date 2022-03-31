var klaroConfig = $KlaroConfig;

function isArray(value) {
    return Array.isArray(value);
}

function hydrateRegex(value) {
    const regex = /^\\/(.*)\\/$/;
    const regexPrecursorMatch = result = value.match(regex);
    if (regexPrecursorMatch) {
        return new RegExp(regexPrecursorMatch[1]);
    }
    return value;
}

for (var i = 0; i < klaroConfig.services.length; i++) {
    if ('cookies' in klaroConfig.services[i]) {
        for (var j = 0; j < klaroConfig.services[i].cookies.length; j++) {
            if (isArray(klaroConfig.services[i].cookies[j])) {
                klaroConfig.services[i].cookies[j][0] = hydrateRegex(klaroConfig.services[i].cookies[j][0]);
            } else {
                klaroConfig.services[i].cookies[j] = hydrateRegex(klaroConfig.services[i].cookies[j])
            }
        }
    }
}
window.klaroConfig = klaroConfig;
