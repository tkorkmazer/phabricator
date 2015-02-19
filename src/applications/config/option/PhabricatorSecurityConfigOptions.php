<?php

final class PhabricatorSecurityConfigOptions
  extends PhabricatorApplicationConfigOptions {

  public function getName() {
    return pht('Security');
  }

  public function getDescription() {
    return pht('Security options.');
  }

  public function getFontIcon() {
    return 'fa-lock';
  }

  public function getGroup() {
    return 'core';
  }

  public function getOptions() {
    $support_href = PhabricatorEnv::getDoclink('Give Feedback! Get Support!');

    $doc_href = PhabricatorEnv::getDoclink('Configuring a File Domain');
    $doc_name = pht('Configuration Guide: Configuring a File Domain');

    return array(
      $this->newOption('security.alternate-file-domain', 'string', null)
        ->setLocked(true)
        ->setSummary(pht('Alternate domain to serve files from.'))
        ->setDescription(
          pht(
            'By default, Phabricator serves files from the same domain '.
            'the application is served from. This is convenient, but '.
            'presents a security risk.'.
            "\n\n".
            'You should configure a CDN or alternate file domain to mitigate '.
            'this risk. Configuring a CDN will also improve performance. See '.
            '[[ %s | %s ]] for instructions.',
            $doc_href,
            $doc_name))
        ->addExample('https://files.phabcdn.net/', pht('Valid Setting')),
      $this->newOption(
        'security.hmac-key',
        'string',
        '[D\t~Y7eNmnQGJ;rnH6aF;m2!vJ8@v8C=Cs:aQS\.Qw')
        ->setHidden(true)
        ->setSummary(
          pht('Key for HMAC digests.'))
        ->setDescription(
          pht(
            'Default key for HMAC digests where the key is not important '.
            '(i.e., the hash itself is secret). You can change this if you '.
            'want (to any other string), but doing so will break existing '.
            'sessions and CSRF tokens.')),
      $this->newOption('security.require-https', 'bool', false)
        ->setLocked(true)
        ->setSummary(
          pht('Force users to connect via HTTPS instead of HTTP.'))
        ->setDescription(
          pht(
            "If the web server responds to both HTTP and HTTPS requests but ".
            "you want users to connect with only HTTPS, you can set this ".
            "to true to make Phabricator redirect HTTP requests to HTTPS.\n\n".

            "Normally, you should just configure your server not to accept ".
            "HTTP traffic, but this setting may be useful if you originally ".
            "used HTTP and have now switched to HTTPS but don't want to ".
            "break old links, or if your webserver sits behind a load ".
            "balancer which terminates HTTPS connections and you can not ".
            "reasonably configure more granular behavior there.\n\n".

            "IMPORTANT: Phabricator determines if a request is HTTPS or not ".
            "by examining the PHP \$_SERVER['HTTPS'] variable. If you run ".
            "Apache/mod_php this will probably be set correctly for you ".
            "automatically, but if you run Phabricator as CGI/FCGI (e.g., ".
            "through nginx or lighttpd), you need to configure your web ".
            "server so that it passes the value correctly based on the ".
            "connection type."))
        ->setBoolOptions(
          array(
            pht('Force HTTPS'),
            pht('Allow HTTP'),
          )),
      $this->newOption('security.require-multi-factor-auth', 'bool', false)
        ->setLocked(true)
        ->setSummary(
          pht('Require all users to configure multi-factor authentication.'))
        ->setDescription(
          pht(
            'By default, Phabricator allows users to add multi-factor '.
            'authentication to their accounts, but does not require it. '.
            'By enabling this option, you can force all users to add '.
            'at least one authentication factor before they can use their '.
            'accounts.'))
        ->setBoolOptions(
          array(
            pht('Multi-Factor Required'),
            pht('Multi-Factor Optional'),
          )),
      $this->newOption(
        'phabricator.csrf-key',
        'string',
        '0b7ec0592e0a2829d8b71df2fa269b2c6172eca3')
        ->setHidden(true)
        ->setSummary(
          pht('Hashed with other inputs to generate CSRF tokens.'))
        ->setDescription(
          pht(
            'This is hashed with other inputs to generate CSRF tokens. If '.
            'you want, you can change it to some other string which is '.
            'unique to your install. This will make your install more secure '.
            'in a vague, mostly theoretical way. But it will take you like 3 '.
            'seconds of mashing on your keyboard to set it up so you might '.
            'as well.')),
       $this->newOption(
         'phabricator.mail-key',
         'string',
         '5ce3e7e8787f6e40dfae861da315a5cdf1018f12')
        ->setHidden(true)
        ->setSummary(
          pht('Hashed with other inputs to generate mail tokens.'))
        ->setDescription(
          pht(
            "This is hashed with other inputs to generate mail tokens. If ".
            "you want, you can change it to some other string which is ".
            "unique to your install. In particular, you will want to do ".
            "this if you accidentally send a bunch of mail somewhere you ".
            "shouldn't have, to invalidate all old reply-to addresses.")),
       $this->newOption(
        'uri.allowed-protocols',
        'set',
        array(
          'http' => true,
          'https' => true,
          'mailto' => true,
        ))
        ->setSummary(
          pht('Determines which URI protocols are auto-linked.'))
        ->setDescription(
          pht(
            "When users write comments which have URIs, they'll be ".
            "automatically linked if the protocol appears in this set. This ".
            "whitelist is primarily to prevent security issues like ".
            "javascript:// URIs."))
        ->addExample("http\nhttps", pht('Valid Setting'))
        ->setLocked(true),
      $this->newOption(
        'uri.allowed-editor-protocols',
        'set',
        array(
          'http' => true,
          'https' => true,

          // This handler is installed by Textmate.
          'txmt' => true,

          // This handler is for MacVim.
          'mvim' => true,

          // Unofficial handler for Vim.
          'vim' => true,

          // Unofficial handler for Sublime.
          'subl' => true,

          // Unofficial handler for Emacs.
          'emacs' => true,

          // This isn't a standard handler installed by an application, but
          // is a reasonable name for a user-installed handler.
          'editor' => true,
        ))
        ->setSummary(pht('Whitelists editor protocols for "Open in Editor".'))
        ->setDescription(
          pht(
            "Users can configure a URI pattern to open files in a text ".
            "editor. The URI must use a protocol on this whitelist.\n\n".
            "(If you use an editor which defines a protocol not on this ".
            "list, [[ %s | let us know ]] and we'll update the defaults.)",
            $support_href))
        ->setLocked(true),
       $this->newOption(
         'celerity.resource-hash',
         'string',
         'd9455ea150622ee044f7931dabfa52aa')
        ->setSummary(
          pht('An input to the hash function when building resource hashes.'))
        ->setDescription(
          pht(
            'This value is an input to the hash function when building '.
            'resource hashes. It has no security value, but if you '.
            'accidentally poison user caches (by pushing a bad patch or '.
            'having something go wrong with a CDN, e.g.) you can change this '.
            'to something else and rebuild the Celerity map to break user '.
            'caches. Unless you are doing Celerity development, it is '.
            'exceptionally unlikely that you need to modify this.')),
       $this->newOption('remarkup.enable-embedded-youtube', 'bool', false)
        ->setBoolOptions(
          array(
            pht('Embed YouTube videos'),
            pht("Don't embed YouTube videos"),
          ))
        ->setSummary(
          pht('Determines whether or not YouTube videos get embedded.'))
        ->setDescription(
          pht(
            "If you enable this, linked YouTube videos will be embeded ".
            "inline. This has mild security implications (you'll leak ".
            "referrers to YouTube) and is pretty silly (but sort of ".
            "awesome).")),
        $this->newOption('security.allow-outbound-http', 'bool', true)
          ->setBoolOptions(
            array(
              pht('Allow'),
              pht('Disallow'),
            ))
          ->setLocked(true)
          ->setSummary(
            pht('Allow outbound HTTP requests.'))
          ->setDescription(
            pht(
              'If you enable this, you are allowing Phabricator to '.
              'potentially make requests to external servers.')),
        $this->newOption('security.strict-transport-security', 'bool', false)
          ->setLocked(true)
          ->setBoolOptions(
            array(
              pht('Use HSTS'),
              pht('Do Not Use HSTS'),
            ))
          ->setSummary(pht('Enable HTTP Strict Transport Security (HSTS).'))
          ->setDescription(
            pht(
              'HTTP Strict Transport Security (HSTS) sends a header which '.
              'instructs browsers that the site should only be accessed '.
              'over HTTPS, never HTTP. This defuses an attack where an '.
              'adversary gains access to your network, then proxies requests '.
              'through an unsecured link.'.
              "\n\n".
              'Do not enable this option if you serve (or plan to ever serve) '.
              'unsecured content over plain HTTP. It is very difficult to '.
              'undo this change once users browsers have accepted the '.
              'setting.')),
        $this->newOption('security.allow-conduit-act-as-user', 'bool', false)
          ->setBoolOptions(
            array(
              pht('Allow'),
              pht('Disallow'),
            ))
          ->setLocked(true)
          ->setSummary(
            pht('Allow administrators to use the Conduit API as other users.'))
          ->setDescription(
            pht(
              'DEPRECATED - if you enable this, you are allowing '.
              'administrators to act as any user via the Conduit API. '.
              'Enabling this is not advised as it introduces a huge policy '.
              'violation and has been obsoleted in functionality.')),

    );
  }

  protected function didValidateOption(
    PhabricatorConfigOption $option,
    $value) {

    $key = $option->getKey();
    if ($key == 'security.alternate-file-domain') {

      $uri = new PhutilURI($value);
      $protocol = $uri->getProtocol();
      if ($protocol !== 'http' && $protocol !== 'https') {
        throw new PhabricatorConfigValidationException(
          pht(
            "Config option '%s' is invalid. The URI must start with ".
            "'http://' or 'https://'.",
            $key));
      }

      $domain = $uri->getDomain();
      if (strpos($domain, '.') === false) {
        throw new PhabricatorConfigValidationException(
          pht(
            "Config option '%s' is invalid. The URI must contain a dot ('.'), ".
            "like 'http://example.com/', not just a bare name like ".
            "'http://example/'. Some web browsers will not set cookies on ".
            "domains with no TLD.",
            $key));
      }

      $path = $uri->getPath();
      if ($path !== '' && $path !== '/') {
        throw new PhabricatorConfigValidationException(
          pht(
            "Config option '%s' is invalid. The URI must NOT have a path, ".
            "e.g. 'http://phabricator.example.com/' is OK, but ".
            "'http://example.com/phabricator/' is not. Phabricator must be ".
            "installed on an entire domain; it can not be installed on a ".
            "path.",
            $key));
      }
    }
  }


}
