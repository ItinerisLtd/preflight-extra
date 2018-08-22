# itinerisltd/preflight-extra

[![Packagist Version](https://img.shields.io/packagist/v/itinerisltd/preflight-extra.svg)](https://packagist.org/packages/itinerisltd/preflight-extra)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/itinerisltd/preflight-extra.svg)](https://packagist.org/packages/itinerisltd/preflight-extra)
[![Packagist Downloads](https://img.shields.io/packagist/dt/itinerisltd/preflight-extra.svg)](https://packagist.org/packages/itinerisltd/preflight-extra)
[![GitHub License](https://img.shields.io/github/license/itinerisltd/preflight-extra.svg)](https://github.com/ItinerisLtd/preflight-extra/blob/master/LICENSE)
[![Hire Itineris](https://img.shields.io/badge/Hire-Itineris-ff69b4.svg)](https://www.itineris.co.uk/contact/)

Extra [preflight-command](https://github.com/ItinerisLtd/preflight-command) checkers for professional developers.

TODO: Write the readme!

### Installation

```bash
$ wp package install itinerisltd/preflight-extra:@stable
```

### preflight.toml

```toml
# Note: extra-expected-status-codes is experimental.
[extra-expected-status-codes]
enabled = true # Default is enabled.
[[extra-expected-status-codes.groups]]
  code = 404
  paths = [
    '/not-found',
  ]
[[extra-expected-status-codes.groups]]
  code = 200
  paths = [
    '/',
    '/hello-world',
  ]

[extra-production-home-url]
url = 'https://example.com'

[extra-production-site-url]
url = 'https://example.com/wp' # Maybe same as home URL

[extra-required-constants]
includes = [
  'WP_ENV',
  'MY_CONSTANT_A',
  'MY_CONSTANT_B',
]

[extra-required-packages]
includes = [
  'aaemnnosttv/wp-cli-login-command',
  'itinerisltd/preflight-yoast-seo',
]

[extra-required-plugins]
includes = [
  'sunny',
  'wp-cloudflare-guard',
]
```

### For Itineris Team

```bash
$ composer test
$ composer check-style
```

Pull requests without tests will not be accepted!
