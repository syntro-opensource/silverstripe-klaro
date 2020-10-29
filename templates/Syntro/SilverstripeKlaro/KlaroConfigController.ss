var klaroConfig = {
  translations: {
    de: {
      privacyPolicyUrl: '/datenschutz',
      consentNotice: {
        description: 'Bitte nimm dir einen Moment zeit und schau dir an, welche Services wir für unsere Webseite nutzen. Du hilfst uns damit, unser Angebot zu verbessern.',
      },
      purposes: {
        required: 'Erforderlich',
        // analytics: 'Besucherstatistik',
      },
      session: {
        title: 'PHP Session',
        description: 'Die lokale Session. Ohne funktioniert diese Seite nicht richtig.',
      },
      ganalytics: {
        title: 'Google Analytics',
        description: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ',
      },
    },
  },
  apps: [
    {
      name: 'session',
      purposes: ['required'],
      default: true,
      cookies: ['PHPSESSID'],
      required: true,
    },
    {
      name: 'ganalytics',
      purposes: ['analytics'],
      default: true,
      cookies: ['_ga'],
    },
  ],
};
window.klaroConfig = klaroConfig;
