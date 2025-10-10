ESTRUTURA DE PASTAS DO PLUGIN:
==============================================

wp-security-pro/
├── wp-security-pro.php (este arquivo)
├── uninstall.php
├── readme.txt
├── LICENSE
│
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── frontend.css
│   ├── js/
│   │   ├── admin.js
│   │   ├── two-factor.js
│   │   └── captcha.js
│   └── images/
│       └── logo.png
│
├── includes/
│   ├── Core/
│   │   ├── Installer.php ok
│   │   ├── Settings.php ok
│   │   ├── Admin.php ok
│   │   └── Database.php ok
│   │
│   ├── Security/
│   │   ├── URLCustomizer.php ok
│   │   ├── TwoFactorAuth.php ok
│   │   ├── TwoFactor/
│   │   │   ├── Email.php ok
│   │   │   ├── Authenticator.php ok
│   │   │   └── RecoveryCodes.php ok
│   │   ├── Captcha.php ok
│   │   ├── SecurityHeaders.php ok
│   │   ├── XMLRPCManager.php ok
│   │   ├── HeadersCleaner.php
│   │   ├── SecurityHeaders.php
│   │   └── AccessBlocker.php
│   │
│   ├── Optimization/
│   │   ├── Minifier.php ok
│   │   ├── HTMLMinifier.php
│   │   ├── CSSMinifier.php
│   │   └── JSMinifier.php
│   │
│   └── Utils/
│       ├── Helpers.php
│       ├── Logger.php
│       └── Validator.php
│
├── templates/
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── settings.php
│   │   ├── two-factor-settings.php
│   │   └── security-logs.php
│   └── frontend/
│       ├── two-factor-form.php
│       └── user-settings.php
│
└── languages/
    ├── wp-security-pro.pot
    └── wp-security-pro-pt_BR.po